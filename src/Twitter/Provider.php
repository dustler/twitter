<?php


namespace Twitter;

use VT\Worker\Application;
use VT\Worker\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    public function register(Application $app, array $params)
    {
        $application['twitter.client'] = function ($app) {

            //return new Client($app['evernote.client.options']);
        };
    }
} 