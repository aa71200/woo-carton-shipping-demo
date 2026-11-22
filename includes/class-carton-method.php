<?php
/**
 * WooCommerce shipping-zone adapter for the carton calculation.
 *
 * @package CodexaWP\CartonShippingDemo
 */

declare(strict_types=1);

namespace CodexaWP\CartonShippingDemo;

defined( 'ABSPATH' ) || exit;

final class Carton_Method extends \WC_Shipping_Method {
	public const ID = 'codexawp_carton_demo';

	public function __construct( $instance_id = 0 ) {
		$this->id                 = self::ID;
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Carton Shipping Demo', 'woo-carton-shipping-demo' );
		$this->method_description = __( 'Charge a fixed amount per carton for this shipping zone.', 'woo-carton-shipping-demo' );
		$this->supports           = [ 'shipping-zones', 'instance-settings', 'instance-settings-modal' ];
		$this->instance_form_fields = [
			'title'    => [
				'title'   => __( 'Checkout label', 'woo-carton-shipping-demo' ),
				'type'    => 'text',
				'default' => __( 'Carton delivery', 'woo-carton-shipping-demo' ),
			],
			'capacity' => [
				'title'             => __( 'Units per carton', 'woo-carton-shipping-demo' ),
				'type'              => 'number',
				'default'           => '12',
				'custom_attributes' => [ 'min' => '1', 'step' => '1' ],
			],
			'price'    => [
				'title'       => __( 'Price per carton', 'woo-carton-shipping-demo' ),
				'type'        => 'price',
				'description' => __( 'Enter a positive price in the store currency.', 'woo-carton-shipping-demo' ),
				'desc_tip'    => true,
				'default'     => '',
			],
		];
		$this->title = $this->get_option( 'title', __( 'Carton delivery', 'woo-carton-shipping-demo' ) );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	public function calculate_shipping( $package = [] ): void {
		$quantities = 0;
		foreach ( $package['contents'] ?? [] as $item ) {
			$product = $item['data'] ?? null;
			if ( ! $product instanceof \WC_Product || ! $product->needs_shipping() ) {
				continue;
			}

			$quantity = filter_var( $item['quantity'] ?? null, FILTER_VALIDATE_INT, [ 'options' => [ 'min_range' => 0 ] ] );
			if ( false === $quantity || $quantity > PHP_INT_MAX - $quantities ) {
				return;
			}
			$quantities += $quantity;
		}

		$capacity = filter_var( $this->get_option( 'capacity' ), FILTER_VALIDATE_INT );
		$price    = $this->get_option( 'price' );
		if ( false === $capacity || ! is_numeric( $price ) ) {
			return;
		}

		$decimals = wc_get_price_decimals();
		$result   = quote( $quantities, $capacity, round( (float) $price, $decimals ) );
		if ( null === $result ) {
			return;
		}

		/* translators: %s: Number of cartons needed for this package. */
		$cartons = _n( '%s carton', '%s cartons', $result['cartons'], 'woo-carton-shipping-demo' );
		$this->add_rate( [
			'label'   => $this->get_title() . ' (' . sprintf( $cartons, number_format_i18n( $result['cartons'] ) ) . ')',
			'cost'    => wc_format_decimal( $result['amount'], $decimals ),
			'package' => $package,
		] );
	}
}
