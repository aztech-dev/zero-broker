<?php

namespace Aztech\Zero;

use Aztech\Zero\Broker\StateManager;
class Broker
{

    private $dsn;

    private $snapshotDsn;

    private $publisher;

    private $pushSocket;

    public function __construct($scheme = 'tcp', $host = '*', $port = 5557, $snapshotPort = 5556)
    {
        $this->dsn = sprintf('%s://%s:%s', $scheme, $host, $port);
        $this->snapshotDsn = sprintf('%s://%s:%s', $scheme, $host, $snapshotPort);

        $context = new  \ZMQContext();
        $this->publisher = new \ZMQSocket($context, \ZMQ::SOCKET_PUB);
    }

    public function run()
    {
        $this->publisher->bind($this->dsn);

        $sequence = 0;
        $stateManager = new StateManager($this->publisher, $this->snapshotDsn);
        $tick = $stateManager->getTickCallback();

        while (true) {
            $kv = new KeyValuePair(++$sequence);
            $kv->setKey(rand());
            $kv->setBody(rand());

            $kv->send($publisher);

            sleep(1);
        }
    }

}
