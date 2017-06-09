<?php

namespace Koalamon\Client\Entity;

class System
{
    private $id;

    private $identifier;
    private $name;

    private $url;
    private $project;

    private $subSystems;

    private $login;

    private $speed;

    /**
     * System constructor.
     * @param $identifier
     * @param $name
     * @param $url
     * @param $project
     */
    public function __construct($id, $identifier, $name, $url, Project $project, $speed, $subSystems = array(), $login = null)
    {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->url = $url;
        $this->project = $project;
        $this->subSystems = $subSystems;
        $this->login = $login;
        $this->speed = $speed;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
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
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getSubSystems()
    {
        return $this->subSystems;
    }

    /**
     * @return integer
     */
    public function getSpeed()
    {
        return $this->speed;
    }
}
