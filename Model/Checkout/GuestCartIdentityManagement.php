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

namespace SystemCode\CustomerIdentityBrazil\Model\Checkout;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Api\Data\QuoteIdentityDataInterface;
use SystemCode\CustomerIdentityBrazil\Api\GuestCartIdentityManagementInterface;

class GuestCartIdentityManagement implements GuestCartIdentityManagementInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteIdentity $quoteIdentity
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly QuoteIdMaskFactory $quoteIdMaskFactory,
        private readonly QuoteIdentity $quoteIdentity
    ) {
    }

    /**
     * @inheritdoc
     */
    public function save(string $cartId, QuoteIdentityDataInterface $identity): bool
    {
        if (!$this->config->isActive()) {
            return false;
        }

        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        if (!$quoteIdMask->getQuoteId()) {
            throw new NoSuchEntityException(__('Cart not found.'));
        }

        $quote = $this->quoteRepository->getActive((int) $quoteIdMask->getQuoteId());
        $this->quoteIdentity->applyIdentityDataToQuote($quote, $identity);
        $this->quoteRepository->save($quote);

        return true;
    }
}
