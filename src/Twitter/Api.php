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
    public function init()
    {
        $this->gate = new TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            $this->oAuthToken,
            $this->oAuthSecret
        );
        $this->gate->get('account/verify_credentials');
    }
    public function message($user, $message)
    {
        $this->gate->post('direct_messages/new',
            array('user' => $user, 'text' => $message));
    }
}
