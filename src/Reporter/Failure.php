<?php

namespace Koalamon\Client\Reporter;


class Failure
{
    private $message;
    private $type;
    private $tool;
    private $command = "";

    private $componentId;

    /**
     * Failure constructor.
     * @param $message
     * @param $type
     * @param $tool
     */
    public function __construct($message, $type, $tool)
    {
        $this->message = $message;
        $this->type = $type;
        $this->tool = $tool;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * @return integer
     */
    public function getSystemId()
    {
        return $this->componentId;
    }

    /**
     * @param integer $componentId
     */
    public function setSystemId($systemId)
    {
        $this->componentId = $systemId;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }
}