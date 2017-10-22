<?php

namespace Geny\ApiClientBundle\Http\Rest;

/**
 * Interface RestApiClientInterface
 * @package Geny\ApiClientBundle\Http\Rest
 */
interface RestApiClientInterface {

    /**
     * Get method
     *
     * @param $url
     * @param $headers
     * @return mixed
     */
    public function get($url, $headers = array());


    /**
     * Post Method
     *
     * @param $url
     * @param $postBody
     * @param $headers
     * @return mixed
     */
    public function post($url,$postBody, $headers = array());

    /**
     * Put Method
     *
     * @param $url
     * @param $putBody
     * @param $headers
     * @return mixed
     */
    public function put($url, $putBody, $headers = array());

    /**
     * Delete Method
     *
     * @param $url
     * @param $headers
     * @return mixed
     */
    public function delete($url, $headers = array());

}