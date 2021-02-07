<?php

namespace Koalamon\Client\Reporter\Event\Processor;

use Koalamon\Client\Reporter\Event;

class FileProcessor implements Processor
{
    private $baseDir;

    private $publicHost;

    private $memory = [];

    public function __construct($baseDir = '/leankoala/filestore/', $publicHost = 'http://localhost')
    {
        $this->baseDir = $baseDir;
        $this->publicHost = $publicHost;
    }

    public function process(Event $event)
    {
        $attributes = [];

        foreach ($event->getAttributes() as $attribute) {
            if ($attribute->isIsStorable()) {
                try {
                    $storageString = $this->fromInMemory($attribute->getValue());
                    if ($storageString) {
                        $attributes[$attribute->getKey()] = $storageString;
                    } else {
                        $attributes[$attribute->getKey()] = $this->persistValue(
                            $attribute->getKey(),
                            $attribute->getValue(),
                            $attribute->getTimeToLiveInDays(),
                            $event
                        );
                    }

                } catch (\Exception $e) {
                    $attributes[$attribute->getKey()] = '_error: ' . json_encode($e->getMessage());
                }
            } else {
                $attributes[$attribute->getKey()] = $attribute->getValue();
            }
        }

        return [
            "identifier" => $event->getIdentifier(),
            "system" => $event->getSystem(),
            "status" => $event->getStatus(),
            "message" => $event->getMessage(),
            "type" => $event->getTool(),
            "value" => $event->getValue(),
            "componentId" => $event->getComponentId(),
            "url" => $event->getUrl(),
            'attributes' => $attributes
        ];
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

    private function persistValue($key, $value, $timeToLiveInDays, Event $event)
    {
        $date = date('m0d', strtotime('+ ' . $timeToLiveInDays . ' days'));
        $hash = $date . md5(json_encode($value));

        $dir = $this->baseDir . '/' . substr($hash, 0, 2) . '/' . substr($hash, 3, 2) . '/';

        if (!file_exists($dir)) {
            echo "Creating directory: " . $dir;
            @mkdir($dir, 0777, true);

            if (!file_exists($dir)) {
                echo "\nWARNING: Unable to create " . $dir . ". Attributes are not stored.\n";
                return;
            }
        }

        file_put_contents($dir . $hash . '.json', base64_encode(json_encode($value)));

        $storageString = 'storage:' . $this->publicHost . '/storage/' . $hash;

        $this->memory[md5(serialize($value))] = $storageString;

        return $storageString;
    }

    static public function createByEnvironmentVars($baseDirectory)
    {
        if (array_key_exists('MONGO_PUBLIC_HOST', $_ENV)) {
            $mongoPublicHost = $_ENV['MONGO_PUBLIC_HOST'];
        } else {
            $mongoPublicHost = 'http://localhost';
        }

        return new self($baseDirectory, $mongoPublicHost);
    }
}
