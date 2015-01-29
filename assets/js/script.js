    // by PROBAL
jQuery(document).ready(function($) {

    //hide show person and resource block
    var selected = $('#_uou_booking_select_type').val();

    if(selected === 'uou_person') {
        $('body').find('.min_max_duration').hide();
    }
    else{
        $('body').find('.number_cost').hide();
    }

    // on change show & hide person/resource
    $('body').on('change', '#_uou_booking_select_type', function(){
        if ( $(this).val() == 'uou_resource' ) {
            var right_now = $(this);
            right_now.parent().parent().find('.number_cost').hide();
            right_now.parent().parent().find('.min_max_duration').show();
        } else {
            var right_now = $(this);
            right_now.parent().parent().find('.number_cost').show();
            right_now.parent().parent().find('.min_max_duration').hide();
        }
    });


    // add and remove main blocks
    $('#add_uou_attribute').click(function(e){
            e.preventDefault();
            $( ".uou_repeatable_hidden .uou_repeatable").clone().appendTo( "#sortable_holder" );

        });

    $('body').on('click','#remove_uou_attribute',function(f){
            $(this).closest('.uou_repeatable').remove();
            return false;
        });


    //add or remove availibility block
    $('#add_uou_availibility').click(function(e){
            e.preventDefault();
            $( "#hidden_repeatable_availibility .repeatable_availibility").clone().appendTo( "#sortable_repeatable_availibility" );

    });

    $('body').on('click','#remove_uou_availibility',function(f){
            $(this).closest('.repeatable_availibility').remove();
            return false;
    });



    //AJAX General Tab
    $('#uou_repeatable_place').on('click','#save_uou_attribute',function(e){
        e.preventDefault();

        var tada=[];
        $("#uou_repeatable_place #sortable_holder .uou_repeatable").each(function(index, value) {

            tada.push($(this).find('input,select').serializeArray());

        });

        //ajax new post name
        var full_list = [];
        for(var i=0; i<tada.length; ++i){
            full_list.push(tada[i]['3'].value);
        };
        console.log(full_list);

        var common = $.grep(full_list, function(element) {
            return $.inArray(element, booking.package_title ) !== -1;
        });

        //check url

        var qs = (function(a) {
            if (a == "") return {};
            var b = {};
            for (var i = 0; i < a.length; ++i)
            {
                var p=a[i].split('=');
                if (p.length != 2) continue;
                b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
            }
            return b;
        })(window.location.search.substr(1).split('&'));


        var data = {
            meta : tada,
            id : booking.post_id

        }


        if( ! qs["post"] ){
            $('.save-product').show(0).delay(5000).hide(0);
        }else{
            // if( common.length > 0){
            //     $('.resource_override').show(0).delay(10000).hide(0);
            // }else{
                $.ajax({
                    url : ajaxurl+'?action=get_my_option',
                    type: "POST",
                    data:data,
                    success: function(data){
                        console.log(data);
                        $('.alert-success').show(0).delay(5000).hide(0);
                       // location.reload();
                    }

                });
            // }

        }

    });


    //AJAX Availibility
    $('#resource_tab').on('click','#save_uou_availibility',function(e){
        e.preventDefault();

        var pada=[];
        $("#sortable_repeatable_availibility .repeatable_availibility").each(function(index, value) {
            pada.push($(this).find('input,select').serializeArray());
        });

        console.log(pada);


        var data = {
            meta : pada,
            id : booking.post_id

        }


        $.ajax({
            url : ajaxurl+'?action=get_availibility',
            type: "POST",
            data:data,
            success: function(data){
                console.log(data);
                // console.log("success");
                $('.alert-success').show(0).delay(5000).hide(0);
                //location.reload();
            }

        });

    });



    //AJAX Resouce tab
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e){


        var target = $(e.target).attr("rel");
        // console.log(target);

         if( target == 'resource'){

             $('div#sortable_repeatable_availibility').html('');

            $.ajax({
                    url : ajaxurl+'?action=resource_tab_change',
                    type: "POST",
                    data: 'post_id='+booking.post_id,
                    dataType: 'json',
                    success: function(data){

                        $('div#sortable_repeatable_availibility').html('');

                        $.each(data.select_item, function(index, val) {
                            // console.log(val);
                            var n = '<option value="'+val.ID+'">'+val.post_title+'</option>';
                            $('.probal_hidden').append(n);
                        });

                        var get_all = $('.probal_hidden').html();

                        $('#hidden_repeatable_availibility .uou_booking_resouce_availibility_class').html(get_all);


                        $.each(data.availibility, function(index, val) {
                             var clone = $( "#hidden_repeatable_availibility .repeatable_availibility").clone();
                              clone.find('select.uou_booking_resouce_availibility_class').val(val[0].value);
                              clone.find('.uou_resource_class').val(val[1].value);
                             // clone.find('select.uou_booking_resouce_availibility_class option[value='+val[0].value+']').attr('selected','selected');


                              // $('#sel1 option[value=2]').attr('selected','selected');


                              clone.appendTo( "#sortable_repeatable_availibility" );

                        });

                    }

            });

         }

     });


    //main part toggle
    $('.uou_repeatable .main_part').hide();

    $( "#sortable_holder" ).sortable({
        placeholder: "uou-ui-state-highlight"
    });

    $( "#sortable_holder" ).disableSelection();

    //toggle attribute show_uou_attribute button
    $('body').on('click', '#show_uou_attribute', function(f) {
        f.preventDefault();

        $(this).closest('div.uou_repeatable').find('div.main_part').slideToggle();
    });



    //availibility toggle
    $('.repeatable_availibility .availibility_main').hide();

    $( "#sortable_repeatable_availibility" ).sortable({
        placeholder: "uou-ui-state-highlight"
    });

    $( "#sortable_repeatable_availibility" ).disableSelection();

    //toggle attribute show_uou_attribute button
    $('body').on('click', '#show_uou_availibility', function(f) {
        f.preventDefault();

        $(this).closest('div.repeatable_availibility').find('div.availibility_main').slideToggle();

    });


    // bookable plugin - availibility
    $('#availability_rows, #bookings_pricing').on('change', '.uou_booking_availability_type select, .uou_booking_pricing_type select', function(){
        var value = $(this).val();
        var row   = $(this).closest('tr');

        $(row).find('.from_date, .from_day_of_week, .from_month, .from_week, .from_time, .from').hide();
        $(row).find('.to_date, .to_day_of_week, .to_month, .to_week, .to_time, .to').hide();

        if ( value == 'custom' ) {
            $(row).find('.from_date, .to_date').show();
        }
        if ( value == 'months' ) {
            $(row).find('.from_month, .to_month').show();
        }
        if ( value == 'weeks' ) {
            $(row).find('.from_week, .to_week').show();
        }
        if ( value == 'days' ) {
            $(row).find('.from_day_of_week, .to_day_of_week').show();
        }
        if ( value.match( "^time" ) ) {
            $(row).find('.from_time, .to_time').show();
        }
        if ( value == 'persons' || value == 'duration' || value == 'blocks' ) {
            $(row).find('.from, .to').show();
        }
    });

    $('body').on('row_added', function(){
        $('.uou_booking_availability_type select, .uou_booking_pricing_type select').change();

        $( '.date-picker' ).datepicker({
            dateFormat: 'mm/dd/yy',
            numberOfMonths: 1,
            showButtonPanel: true,
            showOn: 'button',
            buttonImage: uou_bookings_writepanel_js_params.calendar_image,
            buttonImageOnly: true
        });
    });

    $('body').on( 'woocommerce-product-type-change', function() {
        uou_bookings_trigger_change_events();
    });

    function uou_bookings_trigger_change_events() {
        $('.uou_booking_availability_type select, .uou_booking_pricing_type select, #_uou_booking_select_type, #_uou_booking_duration_unit, #_uou_booking_has_persons, #_uou_booking_has_resources, #_uou_booking_has_person_types').change();
    }

    $( 'input#_virtual' ).change( function () {
        uou_bookings_trigger_change_events();
    });



    $( '#_uou_booking_duration_unit' ).change(function() {
        $('.availability_time, ._uou_booking_first_block_time_field').show();

        if ( $(this).val() != 'hour' && $(this).val() != 'minute' ) {
            $('.availability_time, ._uou_booking_first_block_time_field').hide();
        }
    });

    uou_bookings_trigger_change_events();

    $('#availability_rows, #pricing_rows').sortable({
        items:'tr',
        cursor:'move',
        axis:'y',
        handle: '.sort',
        scrollSensitivity:40,
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'uou-metabox-sortable-placeholder',
        start:function(event,ui){
            ui.item.css('background-color','#f6f6f6');
        },
        stop:function(event,ui){
            ui.item.removeAttr('style');
        }
    });

    $( '.add_row' ).click(function(){
        $(this).closest('table').find('tbody').append( $( this ).data( 'row' ) );
        $('body').trigger('row_added');
        return false;
    });

    $('body').on('click', 'td.remove', function(){
        $(this).closest('tr').remove();
        return false;
    });

    $('#uou_bookings_persons').on('change', 'input.person_name', function(){
        $(this).closest('.woocommerce_booking_person').find('span.person_name').text( $(this).val() );
    });

});




