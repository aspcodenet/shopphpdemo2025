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

// Custom handler for OpenObserve
class OpenObserveHandler extends \Monolog\Handler\AbstractProcessingHandler
{
    private $client;
    private $url;
    private $auth;
    private $organization;
    private $stream;
    private $token;
    
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        
        $this->client = new Client();
        $this->url = $_ENV['OPENOBSERVE_URL'];
        $this->organization = $_ENV['OPENOBSERVE_ORGANIZATION'];
        $this->stream = $_ENV['OPENOBSERVE_STREAM'];
        $this->auth = base64_encode($_ENV['OPENOBSERVE_USERNAME'] . ':' . $_ENV['OPENOBSERVE_PASSWORD']);
        //die($this->auth);
    }
    
    // Monolog 3.x compatible write method
    protected function write(\Monolog\LogRecord $record): void
    {
        $endpoint = "{$this->url}/api/{$this->organization}/{$this->stream}/_json";
        
        // Create a properly structured log object
        $logData = [
            'timestamp' => $record->datetime->format(\DateTimeInterface::ISO8601),
            'level' => $record->level->name,
            'message' => $record->message,
            'channel' => $record->channel,
            'context' => $record->context,
            'extra' => $record->extra
        ];
        
        try {
            $this->client->post($endpoint, [
                'verify' => false,
                'headers' => [
                    'Authorization' => 'Basic ' . $this->auth,
                    'Content-Type' => 'application/json'
                ],
                'json' => $logData  // Send as a single object
            ]);
        } catch (\Exception $e) {
            // Write to error log if sending to OpenObserve fails
            die($e->getMessage());
            //error_log('Failed to send log to OpenObserve: ' . $e->getMessage());
        }
    }
}

// Add OpenObserve handler
$openObserveHandler = new OpenObserveHandler();
$logger->pushHandler($openObserveHandler);

return $logger;
?>