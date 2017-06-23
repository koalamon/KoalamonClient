<?php

namespace Koalamon\Client\Reporter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Koalamon\Client\Reporter\Event\Processor\Processor;
use Koalamon\Client\Reporter\Event\Processor\SimpleProcessor;

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

    private $processor;

    private $koalamonWebhookServer = 'https://webhook.koalamon.com/';
    private $koalamonInformationServer = 'https://monitor.koalamon.com/';

    const ENDPOINT_INFORMATION_DEFAULT = "/api/information/";
    const ENDPOINT_INFORMATION_DEFAULT_DEBUG = "/app_dev.php/api/information/";

    const RESPONSE_STATUS_SUCCESS = "success";
    const RESPONSE_STATUS_FAILURE = "failure";

    /**
     * @param $project string The project name you want to report the event for.
     * @param $apiKey  string The api key can be found on the admin page of a project,
     *                 which can be seen if you are the project owner.
     * @param Client|null $httpClient
     */
    public function __construct($project, $apiKey, Client $httpClient = null, $koalamonWebhookServer = null, $koalamonInformationServer = null)
    {
        $this->project = $project;
        $this->apiKey = $apiKey;

        if (is_null($httpClient)) {
            $this->httpClient = new Client();
        } else {
            $this->httpClient = $httpClient;
        }

        if (!is_null($koalamonWebhookServer)) {
            $this->koalamonWebhookServer = $koalamonWebhookServer;
        }

        if (!is_null($koalamonInformationServer)) {
            $this->koalamonInformationServer = $koalamonInformationServer;
        }

        $this->processor = new SimpleProcessor();
    }

    public function setEventProcessor(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * This function will send the given event to the koalamon default webhook
     *
     * @param Event $event
     * @param bool|false $debug
     */
    public function send(Event $event, $debug = false)
    {
        $this->sendEvent($event, $debug);
    }

    public function sendEvent(Event $event, $debug = false)
    {
        $endpointWithApiKey = "?api_key=" . $this->apiKey;
        $response = $this->getJsonResponse($this->koalamonWebhookServer . $endpointWithApiKey, $event);

        if (is_null($response)) {
            throw new \RuntimeException("Failed sending event to " . $this->koalamonWebhookServer . $endpointWithApiKey);
        }

        if ($response->status != self::RESPONSE_STATUS_SUCCESS) {
            throw new \RuntimeException("Failed sending event with message '" . $response->message . "'");
        }
    }

    public function sendInformation(Information $information, $debug = false)
    {
        if ($debug) {
            $endpoint = self::ENDPOINT_INFORMATION_DEFAULT_DEBUG;
        } else {
            $endpoint = self::ENDPOINT_INFORMATION_DEFAULT;
        }

        $endpointWithApiKey = $this->koalamonInformationServer . $endpoint . "?api_key=" . $this->apiKey;
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
    private function getJsonResponse($endpoint, Event $event)
    {
        $objectJson = json_encode($this->processor->process($event));

        var_dump($endpoint);
        var_dump($objectJson);

        try {
            $response = $this->httpClient->request('POST', $endpoint, ['body' => $objectJson]);
        } catch (\Exception $e) {
            $ex = new KoalamonException('Error sending event to Koalamon server. (' . $e->getMessage() . ')');
            $ex->setPayload($objectJson);
            $ex->setUrl($endpoint);

            throw $ex;
        }

        $responseStatus = json_decode($response->getBody());

        return $responseStatus;
    }
}
