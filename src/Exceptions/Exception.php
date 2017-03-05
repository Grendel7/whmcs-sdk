<?php

namespace HansAdema\WhmcsSdk\Exceptions;

use Psr\Http\Message\ResponseInterface;

class Exception extends \Exception
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Build a new exception
     *
     * @param ResponseInterface $response
     */
    public function __construct($response)
    {
        $data = json_decode($response->getBody(), true);

        parent::__construct($data['message'], $response->getStatusCode());

        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}