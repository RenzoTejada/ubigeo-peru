=== Ubigeo de Perú para Woocommerce y WordPress ===
Contributors: renzotejada
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: ubigeo perú, ubigeo, peru, departamentos, provincia, distrito, Woocommerce
Requires at least: 5.2
Tested up to: 5.8
Stable tag: 3.5.4
Requires PHP: 5.6.20

This plugin adds the tables to the database _ubigeo_departamento, _ubigeo_provincia, _ubigeo_distrito with respect to Peru.

Add the **Ubigeo Peru** to the Woocommerce checkout - _departamento - _provincia - _distrito

You will find this same information in the readme.txt of the plugin and on our website https://renzotejada.com

== Description ==

This plugin adds the tables to the database _ubigeo_departamento, _ubigeo_provincia, _ubigeo_distrito with respect to Peru.
Add the **Ubigeo Peru** to the Woocommerce checkout - _departamento - _provincia - _distrito.

We also have a premium plugin where the shipping cost functionality of **Ubigeo Peru** is added for woocommerce.
Where:

&#9989; Enable cost per **Department**.

&#9989; Enable cost per **District**.

&#9989; Enable and Disable the **shipping cost** functionality without disabling the plugin.

&#9989; Add name of **shipping cost, free shipping and store pickup**.

&#9989; Add shipping methods such as **Shipping cost**, **Free shipping** (plus minimum amount) and **Store Pickup** (whatever you require).

&#9989; **Massive cost** loading, new import functionality via .csv


&#9989; More information about the plugin in [Peru Ubigeo Shipping Cost for Woocommerce](https://renzotejada.com/plugin/costo-de-envio-de-ubigeo-de-peru-para-woocommerce/ "Peru Ubigeo Shipping Cost for Woocommerce")

= View more plugins =

For additional functionality, check out our companion plugin, such as:

* [Ubigeo Perú](https://wordpress.org/plugins/ubigeo-peru/)
* [Libro de Reclamaciones y Quejas](https://wordpress.org/plugins/libro-de-reclamaciones-y-quejas/)
* [Comprobante de Pago Perú](https://wordpress.org/plugins/comprobante-de-pago-peru/)
* [Tipo Documento Perú](https://wordpress.org/plugins/tipo-documento-peru/)
* [Transferencia Bancaria Perú](https://wordpress.org/plugins/transferencia-bancaria-peru/)
* [Utils para WooCommerce y WordPress](https://wordpress.org/plugins/wp-utils/)
* [Multi Link in Bio](https://wordpress.org/plugins/multi-link-in-bio/)
* [Display Price Free](https://wordpress.org/plugins/display-price-free/)
* [Recojo por otra persona](https://wordpress.org/plugins/recojo-por-otra-persona/)

= View more plugins PREMIUM =

* [Costo de envío de Ubigeo Perú](https://renzotejada.com/plugin/costo-de-envio-de-ubigeo-de-peru-para-woocommerce/)
* [Libro de Reclamaciones y Quejas PRO](https://renzotejada.com/plugin/libro-de-reclamaciones-y-quejas-pro/)
* [WooYape para WooCommerce](https://renzotejada.com/plugin/wooyape-para-woocommerce/)
* [WooLukita para WooCommerce](https://renzotejada.com/plugin/woolukita-para-woocommerce/)
* [WooPlin para WooCommerce](https://renzotejada.com/plugin/wooplin-para-woocommerce/)
* [WooTunki para WooCommerce](https://renzotejada.com/plugin/wootunki-para-woocommerce/)
* [WooBilletera para WooCommerce](https://renzotejada.com/plugin/woobilletera-para-woocommerce/)


Visit our [plugins overview page](https://renzotejada.com/categoria-producto/plugins/) for more information.

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

= 3.5.4 (12/08/2021) =
Fix: Corrigiendo compatibilidad de checkout field editor con datos en el email.
Fix: Se agrega la funcionalidad de desactivar el ubigeo en los emils.

= 3.5.3 (20/07/2021) =
Fix: Corrigiendo problemas de vulnerabilidades de team wp.

= 3.5.2 (20/07/2021) =
Fix: Corrigiendo vulnerable to SQL injection.
test: validación con wp 5.8

= 3.5.1 (15/07/2021) =
Fix: corrigiendo notice de variables no declaradas.
Fix: validando versión WC 5.5.1.

= 3.5.0 (07/07/2021) =
Fix: forzando a limpiar los campos de ubigeo en checkout.

= 3.4.9 (29/06/2021) =
Fix: correción de ubigeo solo para Perú.

= 3.4.8 (14/06/2021) =
Fix: validando versión WC 5.4.1.
Fix: agregando links plugins premimum y free en el readme.txt

= 3.4.7 (03/06/2021) =
fix: Se agrega ubigeo perú para el api de WC tanto billing como shipping.

= 3.4.6 (19/05/2021) =
fix: Se corrige el error de las cabeceras han sido enviadas en mi cuenta direcciones.

= 3.4.5 (19/05/2021) =
fix: Elimina una secuencia de js puesta en cola del plugin YITH WooCommerce Request A Quote Premium que afecta a mi cuenta direcciones.
fix: Se agrega la libreria de select2 en mi cuenta direcciones.

= 3.4.4 (14/05/2021) =
fix: Se agrega la opción de activar/desactivar la funcionalidad del ubigeo en el checkout.
fix: correción de error de js de ubigeo checkout de no limpiar distritos.
Fix: Se agrega los addons de RT.

= 3.4.3 (12/05/2021) =
fix: correción de ubigeo con dirección en mi cuenta.
fix: correción de notice en generar orden de pedido desde admin.
test: validación con wp 5.7.2

= 3.4.2 (04/05/2021) =
fix: correción de notice y warnings con las query de shipping ubigeo.

= 3.4.1 (30/04/2021) =
fix: correción con la librería selectWoo para el tema Meabhy.
fix: agregando el shipping ubigeo peru en los emails.(Para envíos de diferentes dirección)

= 3.4.0 (28/04/2021) =
fix: correción con la librería selectWoo.

= 3.3.9 (27/04/2021) =
fix: se elimino la prioridad del campo dirección 2 del plugin ubigeo.

= 3.3.8 (18/04/2021) =
fix: correción de validación ubigeo para otros paises.

= 3.3.7 (08/03/2021) =
fix: correción de carga de ubigeo en tema astra.
fix: correción con plugin de culqui lest go

= 3.3.6 (25/02/2021) =
Refactor: Se agrega los archivos js y css para ubigeo checkout.
Refactor: Se reformatea el codigo fuente según estandares psr-1 y psr-2.
fix: Corrigiendo error de query al momento de calcular tipo departamento con costo de envío.
Testeado en la version  WC tested up to: 5.0.0
Testeado en la version  WP tested up to: 5.6.2

= 3.3.5 (31/01/2021) =
Fix: Corrigiendo error de carga de ubigeo cuando solo seleccionaba departamento.
Fix: Corrigiendo error de session start que afecta a la salud de wp.
Testeado en la version  WC tested up to: 4.9.2

= 3.3.4 (19/01/2021) =
Fix: Corrigiendo el ordenamiento de la data de distritos ascendentemente.

= 3.3.3 (14/01/2021) =
Testeado en la version  WC tested up to: 4.9

= 3.3.2 (14/01/2021) =
Agregando distritos Mi Perú del Callao.
Corrigiendo error notice al activar debug del wp-config.

= 3.3.1 (14/01/2021) =
Agregando distritos faltantes.

= 3.3.0 (12/01/2021) =
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


