(function($){


	/*calculating total cost start*/

	$('form.cart').change(function(){


		var selected_cost = $(this).find('select.my_select_box').serializeArray();

		var base_cost = $('.base_cost').val();
		var total_resource_cost = 0;
		var total_cost = 0;

		for(var i=0; i<selected_cost.length; i++){
			var values = $.parseJSON(selected_cost[i].value);
		
			if($.parseJSON(selected_cost[i].value) != null){
			
				total_resource_cost = parseInt(total_resource_cost) + parseInt(values.cost); 
			}
		}

		var resources_cost = total_resource_cost;

	
		$('.resource_total_price').val(resources_cost);


		var check_in_date = $('.check-in').val(),
			check_out_date = $('.check-out').val();

		function parseDate(str) {
		    var mdy = str.split('/')
		    return new Date(mdy[2], mdy[0]-1, mdy[1]);
		}

		function daydiff(first, second) {
		    return (second-first)/(1000*60*60*24);
		}

		var dayDiff = daydiff(parseDate(check_in_date), parseDate(check_out_date));
		dayDiff = parseInt(dayDiff) +1;

		total_cost = parseInt(resources_cost) + parseInt(base_cost);

		if(!isNaN(dayDiff)){
			total_cost = total_cost*dayDiff;
		}
		
	
		$('.total_cost').html(total_cost);


	});

	/*calculating total cost end*/



	/*add to cart button clicked and ajax request fire start*/

	$('.single_add_to_cart_button').click(function(e){
	
		var resource_cost = $('.resource_total_price').val();
		var single_product_id = parseInt($('#single_product_id').val());
	
		var check_in_date = $('.check-in').val();
		var check_out_date = $('.check-out').val();

		var resources = $('.cart').find('select.my_select_box').serializeArray();

		var booking_resources = new Array();
		var booking_person = new Array();

		for(var j=0; j<resources.length; j++){

			var values = $.parseJSON(resources[j].value);
		
			if(resources[j].name == "resoruce_select"){
				booking_resources.push(values.resource_name);
			}

			if(resources[j].name == "person_select"){			
					
				booking_person.push(values.person_no);		
				
			}
		}


		if(booking_person.length == 0){
			booking_person.push('1');
		}

		function parseDate(str) {
		    var mdy = str.split('/')
		    return new Date(mdy[2], mdy[0]-1, mdy[1]);
		}

		function daydiff(first, second) {
		    return (second-first)/(1000*60*60*24);
		}

		var day_diff = daydiff(parseDate(check_in_date), parseDate(check_out_date));
		var day_count = parseInt(day_diff) + 1;



		$.when(  
			$.ajax({
					type: "POST",				
					url: ajax_object.ajaxurl,
					dataType: "JSON",
					data: {
						'action' : 'calculate_total_cost', 
						'resource_cost' : resource_cost, 
						'single_product_id' : single_product_id,
						'check_in_date' : check_in_date,
						'check_out_date' : check_out_date,
						'booking_resources': booking_resources,
						'booking_person': booking_person,
						'no_of_days': day_count					

					}
				})
			).then(function( data, textStatus, jqXHR ) {
		        //console.log(data);
				var this_page = window.location.toString();
				this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );
				location.reload();
	        	
		        
		});
		
		e.preventDefault();

				
	});

	/*add to cart button clicked and ajax request fire end*/
	
	



})(jQuery);