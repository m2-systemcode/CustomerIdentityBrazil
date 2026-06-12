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

namespace SystemCode\CustomerIdentityBrazil\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class FieldVisibility implements OptionSourceInterface
{
    /**
     * Convert to option array.
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '', 'label' => __('No')],
            ['value' => 'opt', 'label' => __('Optional')],
            ['value' => 'req', 'label' => __('Required')],
            ['value' => 'optuni', 'label' => __('Optional and Unique')],
            ['value' => 'requni', 'label' => __('Required and Unique')],
        ];
    }
}
