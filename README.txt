=== Ubigeo de Perú para Woocommerce y WordPress ===
Contributors: renzotejada
Tags: ubigeo perú, ubigeo, peru, departamentos, provincia, distrito, Woocommerce
Requires at least: 5.2
Tested up to: 5.6
Stable tag: trunk
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin adds the tables to the database _ubigeo_departamento, _ubigeo_provincia, _ubigeo_distrito with respect to Peru.
Add the ubigeo from Peru to the Woocommerce checkout - _departamento - _provincia - _distrito

You will find this same information in the readme.txt of the plugin and on our website https://renzotejada.com

== Description ==

This plugin adds the tables to the database _ubigeo_departamento, _ubigeo_provincia, _ubigeo_distrito with respect to Peru.
Add the ubigeo from Peru to the Woocommerce checkout - _departamento - _provincia - _distrito.

We also have a premium plugin where the shipping cost functionality of ubigeo from Peru is added for woocommerce.
Where:

* Enable cost per Department.
* Enable cost per District.
* Enable and Disable the shipping cost functionality without disabling the plugin.
* Add name of shipping cost, free shipping and store pickup.
* Add shipping methods such as Shipping cost, Free shipping (plus minimum amount) and Store Pickup (whatever you require).
* Massive cost loading, new import functionality via .csv


More information about the plugin in [Peru Ubigeo Shipping Cost for Woocommerce](https://renzotejada.com/plugin/costo-de-envio-de-ubigeo-de-peru-para-woocommerce/ "Peru Ubigeo Shipping Cost for Woocommerce")

== Installation ==

Automatic installation
1. Plugin admin panel and add new option.
2. Search in the common province-region-text box.
3. Placed in the description of this plugin and select install.
4. Activate the plugin from the WordPress administration panel.

ZIP files Installation manual
1. Download the ZIP file from this screen.
2. Select the add plugin option from the admin panel.
3. Select the heavy load option and select the downloaded file.
4. Confirm Installation and activation plugin from the administration panel.

Manual FTP installation
1. Download the ZIP file from this screen and unzip.
2. Go to your FTP folder on your web server.
3. Copy the directory ubigeo into the following address wp-content/plugins/
4. Activate the plugin from the WordPress administration panel.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= Where did you get the data for the departments, provinces and districts? =

The information was obtained from the following urls:
  a. http://sige.inei.gob.pe/test/atlas/
  b. http://www.strategy.org.pe/articulos/cbdf11_strategy_76382231-UBIGEO-PERU-MYSQL.pdf
  c. http://www.scribd.com/doc/220863416/Cbdf11-Strategy-76382231-Ubigeo-Peru-Mysql


== Screenshots ==

1. checkout view
2. Ubigeo Peru Dashboard
3. Ubigeo Peru Menu

== Changelog ==

= 3.3.2 (14/01/2021)=
Agregando distritos Mi Perú del Callao.
Corrigiendo error notice al activar debug del wp-config.

= 3.3.1 (14/01/2021)=
Agregando distritos faltantes.

= 3.3.0 (12/01/2021)=
Agregando ubigeo en pdf yith invoice y actualizando changelog

= 3.2.9 (11/01/2021)=
Agregando class para col2 de shipping peru

= 3.2.8 (11/01/2021) =
Agregando traducciones correctamente y agregando html para el pago thank you

= 3.2.7 =
Agregando un loader para la carga de ubigeo en el checkout y actualizando nombre del plugin para el seo

= 3.2.6 =
Correción de campos de provincia y distrito que no aparecen y actualizando version de test y link de inei ubigeo

= 3.2.5 =
Rename de funciona para plugin de libro de reclamaciones y quejas.

= 3.2.4 =
Plugin formart code cs fix php - formateando codigo y actualizando imagen banner

= 3.2.3 =
Plugin es compatible para WordPress para los funciones de libro de reclamaciones y quejas.

= 3.2.2 =
Fix de distrios de anchash (actualizando distritos de la provincia huayla y marizcal luzuriaga).

= 3.2.1 =
Agregando sessión de shipping ubigeo.

= 3.2.0 =
Agregando Ubigeo a direcciones de mi cuenta.

= 3.1.9 =
Haciendo los campos ubigeos obligatorios (departamento , provincia y distrito).

= 3.1.8 =
Agregando traducciones para el plugin

= 3.1.7 =
Ocultando span de select2 de los campos ubigeo pata temas Pawsitive

= 3.1.6 =
Cambiando texto a español.

= 3.1.5 =
Fix de reload page con combos ubigeo checkout y cambios de texto a español.

= 3.1.4 =
Re-Ordenando la prioridad de los campos del checkout como email y phone

= 3.1.3 =
Agregando la lib de select2 

= 3.1.2 =
Fix de cambio de dep-prov-dist pegados y agregando distrito de Veintiséis de Octubre.

= 3.1.1 =
Agregando distrito de salamanca.

= 3.1.0 =
Fix con el tema avada (doble ajax).

= 3.0.9 =
Mostrando ubigeo billing y shipping en el orden de pedido y en page thanks you.

= 3.0.8 =
Fix de query de ubigeo front provincia

= 3.0.7 =
Agregando info de ubigeo en la orden del pedido, correo y page de thanks you de woocoomerce

= 3.0.6 =
Agregando la lib de select2 para sitio que no incluyen dicha libreria

= 3.0.5 =
Cambio de estructura de programación a nivel back, front misma funcionalidad y agregando validacion de funcion premium

= 2.0.2 =
Corrigiendo de ajax de distrito  y actualizando nombre de distrito

= 2.0.0 =
Corrigiendo la version que soporta plugin

= 1.9.0 =
Nueva estructura de ubigeo para woocommerce

= 1.8.0 =
fix ubigeo para el shipping para woocommerce - error al validar ubigeo

= 1.7.0 =
Agregando ubigeo para el shipping para woocommerce

= 1.6.0 =
Agregando imágenes del plugin woocommerce

= 1.5.0 =
delete files de plugin woocommerce

= 1.4.0 =
Agregando Screenshots 2 del plugin woocommerce

= 1.3.0 =
Agregando iconos y Screenshots del plugin woocommerce

= 1.2.0 =
Corrección de seteo de valor de city en woocommerce

= 1.1.0 =
Corrección de método deprecado

= 1.0.0 =
Inicio


