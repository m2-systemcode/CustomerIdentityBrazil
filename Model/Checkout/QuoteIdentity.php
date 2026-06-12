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

use Magento\Checkout\Api\Data\ShippingInformationExtensionInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\Data\CartInterface;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\Api\Data\QuoteIdentityDataInterface;

class QuoteIdentity
{
    /**
     * Apply extension attributes to quote.
     *
     * @param CartInterface $quote
     * @param ShippingInformationExtensionInterface $extensionAttributes
     * @return void
     */
    public function applyExtensionAttributesToQuote(
        CartInterface $quote,
        ShippingInformationExtensionInterface $extensionAttributes
    ): void {
        foreach (ConfigInterface::QUOTE_FIELDS as $field) {
            $value = $this->readExtensionAttribute($extensionAttributes, $field);
            if ($value === null || $value === '') {
                continue;
            }

            if ($field === 'taxvat') {
                $quote->setData('customer_taxvat', (string) $value);
                continue;
            }

            $quote->setData($field, (string) $value);
        }
    }

    /**
     * Apply identity data to quote.
     *
     * @param CartInterface $quote
     * @param QuoteIdentityDataInterface $identity
     * @return void
     */
    public function applyIdentityDataToQuote(CartInterface $quote, QuoteIdentityDataInterface $identity): void
    {
        $this->setQuoteValue($quote, 'person_type', $identity->getPersonType());
        $this->setQuoteValue($quote, 'rg', $identity->getRg());
        $this->setQuoteValue($quote, 'ie', $identity->getIe());
        $this->setQuoteValue($quote, 'taxvat', $identity->getTaxvat());
    }

    /**
     * Apply customer to quote.
     *
     * @param CartInterface $quote
     * @param CustomerInterface $customer
     * @return void
     */
    public function applyCustomerToQuote(CartInterface $quote, CustomerInterface $customer): void
    {
        $personType = (string) ($customer->getCustomAttribute('person_type')?->getValue() ?? 'cpf');
        if (!in_array($personType, ['cpf', 'cnpj'], true)) {
            $personType = 'cpf';
        }

        $quote->setData('person_type', $personType);

        foreach (ConfigInterface::QUOTE_FIELDS as $field) {
            if ($field === 'person_type') {
                continue;
            }

            $value = $field === 'taxvat'
                ? $customer->getTaxvat()
                : $customer->getCustomAttribute($field)?->getValue();

            if ($value !== null && $value !== '') {
                $this->setQuoteValue($quote, $field, (string) $value);
            }
        }
    }

    /**
     * Apply quote to customer.
     *
     * @param CartInterface $quote
     * @param CustomerInterface $customer
     * @return void
     */
    public function applyQuoteToCustomer(CartInterface $quote, CustomerInterface $customer): void
    {
        foreach (ConfigInterface::QUOTE_FIELDS as $field) {
            $value = $this->readQuoteValue($quote, $field);
            if ($value === null || $value === '') {
                continue;
            }

            if ($field === 'taxvat') {
                $customer->setTaxvat($value);
                continue;
            }

            $customer->setCustomAttribute($field, $value);
        }
    }

    /**
     * Set quote value.
     *
     * @param CartInterface $quote
     * @param string $field
     * @param ?string $value
     * @return void
     */
    private function setQuoteValue(CartInterface $quote, string $field, ?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if ($field === 'taxvat') {
            $quote->setData('customer_taxvat', $value);
            return;
        }

        $quote->setData($field, $value);
    }

    /**
     * Handle read quote value.
     *
     * @param CartInterface $quote
     * @param string $field
     * @return ?string
     */
    private function readQuoteValue(CartInterface $quote, string $field): ?string
    {
        if ($field === 'taxvat') {
            $value = $quote->getData('customer_taxvat');

            return $value !== null && $value !== '' ? (string) $value : null;
        }

        $value = $quote->getData($field);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * Handle read extension attribute.
     *
     * @param ShippingInformationExtensionInterface $extensionAttributes
     * @param string $field
     * @return ?string
     */
    private function readExtensionAttribute(
        ShippingInformationExtensionInterface $extensionAttributes,
        string $field
    ): ?string {
        return match ($field) {
            'person_type' => $extensionAttributes->getPersonType(),
            'rg' => $extensionAttributes->getRg(),
            'ie' => $extensionAttributes->getIe(),
            'taxvat' => $extensionAttributes->getTaxvat(),
            default => null,
        };
    }
}
