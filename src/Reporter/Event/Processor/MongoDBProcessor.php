<?php

namespace Koalamon\Client\Reporter\Event\Processor;

use Koalamon\Client\Reporter\Event;
use MongoDB\Client;

class MongoDBProcessor implements Processor
{
    private $collection;
    private $publicHost;

    private $memory = [];

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
                    $storageString = $this->fromInMemory($attribute->getValue());
                    if ($storageString) {
                        $attributes[$attribute->getKey()] = $storageString;
                    } else {
                        $attributes[$attribute->getKey()] = $this->persistValue($attribute->getValue());
                    }
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

    private function fromInMemory($value)
    {
        $checksum = md5(serialize($value));
        if (array_key_exists($checksum, $this->memory)) {
            return $this->memory[$checksum];
        } else {
            return false;
        }
    }

    private function persistValue($value)
    {
        $insertOneResult = $this->collection->insertOne([
            'value' => $value,
            'created' => time(),
        ]);

        $id = $insertOneResult->getInsertedId();

        $storageString = 'storage:' . $this->publicHost . '/storage/' . $id;

        $this->memory[md5(serialize($value))] = $storageString;

        return $storageString;
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
