<?php
/**
 * Created by PhpStorm.
 * User: nils.langner
 * Date: 29.06.16
 * Time: 14:44
 */

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