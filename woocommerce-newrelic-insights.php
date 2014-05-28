<?php
/*
Plugin Name: NewRelic Insights for WooCommerce
Plugin URI: http://ideaspool.nl/newrelic-insights-for-woocommerce/
Description: Adds NewRelic Insights integration to WooCommerce by tracking key metrics
Version: 0.0.6
Author: Kristaps Horns
Author URI: http://ideaspool.nl

License:
 
	Copyright: © 2014 Kristaps Horns (k.horns@ideaspool.nl)
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check if WC is installed
if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )) ) ) return;
// Check if our class name is free
if ( (class_exists('WC_NewRelic_Insights')) ) return;

add_action ('plugins_loaded', 'newrelic_insights_init', 0);

function newrelic_insights_init() {

		// since WC 1.5.5
		if (!class_exists('WC_Integration')) return;

		class WC_NewRelic_Insights extends WC_Integration {

			public function __construct() {

				// Localization
				load_plugin_textdomain('newrelic_insights_metrics', false, dirname( plugin_basename( __FILE__) ) . '/lang');

				// Lifecycle hooks
				register_activation_hook( __FILE__, array( &$this, 'activate' ) );
				register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );


				// Setup plugin
				$this->id						= 'newrelic_insights';
				$this->method_title				= __( 'New Relic Insights for Woocommerce', 'newrelic_insights' );
				$this->method_description		= __( 'Adds NewRelic Insights integration to WooCommerce by tracking key metrics and allowing to access full potential of your sales oriented data.', 'newrelic_insights' );

				/*
				/ Add tracked events to proper hooks
				*/

				// Signed in
				add_action( 'wp_login', array( &$this, 'nri_signed_in' ));

				// Signed out
				add_action( 'wp_logout', array( &$this, 'nri_signed_out' ));

				// Signed up for new account (on my account page if enabled OR during checkout)
				add_action( 'user_register', array( &$this, 'nri_signed_up' ));

				// Started Checkout
				add_action( 'woocommerce_before_checkout_form', array( &$this, 'nri_started_checkout' ));

				// Viewed Cart (either with items or empty)
				add_action( 'woocommerce_before_cart_contents', array( &$this, 'nri_viewed_cart' ));
				add_action( 'woocommerce_cart_is_empty', array( &$this, 'nri_viewed_cart' ));

				// Viewed Product (Properties: Name)
				add_action( 'woocommerce_before_single_product', array( &$this, 'nri_viewed_product' ));

				// Added Product to Cart (Properties: Name, Quantity)
				add_action( 'woocommerce_add_to_cart', array( &$this, 'nri_added_to_cart' ), 10, 6 );

				// Completed Purchase
				add_action( 'woocommerce_thankyou', array( &$this, 'nri_completed_purchase'), 10, 1);

			}

			function nri_signed_in() { newrelic_add_custom_parameter("signed_in", get_current_user_id()); }

			function nri_added_to_cart() { newrelic_add_custom_parameter("added_to_cart", 0); }

			function nri_viewed_product() { newrelic_add_custom_parameter("viewed_product", 0); }

			function nri_signed_out() { newrelic_add_custom_parameter("signed_out", 0); }

			function nri_signed_up() { newrelic_add_custom_parameter("signed_up", get_current_user_id()); }

			function nri_started_checkout() { newrelic_add_custom_parameter("started_checkout", get_current_user_id()); }

			function nri_viewed_cart() { newrelic_add_custom_parameter("viewed_cart", get_current_user_id()); }

			function nri_completed_purchase($order_id) {
				$order = new WC_Order($order_id);

				if ($order->get_items()) {
					 foreach($order->get_items() as $item)
					 {
						$total_qty += $item['qty'];
					 }
				}
				newrelic_add_custom_parameter("completed_purchase", get_current_user_id());
				newrelic_add_custom_parameter("completed_purchase_order_id", $order_id);
				newrelic_add_custom_parameter("completed_purchase_revenue", $order->order_total);
				newrelic_add_custom_parameter("completed_purchase_total_quantity", $order->total_qty);
				newrelic_add_custom_parameter("completed_purchase_payment_method", $order->payment_method_title);
				newrelic_add_custom_parameter("completed_purchase_city", $order->shipping_city);
			}

			
			/**
			 * Lifecycle functions
			**/

			/**
		     * Fired when plugin is activated
		     *
		     * @param   bool    $network_wide   TRUE if WPMU 'super admin' uses Network Activate option
		     */
			function activate ( $network_wide) {
				//
			}

			/**
		     * Fired when plugin is deactivated
		     *
		     * @param   bool    $network_wide   TRUE if WPMU 'super admin' uses Network Activate option
		     */
			function deactivate ( $network_wide) {
				//
			}

		}
}

/**
 * Add the integration to WooCommerce
 **/

function add_newrelic_insights_integration( $integrations ) {
	$integrations[] = 'WC_NewRelic_Insights'; return $integrations;
}
add_filter('woocommerce_integrations', 'add_newrelic_insights_integration' );?>