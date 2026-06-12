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

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Initialize dependencies.
     *
     * @param IdentityConfig $identityConfig
     */
    public function __construct(
        private readonly IdentityConfig $identityConfig
    ) {
    }

    /**
     * Retrieve config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'customerIdentityBrazil' => $this->identityConfig->toArray(),
        ];
    }
}
