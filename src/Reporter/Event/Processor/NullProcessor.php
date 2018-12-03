<?php

namespace Koalamon\Client\Reporter\Event\Processor;

use Koalamon\Client\Reporter\Event;

class NullProcessor implements Processor
{
    public function process(Event $event)
    {
        return array("identifier" => $event->getIdentifier(),
            "system" => $event->getSystem(),
            "status" => $event->getStatus(),
            "message" => $event->getMessage(),
            "type" => $event->getTool(),
            "value" => $event->getValue(),
            "componentId" => $event->getComponentId(),
            "url" => $event->getUrl(),
            'attributes' => ['logger' => 'nullProcessor', 'warning' => 'all attributes are removed by this processor']);
    }
}
