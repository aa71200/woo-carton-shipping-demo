<?php
/**
 * Plugin Name: Carton Shipping Demo for WooCommerce
 * Description: A shipping zone method that charges a fixed amount for each carton needed to ship a package.
 * Version: 1.0.0
 * Author: Amirhossein / CodexaWP
 * Requires at least: 7.0
 * Requires PHP: 8.2
 * Requires Plugins: woocommerce
 * License: GPL-2.0-or-later
 * Text Domain: woo-carton-shipping-demo
 */

declare(strict_types=1);

namespace CodexaWP\CartonShippingDemo;

defined( 'ABSPATH' ) || exit;

add_action( 'woocommerce_shipping_init', static function (): void {
	if ( class_exists( \WC_Shipping_Method::class ) ) {
		require_once __DIR__ . '/includes/quote.php';
		require_once __DIR__ . '/includes/class-carton-method.php';
	}
} );

add_filter( 'woocommerce_shipping_methods', static function ( array $methods ): array {
	if ( class_exists( Carton_Method::class ) ) {
		$methods[ Carton_Method::ID ] = Carton_Method::class;
	}
	return $methods;
} );
