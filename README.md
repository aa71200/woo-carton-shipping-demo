# Carton Shipping Demo for WooCommerce

[![Standalone PHP checks](https://github.com/aa71200/woo-carton-shipping-demo/actions/workflows/checks.yml/badge.svg?branch=main)](https://github.com/aa71200/woo-carton-shipping-demo/actions/workflows/checks.yml)

With 12 units per carton, placing 13 units in the cart should charge for two cartons. This installable WooCommerce shipping method calculates the rate using the capacity and price configured for the shipping zone.

![WooCommerce block checkout displaying Carton delivery (2 cartons) for $10](docs/checkout-block.png)

## Try it locally

Download the [latest verified installation ZIP](https://github.com/aa71200/woo-carton-shipping-demo/releases/latest) and upload it under **Plugins → Add New → Upload Plugin**, or use the source setup below. If version 1.0.0 is already installed, choose **Replace current with uploaded** to install the refreshed build.

1. Copy the repository contents into a folder named **woo-carton-shipping-demo** under **wp-content/plugins** on a WordPress test site with WooCommerce.
2. Activate **Carton Shipping Demo for WooCommerce**.
3. In **WooCommerce → Settings → Shipping → Shipping zones**, add **Carton Shipping Demo** to a zone. Set **Units per carton** to **12** and **Price per carton** to **5.00**.
4. Add 12 then 13 units of a physical product with a shipping address in that zone. Checkout should show one carton at $5 and two cartons at $10, respectively, before any shipping tax.

## Implementation

The plugin registers a [native shipping method](https://developer.woocommerce.com/docs/features/shipping/shipping-method-api/) and uses WooCommerce's zone settings and rate API. The quote function counts full or partial cartons and is tested separately from WordPress. Each package uses its own physical-unit count, so virtual items do not consume carton capacity. WooCommerce calculates any applicable shipping tax.

## Checks

Run the calculation checks with PHP 8.2 or newer:

    php tests/quote-test.php

The shipping adapter was also checked against real WooCommerce product objects for full cartons, overflow units, mixed physical and virtual items, empty packages, virtual-only packages, and invalid settings. Both classic and block checkout displayed the expected 12-unit and 13-unit rates on WordPress 7.1.3, WooCommerce 11.1.2, and PHP 8.2.12.

The shared checkout fixture submits a 13-unit cart through Classic and Blocks and compares the displayed two-carton rate with the shipping total on the saved order. This demo counts identical capacity units per package. Weight- and dimension-based packing belong to broader shipping rules.

With the plugin active on a disposable WooCommerce site, set `CODEXAWP_TEST_WP_LOAD` and run `php tests/check.php` for the adapter cases.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
