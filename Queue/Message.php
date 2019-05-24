<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Queue;

use Vivapets\Moloni\Api\Queue\MessageInterface;

class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $message;

    /**
     * Sets a message to be serialized
     *
     * @param  mixed  $message
     *
     * @return \Vivapets\Moloni\Api\Queue\MessageInterface
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Unserializes and gets the message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
