<?php
/**
 * NOTICE OF LICENSE
 *
 * @category  SystemCode
 * @package   Systemcode_CustomerIdentityBrazil
 * @author    Eduardo Diogo Dias <contato@systemcode.com.br>
 * @copyright System Code LTDA - ME
 * @license   http://opensource.org/licenses/osl-3.0.php
 */
declare(strict_types=1);

namespace SystemCode\CustomerIdentityBrazil\Plugin\Quote;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Model\Checkout\QuoteIdentity;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QuoteManagementPlugin
{
    private const array ORDER_FIELDS = [
        'person_type',
        'rg',
        'ie',
    ];

    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param CartRepositoryInterface $quoteRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param QuoteIdentity $quoteIdentity
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly QuoteIdentity $quoteIdentity
    ) {
    }

    /**
     * Execute before submit.
     *
     * @param QuoteManagement $subject
     * @param mixed $quote
     * @return void
     */
    public function beforeSubmit(QuoteManagement $subject, $quote): void
    {
        if (!$this->config->isActive() || !$quote->getCustomerId()) {
            return;
        }

        try {
            $customer = $this->customerRepository->getById((int) $quote->getCustomerId());
            $this->quoteIdentity->applyQuoteToCustomer($quote, $customer);
            $this->customerRepository->save($customer);
        } catch (LocalizedException) {
            return;
        }
    }

    /**
     * Execute after submit.
     *
     * @param QuoteManagement $subject
     * @param mixed $order
     * @param mixed $quote
     * @param mixed $orderData
     */
    public function afterSubmit(QuoteManagement $subject, $order, $quote, $orderData = [])
    {
        if (!$order || !$quote) {
            return $order;
        }

        $hasData = false;

        foreach (self::ORDER_FIELDS as $field) {
            $value = $quote->getData($field);
            if ($value === null || $value === '') {
                continue;
            }

            $order->setData($field, (string) $value);
            $hasData = true;
        }

        if ($hasData) {
            $this->orderRepository->save($order);
        }

        return $order;
    }
}
