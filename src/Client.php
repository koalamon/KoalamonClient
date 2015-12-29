<?php

namespace Koalamon\Client;


use GuzzleHttp\Psr7\Uri;

class Client
{
    private $username;
    private $userApiKey;

    const REST_USER_GET_PROJECTS = 'http://www.koalamon.com/rest/user/projects/';

    public function __construct($username, $userApiKey)
    {
        $this->username = $username;
        $this->userApiKey = $userApiKey;
    }

    public function getProjects()
    {
        $url = self::REST_USER_GET_PROJECTS . '?username=' . $this->username . '&api_key=' . $this->userApiKey;

        $client = new \GuzzleHttp\Client();
        $response = $client->get(new Uri($url));

        var_dump($response);
    }
}