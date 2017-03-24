(function( $ ) {

    var formExists = setInterval(function() {
        if ($(".nf-form-cont").length) {
            console.log("Listening for length");

            // Set your event listeners here, example:
            //$(".api-postcode, .api-house_number").on('focusout', console.log("SWEK!"));
            

            $(".api-postcode, .api-house_number").on('focusout',getPostcodeAndNumber);
            clearInterval(formExists);
        }
    }, 100); // check every 100ms



    function getPostcodeAndNumber() {
        console.log("Called!");


        if ($('.api-postcode').val() != '' && $('.api-house_number').val() != '') {
            console.log("Calling getPostcode();");
            getPostcode();
        }
    }

    function getPostcode() {

        // Init vars
        var postcode = $('.api-postcode').val();
        var house_number = $('.api-house_number').val();
        var house_number_suffix = "";

        // Error notofications (todo)

        if ( $('.nf-form-cont').parent().find( '.postcode-api-error' ).length < 1 ) {
            $( '.api-postcode' ).closest( '.nf-field-container' ).before( '<div class="postcode-api-error" style="color: red;"></div>' );
        }

        // Init array for data sync (todo nonce + suffix)
        var data = {
            security:            nf_ajax_url.nonce,
            postcode:            postcode,
            house_number:        house_number,
            house_number_suffix: house_number_suffix,
        };

        // Call Postcode API
        xhr = $.ajax({
            type:		'POST',
            url:		nf_ajax_url.ajaxurl+'?action=nf_postcode_api_request',
            data:		data,
            dataType:   'json',
            success:	function( data ) {
                if (!data) {
                    return;  // nonce check failed
                    console.log('test');
                }

                // Debugging
                console.log(JSON.stringify(data));

                // If address is valid
                if ( typeof data.street != 'undefined' && typeof data.city != 'undefined') {

                    // Remove error and set fields to validated
                    $('.postcode-api-error').html( '' );

                    // Fill address fields
                    $('.api-street_name').val(data.street);
                    $('.api-city').val(data.city);

                }
                // If address is not valid
                else if (typeof data.exceptionId != 'undefined' ) {
                    // console.log(data);
                    switch(data.exceptionId) {
                        case 'PostcodeNl_Controller_Address_PostcodeTooShortException':
                            $( '.postcode-api-error' ).html( data.exception );
                            break;
                        case 'PostcodeNl_Controller_Address_InvalidPostcodeException':
                            $('.postcode-api-error' ).html( data.exception );
                            break;
                        case 'PostcodeNl_Controller_Address_InvalidHouseNumberException':
                            $('.postcode-api-error' ).html( data.exception );
                            break;
                        case 'PostcodeNl_Service_PostcodeAddress_AddressNotFoundException':
                            $( '.postcode-api-error' ).html( data.exception );
                            break;
                        case 'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_NotAuthorizedException':
                        case 'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_PasswordNotCorrectException':
                            // API credentials not valid - admin notice?
                            break;
                        default:
                            $('.postcode-api-error' ).html( data.exception );
                            break;
                    }
                }

            }
        });
    }

})( jQuery );
