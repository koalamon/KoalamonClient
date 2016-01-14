<?php

namespace Koalamon\Client;

use GuzzleHttp\Psr7\Uri;
use Koalamon\Client\Entity\Project;
use Koalamon\Client\Entity\System;
use Koalamon\Client\Entity\User;

class Client
{
    private $client;

    private $koalamonServer = 'http://www.koalamon.com';

    const REST_USER_GET_PROJECTS = '/rest/user/projects/';
    const REST_PROJECT_GET_SYSTEMS = '/rest/{project}/systems/';

    public function __construct($httpClient, $koalamonServer = null)
    {
        if ($koalamonServer) {
            $this->koalamonServer = $koalamonServer;
        }
        $this->client = $httpClient;
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
        try {
            $uri = new Uri($this->koalamonServer . $url);
            $response = $this->client->get($uri);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 403) {
                $message = "You are not allowed to call this action. Please check your API key.";
            } else {
                $message = "Error fetching " . (string)$uri . ': ' . $e->getMessage();
            }
            throw new \RuntimeException($message);
        }

        return json_decode((string)$response->getBody());
    }

    /**
     * @return Project[]
     */
    public function getProjects(User $user)
    {
        $url = self::REST_USER_GET_PROJECTS . '?username=' . $user->getName() . '&api_key=' . $user->getApiKey();

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
            $subSystems = array();

            if (property_exists($systemsElement, 'subSystems')) {
                foreach ($systemsElement->subSystems as $subSystem) {
                    $subSystems[] = new System($subSystem->identifier, $subSystem->name, $subSystem->url, $subSystem->project);
                }
            }
            
            $systems[] = new System($systemsElement->identifier, $systemsElement->name, $systemsElement->url, $systemsElement->project, $subSystems);
        }

        return $systems;
    }
}
