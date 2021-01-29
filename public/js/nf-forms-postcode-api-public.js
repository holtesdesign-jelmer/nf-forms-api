(function ($) {
    var formExists = setInterval(function () {
        if ($(".nf-form-cont").length) {
            $('.api-postcode, .api-house_number, .api-suffix').on("focusout", function () {
                getPostcodeAndNumber();
            });
            clearInterval(formExists);
        }
    }, 500); // check every 100ms
    function getPostcodeAndNumber() {
        if ($('.api-postcode').val() != '' && $('.api-house_number').val() != '') {
            getPostcode();
        }
    }
    function getPostcode() {
        var postcode = $('.api-postcode').val();
        var house_number = $('.api-house_number').val();
        var house_number_suffix = $('.api-suffix').val();
        if ($('.nf-form-cont').parent().find('.postcode-api-error').length < 1) {
            $('.api-postcode').closest('.nf-field-container').before('<div class="postcode-api-error" style="color: red;"></div>');
        }
        var placeholder = nf_ajax_url.loading_placeholder;
        var data = {
            security: nf_ajax_url.nonce,
            postcode: postcode,
            house_number: house_number,
            house_number_suffix: house_number_suffix,
        };
        if (postcode.length > 5 && house_number.length > 0) {
            // Call Postcode API
            xhr = $.ajax({
                type: 'POST',
                url: nf_ajax_url.ajaxurl + '?action=nf_forms_postcode_api_request',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    $('.api-street_name').attr("placeholder", placeholder).addClass('loading');
                    $('.api-city').attr("placeholder", placeholder).addClass('loading');
                },
                success: function (data) {
                    if (!data) {
                        return;  // nonce check failed
                    }
                    if (typeof data.street != 'undefined' && typeof data.city != 'undefined') {
                        $('.postcode-api-error').html('');
                        $('.api-street_name').val(data.street).prop("readonly", true).removeClass('loading');
                        $('.api-city').val(data.city).prop("readonly", true).removeClass('loading');
                    } else if (typeof data.exceptionId != 'undefined') {
                        $('.api-street_name').attr("placeholder", '').val('').prop("readonly", false).removeClass('loading');
                        $('.api-city').attr("placeholder", '').val('').prop("readonly", false).removeClass('loading');
                        switch (data.exceptionId) {
                            case 'PostcodeNl_Controller_Address_PostcodeTooShortException':
                                $('.postcode-api-error').html(data.exception);
                                break;
                            case 'PostcodeNl_Controller_Address_InvalidPostcodeException':
                                $('.postcode-api-error').html(data.exception);
                                break;
                            case 'PostcodeNl_Controller_Address_InvalidHouseNumberException':
                                $('.postcode-api-error').html(data.exception);
                                break;
                            case 'PostcodeNl_Service_PostcodeAddress_AddressNotFoundException':
                                $('.postcode-api-error').html(data.exception);
                                break;
                            case 'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_NotAuthorizedException':
                            case 'PostcodeNl_Controller_Plugin_HttpBasicAuthentication_PasswordNotCorrectException':
                                break;
                            default:
                                $('.postcode-api-error').html(data.exception);
                                break;
                        }
                    } else {
                        $('.api-street_name').attr("placeholder", '').val('').prop("readonly", false).removeClass('loading');
                        $('.api-city').attr("placeholder", '').val('').prop("readonly", false).removeClass('loading');
                    }
                }
            });
        }
    }
})(jQuery);