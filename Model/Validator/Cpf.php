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

namespace SystemCode\CustomerIdentityBrazil\Model\Validator;

use SystemCode\CustomerIdentityBrazil\Api\ConfigInterface;

class Cpf
{
    /**
     * Check whether valid.
     *
     * @param ?string $cpf
     * @return bool
     */
    public function isValid(?string $cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        if (strlen($cpf) !== 11 || in_array($cpf, ConfigInterface::INVALID_CPF_SEQUENCES, true)) {
            return false;
        }

        for ($length = 9; $length < 11; $length++) {
            $sum = 0;
            for ($position = 0; $position < $length; $position++) {
                $sum += (int) $cpf[$position] * (($length + 1) - $position);
            }
            $digit = ((10 * $sum) % 11) % 10;
            if ((int) $cpf[$length] !== $digit) {
                return false;
            }
        }

        return true;
    }
}
