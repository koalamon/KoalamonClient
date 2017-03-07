<?php

namespace Koalamon\Client\Reporter\Event\Processor;

use Koalamon\Client\Reporter\Event;

class SimpleProcessor implements Processor
{
    public function process(Event $event)
    {
        $attributes = array();

        foreach ($event->getAttributes() as $attribute) {
            $attributes[$attribute->getKey()] = $attribute->getValue();
        }

        return array("identifier" => $event->getIdentifier(),
            "system" => $event->getSystem(),
            "status" => $event->getStatus(),
            "message" => $event->getMessage(),
            "type" => $event->getTool(),
            "value" => $event->getValue(),
            "componentId" => $event->getComponentId(),
            "url" => $event->getUrl(),
            'attributes' => $attributes);
    }
}
