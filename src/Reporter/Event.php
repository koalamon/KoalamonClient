<?php

namespace Koalamon\Client\Reporter;

/**
 * Class Event
 *
 * This class represents a koalamon event. It can be used in combination with the Reporter
 * to send an event towards the koalamon webhook.
 *
 * @package Koalamon\EventReporter
 * @author Nils Langner <nils.langner@koalamon.com>
 */
class Event implements \JsonSerializable
{
    const STATUS_FAILURE = "failure";
    const STATUS_SUCCESS = "success";

    private $message;
    private $system;
    private $identifier;
    private $status;
    private $tool;
    private $value;
    private $url;
    private $componentId;

    /**
     * Initialize the event.
     *
     * @param $identifier All events with this identifier will be grouped in koalamon.
     * @param $system The system the events belongs to (e.g. www.example.com).
     * @param success|failed $status Te status of the event. Use the const of this class.
     * @param string $tool The name of the tool that is using this library.
     * @param string $message The message that will be display in koalamon. Only mandatory if the
     *                        status is failure.
     */
    public function __construct($identifier, $system, $status, $tool = "", $message = "", $value = "", $url = "", $componentId = null)
    {
        $this->message = $message;
        $this->system = $system;
        $this->identifier = $identifier;
        $this->tool = $tool;
        $this->value = $value;
        $this->url = $url;
        $this->componentId = $componentId;

        if ($status == self::STATUS_FAILURE || $status == self::STATUS_SUCCESS) {
            $this->status = $status;
        } else {
            throw new \InvalidArgumentException("The given status does not exist. Possible values are: "
                . self::STATUS_SUCCESS . ", " . self::STATUS_FAILURE . ".");
        }
    }

    /**
     * Convert the event to an array.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array("identifier" => $this->identifier,
            "system" => $this->system,
            "status" => $this->status,
            "message" => $this->message,
            "type" => $this->tool,
            "value" => $this->value,
            "componentId" => $this->componentId,
            "url" => $this->url);
    }
}
