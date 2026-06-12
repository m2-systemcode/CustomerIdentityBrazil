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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\CustomerManagement;
use Magento\Quote\Model\Quote;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Model\Checkout\QuoteIdentity;

class CustomerManagementPlugin
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param QuoteIdentity $quoteIdentity
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly QuoteIdentity $quoteIdentity,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    /**
     * Execute before populate customer info.
     *
     * @param CustomerManagement $subject
     * @param Quote $quote
     * @return void
     */
    public function beforePopulateCustomerInfo(CustomerManagement $subject, Quote $quote): void
    {
        if (!$this->config->isActive()) {
            return;
        }

        $this->quoteIdentity->applyQuoteToCustomer($quote, $quote->getCustomer());
    }

    /**
     * Execute after populate customer info.
     *
     * @param CustomerManagement $subject
     * @param mixed $result
     * @param Quote $quote
     * @return void
     */
    public function afterPopulateCustomerInfo(CustomerManagement $subject, mixed $result, Quote $quote): void
    {
        if (!$this->config->isActive() || !$quote->getCustomerId()) {
            return;
        }

        try {
            $customer = $this->customerRepository->getById((int) $quote->getCustomerId());
            $this->quoteIdentity->applyCustomerToQuote($quote, $customer);
        } catch (NoSuchEntityException | LocalizedException) {
            return;
        }
    }
}
