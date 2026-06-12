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
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;

class FieldVisibility
{
    /**
     * Initialize dependencies.
     *
     * @param ConfigInterface $config
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly Session $customerSession,
        private readonly CustomerRepositoryInterface $customerRepository
    ) {
    }

    /**
     * Handle show individual section.
     *
     * @return bool
     */
    public function showIndividualSection(): bool
    {
        return $this->config->isFieldVisible('individual', 'taxvat')
            || $this->config->isFieldVisible('individual', 'rg');
    }

    /**
     * Handle show corporation section.
     *
     * @return bool
     */
    public function showCorporationSection(): bool
    {
        return $this->config->isFieldVisible('corporation', 'taxvat')
            || $this->config->isFieldVisible('corporation', 'ie');
    }

    /**
     * Check whether field visible.
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isFieldVisible(string $attributeCode): bool
    {
        return match ($attributeCode) {
            'taxvat' => $this->config->isFieldVisible('individual', 'taxvat')
                || $this->config->isFieldVisible('corporation', 'taxvat'),
            'rg' => $this->config->isFieldVisible('individual', 'rg'),
            'ie' => $this->config->isFieldVisible('corporation', 'ie'),
            default => false,
        };
    }

    /**
     * Check whether field required.
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isFieldRequired(string $attributeCode): bool
    {
        return match ($attributeCode) {
            'taxvat' => $this->resolvePersonType() === 'cpf'
                ? $this->config->isFieldRequired('individual', 'taxvat')
                : $this->config->isFieldRequired('corporation', 'taxvat'),
            'rg' => $this->config->isFieldRequired('individual', 'rg'),
            'ie' => $this->config->isFieldRequired('corporation', 'ie'),
            default => false,
        };
    }

    /**
     * Check whether field unique.
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isFieldUnique(string $attributeCode): bool
    {
        return match ($attributeCode) {
            'taxvat' => $this->resolvePersonType() === 'cpf'
                ? $this->config->isFieldUnique('individual', 'taxvat')
                : $this->config->isFieldUnique('corporation', 'taxvat'),
            'rg' => $this->config->isFieldUnique('individual', 'rg'),
            'ie' => $this->config->isFieldUnique('corporation', 'ie'),
            default => false,
        };
    }

    /**
     * Resolve person type.
     *
     * @return string
     */
    private function resolvePersonType(): string
    {
        if (!$this->customerSession->isLoggedIn()) {
            return 'cpf';
        }

        try {
            $customer = $this->customerRepository->getById((int) $this->customerSession->getCustomerId());
            $personType = $customer->getCustomAttribute('person_type')?->getValue();

            if (in_array($personType, ['cpf', 'cnpj'], true)) {
                return (string) $personType;
            }
        } catch (NoSuchEntityException | LocalizedException) {
            return 'cpf';
        }

        return 'cpf';
    }
}
