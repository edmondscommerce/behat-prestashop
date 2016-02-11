#PrestaShop Context
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

### Installation

Install via composer

composer require edmondscommerce/behat-prestashop-context

### Include Contexts in Behat Configuration
``` yaml
default:
    suites:
        default:
            contexts:
                - EdmondsCommerce\BehatPrestashop\PrestaShopAdminContext
                    adminUser: adminUserName
                    adminPassword: adminPassword
                    adminPath: uri/to/admin
                - EdmondsCommerce\BehatPrestashop\PrestaShopFrontEndContext

```

The tests do assume the default out of the box theme.