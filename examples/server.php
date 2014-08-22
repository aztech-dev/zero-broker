<?php

include __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Aztech\Events\Plugins\ZeroMq\PubSubTransport;
use Aztech\Events\Plugins\ZeroMq\SocketWrapper;
use Aztech\Events\Core\Event;

$logger = new ConsoleLogger(new ConsoleOutput(ConsoleOutput::VERBOSITY_DEBUG));
$context = new \ZMQContext();

$subscriber = new \ZMQSocket($context, \ZMQ::SOCKET_SUB);
$subscriber = new SocketWrapper($subscriber, 'tcp://127.0.0.1:5557', $logger);

$publisher = new \ZMQSocket($context, \ZMQ::SOCKET_PUB);
$publisher = new SocketWrapper($publisher, 'tcp://127.0.0.1:5557', $logger);

$transport = new PubSubTransport($publisher, $subscriber, $logger);

$in = fopen('php://stdin', 'w');

while ($line = fgets($in)) {
    $transport->write(new Event('cookie.monster'), trim($line));
    sleep(1);
}
