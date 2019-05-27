<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Plugin\Sales\Api;

use Vivapets\Moloni\Queue\Publishers\InvoicesPublisher;
use Vivapets\Moloni\Queue\MessageFactory as QueueMessageFactory;

use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;

class OrderManagement
{
    /**
     * @var \Vivapets\Moloni\Queue\Publishers\InvoicesPublisher
     */
    protected $queuePublisher;

    /**
     * @var \Vivapets\Moloni\Queue\MessageFactory
     */
    protected $queueMessageFactory;

    /**
     * @param  \Vivapets\Moloni\Queue\Publishers\InvoicesPublisher  $queuePublisher
     * @param  \Vivapets\Moloni\Queue\MessageFactory  $queueMessageFactory
     *
     * @return void
     */
    public function __construct(
        InvoicesPublisher $queuePublisher,
        QueueMessageFactory $queueMessageFactory
    ) {
        $this->queuePublisher = $queuePublisher;
        $this->queueMessageFactory = $queueMessageFactory;
    }

    /**
     * @param  \Magento\Sales\Api\OrderManagementInterface  $subject
     * @param  \Magento\Sales\Api\Data\OrderInterface  $order
     * @return $order
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $order
    ) {
        if($order->getEntityId() === null) {
            return $order;
        }

        if($order->getBaseTotalDue() > 0) {
            return $order;
        }

        // Ignores invoice making for test orders made by our team
        if(in_array($order->getCustomerEmail(), [
            'alexandre@vivapets.com',
            'marcos@vivapets.com',
            'mariana@vivapets.com',
            'rita@vivapets.com'
        ])) {
            return $order;
        }

        // Also ignores orders where customer's firstname is test
        if(strtolower($order->getCustomerFirstname()) == 'teste123') {
            return $order;
        }

        $message = $this->queueMessageFactory->create([
            'order_id' => $order->getEntityId(),
        ]);

        $this->queuePublisher->publish($message);

        return $order;
    }
}
