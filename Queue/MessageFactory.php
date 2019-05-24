<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Queue;

use Magento\Framework\ObjectManagerInterface;
use Vivapets\Moloni\Api\Queue\MessageInterface;

class MessageFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param  \Magento\Framework\ObjectManagerInterface  $objectManager
     *
     * @return void
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Creates a message instance
     *
     * @param  mixed  $message
     *
     * @return \Vivapets\Moloni\Queue\Message
     */
    public function create($message = null)
    {
        return $this->objectManager->create(MessageInterface::class)->setMessage(serialize($message));
    }
}
