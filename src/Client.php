<?php

namespace Koalamon\Client;

use GuzzleHttp\Psr7\Uri;
use Koalamon\Client\Entity\Project;
use Koalamon\Client\Entity\System;

class Client
{
    private $username;
    private $userApiKey;
    private $client;

    const REST_USER_GET_PROJECTS = 'http://www.koalamon.com/rest/user/projects/';
    const REST_PROJECT_GET_SYSTEMS = 'http://www.koalamon.com/rest/{project}/systems/';

    public function __construct($username, $userApiKey, $httpClient)
    {
        $this->username = $username;
        $this->userApiKey = $userApiKey;

        $this->client = $httpClient;
        // check credentials
    }

    private function getUrl($url, array $parameters = array())
    {
        $finalUrl = $url;

        foreach ($parameters as $key => $value) {
            $finalUrl = str_replace("{" . $key . "}", $value, $finalUrl);
        }

        return $finalUrl;
    }

    private function getResult($url)
    {
        $response = $this->client->get(new Uri($url));
        return json_decode((string)$response->getBody());
    }

    /**
     * @return Project[]
     */
    public function getProjects()
    {
        $url = self::REST_USER_GET_PROJECTS . '?username=' . $this->username . '&api_key=' . $this->userApiKey;

        $projectArray = $this->getResult($url);

        $projects = array();

        foreach ($projectArray as $projectElement) {
            $projects[] = new Project($projectElement->name, $projectElement->identifier, $projectElement->apiKey);
        }

        return $projects;
    }

    /**
     * @param Project $project
     * @return System[]
     */
    public function getSystems(Project $project)
    {
        $url = $this->getUrl(self::REST_PROJECT_GET_SYSTEMS . '?api_key=' . $project->getApiKey(), array('project' => $project->getIdentifier()));

        $systemsArray = $this->getResult($url);
        $systems = array();

        foreach ($systemsArray as $systemsElement) {
            $systems[] = new System($systemsElement->identifier, $systemsElement->name, $systemsElement->url, $systemsElement->project);
        }

        return $systems;
    }
}
