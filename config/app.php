<?php

use VT\Worker\Application;

return array(
    'id'             => 'twitter_service',
    'consumerKey'    => 'InbRMKMKnOlE51mwTLPzxkUck',
    'consumerSecret' => 'WUDvfZB22tYykKg9dVGhmvZAKDWrnSveQSlgGE1lmZLXH4H1FV',
    'oAuthToken'     => '477071837-9SzUZo6wTpp9ti157O29OrLJE1Pdw78XYINoNgkA',
    'oAuthSecret'    => 'fFhZivjgFpEUq52ogq5ot7fx5ebmAvdj0zb3KAiPMhO0M',
    'logger.name'     => 'twitter_service',
    'logger.handler'  => function () {
            return new Monolog\Handler\SyslogHandler('dinbackend');
        },
    'iterations'      => 0,
    'maxIterations'   => 10000,
    'api' => function (Application $app) {
            /*$api = new Twitter\Api();
            $api->consumerKey    = $app['consumerKey'];
            $api->consumerSecret = $app['consumerSecret'];
            $api->oAuthToken     = $app['oAuthToken'];
            $api->oAuthSecret    = $app['oAuthSecret'];
            $api->init();
            return $api;*/
            return new \TwitterOAuth(
                $app['consumerKey'],
                $app['consumerSecret'],
                $app['oAuthToken'],
                $app['oAuthSecret']
            );
        },
    'writer' => function (Application $app) {
        return new Twitter\Handler\Writer($app);
    },
    'reader' => function (Application $app) {
        return new Twitter\Handler\Reader($app);
    },
    'amqp.options' => array(
        'server'       => array(
            'host'     => 'dinbe1.majordomo.ru',
            'port'     => 5672,
            'login'    => 'guest',
            'password' => 'guest',
        ),
        'requestQueue.writer' => array(
            'name'       => 'service.twitter.write',
            'routingKey' => 'service.twitter.write',
        ),
        'requestQueue.reader' => array(
            'name'       => 'service.twitter.reader',
            'routingKey' => 'service.twitter.reader',
        ),
        'ordersQueue' => array(
            'name'       => 'syncQueue',
            'routingKey' => 'requestsFromFE',
        ),
    ),
    'amqp.connection' => function ($app) {
            $connection = new \AMQPConnection($app['amqp.options']['server']);
            $connection->connect();
            return $connection;
        },
    'amqp.channel' => function (Application $app) {
            return new \AMQPChannel($app->get('amqp.connection'));
        },
    'amqp.exchange'   => function (Application $app) {
            $exchange   = new \AMQPExchange($app->get('amqp.channel'));
            $exchange->setName('amq.topic');
            $exchange->setType(AMQP_EX_TYPE_TOPIC);
            $exchange->setFlags(AMQP_DURABLE);
            $exchange->declareExchange();
            return $exchange;
        },
    'amqp.queue.writer' => function (Application $app) {
            $queue = new \AMQPQueue($app->get('amqp.channel'));
            $name  = $app['amqp.options']['requestQueue.writer']['name'];
            $queue->setFlags(AMQP_DURABLE);
            $queue->setName($name);
            $queue->declareQueue();
            $queue->bind($app->get('amqp.exchange')->getName(), $app['amqp.options']['requestQueue.writer']['routingKey']);

            return $queue;
        },
    'rpc.writer' => function (Application $app) {
            return new Twitter\RPC\Writer($app->get('api'));
        },
    'rpc.reader' => function (Application $app) {
            return new Twitter\RPC\Reader($app->get('api'));
        }
);
