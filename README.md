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

## Explanation of the Process

The subscription module in Spryker expects **two key attributes** for each product:

### `subscribable` (Boolean)
Indicates whether the product is eligible for a subscription.  
- **true** → The product can be subscribed to.  
- **false** → The product cannot be subscribed to.  

### `frequency` (Integer, in seconds)
Defines how often the subscription generates a new order.  
- Example values:  
  - `86400` → Daily order creation (60 × 60 × 24 seconds - 1 day)  
  - `604800` → Weekly order creation (60 × 60 × 24 × 7 seconds - 7 days)  
  - `2592000` → Monthly order creation (approx. 30 days in seconds)  

With these attributes, the module synchronizes data against the subscription table.

---

## Subscription Table Structure

### `id_product_subscription`
Unique identifier for the product subscription entry.

### `fk_customer`
Foreign key linking the subscription to a specific customer.

### `fk_sales_order_item`
Foreign key linking the subscription to the original sales order item.  
This ensures that the subscription is tied to a specific purchase (e.g., the product variant, price, and quantity from the original order).

### `timestampable`
Metadata fields that automatically track creation and update times.  
- Typically includes `created_at` and `updated_at`.  
- Useful for auditing when the subscription was started or last modified.

### `archivable`
Indicates whether the subscription can be archived (soft-deleted) instead of permanently removed.  
- Allows the system to preserve historical data while preventing further processing.  

---

## Example Table Entry

| id_product_subscription | fk_customer | fk_sales_order_item | frequency (sec) | created_at          | updated_at          | archived |
|--------------------------|-------------|---------------------|-----------------|---------------------|---------------------|----------|
| 1                        | 6789        | 555                 | 2592000         | 2025-09-24 10:00:00 | 2025-09-24 10:00:00 | false    |

---

## Workflow Summary

1. **Product definition**  
   - A product is marked as `subscribable = true` and a `frequency` (in seconds) is defined.  

2. **Customer subscription**  
   - When purchased, the subscription entry is created in the `product_subscription` table.  

3. **Recurring execution**  
   - Based on the `frequency` value, new orders are generated for the subscribed product.  

4. **Lifecycle management**  
   - Timestamps (`timestampable`) track changes.  
   - The subscription can be archived (`archivable`) without losing historical data.  

---

## Workflow Visualization

```mermaid
flowchart TD
    A[Product setup with attributes subscribable + frequency (in seconds)] --> B[Customer purchase]
    B --> C[Create subscription entry in product_subscription table]
    C --> D[Recurring order generation based on frequency in seconds]
    D --> E[Update timestamps created_at / updated_at]
    E --> F{Subscription active?}
    F -->|Yes| D
    F -->|No| G[Archive subscription (archivable=true)]
    G --> H[Preserve historical data]
