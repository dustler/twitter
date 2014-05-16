<?php


namespace Twitter;

use TwitterOAuth;

class Client {

    public function __construct($options)
    {
        return new TwitterOAuth(
            $options['consumerKey'],
            $options['consumerSecret'],
            $options['oAuthToken'],
            $options['oAuthSecret']
        );
    }
} 