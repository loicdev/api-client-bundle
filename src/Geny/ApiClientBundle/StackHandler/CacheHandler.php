<?php

namespace Geny\ApiClientBundle\StackHandler;


use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Kevinrob\GuzzleCache\Strategy\PublicCacheStrategy;
use Kevinrob\GuzzleCache\Storage\DoctrineCacheStorage;
use Doctrine\Common\Cache\PredisCache;
use Predis;

/**
 * FactoryClass CacheHandler
 */
class CacheHandler
{

    /**
     * @param string $client
     * @param string $port
     * @return HandlerStack
     */
    public  static function predisHandler($stack, $client, $port, $cache)
    {
        $stack->push(
            new CacheMiddleware(
                new GreedyCacheStrategy(
                    new DoctrineCacheStorage(
                        new PredisCache(
                            new Predis\Client(sprintf('tcp://%s:%s',$client,$port))
                        )
                    ),$cache
                )
            ),
            'predis-cache'
        );

        return $stack;

    }


}