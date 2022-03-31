=== Avify ===
Contributors: juanescobar06, tubipapilla, 
Tags: avify, checkout, orders, payment gateway, woocommerce
Requires at least: 5.6
Tested up to: 5.9.2
Stable tag: 1.0.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Connect your WooCommerce account to Avify and send all your orders to one centralized inventory.

== Description ==

Avify is an Order Management System that allows small businesses to orchestrate multiple sales channels in one central platform.

With our technology you can receive orders and payments coming from Wordpress and merge them with any other one coming from social
media interaction like Instagram, Facebook or WhatsApp, where you can collect orders using magic links. You can connect your delivery
system and your billing provider and operate one powerful workflow in automatic pilot.

With the first version of the plugin you will be able to sync orders and online payments in WooCommerce via Avify Integration.
We support major credit cards brands like Visa, Mastercard and American Express.

Contact your dedicated support channel to get your API Key and the ID of your store.

= Multiple currencies =
Process payments and display prices in USD, CRC, MXN.

= Current version features =
* Customer data and card encryption.
* Processing of payments in USD or CRC.
* Sandbox testing.
* Sincronize orders

Do you want to know more about Avify? Please visit our [website](https://avify.com/) and find out what we can do.

== Frequently Asked Questions ==

= How much is the processing fee of Avify?=

Avify's processing fee is 5.5% + $0.30 per successful transaction. You must have a subscription plan of $29/month to have access to this functionality.

= Where is my money deposited and what is the frecuency? =

Every 15 days Avify sends you a report of your credit card transactions and the money is deposited in the account that you provided when you signed up for the subscription plan.

= Where can I find my dedicated support channel? =

When you sign up for a monthly subscription, our customer success department will provide you a personal support channel to attend any request you have.

== Installation ==

* Make sure that you have at least PHP Version 7.0 and [WooCommerce](https://wordpress.org/plugins/woocommerce/) installed.
* Upload the plugin zip file in Plugins > Add New > Upload Plugin > Choose the zip file and click "Install Now".
* Enable the plugin under Woocommerce > Settings > Payments.
* Press the "Manage" button and add your provided Store ID and Client Secret.
* To connect the orders enable in the avify dashboard Integrations > Woocommerce
== Changelog ==

= 1.0.0 =

* Get shipping options from Avify
* Sincronize orders from woocommerce to Avify
* Process payments through our [Avify PHP Client Library](https://packagist.org/packages/avify/avify-php-client).
* Custom card payment form.
* Error handling with custom error messages.
* Plugin localization: en_US (by default) and es_CR.
* Gateway's custom settings: API mode, API version, store ID and client secret.
* Sandbox testing capability.

