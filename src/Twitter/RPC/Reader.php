<?php


namespace Twitter\RPC;


class Writer {
    /**
     * @var \TwitterOAuth
     */
    protected $api;
    public function __construct($api)
    {
        $this->api = $api;
    }
    public function get($data)
    {
        $action = $data['action'];
        array_shift($data);
        $this->api->get($action, $data);
    }
}