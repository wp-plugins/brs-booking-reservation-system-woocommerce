<?php
/**
 * Plugin Name: BRS Bookings
 * Plugin URI:  http://uouapps.com/
 * Description: Booking plugin | Make any reservation sytem with the help of this Bookings plugin. This plugin extends from WooCommerce. This plugin require WooCommerce plugin. You can download the WooCommerce plugin from http://wordpress.org/plugins/woocommerce/
 * Author:      UOUAPPS
 * Author URI:  http://uouapps.com/
 * Version:     1.0.0
 * Text Domain: uou-bookings
 * Domain Path: /languages/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Put your plugin code here

    Class Uou_Bookings{

    	public function __construct(){

    		// Define constants
            add_action('init',array($this,'session_start_here') );
    		define( 'UOU_BOOKINGS', '0.1' );
    		define( 'UOU_BOOKINGS_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
    		define( 'UOU_BOOKINGS_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
    		define( 'UB_PLUGIN_FILE', __FILE__ );

            //template path
            define( 'UOU_PACKAGE_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

            include( 'includes/class-uou-bookings-post-types.php' );

            include( 'includes/admin/class-uou-meta-boxes.php' );
            include( 'includes/uou-booking-calculate-cost.php' );

            // Plugin functions
            include( 'includes/uou-bookings-functions.php' );


    		// Actions
    		add_action( 'plugins_loaded', array( $this, 'ub_load_bookings_textdomain' ) );

            add_action('admin_enqueue_scripts', array($this, 'ub_admin_load_scripts') );

    		add_action( 'wp_enqueue_scripts', array($this ,'ub_load_scripts') );

            //template actions
            add_action( 'woocommerce_loaded', array( $this, 'includes' ) );

            add_action('woocommerce_uou_booking_add_to_cart', array($this, 'add_to_cart'),30);
    	}



        public function session_start_here(){
            session_start();
        }


    	/**
         * Localisation
         * @return  void
         */
        public function ub_load_bookings_textdomain() {
            load_plugin_textdomain( 'uou-bookings', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }


        /**
         * Load Scripts
         * @return void
         */
        public function ub_load_scripts() {

            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery.ui.all.css');
            wp_enqueue_style('uou-fontawesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css');
            wp_enqueue_style('uou-custom-css', UOU_BOOKINGS_URL . '/assets/css/uou-custom.css');
            wp_enqueue_style('uou-chosen-css', UOU_BOOKINGS_URL . '/assets/css/chosen.css');

            wp_register_script( 'uou-chosen',UOU_BOOKINGS_URL.'/assets/js/chosen.jquery.js', array('jquery'), false, true );
            wp_enqueue_script( 'uou-chosen');

            /*wp_localize_script start*/

            global $post;

            $booking_resource_meta = get_post_meta( $post->ID,'own_availibility_date_ranges', true);

            $unavailable_dates = array();

            if(isset($booking_resource_meta) && is_array($booking_resource_meta)) {

                foreach ($booking_resource_meta as $key=>$years) {

                    foreach ($years as $year => $months) {

                        foreach ($months as $month => $days) {

                            foreach ($days as $day => $value) {

                                array_push($unavailable_dates,"$day-$month-$year");
                            }
                        }
                    }
                }
            }


            $unavailable_dates = $unavailable_dates;

            wp_register_script( 'some_handle',UOU_BOOKINGS_URL.'/assets/js/app.js', array('jquery', 'jquery-ui-datepicker', 'uou-chosen'), false, true );
            wp_localize_script( 'some_handle', 'unavailable_date', $unavailable_dates );
            wp_enqueue_script( 'some_handle' );



            wp_register_script( 'calculate-cost',UOU_BOOKINGS_URL.'/assets/js/uou-booking-calculate-cost.js', array('jquery', 'jquery-ui-datepicker'), false, true );
            wp_enqueue_script( 'calculate-cost');

            /*wp localize script end*/

            wp_localize_script( 'calculate-cost', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

            wp_localize_script( 'calculate-cost', 'wc_add_to_cart_params', array( 'cart_url' => get_permalink( wc_get_page_id( 'cart' ) ) , 'cart_redirect_after_add' => get_option( 'woocommerce_cart_redirect_after_add' ) ) );

        }


        public function ub_admin_load_scripts() {
            wp_register_style( 'bootstrap-admin-style', UOU_BOOKINGS_URL . '/assets/css/bootstrap-admin.css', array(), false, 'all' );
            wp_enqueue_style( 'bootstrap-admin-style' );

            wp_register_style( 'bootstrap-calendar-style', UOU_BOOKINGS_URL . '/assets/css/datepicker.css', array(), false, 'all' );
            wp_enqueue_style( 'bootstrap-calendar-style' );

            wp_enqueue_style( 'fullcalendar-style', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.0.2/fullcalendar.css' );

            wp_register_script( 'moment-script', UOU_BOOKINGS_URL . '/assets/js/moment.min.js', array('jquery'), false, true );
            wp_enqueue_script('moment-script');

            wp_register_script( 'bootstrap-admin-script', '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js', array('jquery'), false, true );
            wp_enqueue_script('bootstrap-admin-script');

            wp_register_script( 'fullcalendar-script', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.0.2/fullcalendar.min.js', array('jquery'), false, true );
            wp_enqueue_script('fullcalendar-script');

            wp_register_script( 'fullcalendar-print', '//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.0.2/fullcalendar.min.js', array('jquery'), false, true );
            wp_enqueue_script('fullcalendar-print');

            wp_register_script( 'cal-script', UOU_BOOKINGS_URL . '/assets/js/cal.script.js', array('jquery'), false, true );
            wp_enqueue_script('cal-script');
        }

        public function includes(){
            //templates
            include( 'includes/class-uou-product-uou_booking.php' );
        }

        public function add_to_cart() {
            wc_get_template( 'single-product/add-to-cart/uou_booking.php',$args = array(), $template_path = '', UOU_PACKAGE_TEMPLATE_PATH);
        }

    }

    $GLOBALS['Uou_bookings'] = new Uou_Bookings();

}else{
    function my_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'Please Install WooCommerce First before activating this Plugin. You can download WooCommerce from <a href="http://wordpress.org/plugins/woocommerce/">here</a>.', 'uou-bookings' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'my_admin_notice' );
}