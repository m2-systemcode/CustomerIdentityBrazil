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

use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;
use SystemCode\CustomerIdentityBrazil\ViewModel\CustomerIdentity;

class IdentityConfig
{
    /**
     * Initialize dependencies.
     *
     * @param CustomerIdentity $customerIdentity
     */
    public function __construct(
        private readonly CustomerIdentity $customerIdentity
    ) {
    }

    /**
     * Check whether active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->customerIdentity->isActive();
    }

    /**
     * Convert configuration to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if (!$this->isActive()) {
            return ['isActive' => false];
        }

        $fields = [];
        foreach (ConfigInterface::FIELD_CODES as $code) {
            if (!$this->customerIdentity->isFieldVisible($code)) {
                continue;
            }

            $fields[$code] = [
                'required' => $code === 'taxvat'
                    ? $this->customerIdentity->isTaxvatRequiredForPersonType(
                        $this->customerIdentity->getPersonType()
                    )
                    : $this->customerIdentity->isFieldRequired($code),
            ];
        }

        return [
            'isActive' => true,
            'showIdentityFields' => $this->customerIdentity->shouldShowCheckoutIdentityFields(),
            'useAccountIdentity' => !$this->customerIdentity->shouldShowCheckoutIdentityFields(),
            'identityData' => $this->customerIdentity->getCheckoutIdentityData(),
            'showToggle' => $this->customerIdentity->shouldShowPersonTypeToggle(),
            'personType' => $this->customerIdentity->getPersonType(),
            'changeFirstnameLabel' => $this->customerIdentity->shouldShowCheckoutIdentityFields()
                && $this->customerIdentity->shouldChangeFirstnameLabel(),
            'changeLastnameLabel' => $this->customerIdentity->shouldShowCheckoutIdentityFields()
                && $this->customerIdentity->shouldChangeLastnameLabel(),
            'showIndividualSection' => $this->customerIdentity->showIndividualSection(),
            'showCorporationSection' => $this->customerIdentity->showCorporationSection(),
            'taxvatIndividual' => $this->customerIdentity->isTaxvatVisibleForPersonType('cpf'),
            'taxvatCorporation' => $this->customerIdentity->isTaxvatVisibleForPersonType('cnpj'),
            'taxvatIndividualRequired' => $this->customerIdentity->isTaxvatRequiredForPersonType('cpf'),
            'taxvatCorporationRequired' => $this->customerIdentity->isTaxvatRequiredForPersonType('cnpj'),
            'rgVisible' => $this->customerIdentity->isFieldVisible('rg'),
            'rgRequired' => $this->customerIdentity->isFieldRequired('rg'),
            'ieVisible' => $this->customerIdentity->isFieldVisible('ie'),
            'ieRequired' => $this->customerIdentity->isFieldRequired('ie'),
            'fields' => $fields,
        ];
    }
}
