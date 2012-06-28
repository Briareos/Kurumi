<?php

namespace App\NodejsBundle\Util;

use Symfony\Component\HttpFoundation\Session\Session;

class Nodejs
{

    public static $messages = array();

    public static $config = NULL;

    public static $baseUrl = NULL;

    public static $headers = NULL;

    private $session;

    private $host;

    private $port;

    private $resource;

    private $serviceKey;

    private $connectTimeout;

    public function __construct(Session $session, $host = 'localhost', $port = 8080, $resource = '/socket.io', $serviceKey = '', $connectTimeout = 5000)
    {
        $this->session = $session;
        $this->authToken = md5($session->getId());
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

    public static function initConfig()
    {
        if (!isset(self::$config)) {
            self::$config = nodejs_get_config();
            self::$headers = array('NodejsServiceKey' => self::$config['serviceKey']);
            self::$baseUrl = nodejs_get_url(self::$config);
        }
    }

    public static function getMessages()
    {
        return self::$messages;
    }

    public static function enqueueMessage(StdClass $message)
    {
        self::$messages[] = $message;
    }

    public static function sendMessages()
    {
        foreach (self::$messages as $message) {
            self::sendMessage($message);
        }
    }

    public static function sendMessage(StdClass $message)
    {
        self::initConfig();
        drupal_alter('nodejs_message', $message);
        $message->clientSocketId = nodejs_get_client_socket_id();
        $options = array(
            'method' => 'POST',
            'data' => drupal_json_encode($message),
            'headers' => self::$headers,
        );
        return drupal_http_request(self::$baseUrl . 'nodejs/publish', $options);
    }

    public static function setUserPresenceList($uid, array $uids)
    {
        self::initConfig();
        return drupal_http_request(self::$baseUrl . "nodejs/user/presence-list/$uid/" . implode(',', $uids), array('headers' => self::$headers));
    }

    public static function logoutUser($token)
    {
        self::initConfig();
        return drupal_http_request(self::$baseUrl . "nodejs/user/logout/$token", array('headers' => self::$headers));
    }

    public static function sendContentTokenMessage($message)
    {
        self::initConfig();
        $message->clientSocketId = nodejs_get_client_socket_id();
        $options = array(
            'method' => 'POST',
            'data' => drupal_json_encode($message),
            'headers' => self::$headers,
            'options' => array('timeout' => 5.0),
        );
        return drupal_http_request(self::$baseUrl . 'nodejs/content/token/message', $options);
    }

    public static function sendContentToken($message)
    {
        self::initConfig();
        $options = array(
            'method' => 'POST',
            'data' => drupal_json_encode($message),
            'headers' => self::$headers,
        );
        return drupal_http_request(self::$baseUrl . 'nodejs/content/token', $options);
    }

    public static function getContentTokenUsers($message)
    {
        self::initConfig();
        $options = array(
            'method' => 'POST',
            'data' => drupal_json_encode($message),
            'headers' => self::$headers,
        );
        return drupal_http_request(self::$baseUrl . 'nodejs/content/token/users', $options);
    }

    public static function kickUser($uid)
    {
        self::initConfig();
        return drupal_http_request(self::$baseUrl . "nodejs/user/kick/$uid", array('headers' => self::$headers));
    }

    public static function addUserToChannel($uid, $channel)
    {
        self::initConfig();
        return drupal_http_request(self::$baseUrl . "nodejs/user/channel/add/$channel/$uid", array('headers' => self::$headers));
    }

    public static function removeUserFromChannel($uid, $channel)
    {
        self::initConfig();
        return drupal_http_request(self::$baseUrl . "nodejs/user/channel/remove/$channel/$uid", array('headers' => self::$headers));
    }

    public static function addChannel($channel)
    {
        self::initConfig();
        return drupal_http_request(self::$baseUrl . "nodejs/channel/add/$channel", array('headers' => self::$headers));
    }

    public static function removeChannel($channel)
    {
        self::initConfig();
        return drupal_http_request(self::$baseUrl . "nodejs/channel/remove/$channel", array('headers' => self::$headers));
    }

    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getServiceKey()
    {
        return $this->serviceKey;
    }

    public function getAuthToken()
    {
        return md5($this->session->getId());
    }
}