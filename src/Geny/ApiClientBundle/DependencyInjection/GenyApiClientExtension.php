<?php

namespace Geny\ApiClientBundle\DependencyInjection;

use Geny\ApiClientBundle\Http\Rest\RestApiClientBridge;
use Geny\ApiClientBundle\StackHandler\CacheHandler;
use Geny\ApiClientBundle\StackHandler\LogHandler;
use GuzzleHttp\Client;
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

            $handlerStackDefinition = new Definition(HandlerStack::class);
            $handlerStackDefinition->setFactory([HandlerStack::class, 'create']);

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

            // Handler Redis for Guzzle
            $redis = array();
            if (isset($apiConfiguration['redis']) && $apiConfiguration['redis']['client'] && $apiConfiguration['redis']['port'])
            {

                $predisHandlerDefinition = new Definition(CacheHandler::class);
                $predisHandlerDefinition->setFactory(array(
                    CacheHandler::class,
                    'predisHandler'
                ));

                $container->setDefinition('gs.predis_handler.'.$apiName, $predisHandlerDefinition);

                $predisHandlerDefinition->setArguments(array(
                    $handlerStackReference,
                    $apiConfiguration['redis']['client'],
                    $apiConfiguration['redis']['port'],
                    $apiConfiguration['redis']['cache'],
                ));

                $config['handler'] = new Reference('gs.predis_handler.'.$apiName);

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
            $definition->addTag('guzzle.ws',$redis);

            $container->setDefinition($guzzleServiceId,$definition);

            // Create a client class by guzzle client
            $container->setDefinition(sprintf('api.%s',$apiName), new Definition(
                 ($apiConfiguration['client']) ? $apiConfiguration['client'] : RestApiClientBridge::class,
                array(new Reference($guzzleServiceId))
            ));

        }
    }
}
