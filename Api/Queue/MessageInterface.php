<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api\Queue;

interface MessageInterface
{
    /**
     * Sets a message to be serialized
     *
     * @param  mixed  $message
     *
     * @return void
     */
    public function setMessage($message);

    /**
     * Unserializes and gets the message
     *
     * @return mixed
     */
    public function getMessage();
}
