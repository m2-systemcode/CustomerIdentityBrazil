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

namespace SystemCode\CustomerIdentityBrazil\Plugin\Checkout;

use Magento\Checkout\Api\Data\ShippingInformationExtensionInterface;
use Magento\Checkout\Api\Data\ShippingInformationExtensionFactory;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Model\Checkout\QuoteIdentity;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ShippingInformationManagementPlugin
{
    private const array EXTENSION_FIELDS = [
        'person_type',
        'rg',
        'ie',
        'taxvat',
    ];

    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteIdentity $quoteIdentity
     * @param RequestInterface $request
     * @param ShippingInformationExtensionFactory $extensionFactory
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly CartRepositoryInterface $quoteRepository,
        private readonly QuoteIdentity $quoteIdentity,
        private readonly RequestInterface $request,
        private readonly ShippingInformationExtensionFactory $extensionFactory
    ) {
    }

    /**
     * Execute after save address information.
     *
     * @param ShippingInformationManagement $subject
     * @param mixed $result
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return mixed
     */
    public function afterSaveAddressInformation(
        ShippingInformationManagement $subject,
        $result,
        int $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        if (!$this->config->isActive()) {
            return $result;
        }

        $extensionAttributes = $this->resolveExtensionAttributes($addressInformation);
        if ($extensionAttributes === null || !$this->hasIdentityData($extensionAttributes)) {
            return $result;
        }

        try {
            $quote = $this->quoteRepository->getActive($cartId);
            $this->quoteIdentity->applyExtensionAttributesToQuote($quote, $extensionAttributes);
            $this->quoteRepository->save($quote);
        } catch (LocalizedException) {
            return $result;
        }

        return $result;
    }

    /**
     * Resolve extension attributes.
     *
     * @param ShippingInformationInterface $addressInformation
     * @return ?ShippingInformationExtensionInterface
     */
    private function resolveExtensionAttributes(
        ShippingInformationInterface $addressInformation
    ): ?ShippingInformationExtensionInterface {
        $extensionAttributes = $addressInformation->getExtensionAttributes();

        if ($extensionAttributes !== null && $this->hasIdentityData($extensionAttributes)) {
            return $extensionAttributes;
        }

        $content = $this->request->getContent();
        if ($content === '') {
            return $extensionAttributes;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return $extensionAttributes;
        }

        $raw = $data['addressInformation']['extension_attributes'] ?? null;
        if (!is_array($raw) || $raw === []) {
            return $extensionAttributes;
        }

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }

        foreach (self::EXTENSION_FIELDS as $code) {
            if (empty($raw[$code])) {
                continue;
            }

            $setter = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $code)));
            if (method_exists($extensionAttributes, $setter)) {
                $extensionAttributes->$setter((string) $raw[$code]);
            }
        }

        return $extensionAttributes;
    }

    /**
     * Check whether entity has identity data.
     *
     * @param ShippingInformationExtensionInterface $extensionAttributes
     * @return bool
     */
    private function hasIdentityData(ShippingInformationExtensionInterface $extensionAttributes): bool
    {
        foreach (self::EXTENSION_FIELDS as $code) {
            $getter = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $code)));
            if (method_exists($extensionAttributes, $getter) && $extensionAttributes->$getter()) {
                return true;
            }
        }

        return false;
    }
}
