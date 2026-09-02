jQuery(function ($) {
    var config = window.rtUbigeoAddress || {};

    function initSelect2($field) {
        if ($field.length && $.fn.select2) {
            $field.select2();
        }
    }

    initSelect2($('#billing_departamento'));
    initSelect2($('#billing_provincia'));
    initSelect2($('#billing_distrito'));
    initSelect2($('#shipping_departamento'));
    initSelect2($('#shipping_provincia'));
    initSelect2($('#shipping_distrito'));

    function loadProvincias(select, selectType) {
        var idDepa = $(select).val();
        var $provincia = $('#' + selectType + '_provincia');
        var $distrito = $('#' + selectType + '_distrito');

        $provincia.html('<option value="">' + (config.provinceText || 'Seleccionar Provincia') + '</option>');
        $distrito.html('<option value="">' + (config.districtText || 'Seleccionar Distrito') + '</option>');

        if (!idDepa) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: config.ajaxurl,
            dataType: 'json',
            data: {
                action: 'rt_ubigeo_load_provincias_address',
                idDepa: idDepa
            },
            success: function (response) {
                if (response && response.length) {
                    $.each(response, function (_, row) {
                        $provincia.append($('<option>', {value: row.idProv, text: row.provincia}));
                    });
                }
                $provincia.trigger('change.select2');
            }
        });
    }

    function loadDistritos(select, selectType) {
        var idProv = $(select).val();
        var $distrito = $('#' + selectType + '_distrito');

        $distrito.html('<option value="">' + (config.districtText || 'Seleccionar Distrito') + '</option>');

        if (!idProv) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: config.ajaxurl,
            dataType: 'json',
            data: {
                action: 'rt_ubigeo_load_distritos_address',
                idProv: idProv
            },
            success: function (response) {
                if (response && response.length) {
                    $.each(response, function (_, row) {
                        $distrito.append($('<option>', {value: row.idDist, text: row.distrito}));
                    });
                }
                $distrito.trigger('change.select2');
            }
        });
    }

    $('#billing_departamento').on('change', function () {
        loadProvincias(this, 'billing');
    });
    $('#billing_provincia').on('change', function () {
        loadDistritos(this, 'billing');
    });
    $('#shipping_departamento').on('change', function () {
        loadProvincias(this, 'shipping');
    });
    $('#shipping_provincia').on('change', function () {
        loadDistritos(this, 'shipping');
    });
});
