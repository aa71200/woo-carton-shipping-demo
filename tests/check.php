<?php
declare(strict_types=1);

if ( PHP_SAPI !== 'cli' || ! getenv( 'CODEXAWP_TEST_WP_LOAD' ) ) {
	throw new RuntimeException( 'Set CODEXAWP_TEST_WP_LOAD to a disposable WordPress installation.' );
}
require getenv( 'CODEXAWP_TEST_WP_LOAD' );
if ( 'local' !== wp_get_environment_type() || ! function_exists( 'WC' ) ) {
	throw new RuntimeException( 'Use a local WooCommerce fixture.' );
}
use CodexaWP\CartonShippingDemo\Carton_Method;

WC()->shipping()->load_shipping_methods();
require __DIR__ . '/quote-test.php';
$physical = new WC_Product_Simple();
$virtual = new WC_Product_Simple();
$virtual->set_virtual( true );
update_option( 'woocommerce_codexawp_carton_demo_10001_settings', [ 'capacity' => '12', 'price' => '5.25' ] );
update_option( 'woocommerce_codexawp_carton_demo_10002_settings', [ 'capacity' => '6', 'price' => '8.50' ] );
$rate = static function ( int $instance, array $contents ): ?float {
	$method = new Carton_Method( $instance );
	$rates = $method->get_rates_for_package( [ 'contents' => $contents ] );
	return $rates ? (float) reset( $rates )->get_cost() : null;
};
$items = static fn( WC_Product $product, int $quantity ): array => [ 'data' => $product, 'quantity' => $quantity ];
foreach ( [
	[ 10001, [ $items( $physical, 12 ), $items( $virtual, 50 ) ], 5.25, 'Virtual products do not consume capacity' ],
	[ 10001, [ $items( $physical, 13 ) ], 10.50, 'Overflow adds a carton at the configured decimal rate' ],
	[ 10001, [ $items( $physical, 8 ) ], 5.25, 'The first package is priced independently' ],
	[ 10001, [ $items( $physical, 5 ) ], 5.25, 'The second package is priced independently' ],
	[ 10002, [ $items( $physical, 13 ) ], 25.50, 'The other zone uses its own settings' ],
	[ 10001, [ $items( $virtual, 5 ) ], null, 'A virtual-only package exposes no rate' ],
] as [ $instance, $contents, $expected, $label ] ) {
	if ( $rate( $instance, $contents ) !== $expected ) {
		throw new RuntimeException( $label );
	}
	echo "PASS: $label\n";
}
