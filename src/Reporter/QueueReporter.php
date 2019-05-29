<?php

namespace Koalamon\Client\Reporter;

use Koalamon\Client\Reporter\Event\Attribute;
use Koalamon\Client\Reporter\Event\Processor\Processor;
use Koalamon\Client\Reporter\Event\Processor\SimpleProcessor;

/**
 * Class QueueReporter
 *
 * This class can be used to report an event to the leankoala redis queue.
 *
 * @package Koalamon\Client\Reporter
 * @author Nils Langner <nils.langner@leankoala.com>
 */
class QueueReporter implements Reporter
{
    const QUEUE_EVENT = 'event';

    private $apiKey;

    private $processor;

    /**
     * @var \Redis
     */
    private $redis;

    private $redisHost;

    private $queue;

    /**
     * @param $apiKey  string The api key can be found on the admin page of a project,
     *                 which can be seen if you are the project owner.
     * @param string $redisServer
     * @param string $redisPassword
     */
    public function __construct($apiKey, $woodstockServer, $woodstockPassword, $woodstockQueue = 'event')
    {
        if (class_exists("\Redis")) {
            $this->redis = new \Redis();
            $this->redis->connect($woodstockServer);
            $this->redis->auth($woodstockPassword);
        }

        $this->redisHost = $woodstockServer;

        $this->queue = $woodstockQueue;

        $this->apiKey = $apiKey;
        $this->processor = new SimpleProcessor();
    }

    /**
     * @param Processor $processor
     */
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

    /**
     * Add an event to the redis event queue
     *
     * @param Event $event
     * @param string $queue
     * @param bool $debug
     */
    public function sendEvent(Event $event, $debug = true)
    {
        $data = [
            'event' => $this->processor->process($event),
            'apiKey' => $this->apiKey,
            'date' => date('Y-m-d H:i:s')
        ];

        if ($this->redis) {
            $this->redis->lPush($this->queue, json_encode($data));
        } else {
            throw new \RuntimeException('Redis not initialized. Tried to send: ' . json_encode($data));
        }

        if ($debug) {
            var_dump($this->redisHost . '/' . $this->queue);
            var_dump(json_encode($data));
        }
    }

    /**
     * @param $projectId
     * @param Failure[] $failures
     */
    public function sendFailures($failures)
    {
        foreach ($failures as $failure) {
            $this->sendFailure($failure);
        }
    }

    public function sendFailure(Failure $failure)
    {
        $event = new Event(
            'worker_failure',
            '__system__koala____WORKER__',
            Event::STATUS_SUCCESS, '',
            '',
            null
        );

        if ($failure->getSystemId()) {
            $event->addAttribute(new Attribute('componentId', $failure->getSystemId()));
        }

        $event->addAttribute(new Attribute('message', $failure->getMessage()));
        $event->addAttribute(new Attribute('type', $failure->getType()));
        $event->addAttribute(new Attribute('tool', $failure->getTool()));
        $event->addAttribute(new Attribute('command', $failure->getCommand()));

        // var_dump($event);

        $this->send($event);
    }
}
