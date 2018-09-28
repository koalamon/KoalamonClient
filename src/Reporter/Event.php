<?php

namespace Koalamon\Client\Reporter;

use Koalamon\Client\Reporter\Event\Attribute;

/**
 * Class Event
 *
 * This class represents a koalamon event. It can be used in combination with the Reporter
 * to send an event towards the koalamon webhook.
 *
 * @package Koalamon\EventReporter
 * @author Nils Langner <nils.langner@koalamon.com>
 */
class Event
{
    const STATUS_FAILURE = "failure";
    const STATUS_SUCCESS = "success";
    const STATUS_SKIPPED = "skipped";

    private $message;
    private $system;
    private $identifier;
    private $status;
    private $tool;
    private $value;
    private $url;
    private $componentId;

    private $attributes = [];

    /**
     * Initialize the event.
     *
     * @param string $identifier All events with this identifier will be grouped in koalamon.
     * @param string $system The system the events belongs to (e.g. www.example.com).
     * @param "success"|"failed" $status Te status of the event. Use the const of this class.
     * @param string $tool The name of the tool that is using this library.
     * @param string $message The message that will be display in koalamon. Only mandatory if the
     *                        status is failure.
     * @param integer value
     * @param string url
     */
    public function __construct($identifier, $system, $status, $tool = "", $message = "", $value = null, $url = "", $componentId = null)
    {
        if ($value === "") {
            $value = null;
        }

        if (is_string($value)) {
            throw new \RuntimeException('Value must be integer or null.');
        }

        $this->message = $message;
        $this->system = $system;
        $this->identifier = $identifier;
        $this->tool = $tool;
        $this->value = $value;
        $this->url = $url;
        $this->componentId = $componentId;

        if ($status == self::STATUS_FAILURE || $status == self::STATUS_SUCCESS || $status == self::STATUS_SKIPPED) {
            $this->status = $status;
        } else {
            throw new \InvalidArgumentException("The given status does not exist. Possible values are: "
                . self::STATUS_SUCCESS . ", " . self::STATUS_FAILURE . ", " . self::STATUS_SKIPPED . ".");
        }

        if (array_key_exists('WORKER_IDENTIFIER', $_ENV)) {
            $this->addAttribute(new Attribute('_leankoala_worker', $_ENV['WORKER_IDENTIFIER']));
        }else{
            $this->addAttribute(new Attribute('_leankoala_worker', ''));
        }
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
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * @return null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return null
     */
    public function getComponentId()
    {
        return $this->componentId;
    }

    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
