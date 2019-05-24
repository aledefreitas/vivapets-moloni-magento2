<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Queue;

use Vivapets\Moloni\Api\Queue\MessageInterface;

interface PublisherInterface
{
    /**
     * Publishes a message to the AMQP
     *
     * @param  Vivapets\Moloni\Api\Queue\MessageInterface  $message
     *
     * @return void
     */
    public function publish(MessageInterface $message);
}
