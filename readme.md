# System Code Brazil Customer Identity

## About Module

Brazilian customer identity for Magento using native Tax/VAT and name fields. Supports individual (CPF, RG) and corporation (CNPJ, IE) profiles, field visibility rules, customer group assignment, checkout integration, and address template guidance.

For dedicated CPF, CNPJ, Social Name, and Trade Name attributes, install [CustomerIdentityBrazilEnhanced](https://github.com/m2-systemcode/CustomerIdentityBrazilEnhanced).

### Configuration

**Stores > Configuration > System Code > Brazil Customer Identity**

Enable Tax/VAT under **Stores > Configuration > Customers > Customer Configuration > Name and Address Options**.

### Screenshots

#### Admin Configuration
![Admin Configuration](.github/images/configuration.png)

#### Customer Registration
![Customer Registration](.github/images/registration.png)

#### My Account — Edit Profile
![My Account Edit Profile](.github/images/my-account-edit.png)

#### Checkout
![Checkout](.github/images/checkout.png)

### Requirements

- `systemcode/base`
- `systemcode/customer`
- `magento/module-customer`
- `magento/module-checkout`

### How to install

#### ✓ Install by Composer (recommended)
```
composer require systemcode/base systemcode/customer systemcode/customer-identity-brazil
php bin/magento module:enable SystemCode_CustomerIdentityBrazil
php bin/magento setup:upgrade
```

#### ✓ Install Manually
- Copy module to folder `app/code/SystemCode/CustomerIdentityBrazil` and run commands:
```
php bin/magento module:enable SystemCode_CustomerIdentityBrazil
php bin/magento setup:di:compile
php bin/magento setup:upgrade
```

### Suggested installation bundles

These modules are not required by Brazil Customer Identity, but work well together for a complete Brazilian storefront.

#### Address essentials
Custom street labels, street prefix, and CEP autocomplete.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-street-lines \
  systemcode/customer-street-prefix \
  systemcode/customer-address-autocomplete-brazil
```

#### Address autocomplete + registration
Collect the customer address during account creation.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-address-registration
```

#### Address + Brazilian identity
Combine address autocomplete with CPF/CNPJ customer identity.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-identity-brazil
```

#### Full Brazilian storefront stack
Address, identity, registration, street labels, and street prefix.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-address-registration \
  systemcode/customer-street-lines \
  systemcode/customer-street-prefix \
  systemcode/customer-identity-brazil
```

#### Full stack with Enhanced identity
Dedicated CPF, CNPJ, Social Name, and Trade Name fields.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-address-registration \
  systemcode/customer-street-lines \
  systemcode/customer-street-prefix \
  systemcode/customer-identity-brazil \
  systemcode/customer-identity-brazil-enhanced
```

### License
OSL-3.0

### Authors
* [Eduardo Diogo Dias](https://github.com/eduardoddias)


---


## Sobre o Módulo

Identidade de cliente brasileira para Magento usando os campos nativos Tax/VAT e nome. Suporta pessoa física (CPF, RG) e jurídica (CNPJ, IE), regras de visibilidade, atribuição de grupo de cliente, integração com checkout e orientações para modelos de endereço.

Para atributos dedicados de CPF, CNPJ, Razão Social e Nome Fantasia, instale o [CustomerIdentityBrazilEnhanced](https://github.com/m2-systemcode/CustomerIdentityBrazilEnhanced).

### Configuração

**Lojas > Configuração > System Code > Brazil Customer Identity**

Ative Tax/VAT em **Lojas > Configuração > Clientes > Configuração de Cliente > Opções de Nome e Endereço**.

### Screenshots

#### Configuração no Admin
![Configuração no Admin](.github/images/configuration.png)

#### Cadastro de Cliente
![Cadastro de Cliente](.github/images/registration.png)

#### Minha Conta — Editar Perfil
![Minha Conta — Editar Perfil](.github/images/my-account-edit.png)

#### Checkout
![Checkout](.github/images/checkout.png)

### Requisitos

- `systemcode/base`
- `systemcode/customer`
- `magento/module-customer`
- `magento/module-checkout`

### Como Instalar

#### ✓ Instalação via Composer (recomendado)
```
composer require systemcode/base systemcode/customer systemcode/customer-identity-brazil
php bin/magento module:enable SystemCode_CustomerIdentityBrazil
php bin/magento setup:upgrade
```

#### ✓ Instalação Manual
- Copie o módulo para `app/code/SystemCode/CustomerIdentityBrazil` e execute:
```
php bin/magento module:enable SystemCode_CustomerIdentityBrazil
php bin/magento setup:di:compile
php bin/magento setup:upgrade
```

### Combinações de instalação sugeridas

Estes módulos não são obrigatórios para o Brazil Customer Identity, mas combinam bem para uma loja brasileira completa.

#### Essenciais de endereço
Rótulos de rua, prefixo de logradouro e autocomplete de CEP.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-street-lines \
  systemcode/customer-street-prefix \
  systemcode/customer-address-autocomplete-brazil
```

#### Autocomplete + cadastro
Coleta o endereço do cliente já no cadastro.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-address-registration
```

#### Endereço + identidade brasileira
Autocomplete de endereço com identidade CPF/CNPJ.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-identity-brazil
```

#### Stack completo brasileiro
Endereço, identidade, cadastro, rótulos de rua e prefixo de logradouro.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-address-registration \
  systemcode/customer-street-lines \
  systemcode/customer-street-prefix \
  systemcode/customer-identity-brazil
```

#### Stack completo com identidade Enhanced
Campos dedicados de CPF, CNPJ, Razão Social e Nome Fantasia.
```
composer require systemcode/base systemcode/customer \
  systemcode/customer-address-autocomplete-brazil \
  systemcode/customer-address-registration \
  systemcode/customer-street-lines \
  systemcode/customer-street-prefix \
  systemcode/customer-identity-brazil \
  systemcode/customer-identity-brazil-enhanced
```

### Licença
OSL-3.0

### Autores
* [Eduardo Diogo Dias](https://github.com/eduardoddias)
