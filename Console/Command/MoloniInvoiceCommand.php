<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Magento\Framework\App\State;
use Magento\Sales\Model\Order;
use Vivapets\Moloni\Queue\Publishers\InvoicesPublisher;
use Vivapets\Moloni\Queue\MessageFactory as QueueMessageFactory;

use Vivapets\Moloni\Exceptions\ApiResponseException;
use Magento\Framework\App\Area;

class MoloniInvoiceCommand extends Command
{
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Vivapets\Moloni\Queue\Publishers\InvoicesPublisher
     */
    protected $queuePublisher;

    /**
     * @var \Vivapets\Moloni\Queue\MessageFactory
     */
    protected $queueMessageFactory;

    /**
     *
     * @return void
     */
    public function __construct(
        State $state,
        Order $order,
        InvoicesPublisher $queuePublisher,
        QueueMessageFactory $queueMessageFactory
    ) {
        $this->state = $state;
        $this->order = $order;
        $this->queuePublisher = $queuePublisher;
        $this->queueMessageFactory = $queueMessageFactory;
        parent::__construct();
    }

    /**
     * Sets command's configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('vivapets:moloni:moloni-invoice')
            ->setDescription('Sends an invoice to moloni for a given order id.')
            ->setDefinition([
                new InputArgument('order_id', InputArgument::REQUIRED, 'Order ENTITY ID in database to create moloni invoice for'),
            ]);
    }

    /**
     * Executes the command
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Sets App State Area Code
        $this->state->setAreaCode(Area::AREA_FRONTEND);

        $orderCollection = $this->order->getCollection();
        $orderCollection->addAttributeToSelect('*');
        $orderCollection->addAttributeToFilter('entity_id', $input->getArgument('order_id'));

        foreach($orderCollection as $order) {
            try {
                $message = $this->queueMessageFactory->create([
                    'order_id' => $order->getEntityId(),
                ]);

                $this->queuePublisher->publish($message);
            } catch(\Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}
