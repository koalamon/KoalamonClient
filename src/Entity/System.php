<?php

namespace Koalamon\Client\Entity;

class System
{
    private $identifier;
    private $name;

    private $url;
    private $project;

    private $subSystems;

    /**
     * System constructor.
     * @param $identifier
     * @param $name
     * @param $url
     * @param $project
     */
    public function __construct($identifier, $name, $url, $project, $subSystems = array())
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->url = $url;
        $this->project = $project;
        $this->subSystems = $subSystems;
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return mixed
     */
    public function getSubSystems()
    {
        return $this->subSystems;
    }
}
