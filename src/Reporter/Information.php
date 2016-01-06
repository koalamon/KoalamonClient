<?php

namespace Koalamon\Client\Reporter;

class Information implements \JsonSerializable
{
    private $message;
    private $duration;

    /**
     * Information constructor.
     * @param $message
     * @param $duration
     */
    public function __construct($message, $duration)
    {
        $this->message = $message;
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    function jsonSerialize()
    {
        return [
            'message' => $this->getMessage(),
            'duration' => $this->getDuration()
        ];
    }
}
