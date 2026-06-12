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

use Magento\Quote\Api\CartRepositoryInterface;
use SystemCode\CustomerIdentityBrazil\Api\CartIdentityManagementInterface;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Api\Data\QuoteIdentityDataInterface;

class CartIdentityManagement implements CartIdentityManagementInterface
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteIdentity $quoteIdentity
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly QuoteIdentity $quoteIdentity
    ) {
    }

    /**
     * @inheritdoc
     */
    public function save(int $cartId, QuoteIdentityDataInterface $identity): bool
    {
        if (!$this->config->isActive()) {
            return false;
        }

        $quote = $this->quoteRepository->getActive($cartId);
        $this->quoteIdentity->applyIdentityDataToQuote($quote, $identity);
        $this->quoteRepository->save($quote);

        return true;
    }
}
