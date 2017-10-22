<?php

namespace Geny\ApiClientBundle\DependencyInjection\Compiler;

use Geny\ApiClientBundle\Middleware\EventDispatcherMiddleware;
use GuzzleHttp\HandlerStack;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

class GuzzleCompilerPass implements CompilerPassInterface
{


    public function process(ContainerBuilder $container)
    {
//       $loaderTags = $container->findTaggedServiceIds('guzzle.ws');
//        foreach ($loaderTags as $loaderId => $tags)
//        {
//            $loaderDefinition = $container->getDefinition($loaderId);
//            $loaderReflection = new \ReflectionClass($loaderDefinition->getClass());
//            foreach ($tags as $attributes)
//            {
//                $apiName = explode('.',$loaderId);
//                $config = array();
//
//                if (isset($attributes['redis'])){
//                    $predisHandler = $container->getDefinition(sprintf('%s','gs.predis.handler'));
//                    $predisHandler->setArguments(array(
//                        new Reference('guzzlehttp.guzzle.handlerstack.'.$apiName[1]),
//                        $attributes['redis_client'],
//                        $attributes['redis_port'],
//                        $attributes['redis_cache'],
//                    ));
//
//                    $config['handler'] = new Reference('gs.predis.handler');
//
//                }
//
//                $arguments = array_merge($loaderDefinition->getArgument(0),$config);
//
//                $loaderDefinition->replaceArgument(0, $arguments);
//
//            }
//
//
//        }


    }
}