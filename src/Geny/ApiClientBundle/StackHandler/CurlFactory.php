<?php

namespace Geny\ApiClientBundle\StackHandler;

use GuzzleHttp\Handler\CurlFactory as GuzzleCurlFactory;
use GuzzleHttp\Handler\EasyHandle;

/**
 * Extends the Guzzle curl factory to set curl info in response
 */
class CurlFactory extends GuzzleCurlFactory
{
    /**
     * {@inheritdoc}
     */
    public function release(EasyHandle $easy)
    {
        if (!is_null($easy->response)) {
            $easy->response->curlInfo = curl_getinfo($easy->handle);
        }

        return parent::release($easy);
    }
}
