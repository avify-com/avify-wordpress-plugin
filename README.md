# Avify Wordpress Plugin

Contributors: juanescobar06, tubipapilla, jupagar77
Tags: avify, checkout, orders, payment gateway, woocommerce
Requires at least: 5.6
Tested up to: 6.7.2
Stable tag: 1.3.6
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connect your WooCommerce account to Avify and send all your orders to one centralized inventory.

## Description

Avify is an Order Management System that allows small businesses to orchestrate multiple sales channels in one central platform.

With our technology you can receive orders and payments coming from Wordpress and merge them with any other one coming from social
media interaction like Instagram, Facebook or WhatsApp, where you can collect orders using magic links. You can connect your delivery
system and your billing provider and operate one powerful workflow in automatic pilot.

With the first version of the plugin you will be able to sync orders and online payments in WooCommerce via Avify Integration.
We support major credit cards brands like Visa, Mastercard and American Express.

Contact your dedicated support channel to get your API Key and the ID of your store.

### Multiple currencies

Process payments and display prices in USD, CRC, MXN.

### Current version features

-  Customer data and card encryption.
-  Processing of payments in USD or CRC.
-  Sandbox testing.
-  Synchronize orders

Do you want to know more about Avify? Please visit our [website](https://avify.com/?utm_source=WP.PLUGIN.PAGE&utm_medium=LINK.WEB) and find out what we can do.

## Frequently Asked Questions

### How much is the processing fee of Avify Payments?

Avify's processing fee is 5.5% + $0.30 per successful transaction. You must have a subscription plan of $29/month to have access to this functionality.

Where is my money deposited and what is the frecuency? =

Every 15 days Avify Payments sends you a report of your credit card transactions and the money is deposited in the account that you provided when you signed up for the subscription plan.

### Where can I find my dedicated support channel?

When you sign up for a monthly subscription, our customer success department will provide you a personal support channel to attend any request you have.

## Installation

-  Make sure that you have at least PHP Version 7.0 and [WooCommerce](https://wordpress.org/plugins/woocommerce/) installed.
-  Upload the plugin zip file in Plugins > Add New > Upload Plugin > Choose the zip file and click "Install Now".
-  Enable the plugin under Woocommerce > Settings > Payments.
-  Press the "Manage" button and add your provided Store ID and Client Secret.
-  To connect the orders enable in the avify dashboard Integrations > Woocommerce

## Changelog

### 1.3.6

* Tested up to: 6.7.2

### 1.3.5

* Disable auth check on Avify REST

### 1.3.4

* Avify product categories REST endpoint

### 1.3.3

* Fix subtotal visual error on checkout page

### 1.3.2

* Add attachment src REST endpoint
* Fix tax total error when prices include tax setting is enabled

### 1.3.1

* Allow user to customize state,city,district labels
* Allow user to make zip code optional

### 1.3.0

-  Fix loader UX
-  Allow user to customize loader asset
-  Allow user to customize buttons color

### 1.2.9

-  Fix custom options critical error

### 1.2.8

-  Send full address on rates calculation

### 1.2.7

-  Fix pickup error on empty address

### 1.2.6

-  Fix loaders errors

### 1.2.5

-  Manage regions selectors

### 1.2.4

-  Avify checkout show multiple totals

### 1.2.3

-  Avify checkout hide woo summary on success.

### 1.2.2

-  Avify checkout allow hide city field fix.

### 1.2.1

-  Avify checkout allow hide city field.

### 1.2.0

-  Avify checkout fixes.

### 1.1.9

-  Avify checkout fix payments.

### 1.1.8

-  Avify checkout fix city error.

### 1.1.7

-  Avify checkout visual fixes.

-  ### 1.1.6

-  Avify checkout visual updates.

-  ### 1.1.5

-  Avify checkout bug fixing.

### 1.1.4

-  Avify checkout bug fixing.

### 1.1.3

-  WordPress 6.3.1.

### 1.1.2

-  Avify checkout.

### 1.1.1

-  Remove cache shipping.

### 1.1.0

-  Fix configurable products custom option pricing.

### 1.0.9

-  Add max width and height for image custom option.
-  Add missing spanish translations.

### 1.0.8

-  Add support to Avify custom options.

### 1.0.7

-  Fix locked bug.

### 1.0.6

-  Upgrade tested up to.

### 1.0.5

-  Fix package bug.

### 1.0.4

-  Improve Avify rates collect.

### 1.0.3

-  Remove avify url as default value.

### 1.0.2

-  Fixed bug when updating status of failed orders.

### 1.0.1

-  Fixed minor bug with localization.

### 1.0.0

-  Get shipping options from Avify
-  Sincronize orders from woocommerce to Avify
-  Process payments through our [Avify PHP Client Library](https://packagist.org/packages/avify/avify-php-client).
-  Custom card payment form.
-  Error handling with custom error messages.
-  Plugin localization: en_US (by default) and es_CR.
-  Gateway's custom settings: API mode, API version, store ID and client secret.
-  Sandbox testing capability.
