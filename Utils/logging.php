<?php

require_once ('vendor/autoload.php');

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;
use GuzzleHttp\Client;

// Load environment variables
$dotenv = Dotenv::createImmutable(".");
$dotenv->load();



$logger = new Logger('php-app');

// Add processors for additional context
$logger->pushProcessor(new IntrospectionProcessor());
$logger->pushProcessor(new WebProcessor());
$logger->pushProcessor(function ($record) {
    // Add a unique request ID to track requests across log entries
    $record->extra['request_id'] = $_SERVER['HTTP_X_REQUEST_ID'] ?? uniqid();
    return $record;
});

// Add local file handler (for backup)
$fileHandler = new StreamHandler(__DIR__ . '/../logs/app.log', Logger::DEBUG);
//$fileHandler->setFormatter(new JsonFormatter());
$logger->pushHandler($fileHandler);


return $logger;
?>
