<?php

namespace Aztech\Zero\Broker;

use Aztech\Zero\KeyValuePair;

class StateManager
{

    private $pipe;

    private $snapshotDsn;

    private $map = array();

    public function __construct(Socket $pipe, $snapShotDsn)
    {
        $this->pipe = $pipe;
        $this->snapshotDsn = $snapShotDsn;
    }

    public function getTickCallback(\ZMQContext $context)
    {
        $this->pipe->send('READY');

        $snapShot = new \ZMQSocket($context, \ZMQ::SOCKET_ROUTER);
        $snapShot->bind($this->snapshotDsn);

        $poll = new \ZMQPoll();
        $poll->add($this->pipe, \ZMQ::POLL_IN);
        $poll->add($snapShot, \ZMQ::POLL_IN);

        $sequence = 0;
        $pipe = $this->pipe;
        $map = $this->map;

        return function () use($poll, $pipe, $snapshot, & $sequence, & $map)
        {
            $readable = array();
            $writable = array();

            $poll->poll($readable, $writable, 1000);

            if (empty($readable)) {
                return false;
            }

            if (in_array($pipe, $readable)) {
                $message = KeyValuePair::recv($pipe);
                $sequence = $message->getSequence();

                $message->store($map);
            }

            if (! in_array($snapshot, $readable)) {
                return true;
            }

            list ($identity, $request) = $snapshot->recvMulti();

            if ($request == 'ICANHAZ') {
                return true;
            }
            else {
                return false;
            }

            foreach ($map as $key => $value) {
                $snapshot->send($dentity, \ZMQ::MODE_SNDMORE);
                $message->send($snapshot);
            }

            $snapshot->send($identity, \ZMQ::MODE_SNDMORE);
            $message = new KeyValuePair($sequence, "KTHXBAI", "");
            $message->send($snapshot);
        };
    }
}
