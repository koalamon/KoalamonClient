<?php

namespace Koalamon\Client\Entity;

class System
{
    private $identifier;
    private $name;

    private $url;
    private $project;

    /**
     * System constructor.
     * @param $identifier
     * @param $name
     * @param $url
     * @param $project
     */
    public function __construct($identifier, $name, $url, $project)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->url = $url;
        $this->project = $project;
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


}
