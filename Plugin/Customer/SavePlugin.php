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

namespace SystemCode\CustomerIdentityBrazil\Plugin\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use SystemCode\CustomerIdentityBrazil\Model\Customer\SaveValidator;

/**
 * Provide configured behavior.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SavePlugin
{
    /**
     * Initialize dependencies.
     *
     * @param SaveValidator $saveValidator
     */
    public function __construct(
        private readonly SaveValidator $saveValidator
    ) {
    }

    /**
     * Execute before save.
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $customer
     * @param ?string $passwordHash
     * @return array
     */
    public function beforeSave(
        CustomerRepositoryInterface $subject,
        CustomerInterface $customer,
        $passwordHash = null
    ): array {
        $this->saveValidator->execute($customer);

        return [$customer, $passwordHash];
    }
}
