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

class CustomerEdit implements OptionSourceInterface
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
            ['value' => 'yes', 'label' => __('Yes, except change person type')],
            ['value' => 'yesall', 'label' => __('Yes, and allow change person type')],
        ];
    }
}
