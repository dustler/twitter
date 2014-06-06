<?php


namespace Twitter;

use TwitterOAuth;
use VT\Worker\Application;
use VT\Worker\WorkerInterface;
use Twitter\Api;

class Worker implements WorkerInterface
{
    protected $application;
    public function __construct(Application $app)
    {
        $this->application = $app;
    }
    public function handle()
    {
        $connection = new \AMQPConnection($this->application['amqp.options']);
        $connection->connect();

        $channel = new \AMQPChannel($connection);

        $queue = new \AMQPQueue($channel);
        $queue->setName('twitter');
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();

        $app = $this->application;

        $queue->consume(function ($message, $queue) use (&$app) {

            $api = new Api();
            $api->consumerKey = 'InbRMKMKnOlE51mwTLPzxkUck';
            $api->consumerSecret = 'WUDvfZB22tYykKg9dVGhmvZAKDWrnSveQSlgGE1lmZLXH4H1FV';
            $api->oAuthToken = '477071837-9SzUZo6wTpp9ti157O29OrLJE1Pdw78XYINoNgkA';
            $api->oAuthSecret = 'fFhZivjgFpEUq52ogq5ot7fx5ebmAvdj0zb3KAiPMhO0M';
            $api->init();
            $api->message('imf4wsh', $message->getBody());

            //$messageBody = $message->getBody();
            //$json = json_decode($messageBody, true);
            //$store = $app['twitter']->getNoteStore();
/*
            $payload = $json['payload'];
            if (is_array($payload)) {
                $payload = implode($payload);
            }

            $nBody = '<?xml version="1.0" encoding="UTF-8"?>';
            $nBody .= '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">';
            $nBody .= '<en-note>' . $payload . '</en-note>';

            $note = new Note();
            $note->notebookGuid = '';
            $note->content = $nBody;
            $note->title = $json['title'];

            try {
                $newnote = $store->createNote($note);
            } catch (Exception $e) {
                var_dump($e);
            }
            $queue->ack($message->getDeliveryTag());*/
        });
    }
} 