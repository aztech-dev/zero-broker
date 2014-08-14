<?php

include __DIR__ . '/../vendor/autoload.php';

use Aztech\Zero\KeyValuePair;
$context = new \ZMQContext();
$publisher = new \ZMQSocket($context, \ZMQ::SOCKET_PUB);
$publisher->bind('tcp://*:5557');


