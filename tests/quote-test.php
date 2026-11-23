<?php
/**
 * Run the quote checks with: php tests/quote-test.php
 */

declare(strict_types=1);

if ( ! function_exists( 'CodexaWP\CartonShippingDemo\quote' ) ) {
	require_once dirname( __DIR__ ) . '/includes/quote.php';
}

use function CodexaWP\CartonShippingDemo\quote;

$cases = [
	'empty package'   => [ 0, 12, 5.00, null ],
	'one physical item' => [ 1, 12, 5.00, [ 'cartons' => 1, 'amount' => 5.00 ] ],
	'exact carton'    => [ 12, 12, 5.00, [ 'cartons' => 1, 'amount' => 5.00 ] ],
	'one extra unit'  => [ 13, 12, 5.00, [ 'cartons' => 2, 'amount' => 10.00 ] ],
	'decimal price'   => [ 24, 12, 4.25, [ 'cartons' => 2, 'amount' => 8.50 ] ],
	'zero capacity'   => [ 12, 0, 5.00, null ],
	'negative units'  => [ -1, 12, 5.00, null ],
	'negative price'  => [ 12, 12, -5.00, null ],
	'missing price'   => [ 12, 12, 0.00, null ],
	'infinite amount' => [ 2, 1, PHP_FLOAT_MAX, null ],
];

foreach ( $cases as $name => [ $quantity, $capacity, $price, $expected ] ) {
	$actual = quote( $quantity, $capacity, $price );
	if ( $actual !== $expected ) {
		throw new RuntimeException( 'Incorrect carton quote for ' . $name );
	}
}

echo count( $cases ) . " carton quote checks passed.\n";
