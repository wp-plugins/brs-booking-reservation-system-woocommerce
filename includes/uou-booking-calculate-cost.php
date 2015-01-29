<?php



class Wc_Booking_Calculate_Cost{

	public function __construct(  ){

		add_action( 'woocommerce_before_single_product', array($this,'uou_booking_show_custom_meta'));
		add_action( 'wp_ajax_calculate_total_cost', array( $this, 'uou_booking_calculate_total_cost'));
		add_action( 'wp_ajax_nopriv_calculate_total_cost', array( $this , 'uou_booking_calculate_total_cost'));
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'uou_booking_add_custom_price'));

		add_filter( 'woocommerce_cart_item_name',array( $this,'uou_add_user_custom_option_from_session_into_cart') ,1,2);
		add_action( 'woocommerce_add_order_item_meta',array($this, 'uou_add_values_to_order_item_meta') ,1,3);
		add_action( 'woocommerce_email_order_meta',array($this, 'uou_add_values_to_order_item_meta') ,1,3);

		add_action( 'woocommerce_new_order', array($this, 'uou_new_order') );
		add_filter( 'woocommerce_cart_item_quantity', array($this, 'uou_cart_item_quantity'), 10, 2 );
		add_filter( 'woocommerce_product_single_add_to_cart_text', array($this, 'uou_custom_add_to_cart_button_text')  );
	
	}

	/*-------------------------------------------------------------------------
	  DISABLE BOOK NOW BUTTON IF THE PRODUCT IS ALREADY IN THE CART START
	------------------------------------------------------------------------- */

	public function uou_custom_add_to_cart_button_text(){

		global $woocommerce;

		foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			$_product = $values['data'];

			if( get_the_ID() == $_product->id ) {
				return __('Already Booked', 'uou-bookings');
			}
		}

		return __('Book Now', 'uou-bookings');
	}

	/*-------------------------------------------------------------------------
	  DISABLE BOOK NOW BUTTON IF THE PRODUCT IS ALREADY IN THE CART END
	------------------------------------------------------------------------- */


	/*-------------------------------------------------------------------------
	  ALWAYS RETURN QUANTITY 1 IN EVERY PRODUCT IN CART PAGE START
	------------------------------------------------------------------------- */

	public function uou_cart_item_quantity($product_quantity, $cart_item_key){

		return 1;
	}

	/*-------------------------------------------------------------------------
	  ALWAYS RETURN QUANTITY 1 IN EVERY PRODUCT IN CART PAGE END
	------------------------------------------------------------------------- */


	/*-------------------------------------------------------------------------
	  START CODE TO DISABLE BOOKED DATES
	------------------------------------------------------------------------- */

	public function uou_new_order() {

		global $post;

		if(isset($_SESSION['date_range'])){
			$date_range = $_SESSION['date_range'];
		}

      	$check_in_date = '';
      	$check_out_date = '';
      	$single_product_id = '';

      	if(is_array($date_range)){

      		foreach ($date_range as $keys => $values) {

	      		$single_product_id = $keys;
	      		foreach ($values as $key => $value) {

	      			if($key == 'check_in_date'){
	      				$check_in_date = $value;
	      			}
	      			if($key == 'check_out_date'){
	      				$check_out_date = $value;
	      			}
	      		}

		      	$new_entry = $this->calculate_date($check_in_date,$check_out_date);

			    $new_entry_normal = array(
		    		'type' => 'custom',
		    		'bookable' => 'no',
		    		'from' => $check_in_date,
		    		'to' => $check_out_date
		    	);

			    $getting_availibility = get_post_meta( $single_product_id, 'bookable_availibility',true);

			    array_push($getting_availibility,$new_entry_normal);

			    update_post_meta( $single_product_id, 'bookable_availibility', $getting_availibility );

			    $bookable_availibility_uou = get_post_meta($single_product_id, 'own_availibility_date_ranges', true );

			    array_push($bookable_availibility_uou, $new_entry);

			    update_post_meta( $single_product_id, 'own_availibility_date_ranges', $bookable_availibility_uou );
	      	}
      	}
	}

	/*-------------------------------------------------------------------------
	  END CODE TO DISABLE BOOKED DATES
	------------------------------------------------------------------------- */


	/*-------------------------------------------------------------------------
	  START ADD CUSTOM META DATA IN CHECK OUT PAGE
	------------------------------------------------------------------------- */

    public function uou_add_values_to_order_item_meta($item_id, $values , $cart_item_key) {

        global $woocommerce, $wpdb;

        if(isset($_SESSION['uou_resource'])){
        	$uou_booking_resource = $_SESSION['uou_resource'];
        }

        if(isset($uou_booking_resource) && is_array($uou_booking_resource)){

        	foreach ($uou_booking_resource as $booking_keys => $booking_values) {

	      		if($values['product_id'] == $booking_keys){
	      			$resource_name = array();
	      			foreach ($booking_values as $meta_key => $meta_value) {

		      			foreach ($meta_value as $key => $value) {
		      				array_push($resource_name, $value);
		      			}
		      		}

		      	    wc_add_order_item_meta($item_id,'resource_name', json_encode($resource_name));
		      	    unset($_SESSION['uou_resource'][$booking_keys]);
	      		}
	      	}

        }

      	if(isset($_SESSION['uou_person'])){
      		$uou_booking_person = $_SESSION['uou_person'];
      	}

      	if(is_array($uou_booking_person)){

      		foreach ($uou_booking_person as $booking_keys => $booking_values) {

	      		if($values['product_id'] == $booking_keys){

	      			$person_count = array();
	      			foreach ($booking_values as $meta_key => $meta_value) {

		      			foreach ($meta_value as $key => $value) {
		      				array_push($person_count, $value);
		      			}
		      		}

		      		wc_add_order_item_meta($item_id,'person_count', json_encode($person_count));
		      		$_SESSION['uou_person'][booking_keys];
	      		}
	      	}
      	}

      	if(isset($_SESSION['date_range'])){
      		$date_range = $_SESSION['date_range'];
      	}

      	if(isset($date_range) && is_array($date_range)){

      		foreach ($date_range as $keys => $valuese) {
	      		if($values['product_id'] == $keys){

	      			$check_in_date = '';
	      			$check_out_date = '';
	      			foreach ($valuese as $key => $value) {
		      			if($key == 'check_in_date'){
		      				$check_in_date = $value;
		      			}
		      			if($key == 'check_out_date'){
		      				$check_out_date = $value;
		      			}
		      		}

		      		wc_add_order_item_meta($item_id,'check_in_date', $check_in_date);
	        		wc_add_order_item_meta($item_id,'check_out_date', $check_out_date);
	      		}
	      	}
      	}

      	if(isset($_SESSION['day_count'])){
      		$day_count = $_SESSION['day_count'];
      	}

      	if(isset($day_count) && is_array($day_count)){

      		foreach ($day_count as $key => $value) {

      			if($values['product_id'] == $key){
      				wc_add_order_item_meta($item_id,'total_days ', $value);
      			}
      		}
      	}
    }

    /*-------------------------------------------------------------------------
	  END ADD CUSTOM META DATA IN CHECK OUT PAGE
	------------------------------------------------------------------------- */


    /*-------------------------------------------------------------------------
      START ADD CUSTOM DATA ON CART AND CHECKOUT PAGE
    ------------------------------------------------------------------------- */

 	public function uou_add_user_custom_option_from_session_into_cart($product_name, $cart_item_key ) {

        /*code to add custom data on Cart & checkout Page*/
        ?>
      	<?php echo $product_name; ?>

      	<dl class='variation'>
           <table class='uou-options-table'>
			<tr><td>
		<?php

		if(isset($_SESSION['uou_resource'])){
			$uou_booking_resource = $_SESSION['uou_resource'];
		}

      	if( isset($uou_booking_resource) && is_array($uou_booking_resource)){
      		foreach ($uou_booking_resource as $booking_keys => $booking_values) {

	      		if($cart_item_key['product_id'] == $booking_keys){
	      			?>
					<h5 class="uou-cart-title"><?php _e('Resource: ', 'uou-bookings') ?><small>
	      			<?php

	      			foreach ($booking_values as $meta_key => $meta_value) {

		      			foreach ($meta_value as $key => $value) {
		      				echo $value . ' , ';
		      			}
		      		}
	      		}
	      	}

      	}
      	?>
		</small></h5>
		<h5 class="uou-cart-title"><?php _e('Person: ', 'uou-bookings') ?><small>

      	<?php
	      	if(isset($_SESSION['uou_person'])){
	      		$uou_booking_person = $_SESSION['uou_person'];
	      	}


	     	if(isset($uou_booking_person) && is_array($uou_booking_person)){

	     		foreach ($uou_booking_person as $booking_keys => $booking_values) {

	     			if($cart_item_key['product_id'] == $booking_keys){

	     				foreach ($booking_values as $meta_key => $meta_value) {

			      			foreach ($meta_value as $key => $value) {
			      				echo $value;
			      			}
			      		}
	     			}
		      	}
	     	}
      	?>

      	</small></h5>

      	<?php

      	if(isset($_SESSION['date_range'])){
      		$date_range = $_SESSION['date_range'];
      	}

      	$check_in_date = '';
      	$check_out_date = '';

      	if(isset($date_range) && is_array($date_range)){

      		foreach ($date_range as $keys => $values) {

      			if($cart_item_key['product_id'] == $keys){

      				foreach ($values as $key => $value) {

		      			if($key == 'check_in_date'){
		      				$check_in_date = $value;
		      			}

		      			if($key == 'check_out_date'){
		      				$check_out_date = $value;
		      			}
		      		}

		      		?>
		      		<h5 class="uou-cart-title"><?php _e('check in : ', 'uou-bookings'); ?><small><?php echo $check_in_date; ?></small></h5>
					<h5 class="uou-cart-title"><?php _e('check out : ', 'uou-bookings'); ?><small><?php echo $check_out_date; ?></small></h5>
					<?php
      			}
	      	}
      	}
		?>
		<h5 class="uou-cart-title"><?php _e('total_days: ', 'uou-bookings') ?><small>

			<?php
				if(isset($_SESSION['day_count'])){
					$day_count = $_SESSION['day_count'];
				}

				if(isset($day_count) && is_array($day_count)){

					foreach ($day_count as $key => $value) {

						if($cart_item_key['product_id'] == $key){
							echo $value;
						}
					}
				}
		 	?>
		</small></h5>

        </td></tr>
           </table>
        </dl>

		<?php
    }

    /*-------------------------------------------------------------------------
      END ADD CUSTOM DATA ON CART AND CHECKOUT PAGE
    ------------------------------------------------------------------------- */


	/*-------------------------------------------------------------------------
	 START UOU BOOKING SHOW CUSTOM META
	------------------------------------------------------------------------- */

	public function uou_booking_show_custom_meta(){

		global $wpdb, $post, $product;

	    $type = 'product';
	    $a = 'bookings_resources_cost';

	    
	    update_post_meta( $post->ID,'_price', get_post_meta($post->ID,'uou_bookable_main_cost',true));

	    $price = get_post_meta( $post->ID, '_price');
	    $product_data = new Wc_Product($post->ID);
	    $base_cost = $product_data->price;

	    /* start responsable for displaying person cost*/

	    $booking_meta = json_decode( get_post_meta( $post->ID, 'bookable_meta', true ) );

	    if(isset($booking_meta) && is_array($booking_meta)){

	    	foreach ($booking_meta as $meta) {
		    	foreach ($meta as $key => $value) {

		    		if($meta[$key]->name === "_uou_booking_cost"){


		    		}

		    	}
		    	echo '<input type="hidden" class="price" value="'.$price[0].'">';
			    echo '<input type="hidden" class="postId" value="'.$post->ID.'">';
		    }
	    }

	    echo '<input type="hidden" name="resource_total_price" id="resource_total_price" class="resource_total_price" value="0">';
	    echo '<input type="hidden" name="single_product_id" id="single_product_id" class="single_product_id" value="'.$post->ID.'">';

	    echo '<input type="hidden" class="base_cost" value="'.$base_cost.'">';

	    /*end responsable for displaying person cost*/

	    /*start responsable for displaying resource cost*/

	    $booking_resource_meta = json_decode(get_post_meta( $post->ID,'bookable_availibility_resource',true));

	    $resource_name = '';
	    $resource_value = '';

	    if(is_array($booking_resource_meta)){

	    	foreach ($booking_resource_meta as $meta) {

		    	foreach ($meta as $key => $value) {
		    		if($meta[$key]->name == 'uou_booking_resouce_availibility'){
		    			$resource_name = $meta[$key]->value;
		    		}
		    		if($meta[$key]->name == '_uou_booking_resource_cost'){
		    			$resource_value = $meta[$key]->value;
		    		}

		    	}
		    }

	    }

	    $blocked_dates = get_post_meta( $post->ID, 'bookable_availibility');
	}

	/*-------------------------------------------------------------------------
	 END UOU BOOKING SHOW CUSTOM META
	------------------------------------------------------------------------- */


	/*-------------------------------------------------------------------------
	 START UOU BOOKING CALCULATE TOTAL COST
	------------------------------------------------------------------------- */

	public function uou_booking_calculate_total_cost(){

	    global $woocommerce , $product , $post;

	    if(isset($_POST)){

	    	$resource_cost = $_POST['resource_cost'];
		    $single_product_id = $_POST['single_product_id'];

		    if(isset($_POST['booking_resources'])){
		    	$booking_resources = $_POST['booking_resources'];
		    }

		    $booking_person = $_POST['booking_person'];

		    $check_in_date = $_POST['check_in_date'];
		    $check_out_date = $_POST['check_out_date'];

		    $no_of_days = $_POST['no_of_days'];

	    }

	    if(empty($resource_cost)){
	    	$resource_cost = 0;
	    }

	    $product_info = new Wc_Product($single_product_id);
	    $base_cost = $product_info->price;

	    $total_cost = ($resource_cost + $base_cost)*$no_of_days;

	    $_SESSION['price'][$single_product_id] = $total_cost;

	    $_SESSION['day_count'][$single_product_id] = $no_of_days;


	    if(isset($_SESSION['uou_resource'])){
	    	$unset_resource = $_SESSION['uou_resource'];
	    }

	    if(isset($unset_resource) && is_array($unset_resource)){

	    	foreach ($unset_resource as $keys => $values) {

		    	if($keys == $single_product_id){
		    		unset($_SESSION['uou_resource'][$single_product_id]);
		    	}
		    }

	    }

	    if(isset($booking_resources) && is_array($booking_resources)){

	    	foreach ($booking_resources as $key => $value) {
		    	$_SESSION['uou_resource'][$single_product_id]['resource'][$key] = $value;
		    }

	    }

	    if(isset($_SESSION['uou_person'])){
	    	$unset_person = $_SESSION['uou_person'];
	    }
	    

	    if( isset($uou_person) && is_array($unset_person)){

	    	foreach ($unset_person as $keys => $values) {

		    	if($keys == $single_product_id){
		    		unset($_SESSION['uou_person'][$single_product_id]);
		    	}
		    }

	    }

	    if(isset($booking_person) && is_array($booking_person)){

	    	foreach ($booking_person as $key => $value) {
		    	$_SESSION['uou_person'][$single_product_id]['person'][$key] = $value;
		    }

	    }

	    $_SESSION['date_range'][$single_product_id]['check_in_date'] = $check_in_date;
	    $_SESSION['date_range'][$single_product_id]['check_out_date'] = $check_out_date;

	    $check_cart =  $woocommerce->cart->add_to_cart($single_product_id);


	    if($check_cart){

			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
				wc_add_to_cart_message( $single_product_id );
			}

			//$_SESSION['kamhoise'] = 'great';
			

	    }

	    wc_add_to_cart_message( $single_product_id );

	    echo json_encode($_SESSION);


	    wp_die();
	}

	/*-------------------------------------------------------------------------
	 END UOU BOOKING CALCULATE TOTAL COST
	------------------------------------------------------------------------- */


	/*-------------------------------------------------------------------------
	 START UOU BOOKING ADD CUSTOM PRICE
	------------------------------------------------------------------------- */

	public function uou_booking_add_custom_price( $cart_object ) {

	    foreach ( $cart_object->cart_contents as $key => $value ) {

	    	if(isset($_SESSION['price'])){

	    		foreach ($_SESSION['price'] as $keyy => $valuee) {

		            if ( $value['product_id'] == $keyy ){
		                $value['data']->price = $_SESSION['price'][$keyy];

		            }
		        }
	    	}
	    }
	}

	/*-------------------------------------------------------------------------
	 END UOU BOOKING ADD CUSTOM PRICE
	------------------------------------------------------------------------- */

	/*-------------------------------------------------------------------------
	  START CALCULATE DATE
	------------------------------------------------------------------------- */

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

		/*-------------------------------------------------------------------------
		  END CALCULATE DATE
		------------------------------------------------------------------------- */
}

new Wc_Booking_Calculate_Cost();