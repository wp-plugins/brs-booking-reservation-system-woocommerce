(function($){


	/*unavailable_date is passed from localize scripts*/


	/*disable unavailable dates start*/

	function unavailable(date) {
		dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" +date.getFullYear();
	    if ($.inArray(dmy, unavailable_date) < 0) {
	    	return [true,"","Book Now"];
	    } else {
	    	return [false,"","Booked Out"];
	  	}
	}

	$('.check-in').datepicker({ minDate: new Date(),beforeShowDay: unavailable });
	$('.check-out').datepicker({ minDate: new Date(),beforeShowDay: unavailable });

	/*disable unavailable dates end*/



	/*chosen plugin initialize start*/

	$(".my_select_box").chosen(
    	/*disable_search_threshold: 10,
    	no_results_text: "Oops, nothing found!",
    	width: "95%"*/
  	);

  	/*chosen plugin initialize end*/



	/*set base cost in total booking price */
	var base_cost = $('.base_cost').val();	
	$('h1 span.total_cost').html(base_cost);




  	/*initial disable book now button start*/

  	var check_in_date = $('.check-in').val(),
		check_out_date = $('.check-out').val();

	if(!check_in_date || !check_out_date){
		$('.single_add_to_cart_button').attr('disabled','disabled');
	}	
	
	/*initial disable book now button end*/
	


  	$('form.cart').change(function(){
		
		var check_in_date = $('.check-in').val(),
			check_out_date = $('.check-out').val();

		if(check_in_date){
			if(check_out_date.length == 0){
				$('.check-out').val(check_in_date);
				check_out_date = check_in_date;
			}
		}		

		/*check the product is already booked or not*/
		var already_booked = $('.single_add_to_cart_button').html();


		/*start date is greater than end date*/

		var x = new Date(check_in_date);
		var y = new Date(check_out_date);	
		
		/*date between disable days start*/		

		function parseDate(str) {
		    var mdy = str.split('/')
		    return new Date(mdy[2], mdy[0]-1, mdy[1]);
		}
		function daydiff(first, second) {
		    return (second-first)/(1000*60*60*24);
		}
		var dayDiff = daydiff(parseDate(check_in_date), parseDate(check_out_date));
	


		Date.prototype.addDays = function(days) {
	       var dat = new Date(this.valueOf())
	       dat.setDate(dat.getDate() + days);
	       return dat;
		}
		function getDates(startDate, stopDate) {
			var dateArray = new Array();
			var currentDate = startDate;
			while (currentDate <= stopDate) {
				dateArray.push(currentDate)
				currentDate = currentDate.addDays(1);
			}
			return dateArray;
		}
		var dateArray = getDates(new Date(check_in_date), (new Date(check_in_date)).addDays(dayDiff));
		

		var changeDateFromat = new Array();

		for (i = 0; i < dateArray.length; i ++ ) {		   
		   var date = new Date(dateArray[i]);
		   var a = date.getDate() + '-' + (date.getMonth()+1) + '-' +  date.getFullYear();
		   changeDateFromat.push(a);
		}

		

		for(var j=0; j<changeDateFromat.length; j++){

			if(jQuery.inArray(changeDateFromat[j], unavailable_date)!==-1){

				$('.single_add_to_cart_button').attr('disabled','disabled');
				break;
			}else{
				$('.single_add_to_cart_button').removeAttr('disabled','disabled');
			}			
		}

	

		if(dayDiff < 0){
			$('.single_add_to_cart_button').attr('disabled','disabled');
		}

		if(already_booked == 'Already Booked'){
			alert('This product is already booked. Please checkout first for another booking');
			$('.single_add_to_cart_button').attr('disabled','disabled');
		}


	});


  	/*date between disable days start*/	



})(jQuery);