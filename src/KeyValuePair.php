<?php

namespace Aztech\Zero;

class KeyValuePair
{

    const FRAME_KEY = 0;

    const FRAME_SEQ = 1;

    const FRAME_BODY = 2;

    const KVMSG_FRAMES = 3;

    private $sequence;

    private $key;

    private $body;

    public function __construct($sequence, $key = null, $body = null)
    {
        $this->sequence = $sequence;
        $this->key = $key;
        $this->body = $body;
    }

    public function getSequence()
    {
        return $this->sequence;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function store(\ArrayAccess $array)
    {
        if (! empty($this->key) && ! empty($this->body)) {
            $array[$this->key] = $this->body;
        }
    }

    public function send(\ZMQSocket $socket)
    {
        $key = $this->key ?: '';
        $body = $this->body ?: '';

        $socket->sendmulti(array($key, $this->sequence, $body));
    }

    public static function recv(\ZMQSocket $socket)
    {
        $data = $socket->recvMulti();

        if (count ($data) !== 3) {
            return null;
        }

        list($key, $sequence, $body) = $data;

        return new self($sequence, $key, $body);
    }

}
