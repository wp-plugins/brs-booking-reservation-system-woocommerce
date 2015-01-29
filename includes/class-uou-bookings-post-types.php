<?php


class Uou_Bookings_Post_Types {

    public function __construct(){

        include_once( UOU_BOOKINGS_DIR . '/includes/vendor/cuztom/cuztom.php' );

        $package  = new Cuztom_Post_Type( 'package', array(
		    'has_archive' 		=> true,
		    'menu_position'     => 57,
		    'menu_icon'         => 'dashicons-calendar',
		    'supports' 			=> array( 'title', 'thumbnail' )

		));
    }
}

new Uou_Bookings_Post_Types();