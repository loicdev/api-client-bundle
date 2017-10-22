<?php

namespace Geny\ApiClientBundle\Middleware;

use GuzzleHttp\HandlerStack;

/**
 * Interface MiddlewareInterface
 * @package Geny\ApiClientBundle\Middleware
 */
interface MiddlewareInterface
{

    /**
     * Push function to middleware handler
     *
     * @param HandlerStack $stack
     *
     * @return HandlerStack
     */
    public function push(HandlerStack $stack);
}