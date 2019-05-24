<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Queue;

use Vivapets\Moloni\Api\Queue\MessageInterface;

interface ConsumerInterface
{
    /**
     * Processes the message received from queue manager
     *
     * @param  Vivapets\Moloni\Api\Queue\MessageInterface  $message
     *
     * @return void
     */
    public function processMessage(MessageInterface $message);
}
