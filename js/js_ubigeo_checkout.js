jQuery(function ($) {
    function initSelect2(selector) {
        var $field = $(selector);
        if ($field.length && $.fn.select2) {
            $field.select2();
        }
    }

    initSelect2('#billing_departamento');
    initSelect2('#billing_provincia');
    initSelect2('#billing_distrito');
    initSelect2('#shipping_departamento');
    initSelect2('#shipping_provincia');
    initSelect2('#shipping_distrito');

    function loader() {
        $('.loader').toggleClass('active');
    }

    // Mantiene los campos estándar de WooCommerce disponibles para pasarelas
    // y APIs sin reemplazar los IDs propios de Ubigeo.
    // Provincia -> state | Distrito -> city.
    function rtSelectedText(selector) {
        var $field = $(selector);
        var value = $.trim($field.val() || '');
        var text = $.trim($field.find('option:selected').text() || '');

        if (!value || !text || /^seleccion|^select/i.test(text)) {
            return '';
        }

        return text;
    }

    function rtEnsureHiddenStandardField(name) {
        var $field = $('[name="' + name + '"]');

        if (!$field.length) {
            $field = $('<input>', {
                type: 'hidden',
                name: name,
                id: name
            }).appendTo('form.checkout');
        }

        return $field;
    }

    function rtSyncStandardAddress(type) {
        var country = $('#' + type + '_country').val() || 'PE';
        var state = '';
        var city = '';

        if (country === 'PE') {
            state = rtSelectedText('#' + type + '_provincia');
            city = rtSelectedText('#' + type + '_distrito');
        }

        rtEnsureHiddenStandardField(type + '_state').val(state);
        rtEnsureHiddenStandardField(type + '_city').val(city);
    }

    function rtSyncStandardAddressFields() {
        rtSyncStandardAddress('billing');
        rtSyncStandardAddress('shipping');
    }

    // Los options iniciales ya vienen construidos desde PHP. Solo seleccionamos
    // los IDs resueltos; no lanzamos AJAX para evitar borrar Provincia/Distrito.
    function applyInitialValue(selector, value) {
        if (value && $(selector).length) {
            $(selector).val(String(value)).trigger('change.select2');
        }
    }

    applyInitialValue('#billing_departamento', window.idDepa);
    applyInitialValue('#billing_provincia', window.idProv);
    applyInitialValue('#billing_distrito', window.idDist);
    applyInitialValue('#shipping_departamento', window.idDepa_shipping);
    applyInitialValue('#shipping_provincia', window.idProv_shipping);
    applyInitialValue('#shipping_distrito', window.idDist_shipping);

    rtSyncStandardAddressFields();

    function loadProvincias(select, selectType) {
        var idDepaValue = $(select).val();
        var $provincia = $('#' + selectType + '_provincia');
        var $distrito = $('#' + selectType + '_distrito');

        $provincia.html('<option value="">Seleccionar Provincia</option>');
        $distrito.html('<option value="">Seleccionar Distrito</option>');

        if (!idDepaValue) {
            $(document.body).trigger('update_checkout');
            return;
        }

        loader();
        $.ajax({
            type: 'POST',
            url: window.ajaxurl,
            dataType: 'json',
            data: {
                action: 'rt_ubigeo_load_provincias_front',
                idDepa: idDepaValue
            },
            success: function (response) {
                if (response && response.length) {
                    $.each(response, function (_, row) {
                        $provincia.append($('<option>', {value: row.idProv, text: row.provincia}));
                    });
                }
                $provincia.trigger('change.select2');
            },
            complete: loader
        });
    }

    function loadDistritos(select, selectType) {
        var idProvValue = $(select).val();
        var $distrito = $('#' + selectType + '_distrito');

        $distrito.html('<option value="">Seleccionar Distrito</option>');

        if (!idProvValue) {
            $(document.body).trigger('update_checkout');
            return;
        }

        loader();
        $.ajax({
            type: 'POST',
            url: window.ajaxurl,
            dataType: 'json',
            data: {
                action: 'rt_ubigeo_load_distritos_front',
                idProv: idProvValue
            },
            success: function (response) {
                if (response && response.length) {
                    $.each(response, function (_, row) {
                        $distrito.append($('<option>', {value: row.idDist, text: row.distrito}));
                    });
                }
                $distrito.trigger('change.select2');
            },
            complete: loader
        });
    }

    $('#billing_departamento').on('change.rtUbigeo', function () {
        loadProvincias(this, 'billing');
    });
    $('#shipping_departamento').on('change.rtUbigeo', function () {
        loadProvincias(this, 'shipping');
    });
    $('#billing_provincia').on('change.rtUbigeo', function () {
        rtSyncStandardAddress('billing');
        loadDistritos(this, 'billing');
    });
    $('#shipping_provincia').on('change.rtUbigeo', function () {
        rtSyncStandardAddress('shipping');
        loadDistritos(this, 'shipping');
    });

    $('#billing_distrito, #shipping_distrito').on('change.rtUbigeo', function () {
        rtSyncStandardAddressFields();
        $(document.body).trigger('update_checkout', {update_shipping_method: true});
    });

    $('#billing_country').on('change.rtUbigeo', function () {
        if ($(this).val() !== 'PE') {
            $('#billing_departamento').val('').trigger('change.select2');
            $('#billing_provincia').html('<option value="">Seleccionar Provincia</option>').trigger('change.select2');
            $('#billing_distrito').html('<option value="">Seleccionar Distrito</option>').trigger('change.select2');
        }
        rtSyncStandardAddress('billing');
    });

    $('#shipping_country').on('change.rtUbigeo', function () {
        if ($(this).val() !== 'PE') {
            $('#shipping_departamento').val('').trigger('change.select2');
            $('#shipping_provincia').html('<option value="">Seleccionar Provincia</option>').trigger('change.select2');
            $('#shipping_distrito').html('<option value="">Seleccionar Distrito</option>').trigger('change.select2');
        }
        rtSyncStandardAddress('shipping');
    });

    $(document.body).on('updated_checkout', rtSyncStandardAddressFields);
    $(document.body).on('checkout_place_order', function () {
        rtSyncStandardAddressFields();
        return true;
    });

    // Cliente recurrente o selección proveniente del carrito: recalcular el
    // envío inmediatamente con el Ubigeo ya preseleccionado.
    if (window.idDepa && window.idProv && window.idDist) {
        $(document.body).trigger('update_checkout', {update_shipping_method: true});
    }
});
