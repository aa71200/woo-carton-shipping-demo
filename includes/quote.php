<?php
/**
 * Carton arithmetic without a WordPress or WooCommerce dependency.
 *
 * @package CodexaWP\CartonShippingDemo
 */

declare(strict_types=1);

namespace CodexaWP\CartonShippingDemo;

/**
 * Calculate the carton count and unformatted shipping amount.
 *
 * @return array{cartons: int, amount: float}|null
 */
function quote( int $quantity, int $capacity, float $price_per_carton ): ?array {
	if ( $quantity < 1 || $capacity < 1 || ! is_finite( $price_per_carton ) || $price_per_carton <= 0 ) {
		return null;
	}

	$cartons = intdiv( $quantity - 1, $capacity ) + 1;
	$amount  = $cartons * $price_per_carton;

	if ( ! is_finite( $amount ) ) {
		return null;
	}

	return [
		'cartons' => $cartons,
		'amount'  => $amount,
	];
}
