<?php

namespace Koalamon\Client\Reporter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

/**
 * Class Reporter
 *
 * This class can be used to report an event to the koalamon applicaton.
 *
 * @package Koalamon\EventReporter
 * @author Nils Langner <nils.langner@koalamon.com>
 */
class Reporter
{
    private $apiKey;
    private $project;

    /**
     * @var HttpAdapterInterface
     */
    private $httpClient;

    private $koalamonServer = 'http://www.koalamon.com';

    const ENDPOINT_WEBHOOK_DEFAULT = "/webhook/";
    const ENDPOINT_WEBHOOK_DEFAULT_DEBUG = "/app_dev.php/webhook/";

    const ENDPOINT_INFORMATION_DEFAULT = "/api/information/";
    const ENDPOINT_INFORMATION_DEFAULT_DEBUG = "/app_dev.php/api/information/";

    const RESPONSE_STATUS_SUCCESS = "success";
    const RESPONSE_STATUS_FAILURE = "failure";

    /**
     * @param $project The project name you want to report the event for.
     * @param $apiKey  The api key can be found on the admin page of a project,
     *                 which can be seen if you are the project owner.
     * @param null $httpClient
     */
    public function __construct($project, $apiKey, Client $httpClient = null, $koalamonServer = null)
    {
        $this->project = $project;
        $this->apiKey = $apiKey;

        if (is_null($httpClient)) {
            $this->httpClient = new Client();
        } else {
            $this->httpClient = $httpClient;
        }

        if (!is_null($koalamonServer)) {
            $this->koalamonServer = $koalamonServer;
        }
    }

    /**
     * This function will send the given event to the koalamon default webhook
     *
     * @param Event $event
     * @param bool|false $debug
     */
    public function send(Event $event, $debug = false)
    {
        $this->send($event, $debug);
    }

    public function sendEvent(Event $event, $debug = false)
    {
        if ($debug) {
            $endpoint = self::ENDPOINT_WEBHOOK_DEFAULT_DEBUG;
        } else {
            $endpoint = self::ENDPOINT_WEBHOOK_DEFAULT;
        }

        $endpointWithApiKey = $endpoint . "?api_key=" . $this->apiKey;
        $response = $this->getJsonResponse($endpointWithApiKey, $event);

        if ($response->status != self::RESPONSE_STATUS_SUCCESS) {
            throw new \RuntimeException("Failed sending event with message '" . $response->message . "'");
        }
    }

    public function sendInformation(Information $information, $debug)
    {
        if ($debug) {
            $endpoint = self::ENDPOINT_INFORMATION_DEFAULT_DEBUG;
        } else {
            $endpoint = self::ENDPOINT_INFORMATION_DEFAULT;
        }

        $endpointWithApiKey = $endpoint . "?api_key=" . $this->apiKey;
        $response = $this->getJsonResponse($endpointWithApiKey, $information);

        if ($response->status != self::RESPONSE_STATUS_SUCCESS) {
            throw new ServerException("Failed sending event (" . $response->message . ").", $response);
        }
    }

    /**
     * Returns the json answer of the web server.
     *
     * failure:
     * {
     *   status: "failure",
     *   message: "unknown api key"
     * }
     *
     * success:
     * {
     *   status: "success"
     * }
     */
    private function getJsonResponse($endpoint, \JsonSerializable $object)
    {
        $objectJson = json_encode($object);

        try {
            $response = $this->httpClient->request('POST', $this->koalamonServer . $endpoint, ['body' => $objectJson]);
        } catch (\Exception $e) {
            throw $e;
        }

        $responseStatus = json_decode($response->getBody());

        return $responseStatus;
    }
}
