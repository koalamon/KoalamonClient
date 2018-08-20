<?php

namespace Koalamon\Client\Reporter;

class KoalamonException extends \Exception
{
    private $payload;
    private $url;

    public function setPayload($payload)
    {
        $this->payload = $payload;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }
}