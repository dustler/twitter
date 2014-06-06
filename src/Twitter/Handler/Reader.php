<?php

namespace Twitter\Handler;

use VT\Worker\Application;
use VT\Worker\WorkerInterface;
use Psr\Log\LogLevel;
use Exception;

class Reader implements WorkerInterface
{
    /**
     * @var Application
     */
    public $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    /**
     * @return mixed
     */
    public function run()
    {
        $queue = $this->app->get('amqp.queue.writer');
        $app = $this->app;

        $queue->consume(function ($envelope, $queue) use ($app) {
            try {
                $requestMessage = unserialize(base64_decode($envelope->getBody()));
                $requestMethod  = $envelope->getHeader('method');

                if ($envelope->isRedelivery()) {
                    $queue->nack($envelope->getDeliveryTag(), AMQP_NOPARAM);
                    $app->get('logger')->log(LogLevel::INFO, 'REQUEST SYNC', array(
                        'REDELIVERY' => true,
                        'ITERATION'  => $app['iterations'],
                        'METHOD'     => $requestMethod
                    ));
                    $app['iterations'] = $app['iterations'] + 1;

                    return true;
                }

                $app->get('logger')->log(LogLevel::INFO, "REQUEST SYNC", array(
                    'ITERATION' => $app['iterations'],
                    'METHOD'    => $requestMethod
                ));
                $response   = call_user_func_array(array($app->get('rpc.reader'), 'get'), $requestMessage);
                $app->get('amqp.exchange')
                     ->publish(base64_encode(serialize($response)), $envelope->getReplyTo(), AMQP_MANDATORY, array(
                         'content_encoding' => 'base64',
                         'correlation_id'   => $envelope->getCorrelationId(),
                     ));

                $app->get('logger')->log(LogLevel::INFO, 'RESPONSE SYNC', array(
                    'ITERATION' => $app['iterations'],
                    'METHOD'    => $requestMethod
                ));
            } catch (Exception $e) {
                $app->get('amqp.exchange')
                    ->publish('DIE UNSERIALIZE! DIE!!!', $envelope->getReplyTo(), AMQP_MANDATORY, array(
                        'content_encoding' => 'base64',
                        'correlation_id'   => $envelope->getCorrelationId(),
                        'headers'          => array(
                            'error' => 'error'
                        )
                    ));
                $app->get('logger')->log(LogLevel::ERROR, "ERROR IN SYNC", array(
                    'ITERATION' => $app['iterations'],
                    'METHOD'    => $requestMethod,
                    'REASON'    => $e->getMessage()
                ));
            }

            $queue->ack($envelope->getDeliveryTag());
            $app['iterations'] = $app['iterations'] + 1;

            if ($app['iterations'] >= $app['maxIterations']) {
                return false;
            }
        });
    }
}
