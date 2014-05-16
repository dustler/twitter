<?php


namespace Twitter;

use TwitterOAuth;

/**
 * Class Api
 *
 * @package Twitter
 */
class Api {
    public $consumerKey;
    public $consumerSecret;
    public $oAuthToken;
    public $oAuthSecret;
    /**
     * @var \TwitterOAuth
     */
    protected $gate;
    public function __construct()
    {
        $this->gate = new TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            $this->oAuthToken,
            $this->oAuthSecret
        );

    }
    public function status($message)
    {
        $this->gate->post('statuses/update', array('status' => $message));
    }
}
