<?php

namespace Geny\ApiClientBundle\StackHandler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;

//Test

use Symfony\Component\HttpKernel\Kernel;

/**
 * FactoryClass LogHandler
 * @package Geny\ApiClientBundle\StackHandler
 */
class LogHandler {

    /**
     * @param $stack
     * @param $apiName
     * @param $logDir
     * @return HandlerStack
     */
    public  static function monologHandler($stack, $apiName, Kernel $kernel)
    {
        $logger = new Logger(sprintf('api.%s', $apiName));
        $logDir = $kernel->getLogDir();

        $logger->pushHandler(
            new StreamHandler($logDir.sprintf('/api/api-%s-%s.log', $apiName, $kernel->getEnvironment()))
        );

        $stack->push(
            Middleware::log(
                $logger,
                new MessageFormatter()
            )
        );
        

        return $stack;
    }
}
