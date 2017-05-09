<?php

include_once __DIR__ . '/../vendor/autoload.php';

$reporter = new \Koalamon\Client\Reporter\Reporter('<my api key>', '<my project name>');

$event = new \Koalamon\Client\Reporter\Event('test_tool_www_example_com',
    'www_example_com',
    \Koalamon\Client\Reporter\Event::STATUS_SUCCESS,
    'TestTool');

$event->addAttribute(new \Koalamon\Client\Reporter\Event\Attribute('htmlContent', '<html></html>', true));

if (!array_key_exists('MONGO_HOST', $_ENV) || !array_key_exists('MONGO_PUBLIC_HOST', $_ENV)) {
    die('ENVIRONMENT VARS NOT SET!!!');
}



$mongoHost = 'mongodb://' . $_ENV['MONGO_HOST'] . '/';
$mongoPublicHost = $_ENV['MONGO_PUBLIC_HOST'];

$reporter->setEventProcessor(new \Koalamon\Client\Reporter\Event\Processor\MongoDBProcessor($mongoHost, 'leankoala', $mongoPublicHost));

try {
    $reporter->send($event);
} catch (\Koalamon\Client\Reporter\ServerException $e) {
    echo $e->getMessage();
    echo $e->getResponse()->getBody();
}
