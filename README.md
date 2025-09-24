## Spryker Product Subscription Module
This module adds the subscription feature to the product page.

### Steps to do for install
1. `composer require spryker-community/product-subscription`
2. Configure Spryker Core Namespaces

Add the SprykerCommunity namespace to your Spryker configuration:

File: `config/Shared/config_default.php`

```php
<?php

// Add SprykerCommunity to the core namespaces array
$config[KernelConstants::CORE_NAMESPACES] = [
    'SprykerCommunity',  // Add this line
    'SprykerShop',
    'SprykerEco',
    'Spryker',
    'SprykerSdk',
];
```









# TODOs



# SprykerCommunity Dummy Module Integration Guide

This README provides step-by-step instructions to integrate the SprykerCommunity Dummy Module into your Spryker B2B Demo Shop.

## Prerequisites

1. Spryker B2B Demo Shop installed and running
2. Git access to clone the Product Subscription module
3. Composer installed

## Workflow

### Set up a place for packagable modules to work on

1. Create local-packages Directory

Create a local-packages directory in your demo shop root:

```bash
mkdir local-packages
cd local-packages
```

2. Adjust .gitignore of demo-shop

Add the module directory to your main project's .gitignore file to prevent tracking the module as part of the main project:

```
# Add to .gitignore
/local-packages/
```

### Install the Product Subscription Module

1. Clone Product Subscription Module

Clone the Product Subscription module repository into the module directory:

```bash
git clone git@github.com:spryker-community/product-subscription.git
```

Your directory structure should now look like:

```text
b2b-demo-shop/
├── local-packages/
│   └── dummy-module/
│       ├── assets/
│       │   ├── Zed/
│       │   │   └── package.json
│       │   └── package.json
│       └── src/
│           └── SprykerCommunity/
│               └── Zed/
│                   └── DummyModule/
├── src/
├── vendor/
└── composer.json
```

2. Update Main Project composer.json

Add the path repository configuration to your main project's composer.json:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "local-packages/product-subscription",
            "options": {
                "symlink": true
            }
        }
    ],
}
```

3. Install the Module

Run the composer require command from your demo shop root directory:

```bash
composer require spryker-community/product-subscription:@dev
```

### Make your project aware of Spryker Community

#### Sprykers Autoloading (PHP-side)

1. Configure Spryker Core Namespaces

Add the SprykerCommunity namespace to your Spryker configuration:

File: `config/Shared/config_default.php`

```php
<?php

// Add SprykerCommunity to the core namespaces array
$config[KernelConstants::CORE_NAMESPACES] = [
    'SprykerCommunity',  // Add this line
    'SprykerShop',
    'SprykerEco',
    'Spryker',
    'SprykerSdk',
];
```

2. Clear Cache (Optional)

If needed, clear the Spryker cache:

```bash
vendor/bin/console cache:empty-all
```

#### Node Modules

1. Add the `spryker-community` workspace to the root `package.json` of your project:

```
"workspaces": [
   "vendor/spryker/*",
   "vendor/spryker-community/*",
   "vendor/spryker-community/*/assets/",
   "vendor/spryker/*/assets/Zed",
   "vendor/spryker-community/*/assets/Zed"
],
```

2. Install all JavaScript dependencies from the `/vendor/spryker-community` directory and compile them for use in your application:

Note: Execute inside your `docker/sdk cli`
```bash
npm install
```

With `ls -la node_modules` you should see that we installed the node modules `dummy-package-tsl` and `hello-world-npm`.


### Verification

After successful installation, you should be able to access the test module at:
http://backoffice.eu.spryker.local/dummy-module
# product-subscription
