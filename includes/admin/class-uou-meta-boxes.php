<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Add Meta Boxes of woocommerce
 */
class UOU_Add_Meta_Box{

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'product_type_options', array( $this, 'product_type_options_uou' ) );
		add_filter( 'product_type_selector' , array( $this, 'add_selector_uou' ) );
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab_uou' ), 6 );
		add_action( 'woocommerce_product_write_panels', array( $this, 'booking_panels' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
		add_action( 'woocommerce_process_product_meta', array( $this,'save_booking_data' ), 20 );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'booking_data_uou' ) );

		// For ajax
        add_action("wp_ajax_get_my_option", array( $this , 'amar_ajax') );
        add_action("wp_ajax_nopriv_get_my_option", array( $this , 'amar_ajax') );

        add_action("wp_ajax_get_availibility", array( $this , 'get_availibility') );
        add_action("wp_ajax_nopriv_get_availibility", array( $this , 'get_availibility') );

        add_action("wp_ajax_resource_tab_change", array( $this , 'resource_tab_change') );
        add_action("wp_ajax_nopriv_resource_tab_change", array( $this , 'resource_tab_change') );

	}

	/**
	 * Tweak product type options
	 * @param  array $options
	 * @return array
	 */
	public function product_type_options_uou( $uou_options ) {
		$uou_options['virtual']['wrapper_class'] .= ' show_if_uou';
		return $uou_options;
	}

	/**
	 * Add the bookable product type
	 */
	public function add_selector_uou($types) {
		$types[ 'uou_booking' ] = __( 'Bookable product', 'uou-bookings' );
		return $types;
	}

	/**
	 * Show the booking tab
	 */
	public function add_tab_uou() {
		include( 'views/uou-booking-tab.php' );
	}

	/**
	 * Show the booking data view
	 */
	public function booking_data_uou() {
		global $post;
		$post_id = $post->ID;
		include( 'views/uou-booking-data_uou.php' );
	}

	/**
	 * Save Booking data for the product
	 *
	 * @param  int $post_id
	 */
	public function save_booking_data( $post_id ) {

		global $wpdb;

		//Main Cost

		$main_cost = $_POST[ "uou_booking_main_cost" ];

		if( $main_cost ){
			update_post_meta( $post_id, 'uou_bookable_main_cost', $main_cost );
		}

		// Availability
		$availability = array();
		$row_size     = isset( $_POST[ "uou_booking_availability_type" ] ) ? sizeof( $_POST[ "uou_booking_availability_type" ] ) : 0;
		for ( $i = 0; $i < $row_size; $i ++ ) {
			$availability[ $i ]['type']     = wc_clean( $_POST[ "uou_booking_availability_type" ][ $i ] );
			$availability[ $i ]['bookable'] = wc_clean( $_POST[ "uou_booking_availability_bookable" ][ $i ] );

			switch ( $availability[ $i ]['type'] ) {
				case 'custom' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "uou_booking_availability_from_date" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "uou_booking_availability_to_date" ][ $i ] );
				break;
				case 'months' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "uou_booking_availability_from_month" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "uou_booking_availability_to_month" ][ $i ] );
				break;
				case 'weeks' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "uou_booking_availability_from_week" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "uou_booking_availability_to_week" ][ $i ] );
				break;
				case 'days' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "uou_booking_availability_from_day_of_week" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "uou_booking_availability_to_day_of_week" ][ $i ] );
				break;
				case 'time' :
				case 'time:1' :
				case 'time:2' :
				case 'time:3' :
				case 'time:4' :
				case 'time:5' :
				case 'time:6' :
				case 'time:7' :
					$availability[ $i ]['from'] = wc_clean( $_POST[ "uou_booking_availability_from_time" ][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST[ "uou_booking_availability_to_time" ][ $i ] );
				break;
			}
		}
		update_post_meta( $post_id, 'bookable_availibility', $availability );

		//Own availibility calculation
		function calculate_date($data_from, $data_to){
			//from date calculation
			$month_from = ($data_from[0]*10)+($data_from[1]*1);
			$date_from = ($data_from[3]*10)+($data_from[4]*1);
			$year_from = ($data_from[6]*1000)+($data_from[7]*100+$data_from[8]*10+$data_from[9]*1);

			//to date calculation
			$month_to = ($data_to[0]*10)+($data_to[1]*1);
			$date_to = ($data_to[3]*10)+($data_to[4]*1);
			$year_to = ($data_to[6]*1000)+($data_to[7]*100+$data_to[8]*10+$data_to[9]*1);

			$main_array = array();

			//same month
			if( $month_from == $month_to && $year_from == $year_to ){
				$cal = $date_to-$date_from;
				foreach (range($date_from, $date_to) as $number) {
				    $main_array[$year_from][$month_from][$number] = 1;
				}
			}

			//Different month
			if ( $month_from != $month_to && $year_from == $year_to ) {
				switch ($month_from) {
				    case 2:
				        $month_end = 28;
				        break;
				    case 4:
				        $month_end = 30;
				        break;
				    case 6:
				        $month_end = 30;
				        break;
				    case 9:
				        $month_end = 30;
				        break;
				    case 11:
				        $month_end = 30;
				        break;
				    default:
				       $month_end = 31;
				}
				foreach ( range( $date_from, $month_end ) as $number ) {
				    $main_array[$year_from][$month_from][$number] = 1;
				}
				foreach (range( 1, $date_to ) as $number ) {
				    $main_array[$year_from][$month_to][$number] = 1;
				}
			}

			//Different Year
			if( $year_from != $year_to ){
				$month_end = 31;
				foreach ( range( $date_from, $month_end ) as $number ) {
				    $main_array[$year_from][$month_from][$number] = 1;
				}
				foreach (range( 1, $date_to ) as $number ) {
				    $main_array[$year_from][$month_to][$number] = 1;
				}
			}

			return $main_array;
		}

		$values = get_post_meta( $post_id, 'bookable_availibility', true );
		$final_date_array = array();
		if ( ! empty( $values ) && is_array( $values ) ) {
			foreach ( $values as $availability ) {
				$test = array();

				if( isset($availability['from']) && array_key_exists("from", $availability ) ) {
					$from = str_split($availability['from']);
				}

				if ( isset($availability['to']) && array_key_exists("to", $availability) ) {
					$to = str_split($availability['to']);
				}

				if( isset($availability['bookable']) && array_key_exists("bookable", $availability ) ){
					if($availability['bookable'] == 'no'){
						$final_date = calculate_date($from, $to);
						array_push($final_date_array, $final_date);
					}
				}

			}
		}

		update_post_meta( $post_id, 'own_availibility_date_ranges', $final_date_array );

	}


	/**
	 * Show the booking panels views
	 */
	public function booking_panels() {
		global $post;

		$post_id = $post->ID;

		wp_enqueue_script( 'uou_bookings_writepanel_js' );
		wp_enqueue_script( 'uou_bookings_custom_js' );

		include( 'views/uou-own-booking-availability.php' );
	}

	/**
	 * Add admin styles
	 */
	public function styles_and_scripts() {
		global $post, $woocommerce, $wp_scripts;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'uou_bookings_admin_styles', UOU_BOOKINGS_URL . '/assets/css/admin.css', null, UOU_BOOKINGS );

		if ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) {
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', null, WC_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
		}

		wp_register_script( 'uou-jq-ui', '//code.jquery.com/ui/1.11.0/jquery-ui.js', array(), false, true );
        wp_enqueue_script( 'uou-jq-ui' );

		wp_register_script( 'uou_bookings_writepanel_js', UOU_BOOKINGS_URL . '/assets/js/writepanel.js', array( 'jquery', 'jquery-ui-datepicker' ), UOU_BOOKINGS, true );

		wp_register_script( 'uou_bookings_custom_js', UOU_BOOKINGS_URL . '/assets/js/script' . '.js', array( 'jquery', ), UOU_BOOKINGS, true );

	 		$title_db = array();

			$args = array(
				'post_type' 		=> 'package',
			);

			// the query
			$the_query = new WP_Query( $args );

			$test = $the_query->posts;

			$array_length = count($test);

			for ($i=0; $i < $array_length; $i++) {
				array_push($title_db, $test[$i]->post_title);
			}

 		wp_localize_script('uou_bookings_custom_js','booking', array( 'post_id'=> isset( $post->ID ) ? $post->ID : '' , 'package_title' => $title_db  ) );

		$params = array(
			'post'                   => isset( $post->ID ) ? $post->ID : '',
			'plugin_url'             => $woocommerce->plugin_url(),
			'ajax_url'               => admin_url( 'admin-ajax.php' ),
			'calendar_image'         => $woocommerce->plugin_url() . '/assets/images/calendar.png',
		);

		wp_localize_script( 'uou_bookings_writepanel_js', 'uou_bookings_writepanel_js_params', $params );
	}

	// For ajaxing
    public function amar_ajax() {

        $save_data = json_encode($_POST['meta']);

        update_post_meta($_POST['id'], 'bookable_meta', $save_data );

		$row_count = $_POST['meta'];

		$title_backend = array();

		$test = array();

		if(is_array($row_count)){
			// $i = 0;
			foreach ($row_count as $key => $data) {

				if($data['0']['value'] == 'uou_resource'){

					array_push($title_backend, $data['3']['value']);

				}
			}
		}

		$title_db = array();

		$args = array(
			'post_type' 		=> 'package',
		);

		// the query
		$the_query = new WP_Query( $args );

		$test = $the_query->posts;

		$array_length = count($test);

		for ($i=0; $i < $array_length; $i++) {
			array_push($title_db, $test[$i]->post_title);
		}

		$result = array_diff($title_backend, $title_db);

		if(is_array($result)){

			foreach ($result as $key => $post) {

				$my_post = array(
				  'post_title'    => $post,
				  'post_status'   => 'publish',
				  'post_type'     => 'package'
				);

				// Insert the post into the database
				wp_insert_post( $my_post );

			}
		}

       wp_die();
    }

    public function get_availibility() {

       $save_available = json_encode($_POST['meta']);

       update_post_meta($_POST['id'], 'bookable_availibility_resource', $save_available );

       wp_die();
    }

    public function resource_tab_change() {

    	$post_id = $_POST['post_id'];

		$availibility_meta = json_decode( get_post_meta( $post_id, 'bookable_availibility_resource', true ) );

		$args = array(
			'post_type' 	 => 'package',
			'posts_per_page' => -1
		);

		// the query
		$the_query = new WP_Query( $args );

		$data = array();
		$data['availibility'] = $availibility_meta;

		$data['select_item'] = $the_query->posts;

		echo json_encode($data);

        wp_die();
    }

}

new UOU_Add_Meta_Box();