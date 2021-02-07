<?php

namespace Koalamon\Client\Reporter\Event;

class Attribute
{
    private $key;
    private $value;
    private $isStorable;

    private $timeToLiveInDays;

    public function __construct($key, $value, $isStorable = false, $timeToLiveInDays = 30)
    {
        $this->key = $key;
        $this->value = $value;
        $this->isStorable = $isStorable;
        $this->timeToLiveInDays = $timeToLiveInDays;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isIsStorable()
    {
        return $this->isStorable;
    }

    /**
     * @return int
     */
    public function getTimeToLiveInDays(): int
    {
        return $this->timeToLiveInDays;
    }
}
