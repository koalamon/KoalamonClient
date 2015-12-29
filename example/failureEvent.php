<?php

include_once __DIR__ . '/../.vendor/autoload.php';

$reporter = new \Koalamon\EventReporter\Reporter('<my api key>', '<my project name>');

$event = new \Koalamon\EventReporter\Event('test_tool_www_example_com',
    'www_example_com',
    \Koalamon\EventReporter\Event::STATUS_FAILURE,
    'TestTool',
    'This is just an test error');

try {
    $reporter->send($event);
} catch (\Koalamon\EventReporter\ServerException $e) {
    echo $e->getMessage();
    echo $e->getResponse()->getBody();
}
