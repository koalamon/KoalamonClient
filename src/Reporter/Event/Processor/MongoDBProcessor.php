<?php

namespace Koalamon\Client\Reporter\Event\Processor;

use Koalamon\Client\Reporter\Event;
use MongoDB\Client;

class MongoDBProcessor implements Processor
{
    private $collection;
    private $publicHost;

    public function __construct($host = 'mongodb://127.0.0.1/', $publicHost = 'http://localhost', $databaseName = "leankoala")
    {
        $this->collection = (new Client($host))->$databaseName->storage;
        $this->publicHost = $publicHost;
    }

    public function process(Event $event)
    {
        $attributes = array();

        foreach ($event->getAttributes() as $attribute) {
            if ($attribute->isIsStorable()) {
                try {
                    $attributes[$attribute->getKey()] = $this->persistValue($attribute->getKey(), $attribute->getValue(), $event);
                } catch (\Exception $e) {
                    $attributes[$attribute->getKey()] = '_error: ' . $e->getMessage();
                }
            } else {
                $attributes[$attribute->getKey()] = $attribute->getValue();
            }
        }

        return array("identifier" => $event->getIdentifier(),
            "system" => $event->getSystem(),
            "status" => $event->getStatus(),
            "message" => $event->getMessage(),
            "type" => $event->getTool(),
            "value" => $event->getValue(),
            "componentId" => $event->getComponentId(),
            "url" => $event->getUrl(),
            'attributes' => $attributes);
    }

    private function persistValue($key, $value, Event $event)
    {
        $insertOneResult = $this->collection->insertOne([
            'key' => $key,
            'value' => $value,
            'tool' => $event->getTool(),
            'created' => time(),
        ]);

        $id = $insertOneResult->getInsertedId();
        return 'storage:' . $this->publicHost . '/storage/' . $id;
    }

    static public function createByEnvironmentVars($databaseName)
    {
        if (array_key_exists('MONGO_HOST', $_ENV)) {
            $mongoHost = 'mongodb://' . $_ENV['MONGO_HOST'] . '/';
        } else {
            $mongoHost = 'mongodb://mongodb/';
        }

        if (array_key_exists('MONGO_PUBLIC_HOST', $_ENV)) {
            $mongoPublicHost = $_ENV['MONGO_PUBLIC_HOST'];
        } else {
            $mongoPublicHost = 'http://localhost';
        }

        return new self($mongoHost, $mongoPublicHost, $databaseName);
    }
}
