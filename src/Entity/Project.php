<?php

namespace Koalamon\Client\Entity;

class Project
{
    private $name;
    private $identifier;
    private $apiKey;

    public function __construct($name, $identifier, $apiKey)
    {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->apiKey = $apiKey;
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
}
