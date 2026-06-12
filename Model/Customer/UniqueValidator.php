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

namespace SystemCode\CustomerIdentityBrazil\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;

class UniqueValidator
{
    /**
     * Initialize dependencies.
     *
     * @param CollectionFactory $customerCollectionFactory
     */
    public function __construct(
        private readonly CollectionFactory $customerCollectionFactory
    ) {
    }

    /**
     * Handle assert unique.
     *
     * @param CustomerInterface $customer
     * @param string $attributeCode
     * @param string $value
     * @param string $label
     * @return void
     */
    public function assertUnique(
        CustomerInterface $customer,
        string $attributeCode,
        string $value,
        string $label
    ): void {
        $value = trim($value);

        if ($value === '') {
            return;
        }

        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToSelect('entity_id');
        $collection->addAttributeToFilter($attributeCode, $value);

        if ($customer->getId()) {
            $collection->addAttributeToFilter('entity_id', ['neq' => $customer->getId()]);
        }

        if ($collection->getSize() > 0) {
            throw new LocalizedException(__('%1 already exists.', $label));
        }
    }
}
