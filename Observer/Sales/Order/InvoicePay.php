<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Observer\Sales\Order;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

use Vivapets\Moloni\Queue\Publishers\InvoicesPublisher;
use Vivapets\Moloni\Queue\MessageFactory as QueueMessageFactory;
use Vivapets\Moloni\Logger\Logger;
use Magento\Sales\Api\OrderRepositoryInterface;

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
     * @var \Vivapets\Moloni\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param  \Vivapets\Moloni\Queue\Publishers\InvoicesPublisher  $queuePublisher
     * @param  \Vivapets\Moloni\Queue\MessageFactory  $queueMessageFactory
     * @param  \Vivapets\Moloni\Logger\Logger  $logger
     * @param  \Magento\Sales\Api\OrderRepositoryInterface  $orderRepository
     *
     * @return void
     */
    public function __construct(
        InvoicesPublisher $queuePublisher,
        QueueMessageFactory $queueMessageFactory,
        Logger $logger,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->queuePublisher = $queuePublisher;
        $this->queueMessageFactory = $queueMessageFactory;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
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
            $invoice = $observer->getData('invoice');
            $order = $invoice->getOrder();

            // Since order is only persisted to DB after calling `place()` in OrderService,
            // auto-capture orders will not have an entity id yet, cause during `place()`
            // they will capture the value and trigger this event prior to order actually saving to DB.
            //
            // So, we need to save it via Repository to get an entity id if it doesn't have one.
            //
            // @see https://github.com/magento/magento2/blob/2.3-develop/app/code/Magento/Sales/Model/Service/OrderService.php#L199-L225
            // @see https://github.com/magento/magento2/blob/2.3-develop/app/code/Magento/Sales/Model/Order.php#L950-L959
            if($order->getEntityId() === null) {
                $order = $this->orderRepository->save($order);
                $this->logger->info(sprintf('Order #%s by %s did not have an Id, so it was created: %s', $order->getIncrementId(), $order->getCustomerEmail(), $order->getEntityId()));
            }

            if($order->getBaseTotalDue() > 0) {
                $this->logger->info(sprintf('Order #%s has a Base Total Due of %s (email: %s) in Observer', $order->getEntityId(), $order->getBaseTotalDue(), $order->getCustomerEmail()));
                $this->logger->info(print_r([
                    'IncrementId' => $order->getIncrementId(),
                    'GrandTotal' => $order->getGrandTotal(),
                    'Subtotal' => $order->getSubtotal(),
                    'CustomerId' => $order->getCustomerId(),
                    'CustomerEmail' => $order->getCustomerEmail(),
                    'CustomerFirstname' => $order->getCustomerFirstname(),
                    'CustomerLastname' => $order->getCustomerLastname(),
                    'BaseTotalDue' => $order->getBaseTotalDue(),
                    'Date' => new \DateTime(),
                ], true));

                return $order;
            }

            // Ignores invoice making for test orders made by our team
            if(in_array($order->getCustomerEmail(), [
                'alexandre@vivapets.com',
                'marcos@vivapets.com',
                'mariana@vivapets.com',
                'rita@vivapets.com'
            ])) {
                return;
            }

            // Also ignores orders where customer's firstname is test
            if(strtolower($order->getCustomerFirstname()) == 'teste123') {
                return;
            }

            $message = $this->queueMessageFactory->create([
                'order_id' => $order->getEntityId(),
            ]);

            $this->queuePublisher->publish($message);
        } catch (\Exception $e) {
            $this->logger->info('Error occurred in order observer: ' . $e->getMessage());
            return __('An error occurred while processing your order. Please contact us now.');
        }
    }
}
