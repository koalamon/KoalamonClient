<?php

namespace Koalamon\EventReporter;

/**
 * Class ServerException
 *
 * This exception will be thrown if the koalamon webhook returns an error.
 *
 * @package Koalamon\EventReporter
 */
class ServerException extends \RuntimeException
{
    /**
     * @var Response
     */
    private $response;

    function __construct($message, Response $response)
    {
        parent::__construct($message);
        // $this->jsonResponse = $jsonReponse;
        $this->response = $response;
    }

    /**
     * Returns the full response from the server.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
