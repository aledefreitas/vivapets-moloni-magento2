<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Queue\Publishers;

use Vivapets\Moloni\Api\Queue\MessageInterface;
use Vivapets\Moloni\Api\Queue\PublisherInterface;
use Magento\Framework\MessageQueue\PublisherInterface as MagentoPublisher;
use Vivapets\Moloni\Logger\Logger;

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
     * @var \Vivapets\Moloni\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     *
     * @return void
     */
    public function __construct(
        MagentoPublisher $publisher,
        Logger $logger
    ) {
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function publish(MessageInterface $message)
    {
        try {
            $this->publisher->publish(self::TOPIC_NAME, $message);
        } catch(\Exception $e) {
            $this->logger->info(sprintf('Error publishing message to moloni worker: %s', $e->getMessage()));
            return;
        }
    }
}
