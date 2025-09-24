# Subscribing Products Module

## Explanation of the Process

The subscription module in Spryker expects **two key attributes** for each product:

### `subscribable` (Boolean)
Indicates whether the product is eligible for a subscription.  
- **true** → The product can be subscribed to.  
- **false** → The product cannot be subscribed to.  

### `frequency` (Integer, in seconds)
Defines how often the subscription generates a new order.  
- Example values:  
  - `86400` → Daily order creation (60 × 60 × 24 seconds)  
  - `604800` → Weekly order creation (60 × 60 × 24 × 7 seconds)  
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
