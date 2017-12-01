<?php

namespace JddjOpenApi\Api;

use JddjOpenApi\Config\Config;
use JddjOpenApi\Protocol\JdClient;

class RequestService
{
    /** @var JdClient  */
    protected $client;
    protected $action='';
    protected $params=array();

    public function __construct($token,Config $config)
    {
        $this->client = new JdClient($token, $config);
    }

    public function check()
    {
        return true;
    }

    public function action()
    {
        return $this->action;
    }

    public function params()
    {
        return $this->params;
    }

    public function call($action='',$params=[])
    {
        $this->action = $action;
        $this->params = $params;
        return $this->client->execute($this);
    }

}