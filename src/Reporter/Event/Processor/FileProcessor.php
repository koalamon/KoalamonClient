<?php

namespace Koalamon\Client\Reporter\Event\Processor;

use Koalamon\Client\Reporter\Event;

class FileProcessor implements Processor
{
    const DEFAULT_STORAGE_URL_LENGTH = 91;

    private $baseDir;

    private $publicHost;

    private $memory = [];

    public function __construct($baseDir = '/leankoala/filestore/', $publicHost = 'http://localhost')
    {
        $this->baseDir = $baseDir;
        $this->publicHost = $publicHost;
    }

    /*
     * @todo the attributes could be stores within ONE json document and not many
     */
    public function process(Event $event)
    {
        $attributes = [];

        foreach ($event->getAttributes() as $attribute) {
            if ($attribute->isIsStorable() && $this->isStorageRational($attribute->getValue())) {
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

    private function isStorageRational($value)
    {
        return strlen($this->prepareDataForStorage($value)) > self::DEFAULT_STORAGE_URL_LENGTH;
    }

    private function prepareDataForStorage($value)
    {
        return base64_encode(json_encode($value));
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
        $date = date('m0d0H', strtotime('+ ' . $timeToLiveInDays . ' days'));
        $hash = $date . md5(json_encode($value));

        $dir = self::getDirectoryFromHash($this->baseDir, $hash);

        if (!file_exists($dir)) {
            echo "Creating directory: " . $dir;
            @mkdir($dir, 0777, true);

            if (!file_exists($dir)) {
                echo "\nWARNING: Unable to create " . $dir . ". Attributes (key: ".$key.", hash: " . $hash . ") are not stored.\n";
                return;
            }
        }

        file_put_contents($dir . $hash . '.json', $this->prepareDataForStorage($value));

        $storageString = 'storage:' . $this->publicHost . '/storage/' . $hash;

        $this->memory[md5(serialize($value))] = $storageString;

        return $storageString;
    }

    /**
     * Create a filename for the given hash.
     *
     * @param string $baseDir
     * @param string $hash
     * @param int $version
     *
     * @return string
     */
    public static function getDirectoryFromHash($baseDir, $hash, $version = 2)
    {
        switch ($version) {
            case 1:
                return $baseDir . '/' . substr($hash, 0, 2) . '/' . substr($hash, 3, 2) . '/';
            case 2:
                return $baseDir . '/' . substr($hash, 0, 2) . '/' . substr($hash, 3, 2) . '/' . substr($hash, 6, 2) . '/';
        }

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
