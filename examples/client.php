<?php

include __DIR__ . '/../vendor/autoload.php';

use Aztech\Zero\KeyValuePair;
$context = new \ZMQContext();
$publisher = new \ZMQSocket($context, \ZMQ::SOCKET_SUB);
$publisher->connect('tcp://localhost:5557');
$publisher->setSockOpt(\ZMQ::SOCKOPT_SUBSCRIBE, '');

$sequence = 0;

while (true) {
    $kv = KeyValuePair::recv($publisher);
    var_dump($kv);
    sleep(1);
}
