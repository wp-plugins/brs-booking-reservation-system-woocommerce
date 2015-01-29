<div id="bookings_availability" class="panel woocommerce_options_panel">

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">

	  	<li class="active">
	  		<a href="#own_tab" role="tab" data-toggle="tab" rel="own"><?php _e('Own Availibility', 'uou-bookings') ?></a>
	  	</li>
	  	<li>
	  		<a href="#resource_tab" role="tab" data-toggle="tab" rel="resource"><?php _e('Manage Resource', 'uou-bookings') ?></a>
	  	</li>

	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
	  	<div class="tab-pane active" id="own_tab">
	  		<div class="options_group own_availibility">
				<div class="table_grid">
					<table class="widefat">
						<thead style="2px solid #eee;">
							<tr>
								<th class="sort" width="1%">&nbsp;</th>
								<th style="display:none"><?php _e( 'Range type', 'uou-bookings' ); ?></th>
								<th><?php _e( 'From', 'uou-bookings' ); ?></th>
								<th><?php _e( 'To', 'uou-bookings' ); ?></th>
								<th><?php _e( 'Bookable', 'uou-bookings' ); ?>&nbsp;<a class="tips" data-tip="<?php _e( 'Please select the date range for which you want the product to be disabled.', 'uou-bookings' ); ?>">[?]</a></th>
								<th class="remove" width="1%">&nbsp;</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="6">
									<a href="#" class="button button-primary add_row" data-row="<?php
										ob_start();
										include( 'uou-resource-booking-availability-fields.php' );
										$html = ob_get_clean();
										echo esc_attr( $html );
									?>"><?php _e( 'Add Range', 'uou-bookings' ); ?></a>
									<span class="description"><?php _e( 'Please select the date range to be disabled for the product.', 'uou-bookings' ); ?></span>
								</th>
							</tr>
						</tfoot>
						<tbody id="availability_rows">
							<?php
								$values = get_post_meta( $post_id, 'bookable_availibility', true );

								if ( ! empty( $values ) && is_array( $values ) ) {
									foreach ( $values as $availability ) {
										include( 'uou-resource-booking-availability-fields.php' );
									}
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
	  	</div>
	  	<div class="tab-pane" id="resource_tab">
		    <div class="alert alert-success" role="alert" style="display: none;"><?php _e('Successfully done !', 'uou-bookings') ?></div>

			<button type="button" class="button" id="save_uou_availibility"><?php _e('save', 'uou-bookings') ?></button>
			<button type="button" class="button button-primary" id="add_uou_availibility"><?php _e('Add', 'uou-bookings') ?></button>

			<div id="sortable_repeatable_availibility"></div>
	  	</div>
	</div>

</div>

<div id="hidden_repeatable_availibility" style="display: none">
	<div class="repeatable_availibility">
		<div class="availibility_head">
			<button type="button" class="button" id="remove_uou_availibility"><?php _e('Remove', 'uou-bookings') ?></button>
			<button type="button" class="button" id="show_uou_availibility"><?php _e('show/hide', 'uou-bookings') ?></button>
		</div>
		<div class="availibility_main">
			<p class="form-field">
				<label><?php _e( 'Select availibility Type', 'uou-bookings' ); ?></label>
				<select name="uou_booking_resouce_availibility"  class="uou_booking_resouce_availibility_class" style="margin-right: 7px;">

					<?php

						$args = array(
							'post_type' => 'package',
						);

						// the query
						$the_query = new WP_Query( $args );
					?>

					<?php if ( $the_query->have_posts() ) : ?>

						<!-- the loop -->
						<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
							<option value="<?php echo get_the_ID(); ?>"><?php _e( the_title(), 'uou-bookings' ); ?></option>
					  	<?php endwhile; ?>
					  	<!-- end of the loop -->

					  	<?php wp_reset_postdata(); ?>

					<?php else:  ?>
						<option value="own_available"><?php _e( 'Sorry No Resource were found', 'uou-bookings' ); ?></option>
					<?php endif; ?>

				</select>

				<div class="probal_hidden hidden"></div>
			</p>
			<?php

				woocommerce_wp_text_input( array( 'class' => 'uou_resource_class', 'id' => '_uou_booking_resource_cost', 'label' => __( 'Resource cost', 'uou-bookings' ), 'description' => __( 'Cost of Resource.', 'uou-bookings' ), 'value' => '', 'desc_tip' => true, 'type' => 'number', 'custom_attributes' => array(
					'min'   => '',
					'step' 	=> '1'
				) ) );

			?>
		</div>
	</div>
</div>



