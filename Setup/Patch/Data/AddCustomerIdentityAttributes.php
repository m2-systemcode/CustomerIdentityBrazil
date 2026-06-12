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

namespace SystemCode\CustomerIdentityBrazil\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCustomerIdentityAttributes implements DataPatchInterface
{
    private const array USED_IN_FORMS = [
        'adminhtml_customer',
        'checkout_register',
        'customer_account_create',
        'customer_account_edit',
        'adminhtml_checkout',
    ];

    private const array ATTRIBUTES = [
        'person_type' => [
            'label' => 'Person Type',
            'position' => 900,
        ],
        'rg' => [
            'label' => 'RG',
            'position' => 1100,
        ],
        'ie' => [
            'label' => 'IE',
            'position' => 1400,
        ],
    ];

    /**
     * Initialize dependencies.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CustomerSetupFactory $customerSetupFactory,
        private readonly AttributeSetFactory $attributeSetFactory
    ) {
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetId = (int) $customerEntity->getDefaultAttributeSetId();
        $attributeGroupId = (int) $this->attributeSetFactory->create()
            ->getDefaultGroupId($attributeSetId);

        foreach (self::ATTRIBUTES as $attributeCode => $attributeData) {
            if ($customerSetup->getAttributeId(Customer::ENTITY, $attributeCode)) {
                continue;
            }

            $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, [
                'type' => 'varchar',
                'input' => 'text',
                'label' => $attributeData['label'],
                'position' => $attributeData['position'],
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'system' => 0,
            ]);

            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, $attributeCode);
            $attribute->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => self::USED_IN_FORMS,
            ]);
            $attribute->save();
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
