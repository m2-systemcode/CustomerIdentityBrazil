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

namespace SystemCode\CustomerIdentityBrazil\Model\Data;

use Magento\Framework\DataObject;
use SystemCode\CustomerIdentityBrazil\Api\Data\QuoteIdentityDataInterface;

class QuoteIdentityData extends DataObject implements QuoteIdentityDataInterface
{
    /**
     * @inheritdoc
     */
    public function getPersonType(): ?string
    {
        $value = $this->getData(self::PERSON_TYPE);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setPersonType(string $personType): QuoteIdentityDataInterface
    {
        return $this->setData(self::PERSON_TYPE, $personType);
    }

    /**
     * @inheritdoc
     */
    public function getCpf(): ?string
    {
        $value = $this->getData(self::CPF);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setCpf(string $cpf): QuoteIdentityDataInterface
    {
        return $this->setData(self::CPF, $cpf);
    }

    /**
     * @inheritdoc
     */
    public function getCnpj(): ?string
    {
        $value = $this->getData(self::CNPJ);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setCnpj(string $cnpj): QuoteIdentityDataInterface
    {
        return $this->setData(self::CNPJ, $cnpj);
    }

    /**
     * @inheritdoc
     */
    public function getRg(): ?string
    {
        $value = $this->getData(self::RG);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setRg(string $rg): QuoteIdentityDataInterface
    {
        return $this->setData(self::RG, $rg);
    }

    /**
     * @inheritdoc
     */
    public function getIe(): ?string
    {
        $value = $this->getData(self::IE);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setIe(string $ie): QuoteIdentityDataInterface
    {
        return $this->setData(self::IE, $ie);
    }

    /**
     * @inheritdoc
     */
    public function getSocialname(): ?string
    {
        $value = $this->getData(self::SOCIALNAME);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setSocialname(string $socialname): QuoteIdentityDataInterface
    {
        return $this->setData(self::SOCIALNAME, $socialname);
    }

    /**
     * @inheritdoc
     */
    public function getTradename(): ?string
    {
        $value = $this->getData(self::TRADENAME);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setTradename(string $tradename): QuoteIdentityDataInterface
    {
        return $this->setData(self::TRADENAME, $tradename);
    }

    /**
     * @inheritdoc
     */
    public function getTaxvat(): ?string
    {
        $value = $this->getData(self::TAXVAT);

        return $value !== null && $value !== '' ? (string) $value : null;
    }

    /**
     * @inheritdoc
     */
    public function setTaxvat(string $taxvat): QuoteIdentityDataInterface
    {
        return $this->setData(self::TAXVAT, $taxvat);
    }
}
