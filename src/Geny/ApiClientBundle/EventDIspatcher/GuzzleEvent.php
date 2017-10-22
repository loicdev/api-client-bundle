<?php

namespace Geny\ApiClientBundle\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class GuzzleEvent
 * @package Geny\ApiClientBundle\EventDispatcher
 */
class GuzzleEvent extends Event {

    const EVENT_NAME = 'gs.guzzlehttp';
    const EVENT_ERROR_NAME = 'gs.guzzlehttp.error';

    /**
     * @var float
     */
    protected $executionStart;

    /**
     * @var float
     */
    protected $executionTime;

    /**
     * @var mixed
     */
    protected $reason;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $apiName;

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set Response
     *
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }
    /**
     * Return response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * Set reason
     *
     * @param mixed $reason
     *
     * @return $this
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }
    /**
     * Return reason
     *
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }
    /**
     * @return float
     */
    public function getExecutionStart()
    {
        return $this->executionStart;
    }
    /**
     * Set execution start of a request
     *
     * @return GuzzleHttpEvent
     */
    public function setExecutionStart()
    {
        $this->executionStart = microtime(true);
        return $this;
    }
    /**
     * Stop the execution of a request
     * and set the request execution time
     *
     * @return GuzzleHttpEvent
     */
    public function setExecutionStop()
    {
        $this->executionTime = microtime(true) - $this->executionStart;
        return $this;
    }
    /**
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }
    /**
     * Return execution time in milliseconds
     *
     * @return float
     */
    public function getTiming()
    {
        return $this->getExecutionTime() * 1000;
    }
    /**
     * Get client ID
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }
    /**
     * Set client ID
     *
     * @param string $clientId
     *
     * @return GuzzleHttpEvent
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
        return $this;
    }

}