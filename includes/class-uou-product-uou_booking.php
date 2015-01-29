<?php

class WC_Product_Uou_Booking extends WC_Product {

	public function __construct( $product ) {
		$this->product_type = 'uou_booking';
		parent::__construct( $product );
	}
}




