<?php

include_once __DIR__ . '/../.vendor/autoload.php';

$reporter = new \Koalamon\Client\Reporter\Reporter('<my api key>', '<my project name>');

$event = new \Koalamon\Client\Reporter\Event('test_tool_www_example_com',
    'www_example_com',
    \Koalamon\Client\Reporter\Event::STATUS_SUCCESS,
    'TestTool');

try {
    $reporter->send($event);
} catch (\Koalamon\Client\Reporter\ServerException $e) {
    echo $e->getMessage();
    echo $e->getResponse()->getBody();
}
