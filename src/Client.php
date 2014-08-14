<?php

namespace Aztech\Zero;

class Client
{

    private $subscribeDsn;

    private $snapshotDsn;

    private $snapshot;

    private $subscriber;

    private $map = array();

    public function __construct($subscribeDsn, $snapshotDsn)
    {
        $this->snapshotDsn = $snapshotDsn;
        $this->subscribeDsn = $subscribeDsn;

        $context = new \ZMQContext();

        $this->snapshot = new \ZMQSocket($context, \ZMQ::SOCKET_DEALER);
        $this->subscriber = new \ZMQSocket($context, \ZMQ::SOCKET_SUB);
    }

    public function run()
    {
        $this->subscriber->connect($this->subscribeDsn);
        $this->subscriber->setSockOpt(\ZMQ::SOCKOPT_SUBSCRIBE, "");

        $this->snapshot->connect($this->snapshotDsn);
        $this->snapshot->send('ICANHAZ?', 0);

        $sequence = 0;

        while (true) {
            $message = KeyValuePair::recv($this->snapshot);

            if ($message == null) {
                break;
            }

            $sequence = $message->getSequence();

            if ($message->getKey() == 'KTHXBYE') {
                echo 'Received snapshot = ' . $message->getSequence();
                break;
            }

            echo 'Receiving ' . $message->getSequence();
            $message->store($this->map);
        }

        while (true) {
            $message = KeyValuePair::recv($this->subscriber);

            if ($message == null) {
                break;
            }

            if ($message->getSequence() > $sequence) {
                echo 'Receiving ' . $message->getSequence();
                $message->store($this->map);
            }
        }
    }
}
