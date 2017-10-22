<?php

namespace Geny\ApiClientBundle\StackHandler;

use GuzzleHttp\Handler\CurlMultiHandler as GuzzleCurlMultiHandler;

/**
 * extends guzzle CurlMultiHandler
 */
class CurlMultiHandler extends GuzzleCurlMultiHandler
{
    use CacheTrait;
}