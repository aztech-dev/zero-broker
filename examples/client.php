<?php

include __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Aztech\Events\Plugins\ZeroMq\PubSubTransport;
use Aztech\Events\Plugins\ZeroMq\SocketWrapper;

$logger = new ConsoleLogger(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG));
$context = new \ZMQContext();

$subscriber = new \ZMQSocket($context, \ZMQ::SOCKET_SUB);
$subscriber = new SocketWrapper($subscriber, 'tcp://127.0.0.1:5557', $logger);

$publisher = new \ZMQSocket($context, \ZMQ::SOCKET_PUB);
$publisher = new SocketWrapper($publisher, 'tcp://127.0.0.1:5557', $logger);

$transport = new PubSubTransport($publisher, $subscriber, $logger);

while (true) {
    $next = $transport->read();
    
    if (substr_count(strtolower($next), 'nanou') > 0) {
        $logger->critical('<3 : ' . $next);
    }
    else {
        $logger->info('Server said : ' . $next);
    }
    
    sleep(1);
}
