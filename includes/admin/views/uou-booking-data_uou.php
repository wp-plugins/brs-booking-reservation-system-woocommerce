<div class="options_group show_if_uou" id="uou_repeatable_place">

    <div class="alert alert-success" role="alert" style="display: none;"><?php _e('Successfully done !', 'uou-bookings') ?></div>
    <div class="alert alert-danger save-product" role="alert" style="display: none;"><?php _e('Please publish or draft the post first !', 'uou-bookings') ?></div>
    <div class="alert alert-danger resource_override" role="alert" style="display: none;"><?php _e('Resouce names exists, Please choose another Resouce names !', 'uou-bookings') ?></div>

	<button type="button" class="button" id="save_uou_attribute"><?php _e('save', 'uou-bookings') ?></button>
	<button type="button" class="button button-primary" id="add_uou_attribute"><?php _e('Add', 'uou-bookings') ?></button>
	<br>
	<hr>

	<?php woocommerce_wp_text_input( array( 'id' => 'uou_booking_main_cost', 'label' => __( 'Booking Cost', 'uou-bookings' ), 'description' => __( 'The cost for the booking.', 'uou-bookings' ), 'value' => max( get_post_meta( $post_id, 'uou_bookable_main_cost', true ), '' ), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array(
			'min'   => '',
			'step' 	=> '1'
		) ) );
	?>

	<?php

	$booking_meta = json_decode( get_post_meta( $post_id, 'bookable_meta', true ) );

	?>

	<div id="sortable_holder">

		<?php

		if( is_array( $booking_meta ) ) {

			foreach ($booking_meta as $id => $data) {

			?>

				<div class="uou_repeatable">

					<div class="header_part">
						<button type="button" class="button" id="remove_uou_attribute"><?php _e('Remove', 'uou-bookings') ?></button>
						<button type="button" class="button" id="show_uou_attribute"><?php _e('show/hide', 'uou-bookings') ?></button>
					</div>

					<div class="main_part">

						<p class="form-field">
							<label><?php _e( 'Please Select type', 'uou-bookings' ); ?></label>
							<select name="_uou_booking_select_type" id="_uou_booking_select_type" class="" style="margin-right: 7px;">
								<option value="uou_person" <?php selected( esc_attr($data[0]->value), 'uou_person' ); ?>><?php _e( 'Person', 'uou-bookings' ); ?></option>
								<option value="uou_resource" <?php selected( esc_attr($data[0]->value), 'uou_resource' ); ?>><?php _e( 'Resouce', 'uou-bookings' ); ?></option>
							</select>

						</p>

						<div class="number_cost">
							<?php woocommerce_wp_text_input( array( 'id' => '_uou_booking_number_person', 'label' => __( 'Number of person', 'uou-bookings' ), 'description' => __( 'The number of person.', 'uou-bookings' ), 'value' => esc_attr($data[1]->value), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array(
									'min'   => '',
									'step' 	=> '1'
								) ) );
							?>

							<?php woocommerce_wp_text_input( array( 'id' => '_uou_booking_cost', 'label' => __( 'Cost', 'uou-bookings' ), 'description' => __( 'The amount of cost per person.', 'uou-bookings' ), 'value' => esc_attr($data[2]->value), 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array(
									'min'   => '',
									'step' 	=> '1'
								) ) );
							?>
						</div>

						<div class="min_max_duration">

							<?php woocommerce_wp_text_input( array( 'id' => '_uou_booking_resource_name', 'label' => __( 'Name of the Resource', 'uou-bookings' ), 'description' => __( 'Enter the Name of the Resource you want.', 'uou-bookings' ), 'value' => esc_attr($data[3]->value), 'desc_tip' => true, 'type' => 'text' ) ); ?>

						</div>
					</div>
				</div>

			<?php

			}

		}

		?>
	</div>

	<script type="text/javascript">
		jQuery('._tax_status_field').closest('.show_if_simple').addClass('show_if_uou');

		jQuery('select#product-type').change(function(){
			if (jQuery(this).val() == "uou_booking") {
				jQuery('.show_if_uou').show();
			}
			else{
				jQuery('.show_if_uou').hide();
			}
		});
	</script>
</div>


	<div class="uou_repeatable_hidden" style="display:none">
		<div class="uou_repeatable">

			<div class="header_part">
				<button type="button" class="button" id="remove_uou_attribute">Remove</button>
				<button type="button" class="button" id="show_uou_attribute">show/hide</button>
			</div>

			<div class="main_part">
				<p class="form-field">
					<label><?php _e( 'Please Select type', 'uou-bookings' ); ?></label>
					<select name="_uou_booking_select_type" id="_uou_booking_select_type" class="" style="margin-right: 7px;">
						<option value="uou_person"><?php _e( 'Person', 'uou-bookings' ); ?></option>
						<option value="uou_resource"><?php _e( 'Resouce', 'uou-bookings' ); ?></option>
					</select>
				</p>

				<div class="number_cost">
						<?php woocommerce_wp_text_input( array( 'id' => '_uou_booking_number_person', 'label' => __( 'Number of person', 'uou-bookings' ), 'description' => __( 'The number of person.', 'uou-bookings' ), 'value' => '', 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array(
								'min'   => '',
								'step' 	=> '1'
							) ) ); ?>

						<?php woocommerce_wp_text_input( array( 'id' => '_uou_booking_cost', 'label' => __( 'Cost', 'uou-bookings' ), 'description' => __( 'The amount of cost per person.', 'uou-bookings' ), 'value' => '', 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array(
							'min'   => '',
							'step' 	=> '1'
						) ) ); ?>
				</div>

				<div class="min_max_duration">

					<?php woocommerce_wp_text_input( array( 'id' => '_uou_booking_resource_name', 'label' => __( 'Name of the Resource', 'uou-bookings' ), 'description' => __( 'Enter the Name of the Resource you want.', 'uou-bookings' ), 'value' => '', 'desc_tip' => true, 'type' => 'text' ) ); ?>

				</div>


			</div>
		</div>
	</div>
