<?php

namespace Geny\ApiClientBundle\DependencyInjection;

use Geny\ApiClientBundle\Http\Rest\RestApiClientBridge;
use Geny\ApiClientBundle\StackHandler\CacheHandler;
use Geny\ApiClientBundle\StackHandler\CurlFactory;
use Geny\ApiClientBundle\StackHandler\CurlHandler;
use Geny\ApiClientBundle\StackHandler\CurlMultiHandler;
use Geny\ApiClientBundle\StackHandler\LogHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\Proxy;
use GuzzleHttp\HandlerStack;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Geny\ApiClientBundle\Middleware\EventDispatcherMiddleware;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class GenyApiClientExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['api'] as $apiName => $apiConfiguration) {
            $this->setGuzzleProxyHandler($container, $apiName, $config['api'][$apiName]);


            $handlerStackDefinition = new Definition(HandlerStack::class);
            $handlerStackDefinition->setFactory([HandlerStack::class, 'create']);
            $handlerStackDefinition->setArguments([new Reference('geny.guzzle.proxyhandler_'.$apiName)]);

            $container->setDefinition('guzzlehttp.guzzle.handlerstack.'.$apiName, $handlerStackDefinition);

            $handlerStackReference = new Reference('guzzlehttp.guzzle.handlerstack.' . $apiName);

            $config['middleware'] = array();
            // Middleware http to profile Api calls in Data Collector
            if ($apiConfiguration['profiler']) {
                $middlewareEventDispatcherDefinition = new Definition(EventDispatcherMiddleware::class);
                $middlewareEventDispatcherDefinition->setArguments([new Reference('event_dispatcher'), sprintf('guzzle.%s', $apiName)]);
                $middlewareEventDispatcherDefinition->addMethodCall('push', [$handlerStackReference]);
                $container->setDefinition('guzzlehttp.guzzle.middleware.' . $apiName, $middlewareEventDispatcherDefinition);
                $config['middleware'][] = new Reference('guzzlehttp.guzzle.middleware.' . $apiName);
            }
            // Monolog Middleware
            if ($apiConfiguration['log']) {
                $monologHandlerDefinition = new Definition(LogHandler::class);
                $monologHandlerDefinition->setFactory(array(
                    LogHandler::class,
                    'monologHandler'
                ));

                $container->setDefinition('gs.monolog_handler' . $apiName, $monologHandlerDefinition);
                $monologHandlerDefinition->setArguments(array(
                    $handlerStackReference,
                    $apiName,
                    new Reference('kernel')
                ));

                $config['middleware'][] = new Reference('gs.monolog_handler' . $apiName);
            }


            // Create a  guzzle service client by api parameter
            $guzzleServiceId = sprintf('guzzle.%s',$apiName);
            $definition = new Definition(Client::class);


            //Set baseUri parameter in guzzle service
            $definition->setArguments(array(
                    array(
                        'base_uri' => $apiConfiguration['endpoint_root'],
                        'connect_timeout' => '5',
                        'middleware' => $config['middleware'],
                        'handler' => (isset($config['handler']) ? $config['handler'] : $handlerStackReference )
                    )
                )
            );


            //Guzzle service cannot be available from container
            $definition->setPublic(true);

            $container->setDefinition($guzzleServiceId,$definition);

            // Create a client class by guzzle client
            $container->setDefinition(sprintf('api.%s',$apiName), new Definition(
                 ($apiConfiguration['client']) ? $apiConfiguration['client'] : RestApiClientBridge::class,
                array(new Reference($guzzleServiceId))
            ));

        }
    }

    /**
     * Set proxy handler definition for the client
     *
     * @param ContainerBuilder $container
     * @param string           $clientId
     * @param array            $config
     */
    protected function setGuzzleProxyHandler(ContainerBuilder $container, $clientId, array $config)
    {
        // arguments (3 and 50) in handler factories below represents the maximum number of idle handles.
        // the values are the default defined in guzzle CurlHanddler and CurlMultiHandler
        $handlerFactorySync = new Definition(CurlFactory::class);
        $handlerFactorySync->setArguments([3]);

        $handlerFactoryNormal = new Definition(CurlFactory::class);
        $handlerFactoryNormal->setArguments([50]);

        $curlhandler = new Definition(CurlHandler::class);
        $curlhandler->setArguments([ ['handle_factory' => $handlerFactorySync] ]);
        $curlhandler->addMethodCall('setDebug', [$container->getParameter('kernel.debug')]);

        $curlMultihandler = new Definition(CurlMultiHandler::class);
        $curlMultihandler->setArguments([ ['handle_factory' => $handlerFactoryNormal] ]);
        $curlMultihandler->addMethodCall('setDebug', [$container->getParameter('kernel.debug')]);

        if (array_key_exists('cache', $config)) {
            $defaultTtl = $config['cache']['ttl'];
            $headerTtl = $config['cache']['use_header_ttl'];
            $cacheServerErrors = $config['cache']['cache_server_errors'];
            $cacheClientErrors = $config['cache']['cache_client_errors'];
            if (is_null($cacheService = $this->getServiceReference($container, $config['cache']['service']))) {
                throw new \InvalidArgumentException(sprintf(
                    '"cache.service" requires a valid service reference, "%s" given',
                    $config['cache']['service']
                ));
            }

            $curlhandler->addMethodCall('setCache', [$cacheService, $defaultTtl, $headerTtl, $cacheServerErrors, $cacheClientErrors]);
            $curlMultihandler->addMethodCall('setCache', [$cacheService, $defaultTtl, $headerTtl, $cacheServerErrors, $cacheClientErrors]);
        }

        $proxyHandler = new Definition(Proxy::class);
        $proxyHandler->setFactory([Proxy::class, 'wrapSync']);
        $proxyHandler->setArguments([$curlMultihandler, $curlhandler]);

        $container->setDefinition('geny.guzzle.proxyhandler_'.$clientId, $proxyHandler);
    }

    protected function getServiceReference(ContainerBuilder $container, $id)
    {
        if (substr($id, 0, 1) == '@') {
            return new Reference(substr($id, 1));
        }

        return null;
    }
}
