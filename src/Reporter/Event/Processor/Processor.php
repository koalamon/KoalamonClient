<?php

namespace Koalamon\Client\Reporter\Event\Processor;

use Koalamon\Client\Reporter\Event;

interface Processor
{
    public function process(Event $event);

}