<?php

namespace Koalamon\Client\Entity;

class User
{
    private $name;
    private $apiKey;

    public function __construct($name, $apiKey)
    {
        $this->name = $name;
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
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
