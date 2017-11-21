<?php

namespace Koalamon\Client\Entity;

class Project
{
    private $name;
    private $identifier;
    private $apiKey;
    private $maxResponseTime;

    public function __construct($name, $identifier, $apiKey, $maxResponseTime)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->apiKey = $apiKey;
        $this->maxResponseTime = $maxResponseTime;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @return integer
     */
    public function getMaxResponseTime()
    {
        return $this->maxResponseTime;
    }
}
