<?php

namespace Briareos\NodejsBundle\Nodejs;

class Message {

    private $callback;

    private $data;

    private $channel;

    private $broadcast;

    public function __construct($callback) {
        $this->setCallback($callback);
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setBroadcast($broadcast)
    {
        $this->broadcast = $broadcast;
    }

    public function getBroadcast()
    {
        return $this->broadcast;
    }

    public function toArray() {
        return array(
            'broadcast' => $this->getBroadcast(),
            'callback' => $this->getCallback(),
            'channel' => $this->getChannel(),
            'data' => $this->getData(),
        );
    }
}