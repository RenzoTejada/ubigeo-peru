jQuery(document).ready(function () {
    jQuery("#billing_departamento").select2();
    jQuery("#billing_provincia").select2();
    jQuery("#billing_distrito").select2();
    jQuery("#shipping_departamento").select2();
    jQuery("#shipping_provincia").select2();
    jQuery("#shipping_distrito").select2();

    jQuery('#billing_departamento').on('change', function () {
        rt_ubigeo_event_departamento(this, 'billing')
    });

    jQuery('#billing_provincia').on('change', function () {
        rt_ubigeo_event_provincia(this, 'billing')
    });

    jQuery('#shipping_departamento').on('change', function () {
        rt_ubigeo_event_departamento(this, 'shipping')
    });

    jQuery('#shipping_provincia').on('change', function () {
        rt_ubigeo_event_provincia(this, 'shipping')
    });

    function rt_ubigeo_event_departamento(select, selectType) {
        var data = {
            'action': 'rt_ubigeo_load_provincias_address',
            'idDepa': jQuery(select).val()
        }
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            dataType: 'json',
            success: function (response) {
                jQuery('#' + selectType + '_provincia').html('<option value="0">' + name_province + '</option>')
                jQuery('#' + selectType + '_distrito').html('<option value="0">' + name_distrito + '</option>')
                if (response.length > 0) {
                    for (var r in response) {
                        jQuery('#' + selectType + '_provincia').append('<option value=' + response[r].idProv + '>' + response[r].provincia + '</option>');
                    }
                }
            }
        });
    }

    function rt_ubigeo_event_provincia(select, selectType) {
        var data = {
            'action': 'rt_ubigeo_load_distritos_address',
            'idProv': jQuery(select).val()
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            dataType: 'json',

            success: function (response) {
                jQuery('#' + selectType + '_distrito').html('<option value="0">' + name_distrito + '</option>')
                if (response) {
                    for (var r in response) {
                        jQuery('#' + selectType + '_distrito').append('<option value=' + response[r].idDist + '>' + response[r].distrito + '</option>')
                    }
                }
            }
        });
    }
});