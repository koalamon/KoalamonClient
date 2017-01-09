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

    /**
     * System constructor.
     * @param $identifier
     * @param $name
     * @param $url
     * @param $project
     */
    public function __construct($id, $identifier, $name, $url, Project $project, $subSystems = array(), $login = null)
    {
        $this->id = $id;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->url = $url;
        $this->project = $project;
        $this->subSystems = $subSystems;
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
}
