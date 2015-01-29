<?php

	if ( ! isset( $availability['type'] ) )
		$availability['type'] = 'custom';
?>

<tr>
	<td class="sort">&nbsp;</td>
	<td style="display:none">
		<div class="select uou_booking_availability_type">
			<select name="uou_booking_availability_type[]">
				<option value="custom" selected="selected"><?php _e( 'Custom date range', 'uou-bookings' ); ?></option>
			</select>
		</div>
	</td>
	<td>
		<div class="from_date">
			<input type="text" style="border: 1px solid #ddd;" class="date-picker" name="uou_booking_availability_from_date[]" value="<?php if ( $availability['type'] == 'custom' && ! empty( $availability['from'] ) ) echo $availability['from'] ?>" />
		</div>
	</td>
	<td>
		<div class="to_date">
			<input type="text" style="border: 1px solid #ddd;" class="date-picker" name="uou_booking_availability_to_date[]" value="<?php if ( $availability['type'] == 'custom' && ! empty( $availability['to'] ) ) echo $availability['to']; ?>" />
		</div>
	</td>
	<td>
		<div class="select">
			<select name="uou_booking_availability_bookable[]">
				<option value="no" <?php selected( isset( $availability['bookable'] ) && $availability['bookable'] == 'no', true ) ?>><?php _e( 'No', 'uou-bookings' ) ;?></option>
				<!-- <option value="yes" <?php selected( isset( $availability['bookable'] ) && $availability['bookable'] == 'yes', true ) ?>><?php _e( 'Yes', 'uou-bookings' ) ;?></option> -->
			</select>
		</div>
	</td>
	<td class="remove">&nbsp;</td>
</tr>