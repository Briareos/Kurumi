<?php

namespace Briareos\NodejsBundle\Nodejs;


class Dispatcher
{
    public function __construct($host = 'localhost', $port = 8080, $resource = '/socket.io', $serviceKey = '', $connectTimeout = 5000)
    {
        $this->host = $host;
        $this->port = $port;
        $this->resource = $resource;
        $this->serviceKey = $serviceKey;
        $this->connectTimeout = $connectTimeout;
    }

    public function getWebsocketSwfLocation()
    {
        return 'bundles/nodejs/WebSocketMain.swf';
    }

    public function send(NodejsMessage $message)
    {
        $ch = curl_init($this->getServiceUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message->toArray()));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'NodejsServiceKey: ' . $this->getServiceKey(),
        ));
        curl_exec($ch);
    }

    public function getServiceUrl($type = 'message')
    {
        switch ($type) {
            case 'message':
                return 'http://' . $this->getHost() . ':' . $this->getPort() . '/nodejs/publish';
        }
    }
}