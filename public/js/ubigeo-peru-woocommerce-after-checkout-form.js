/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function ($) {
    var country = $("select[name*='deptoCode']");
    var state = $("select[name*='billing_city']");
    var shipping_country = $("select[name*='shippingDeptoCode']");
    var shipping_state = $("select[name*='shipping_city']");

    if (country.length) {
        country.change(function () {

            var $this = $(this);
            get_states($(this).val(), function (response) {

                var obj = JSON.parse(response);
                var len = obj.length;
                var $stateValues = '';
                var $distValues = '';

                $("select[name*='billing_city']").empty();
                $("select[name*='distCode']").empty();
                for (i = 0; i < len; i++) {
                    var mystate = obj[i];
                    $stateValues += '<option value="' + mystate.idProv + '-' + mystate.provincia + '">' + mystate.provincia +
                            '</option>';
                }
                $distValues = '<option value="">Distrito</option>';
                $("select[name*='billing_city']").append($stateValues);
                $("select[name*='distCode']").append($distValues);

            });
            /* JSON populate Region/State Listbox */
        });
    }

    if (state.length) {
        state.change(function () {

            var $this = $(this);
            get_cities($(this).val(), function (response) {
                var obj = JSON.parse(response);
                var len = obj.length;
                var $cityValues = '';

                $("select[name*='distCode']").empty();
                for (i = 0; i < len; i++) {
                    var mycity = obj[i];
                    $cityValues += '<option value="' + mycity.id + '">' + mycity.city_name +
                            '</option>';
                }
                $("select[name*='distCode']").append($cityValues);

            });

        });
        /* JSON populate Cities Listbox */
    }

    function get_states(deptoCode, callback) {
        var data = {
            action: 'get_states_call',
            country_code: deptoCode
        };
        $.post(ajaxurl, data, function (response) {
            callback(response);
        });
    }

    function get_cities(rowCODE, callback) {
        var data = {
            action: 'get_cities_call',
            row_code: rowCODE
        };
        $.post(ajaxurl, data, function (response) {
            callback(response);
        });
    }

    if (shipping_country.length) {
        shipping_country.change(function () {

            var $this = $(this);
            get_states($(this).val(), function (response) {

                var obj = JSON.parse(response);
                var len = obj.length;
                var $stateValues = '';

                $("select[name*='shipping_city']").empty();
                $("select[name*='shippingDistCode']").empty();
                for (i = 0; i < len; i++) {
                    var mystate = obj[i];
                    $stateValues += '<option value="' + mystate.idProv + '-' + mystate.provincia + '">' + mystate.provincia +
                            '</option>';
                }
                $("select[name*='shipping_city']").append($stateValues);

            });
            /* JSON populate Region/State Listbox */
        });
    }

    if (shipping_state.length) {
        shipping_state.change(function () {

            var $this = $(this);
            get_cities($(this).val(), function (response) {
                var obj = JSON.parse(response);
                var len = obj.length;
                var $cityValues = '';

                $("select[name*='shippingDistCode']").empty();
                for (i = 0; i < len; i++) {
                    var mycity = obj[i];
                    $cityValues += '<option value="' + mycity.id + '">' + mycity.city_name +
                            '</option>';
                }
                $("select[name*='shippingDistCode']").append($cityValues);

            });

        });
        /* JSON populate Cities Listbox */
    }

    function get_states(deptoCode, callback) {
        var data = {
            action: 'get_states_call',
            country_code: deptoCode
        };
        $.post(ajaxurl, data, function (response) {
            callback(response);
        });
    }

    function get_cities(rowCODE, callback) {
        var data = {
            action: 'get_cities_call',
            row_code: rowCODE
        };
        $.post(ajaxurl, data, function (response) {
            callback(response);
        });
    }
})(jQuery);


