<?php

namespace Geny\ApiClientBundle\StackHandler;

use GuzzleHttp\Handler\CurlHandler as GuzzleCurlHandler;

/**
 * Extends the guzzle curl handler
 */
class CurlHandler extends GuzzleCurlHandler
{
    use CacheTrait;
}