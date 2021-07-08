jQuery("#billing_departamento").select2();
jQuery("#billing_provincia").select2();
jQuery("#billing_distrito").select2();
jQuery("#shipping_departamento").select2();
jQuery("#shipping_provincia").select2();
jQuery("#shipping_distrito").select2();

function loader() {
    jQuery('.loader').toggleClass('active');
}

if (idDepa) {
    jQuery("#billing_departamento").val(idDepa).trigger('change');
    jQuery("#billing_provincia").val(idProv).trigger('change');
    jQuery("#billing_distrito").val(idDist).trigger('change');
}
if (idDepa_shipping) {
    jQuery("#shipping_departamento").val(idDepa_shipping).trigger('change');
    jQuery("#shipping_provincia").val(idProv_shipping).trigger('change');
    jQuery("#shipping_distrito").val(idDist_shipping).trigger('change');
}


function rt_ubigeo_event_departamento(select, selectType) {
    var data = {
        'action': 'rt_ubigeo_load_provincias_front',
        'idDepa': jQuery(select).val()
    };
    loader();
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        dataType: 'json',
        success: function (response) {
            if (!is_theme_avada) {
                jQuery('#' + selectType + '_provincia').html('<option value="0">Select Province</option>');
                jQuery('#' + selectType + '_distrito').html('<option value="0">Select District</option>');
            }
            if (response) {
				let html_provincia = '<option value="">Seleccionar Provincia</option>';
				let html_distrito = '<option value="">Seleccionar Distrito</option>';
                for (var r in response) {
                    html_provincia+='<option value=' + response[r].idProv + '>' + response[r].provincia + '</option>';
                }
				jQuery('#' + selectType + '_provincia').html(html_provincia);
				jQuery('#' + selectType + '_distrito').html(html_distrito);
            }
            loader();
        }
    });
}

function rt_ubigeo_event_provincia(select, selectType) {
    var data = {
        'action': 'rt_ubigeo_load_distritos_front',
        'idProv': jQuery(select).val()
    };
    loader();
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        dataType: 'json',
        success: function (response) {
            if (!is_theme_avada) {
                jQuery('#' + selectType + '_distrito').html('<option value="0">Seleccionar Distrito</option>');
            }
            if (response) {
				let html_distrito = '<option value="">Seleccionar Distrito</option>';
                for (var r in response) {
                    html_distrito+='<option value=' + response[r].idDist + '>' + response[r].distrito + '</option>';
                }
				jQuery('#' + selectType + '_distrito').html(html_distrito);
            }
            loader();
        }
    });
}
setTimeout(function(){
    jQuery('#billing_departamento').val('').trigger('change');
    jQuery('#billing_provincia').val('').trigger('change');
    jQuery('#billing_distrito').val('').trigger('change');
},500);
jQuery('#billing_departamento').on('change', function () {
    rt_ubigeo_event_departamento(this, 'billing');
});
jQuery('#shipping_departamento').on('change', function () {
    rt_ubigeo_event_departamento(this, 'shipping');
});
jQuery('#billing_provincia').on('change', function () {
    rt_ubigeo_event_provincia(this, 'billing');
});
jQuery('#shipping_provincia').on('change', function () {
    rt_ubigeo_event_provincia(this, 'shipping');
});

jQuery('#billing_distrito, #shipping_distrito').on('change', function () {
    jQuery(document.body).trigger("update_checkout", {update_shipping_method: true});
});

jQuery('#billing_country').on('change', function () {
    jQuery('#billing_departamento').val('').trigger('change');
    jQuery('#billing_provincia').val('').trigger('change');
    jQuery('#billing_distrito').val('').trigger('change');
});

