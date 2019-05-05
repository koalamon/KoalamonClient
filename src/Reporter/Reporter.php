<?php

namespace Koalamon\Client\Reporter;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Koalamon\Client\Reporter\Event\Attribute;
use Koalamon\Client\Reporter\Event\Processor\Processor;
use Koalamon\Client\Reporter\Event\Processor\SimpleProcessor;
use phm\HttpWebdriverClient\Http\Client\HttpClient;

/**
 * Class Reporter
 *
 * This class can be used to report an event to the koalamon applicaton.
 *
 * @package Koalamon\EventReporter
 * @author Nils Langner <nils.langner@koalamon.com>
 */
interface Reporter
{
    public function setEventProcessor(Processor $processor);

    /**
     * This function will send the given event to the koalamon default webhook
     *
     * @param Event $event
     * @param bool|false $debug
     */
    public function send(Event $event, $debug = false);

    /**
     * @param Event $event
     * @param bool $debug
     * @throws KoalamonException
     */
    public function sendEvent(Event $event, $debug = false);
}
