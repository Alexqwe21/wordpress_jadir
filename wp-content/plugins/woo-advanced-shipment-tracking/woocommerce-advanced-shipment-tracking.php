<?php
/**
 * @wordpress-plugin
 * Plugin Name: Advanced Shipment Tracking for WooCommerce 
 * Plugin URI: https://www.zorem.com/products/woocommerce-advanced-shipment-tracking/ 
 * Description: Add shipment tracking information to your WooCommerce orders and provide customers with an easy way to track their orders. Shipment tracking Info will appear in customers accounts (in the order panel) and in WooCommerce order complete email. 
 * Version: 3.8.4
 * Author: zorem
 * Author URI: https://www.zorem.com 
 * License: GPL-2.0+
 * License URI: 
 * Text Domain: woo-advanced-shipment-tracking 
 * WC tested up to: 9.9.4
 * Requires Plugins: woocommerce
*/

class Zorem_Woocommerce_Advanced_Shipment_Tracking {

	/**
	 * WooCommerce Advanced Shipment Tracking version.
	 *
	 * @var string
	 */
	public $version = '3.8.4';
	public $plugin_file;
	public $plugin_path;
	public $table;
	public $actions;
	public $install;
	public $admin_notice;
	public $admin;
	public $settings;
	public $ast_integration;
	public $customizer;
	public $wc_logger;

	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {

		if ( ! $this->is_wc_active() ) {
			return;
		}

		$this->plugin_file = __FILE__;

		// Add your templates to this array.
		if (!defined('SHIPMENT_TRACKING_PATH')) {
			define( 'SHIPMENT_TRACKING_PATH', $this->get_plugin_path());
		}

		$user_permission = apply_filters( 'ast_free_plugin_manager_permission', 'manage_woocommerce' );
		if ( !defined('AST_FREE_PLUGIN_ACCESS') ) {			
			define( 'AST_FREE_PLUGIN_ACCESS', $user_permission );
		}

		register_activation_hook( __FILE__, array( $this,'on_activation' ) );

		global $wpdb;

		$this->table = $wpdb->prefix . 'woo_shippment_provider';

		if ( is_multisite() ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}

			if ( is_plugin_active_for_network( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' ) ) {
				$main_blog_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );
				$this->table = $main_blog_prefix . 'woo_shippment_provider';	
			} else {
				$this->table = $wpdb->prefix . 'woo_shippment_provider';
			}

		} else {
			$this->table = $wpdb->prefix . 'woo_shippment_provider';
		}

		if ( $this->is_ast_pro_active() ) {
			deactivate_plugins( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' );
		}

		if ( ! $this->is_ast_pro_active() || ! $this->ast_pro_version_check() ) {
			// Include required files.
			$this->includes();

			// Init REST API.
			$this->init_rest_api();

			//start adding hooks
			$this->init();

			//admin class init
			$this->admin->init();

			//admin class init
			$this->settings->init();

			//plugin install class init
			$this->install->init();

			//plugin admin_notice class init
			$this->admin_notice->init();

			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );

			add_action( 'admin_footer', array( $this, 'uninstall_notice') );
			add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'ast_plugin_action_links' ) );
		}
	}

	/**
	 * Callback on activation and allow to activate if pro deactivated
	 *
	 * @since  1.0.0
	*/
	public function on_activation() {

		// Require parent plugin
		if ( is_plugin_active( 'ast-pro/ast-pro.php' ) && is_plugin_active( 'advanced-shipment-tracking-pro/advanced-shipment-tracking-pro.php' ) && current_user_can( 'activate_plugins' ) ) {
			
			//admin notice for not allow activate plugin
			wp_redirect( admin_url() . 'plugins.php?ast-not-allow=true' );
			exit;
		}
	}

	/**
	 * Check if AST PRO is active
	*/
	private function is_ast_pro_active() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'ast-pro/ast-pro.php' ) || is_plugin_active( 'advanced-shipment-tracking-pro/advanced-shipment-tracking-pro.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}

		return $is_active;
	}

	/**
	 * Check if Advanced Shipment Tracking for WooCommerce is active
	*/
	private function ast_pro_version_check() {
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/ast-pro/ast-pro.php' );
		$ast_pro_version = $plugin_data['Version'];

		if ( $ast_pro_version < '1.1') {
			$is_version = false;
		} else {
			$is_version = true;
		}

		return $is_version;
	}

	/**
	 * Check if WooCommerce is active
	 *	
	 * @since 1.0.0
	 * @return bool
	*/
	private function is_wc_active() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}

		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}		
		return $is_active;
	}

	/**
	 * Display WC active notice
	*/
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p>
			<?php 
			/* translators: %s: search WooCommerce plugin link */
			printf( esc_html__( 'Please install and activate %1$sWooCommerce%2$s for Advanced Shipment Tracking for WooCommerce!', 'woo-advanced-shipment-tracking' ), '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) ) . '">', '</a>' ); 
			?>
			</p>
		</div>
		<?php
	}

	/*
	* init when class loaded
	*/
	public function init() {
		register_activation_hook( __FILE__, array( $this->install, 'woo_shippment_tracking_install' ) );
		add_action( 'add_meta_boxes', array( $this->actions, 'add_meta_box' ) );
		add_action( 'woocommerce_view_order', array( $this->actions, 'show_tracking_info_order' ) );

		add_action( 'woocommerce_my_account_my_orders_actions', array( $this->actions, 'add_column_my_account_orders_ast_track_column' ), 10, 2 );

		add_action( 'wp_ajax_wc_shipment_tracking_delete_item', array( $this->actions, 'meta_box_delete_tracking' ) );
		add_action( 'woocommerce_process_shop_order_meta', array( $this->actions, 'save_meta_box' ), 0, 2 );
		add_action( 'wp_ajax_wc_shipment_tracking_save_form', array( $this->actions, 'save_meta_box_ajax' ) );

		add_action( 'wp_ajax_reassign_order_status', array( $this, 'reassign_order_status' ) );

		$preview = isset( $_REQUEST['wcast-tracking-preview'] ) && '1' === $_REQUEST['wcast-tracking-preview'] ? true : false ;
		if ( !$preview ) {
			$tracking_info_settings = get_option('tracking_info_settings');
			if ( isset( $tracking_info_settings['display_tracking_info_at'] ) && 'after_order' == $tracking_info_settings['display_tracking_info_at'] ) {
				add_action( 'woocommerce_email_order_meta', array( $this->actions, 'email_display' ), 0, 4 );
			} else {
				add_action( 'woocommerce_email_before_order_table', array( $this->actions, 'email_display' ), 0, 4 );
			}
		}

		// Custom tracking column in admin orders list.
		add_filter( 'manage_shop_order_posts_columns', array( $this->actions, 'shop_order_columns' ), 99 );
		add_filter( 'manage_woocommerce_page_wc-orders_columns', array( $this->actions, 'shop_order_columns' ), 5 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this->actions, 'render_shop_order_columns' ) );
		add_action( 'manage_woocommerce_page_wc-orders_custom_column', array( $this->actions, 'render_woocommerce_page_orders_columns' ), 10, 2 );

		add_action( 'admin_footer', array( $this->actions, 'custom_validation_js') );

		add_action( 'wp_ajax_add_inline_tracking_number', array( $this->actions, 'save_inline_tracking_number' ) );

		add_filter( 'get_ast_provider_name', array( $this->actions, 'get_ast_provider_name_callback' ), 10, 2 );
		add_filter( 'get_shipping_provdider_src', array( $this->actions, 'get_shipping_provdider_src_callback' ) );

		//load css js 
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'admin_styles' ), 4);

		//Custom Woocomerce menu
		add_action( 'admin_menu', array( $this->admin, 'register_woocommerce_menu' ), 99 );

		//ajax save admin api settings
		add_action( 'wp_ajax_wc_ast_settings_form_update', array( $this->admin, 'wc_ast_settings_form_update_callback' ) );

		//ajax save admin api settings
		add_action( 'wp_ajax_wc_usage_tracking_form_update', array( $this->admin, 'wc_usage_tracking_form_update_callback' ) );

		add_action( 'wp_ajax_wc_ast_custom_order_status_form_update', array( $this->admin, 'wc_ast_custom_order_status_form_update' ) );

		$wc_ast_status_partial_shipped = get_ast_settings( 'ast_general_settings', 'wc_ast_status_partial_shipped', '' );

		if ( $wc_ast_status_partial_shipped ) {
			add_action( 'woocommerce_order_status_partial-shipped', array( $this, 'email_trigger_partial_shipped' ), 10, 2 );
		}

		$wc_ast_status_updated_tracking = get_ast_settings( 'ast_general_settings', 'wc_ast_status_updated_tracking', '' );
		if ( $wc_ast_status_updated_tracking ) {
			add_action( 'woocommerce_order_status_updated-tracking', array( $this, 'email_trigger_updated_tracking' ), 10, 2 );
		}
		add_filter( 'tracking_item_args', array( $this->actions, 'add_user_in_tracking_item' ), 10, 3 );	
	}

	/**
	 * Send email when order status change to 'Partial Shipped'
	*/
	public function email_trigger_partial_shipped( $order_id, $order = false ) {
		require_once( 'includes/email-manager.php' );
		WC()->mailer()->emails['WC_Email_Customer_Partial_Shipped_Order']->trigger( $order_id, $order );
	}

	/**
	 * Send email when order status change to 'Updated Tracking'
	*/
	public function email_trigger_updated_tracking( $order_id, $order = false ) {
		require_once( 'includes/email-manager.php' );
		WC()->mailer()->emails['WC_Email_Customer_Updated_Tracking_Order']->trigger( $order_id, $order );
	}

	/**
	 * Init advanced shipment tracking REST API.
	*/
	private function init_rest_api() {
		add_action( 'rest_api_init', array( $this, 'rest_api_register_routes' ) );
	}

	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory.
	 *
	 * @return string plugin path
	 */
	public function get_plugin_path() {
		if ( isset( $this->plugin_path ) ) {
			return $this->plugin_path;
		}

		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

		return $this->plugin_path;
	}

	/*
	* include files
	*/
	private function includes() {

		require_once $this->get_plugin_path() . '/includes/class-wc-advanced-shipment-tracking.php';
		$this->actions = WC_Advanced_Shipment_Tracking_Actions::get_instance();

		require_once $this->get_plugin_path() . '/includes/class-wc-advanced-shipment-tracking-logger.php';
		$this->wc_logger = WC_AST_Logger::get_instance();

		require_once $this->get_plugin_path() . '/includes/class-wc-advanced-shipment-tracking-install.php';
		$this->install = WC_Advanced_Shipment_Tracking_Install::get_instance();

		require_once $this->get_plugin_path() . '/includes/class-wc-advanced-shipment-tracking-admin-notice.php';
		$this->admin_notice = WC_Advanced_Shipment_Tracking_Admin_notice::get_instance();

		require_once $this->get_plugin_path() . '/includes/class-wc-advanced-shipment-tracking-admin.php';
		$this->admin = WC_Advanced_Shipment_Tracking_Admin::get_instance();	

		require_once $this->get_plugin_path() . '/includes/class-wc-advanced-shipment-tracking-settings.php';
		$this->settings = WC_Advanced_Shipment_Tracking_Settings::get_instance();

		require_once plugin_dir_path( __FILE__ ) . '/includes/class-ast-integration-options.php';
		$this->ast_integration = AST_Integration::get_instance();

		require_once $this->get_plugin_path() . '/includes/email-manager.php';
	}

	/**
	 * Register shipment tracking routes.
	 *
	 * @since 1.5.0
	 */
	public function rest_api_register_routes() {

		require_once $this->get_plugin_path() . '/includes/api/class-wc-advanced-shipment-tracking-rest-api-controller.php';

		// Register route with default namespace wc/v3.
		$ast_api_controller = new WC_Advanced_Shipment_Tracking_REST_API_Controller();
		$ast_api_controller->register_routes();

		// These are all the same code but with different namespaces for compatibility reasons.
		$ast_api_controller_v1 = new WC_Advanced_Shipment_Tracking_REST_API_Controller();
		$ast_api_controller_v1->set_namespace( 'wc/v1' );
		$ast_api_controller_v1->register_routes();

		$ast_api_controller_v2 = new WC_Advanced_Shipment_Tracking_REST_API_Controller();
		$ast_api_controller_v2->set_namespace( 'wc/v2' );
		$ast_api_controller_v2->register_routes();

		$ast_api_controller_v3 = new WC_Advanced_Shipment_Tracking_REST_API_Controller();
		$ast_api_controller_v3->set_namespace( 'wc/v3' );
		$ast_api_controller_v3->register_routes();

		$shipment_api_controller_v3 = new WC_Advanced_Shipment_Tracking_REST_API_Controller();
		$shipment_api_controller_v3->set_namespace( 'wc-shipment-tracking/v3' );
		$shipment_api_controller_v3->register_routes();
	}

	/*
	* include file on plugin load
	*/
	public function on_plugins_loaded() {

		require_once $this->get_plugin_path() . '/includes/customizer/ast-customizer.php';
		$this->customizer = Ast_Customizer::get_instance();
		
		require_once $this->get_plugin_path() . '/includes/email-manager.php';

		require_once $this->get_plugin_path() . '/includes/tracking-info.php';

		add_action( 'after_setup_theme', array( $this, 'woo_advanced_shipment_tracking_load_textdomain' ) );
	}

	/**
	 * Localisation.
	 *
	 * @since 4.4.5
	 */
	public function woo_advanced_shipment_tracking_load_textdomain() {
		load_plugin_textdomain( 'woo-advanced-shipment-tracking', false, dirname( plugin_basename(__FILE__) ) . '/lang' );
	}

	/*
	* return plugin directory URL
	*/
	public function plugin_dir_url() {
		return plugin_dir_url( __FILE__ );
	}

	/*
	* Plugin uninstall code 
	*/	
	public function uninstall_notice() {
		$screen = get_current_screen();

		if ( 'plugins.php' == $screen->parent_file ) {
			wp_enqueue_style( 'ast_styles', wc_advanced_shipment_tracking()->plugin_dir_url() . 'assets/css/admin.css', array(), wc_advanced_shipment_tracking()->version );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		}

		$ps_count       = array_key_exists( 'wc-partial-shipped', wc_get_order_statuses() ) ? wc_orders_count( 'partial-shipped' ) : 0;
		$delivered_count = array_key_exists( 'wc-delivered', wc_get_order_statuses() ) ? wc_orders_count( 'delivered' ) : 0;

		$order_statuses = wc_get_order_statuses();

		unset( $order_statuses['wc-partial-shipped'] );

		if ( $ps_count > 0 || $delivered_count > 0 ) {
			?>

		<script>

		jQuery(document).on("click","[data-slug='woo-advanced-shipment-tracking'] .deactivate a",function(e){
			e.preventDefault();
			jQuery('.uninstall_popup').show();
			var theHREF = jQuery(this).attr("href");
			jQuery(document).on("click",".uninstall_plugin",function(e){
				jQuery("body").block({
					message: null,
					overlayCSS: {
						background: "#fff",
						opacity: .6
					}
				});
				var form = jQuery('#order_reassign_form');
				jQuery.ajax({
					url: ajaxurl,
					data: form.serialize(),
					type: 'POST',
					success: function(response) {
						jQuery("body").unblock();
						window.location.href = theHREF;
					},
					error: function(response) {
						console.log(response);
					}
				});
			});
		});

		jQuery(document).on("click",".popupclose",function(e){
			jQuery('.uninstall_popup').hide();
		});

		jQuery(document).on("click",".uninstall_close",function(e){
			jQuery('.uninstall_popup').hide();
		});

		jQuery(document).on("click",".popup_close_icon",function(e){
			jQuery('.uninstall_popup').hide();
		});
		</script>
		<div id="" class="popupwrapper uninstall_popup" style="display:none;">
			<div class="popuprow">
				<div class="popup_header">
					<h3 class="popup_title">Advanced Shipment Tracking for WooCommerce</h3>
					<span class="dashicons dashicons-no-alt popup_close_icon"></span>
				</div>
				<div class="popup_body">
					<form method="post" id="order_reassign_form">
						<?php 
						if ( $ps_count > 0 ) {
							?>
							<p>
							<?php 
								/* translators: %s: replace with Partially Shipped order count */
								printf( esc_html__('We detected %s orders that use the Partially Shipped order status, You can reassign these orders to a different status', 'woo-advanced-shipment-tracking'), esc_html( $ps_count ) );
							?>
							</p>

							<select id="reassign_ps_order" name="reassign_ps_order" class="reassign_select">
								<option value=""><?php esc_html_e('Select', 'woocommerce'); ?></option>
								<?php foreach ( $order_statuses as $key => $status ) { ?>
									<option value="<?php esc_html_e( $key ); ?>"><?php esc_html_e( $status ); ?></option>
								<?php } ?>
							</select>

						<?php
						}
						if ( $delivered_count > 0 ) { 
							?>
							<p>
							<?php 
								/* translators: %s: replace with Partially Shipped order count */
								printf( esc_html__('We detected %s orders that use the Delivered order status, You can reassign these orders to a different status', 'woo-advanced-shipment-tracking'), esc_html__( $delivered_count ) ); 
							?>
							</p>
							
							<select id="reassign_delivered_order" name="reassign_delivered_order" class="reassign_select">
								<option value=""><?php esc_html_e('Select', 'woocommerce'); ?></option>
								<?php foreach ( $order_statuses as $key => $status ) { ?>
									<option value="<?php esc_html_e( $key ); ?>"><?php esc_html_e( $status ); ?></option>
								<?php } ?>
							</select>
						
						<?php } ?>
						<p>	
							<?php wp_nonce_field( 'ast_reassign_order_status', 'ast_reassign_order_status_nonce' ); ?>
							<input type="hidden" name="action" value="reassign_order_status">
							<input type="button" value="<?php esc_html_e( 'Deactivate' ); ?>" class="uninstall_plugin button-primary btn_ast2">
							<input type="button" value="<?php esc_html_e( 'Close', 'woocommerce' ); ?>" class="uninstall_close button-primary btn_red">
						</p>
					</form>	
				</div>	
			</div>
			<div class="popupclose"></div>
		</div>
		<?php 
		}
	}
	
	/*
	* Functon for reassign order status on plugin deactivation
	*/
	public function reassign_order_status() {

		check_ajax_referer( 'ast_reassign_order_status', 'ast_reassign_order_status_nonce' );
		
		$reassign_ps_order = isset(	$_POST['reassign_ps_order']	) ? wc_clean( $_POST['reassign_ps_order'] ) : '';
		$reassign_delivered_order = isset(	$_POST['reassign_delivered_order']	) ? wc_clean( $_POST['reassign_delivered_order'] ) : '';
		
		if ( '' != $reassign_ps_order ) {
			
			$args = array(
				'status' => 'partial-shipped',
				'limit' => '-1',
			);
			
			$ps_orders = wc_get_orders( $args );
			
			foreach ( $ps_orders as $order ) {
				$order_id = $order->get_id();
				$order = new WC_Order( $order_id );
				$order->update_status( $reassign_ps_order );
			}
		}
		if ( '' != $reassign_delivered_order ) {
			
			$args = array(
				'status' => 'delivered',
				'limit' => '-1',
			);
			
			$delivered_orders = wc_get_orders( $args );

			foreach ( $delivered_orders as $order ) {
				$order_id = $order->get_id();
				$order = new WC_Order( $order_id );
				$order->update_status( $reassign_delivered_order );
			}
		}
		echo 1;
		die();
	}
	
	/**
	* Add plugin action links.
	*
	* Add a link to the settings page on the plugins.php page.
	*
	* @since 2.6.5
	*
	* @param  array  $links List of existing plugin action links.
	* @return array         List of modified plugin action links.
	*/
	public function ast_plugin_action_links ( $links ) {
		$links = array_merge( array(
			'<a href="https://wordpress.org/support/plugin/woo-advanced-shipment-tracking/reviews/#new-post" target="blank">' . __( 'Review' ) . '</a>'
		), $links );
		$links = array_merge( array(
			'<a href="https://wordpress.org/support/plugin/woo-advanced-shipment-tracking/#new-topic-0" target="blank">' . __( 'Support' ) . '</a>'
		), $links );
		$links = array_merge( array(
			'<a href="https://www.zorem.com/docs/woocommerce-advanced-shipment-tracking/" target="blank">' . __( 'Docs' ) . '</a>'
		), $links );
		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( '/admin.php?page=woocommerce-advanced-shipment-tracking' ) ) . '">' . __( 'Settings' ) . '</a>'
		), $links );
		return $links;
	}
}

/**
 * Returns an instance of Zorem_Woocommerce_Advanced_Shipment_Tracking.
 *
 * @since 1.6.5
 * @version 1.6.5
 *
 * @return Zorem_Woocommerce_Advanced_Shipment_Tracking
*/
function wc_advanced_shipment_tracking() {
	static $instance;

	if ( ! isset( $instance ) ) {
		$instance = new Zorem_Woocommerce_Advanced_Shipment_Tracking();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

if ( ! function_exists( 'zorem_ast_tracking' ) ) {
	function zorem_ast_tracking() {
		require_once dirname(__FILE__) . '/zorem-tracking/zorem-tracking.php';
		$plugin_name = 'Advanced Shipment Tracking for WooCommerce';
		$plugin_slug = 'ast';
		$user_id = '1';
		$setting_page_type = 'top-level';
		$setting_page_location =  'A custom top-level admin menu (admin.php)';
		$parent_menu_type = '';
		$menu_slug = 'woocommerce-advanced-shipment-tracking';
		$plugin_id = '1';
		$zorem_tracking = WC_Trackers::get_instance( $plugin_name, $plugin_slug, $user_id, $setting_page_type, $setting_page_location, $parent_menu_type, $menu_slug, $plugin_id );
		return $zorem_tracking;
	}
	zorem_ast_tracking();
}

if (!function_exists('get_ast_settings')) {
	function get_ast_settings( $name, $key, $default_value = '' ) {
		$data_array = get_option( $name, array() );
		// return $data_array[$key] ?? $default_value;
		return isset($data_array[$key]) ? $data_array[$key] : $default_value;
	}
}

if (!function_exists('update_ast_settings')) {
	function update_ast_settings( $name, $key, $value ) {
		$data_array = get_option( $name, array() );
		$data_array[ $key ] = $value;
		update_option( $name, $data_array );
	}
}

if (!function_exists('delete_ast_settings')) {
	function delete_ast_settings( $name, $key ) {
		$data_array = get_option( $name, array() );
		unset($data_array[$key]);
		update_option( $name, $data_array );
	}
}

wc_advanced_shipment_tracking();
