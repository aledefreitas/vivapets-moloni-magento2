<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Queue\Consumers;

use Vivapets\Moloni\Api\Queue\ConsumerInterface;

use Magento\Framework\App\State;
use Vivapets\Moloni\Helper\InvoiceReceipts;
use Magento\Sales\Api\OrderRepositoryInterface;

use Vivapets\Moloni\Api\Queue\MessageInterface;
use Vivapets\Moloni\Exceptions\ApiResponseException;
use Magento\Framework\Exception\NoSuchEntityException;

class InvoicesConsumer implements ConsumerInterface
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Vivapets\Moloni\Helper\InvoiceReceipts
     */
    protected $invoiceReceiptsService;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param  \Magento\Framework\App\State  $state
     * @param  \Vivapets\Moloni\Helper\InvoiceReceipts  $invoiceReceiptsService
     * @param  \Magento\Sales\Api\OrderRepositoryInterface  $orderRepository
     *
     * @return void
     */
    public function __construct(
        State $state,
        InvoiceReceipts $invoiceReceiptsService,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->state = $state;
        $this->invoiceReceiptsService = $invoiceReceiptsService;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Consumes and processes the message from queue
     *
     * @param  \Vivapets\Moloni\Api\Queue\MessageInterface  $message
     *
     * @return void
     */
    public function processMessage(MessageInterface $message)
    {
        // @TODO: Implement a better error reporting mechanism
        try {
            $data = unserialize($message->getMessage());

            $order = $this->orderRepository->get($data['order_id']);

            $this->invoiceReceiptsService->createInvoiceReceipt($order);
        } catch(ApiResponseException $e) {
            echo 'ERROR IN ORDER #' . $order->getEntityId() . PHP_EOL;
            echo $e->getMessage();
            echo PHP_EOL;
            echo PHP_EOL;
        } catch(NoSuchEntityException $e) {
            echo $e->getMessage();
            echo PHP_EOL;
            echo PHP_EOL;
        } catch(\Exception $e) {
            echo $e->getMessage();
            echo PHP_EOL;
            echo PHP_EOL;
        }
    }
}
