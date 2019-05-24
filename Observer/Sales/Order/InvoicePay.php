<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Observer\Sales\Order;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

use Vivapets\Moloni\Queue\Publishers\InvoicesPublisher;
use Vivapets\Moloni\Queue\MessageFactory as QueueMessageFactory;

class InvoicePay implements ObserverInterface
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
        parent::__construct();
    }

    /**
     * Executes the observer logic
     * Sends the order id to amqp, to be processed by our workers
     *
     * @param  \Magento\Framework\Event\Observer  $observer
     *
     * @see \Vivapets\Moloni\Queue\Consumers\InvoiceConsumer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            $invoice = $observer->getEvent();
            $order = $invoice->getOrder();

            // Ignores invoice making for test orders made by our team
            if(in_array($order->getCustomerEmail(), [
                'alexandre@vivapets.com',
                'marcos@vivapets.com',
                'mariana@vivapets.com',
                'rita@vivapets.com'
            ])) {
                return false;
            }

            // Also ignores orders where customer's firstname is test
            if(strtolower($order->getCustomerFirstname()) == 'teste123') {
                return false;
            }

            $message = $this->queueMessageFactory->create([
                'order_id' => $order->getEntityId(),
            ]);

            $this->queuePublisher->publish($message);
        } catch (\Exception $e) {
            return false;
        }
    }
}
