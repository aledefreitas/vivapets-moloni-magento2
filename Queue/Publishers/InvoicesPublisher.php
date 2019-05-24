<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Queue\Publishers;

use Vivapets\Moloni\Api\Queue\MessageInterface;
use Vivapets\Moloni\Api\Queue\PublisherInterface;
use Magento\Framework\MessageQueue\PublisherInterface as MagentoPublisher;

class InvoicesPublisher implements PublisherInterface
{
    /**
     * @var string
     */
    const TOPIC_NAME = 'moloni.order.invoice.create';

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /**
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     *
     * @return void
     */
    public function __construct(MagentoPublisher $publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(MessageInterface $message)
    {
        $this->publisher->publish(self::TOPIC_NAME, $message);
    }
}
