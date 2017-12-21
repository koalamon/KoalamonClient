<?php

namespace Koalamon\Client;

use GuzzleHttp\Psr7\Uri;
use Koalamon\Client\Entity\Project;
use Koalamon\Client\Entity\System;
use Koalamon\Client\Entity\User;

class Client
{
    private $client;

    private $koalamonServer = 'https://monitor.koalamon.com';

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

    private function getResult($url, $addServer = true)
    {
        try {
            if ($addServer) {
                $uri = new Uri($this->koalamonServer . $url);
            } else {
                $uri = new Uri($url);
            }
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
            $projects[] = new Project($projectElement->name, $projectElement->identifier, $projectElement->apiKey, $projectElement->maxResponseTime);
        }

        return $projects;
    }

    /**
     * @param $url
     * @return System[]
     */
    public function getSystemsFromUrl($url, $withOptions = false)
    {
        return $this->initSystems($this->getResult($url, false), $withOptions);
    }

    public function getUrlsFromUrl($url)
    {
        return $this->getResult($url, false);
    }

    /**
     * @param Project $project
     * @return System[]
     */
    public function getSystems(Project $project)
    {
        $url = $this->getUrl(self::REST_PROJECT_GET_SYSTEMS . '?api_key=' . $project->getApiKey(), array('project' => $project->getIdentifier()));
        return $this->initSystems($this->getResult($url));
    }

    private function initSystems(array $systemsArray, $withOptions = false)
    {
        $systems = array();

        foreach ($systemsArray as $systemsElement) {

            if ($withOptions) {
                $element = $systemsElement->system;
                if (property_exists($systemsElement, 'options')) {
                    $options = $systemsElement->options;
                } else {
                    $options = "";
                }

            } else {
                $element = $systemsElement->system;
                $options = '';
            }

            $subSystems = array();

            if (property_exists($element, 'subSystems')) {
                foreach ($element->subSystems as $subSystem) {
                    $project = new Project($subSystem->project->name, $element->project->identifier, $subSystem->project->api_key, $subSystem->project->maxResponseTime);
                    $subSystems[] = new System(
                        $subSystem->id,
                        $subSystem->identifier,
                        $subSystem->name,
                        $subSystem->url,
                        $project,
                        $subSystem->speed,
                        $subSystem->device
                    );
                }
            }

            if (property_exists($element, 'login')) {
                $login = json_encode($element->login);
            } else {
                $login = '';
            }

            $sysProject = new Project($element->project->name, $element->project->identifier, $element->project->api_key, $element->project->maxResponseTime);
            $queue = $element->project->queue;

            $systems[] = [
                'system' => new System(
                    $element->id,
                    $element->identifier,
                    $element->name,
                    $element->url,
                    $sysProject,
                    $element->speed,
                    $subSystems,
                    $login,
                    $element->device
                ),
                'options' => $options,
                'queue' => $queue
            ];
        }

        return $systems;
    }
}
