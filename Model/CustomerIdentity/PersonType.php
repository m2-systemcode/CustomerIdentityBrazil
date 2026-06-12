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

namespace SystemCode\CustomerIdentityBrazil\Model\CustomerIdentity;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class PersonType
{
    /**
     * Initialize dependencies.
     *
     * @param FieldVisibility $fieldVisibility
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        private readonly FieldVisibility $fieldVisibility,
        private readonly Session $customerSession,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    /**
     * Resolve .
     *
     * @return string
     */
    public function resolve(): string
    {
        if (!$this->fieldVisibility->showIndividualSection()) {
            return 'cnpj';
        }

        if (!$this->fieldVisibility->showCorporationSection()) {
            return 'cpf';
        }

        $customer = $this->getLoggedInCustomer();
        if ($customer === null) {
            return 'cpf';
        }

        $personType = $customer->getCustomAttribute('person_type')?->getValue();
        if (in_array($personType, ['cpf', 'cnpj'], true)) {
            return (string) $personType;
        }

        if ($this->resolveFromCustomerData($customer)) {
            return 'cnpj';
        }

        return 'cpf';
    }

    /**
     * Resolve from customer data.
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    private function resolveFromCustomerData(CustomerInterface $customer): bool
    {
        $taxvat = $customer->getTaxvat();

        return $taxvat !== null && $taxvat !== '' && !$this->isValidCpfFormat($taxvat);
    }

    /**
     * Retrieve logged in customer.
     *
     * @return ?CustomerInterface
     */
    private function getLoggedInCustomer(): ?CustomerInterface
    {
        if (!$this->customerSession->isLoggedIn()) {
            return null;
        }

        try {
            return $this->customerRepository->getById((int) $this->customerSession->getCustomerId());
        } catch (NoSuchEntityException | LocalizedException) {
            return null;
        }
    }

    /**
     * Check whether valid cpf format.
     *
     * @param ?string $value
     * @return bool
     */
    private function isValidCpfFormat(?string $value): bool
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $value);

        return strlen($digits) === 11;
    }
}
