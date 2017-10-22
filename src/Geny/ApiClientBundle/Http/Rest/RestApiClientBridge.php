<?php

namespace Geny\ApiClientBundle\Http\Rest;

use Geny\ApiClientBundle\Http\Rest\RestApiClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;

/**
 * Class RestApiClientBridge
 * @package Geny\ApiClientBundle\Http\Rest
 */
class RestApiClientBridge  implements RestApiClientInterface{

    /**
     * @var ClientInterface
     */
    protected $client;


    /**
     * RestApiClientBridge constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Get method
     *
     * @param $url
     * @param array $headers
     * @return json
     */
    public function get($url, $headers = array())
    {
        $response = $this->client->request('GET', $url , $headers);

        return $response;
    }

    /**
     * Post Method
     *
     * @param $url
     * @param $postBody
     * @param array $headers
     * @return mixed
     */
    public function post($url, $postBody, $headers = array())
    {
        $request  = new Request('POST', $url, $headers, ( $postBody === null ? null :json_encode($postBody)));
        $response = $this->client->send($request);

        return $response;
    }

    /**
     * Put Method
     *
     * @param $url
     * @param $putBody
     * @param array $headers
     * @return mixed
     */
    public function put($url, $putBody, $headers = array())
    {
        $result  = null;

        $request  = new Request('PUT', $url, $headers, json_encode($putBody));
        try {
            $response = $this->client->send($request);
            $result  = $response->getStatusCode();
        } catch (\Exception $e) {
            $result = 'error';
        }

        return $result;
    }

    /**
     * Delete Method
     *
     * @param $url
     * @param array $headers
     * @return mixed
     */
    public function delete($url, $headers = array())
    {
        $request  = new Request('DELETE', $url, $headers);
        $response = $this->client->send($request);

        return $response;
    }
}