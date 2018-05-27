<?php
if(!defined('ABSPATH')) exit;
add_action( 'admin_menu', 'pfb_woo_menu');
function pfb_woo_menu() {add_options_page('Pixel de Facebook', 'Facebook Pixel','manage_options', 'facebook-pixel', 'pfb_woo_conf');}
function pfb_woo_conf() {
?>
<style>
#wrap{margin:0; padding:0;}
#col1{width:600px;float:left;text-align:left;padding:0 0 0 10px; margin: 0 10px 0 0}
#col2{width:170px;float:left;text-align:right;padding:0 10px 0 0}
#col3{width:790px;clear:both; margin:20px 0;border-bottom:1px solid #ccc;font-size:20px;padding:0 0 10px 10px}
#guardar{width:800px;clear:both; margin:20px 0}
.instruccion{ font-style:italic; font-size:13px;color:#888; margin:12px 0 0 0}
@media screen and (max-width: 899px) {
#col1{width:500px}
#col2{width:170px}
#col3{width:690px}
#guardar{width:700px}
}
@media screen and (max-width: 767px) {
#col1{width:450px}
#col2{width:170px}
#col3{width:640px}
#guardar{width:650px}
}
@media screen and (max-width: 479px) {
#col1{width:170px}
#col2{width:150px}
#col3{width:340px}
#guardar{width:350px}
}
</style>

<div class="wrap">	
<h2>Pixel de Facebook para Woocommerce</h2>   
<form method="post" action='options.php' id="guardar">
	<?php settings_fields('pfb_woo_options'); ?>
    <?php do_settings_sections('fb_eventos'); ?>
<input class="button-primary" type="submit" name="submit" value="Guardar cambios" style="margin:20px 0 0 20px" />
</form>      
</div>
  
<?php
}

add_action('admin_init', 'pfb_woo_admin_init');

function pfb_woo_admin_init() {
	register_setting('pfb_woo_options','pfb_woo_options','pfb_woo_validate');
	add_settings_section('pfb_woo_main','', 'pfb_woo_section_text','fb_eventos');
	add_settings_field('pfb_woo_id_', '','pfb_woo_conf_id_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_inc', '','pfb_woo_conf_inc_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_ref', '','pfb_woo_conf_ref_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_mon', '','pfb_woo_conf_mon_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_tax', '','pfb_woo_conf_tax_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_vc_', '','pfb_woo_conf_vc_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_adc', '','pfb_woo_conf_adc_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_ic_', '','pfb_woo_conf_ic_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_pur', '','pfb_woo_conf_pur_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_vcs', '','pfb_woo_conf_vcs_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_vca', '','pfb_woo_conf_vca_input','fb_eventos','pfb_woo_main');
	add_settings_field('pfb_woo_vct', '','pfb_woo_conf_vct_input','fb_eventos','pfb_woo_main');
}

/* DOCUMENTACION */
function pfb_woo_section_text() {
	echo "<a href='https://www.labschool.es/guia-como-configurar-anuncios-dinamicos-en-facebook/' target='_blank'>Documentaci&oacute;n del plugin</a>";
}

/* ID FACEBOOK */
function pfb_woo_conf_id_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['id'];
	echo "<div id='col3'>Configuraci&oacute;n B&aacute;sica</div>";
	echo "<div id='col1'><label>Facebook Pixel ID</label>
		  <div class='instruccion'>Introduce el ID del pixel facilitado por Facebook <a href='https://www.facebook.com/ads/manager/pixel/facebook_pixel/' target='_blank'>&iquest;C&oacute;mo obtener el ID?</a></div></div>";
	echo "<div id='col2'><input id='id' name='pfb_woo_options[id]' type='text' value='$id' /></div>";
}

/* INCLUIR CODIGO SEGUIMIENTO */
function pfb_woo_conf_inc_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['include_snippet'];
	echo "<div id='col1'><label>Agregar p&iacute;xel de Facebook</label><br />
		  <div class='instruccion'>No actives esta opci&oacute;n si has agregado manualmente el c&oacute;digo del pixel en el header.php de la plantilla.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[include_snippet]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* REFERENCIA PRODUCTO */
function pfb_woo_conf_ref_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['product_ref'];
	echo "<div id='col3'>Configuraci&oacute;n Avanzada</div>";
	echo "<div id='col1'><label><strong>Identificador de producto</strong></label>
		  <div class='instruccion'>Puedes identificar la referencia del producto mediante el ID de Wordpress o el SKU que hayas definido.</div></div>";
	echo "<div id='col2'><select name='pfb_woo_options[product_ref]'>
				<option value='1' " . selected( $id , 1,false) . ">ID Producto</option>
				<option value='0' " . selected( $id , 0,false) . ">SKU</option>
		  </div>";
}

/* TIPO DE MONEDA */
function pfb_woo_conf_mon_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['currency'];
	echo "<div id='col1'><label><strong>Tipo de divisa</strong></label>
		  <div class='instruccion'>La divisa debe coincidir con la que hayas configurado en Woocommerce seg&uacute;n norma ISO 4217.</div></div>";
	echo "<div id='col2'><select name='pfb_woo_options[currency]'>
				<option value='EUR' " . selected( $id , 'EUR',false) . ">EUR - Euro</option>
				<option value='GBP' " . selected( $id , 'GBP',false) . ">GBP - Libra esterlina</option>
				<option value='USD' " . selected( $id , 'USD',false) . ">USD - D&oacute;lar estadounidense</option>
				<option value='ARS' " . selected( $id , 'ARS',false) . ">ARS - Peso argentino</option>
				<option value='COP' " . selected( $id , 'COP',false) . ">COP - Peso colombiano</option>
				<option value='CLP' " . selected( $id , 'CLP',false) . ">CLP - Peso chileno</option>
				<option value='MXN' " . selected( $id , 'MXN',false) . ">MXN - Peso mexicano</option>
				<option value='PEN' " . selected( $id , 'PEN',false) . ">PEN - Sol</option>
		  </div>";
}

/* IMPUESTOS */
function pfb_woo_conf_tax_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['taxes'];
	echo "<div id='col1'><label><strong>Aplicar impuestos</strong></label>
		  <div class='instruccion'>Incluye los impuestos sobre los precios recogidos en los diferentes eventos activados.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[taxes]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}


/* EVENTO VIEWCONTENT */
function pfb_woo_conf_vc_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['view_content'];
	echo "<div id='col3'>Eventos Est&aacute;ndar</div>";
	echo "<div id='col1'><label><strong>ViewContent</strong></label>
		  <div class='instruccion'>Incluye el evento 'ViewContent' cada vez que se carga una p&aacute;gina de producto.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[view_content]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* EVENTO ADDTOCART */
function pfb_woo_conf_adc_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['add_to_cart'];
	echo "<div id='col1'><label><strong>AddToCart</strong></label>
		  <div class='instruccion'>Incluye el evento 'AddToCart' cada vez que se carga la p&aacute;gina del carrito.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[add_to_cart]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* EVENTO INITIATECHECKOUT */
function pfb_woo_conf_ic_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['initiate_checkout'];
	echo "<div id='col1'><label><strong>InitiateCheckout</strong></label>
		  <div class='instruccion'>Incluye el evento 'InitiateCheckout' cada vez que se carga la p&aacute;gina de finalizar la compra.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[initiate_checkout]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* EVENTO PURCHASE */
function pfb_woo_conf_pur_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['purchase'];
	echo "<div id='col1'><label><strong>Purchase</strong></label>
		  <div class='instruccion'>Incluye el evento 'Purchase' cada vez que se carga la p&aacute;gina de agradecimiento tras realizar una compra.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[purchase]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* EVENTO VIEWSHOP */
function pfb_woo_conf_vcs_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['view_shop'];
	echo "<div id='col3'>Eventos Personalizados</div>";
	echo "<div id='col1'><label><strong>ViewShop</strong></label>
		  <div class='instruccion'>Incluye el evento personalizado 'ViewShop' cada vez que se carga una p&aacute;gina del cat&aacute;logo de productos.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[view_shop]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* EVENTO VIEWCATEGORY */
function pfb_woo_conf_vca_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['view_category'];
	echo "<div id='col1'><label><strong>ViewCategory</strong></label>
		  <div class='instruccion'>Incluye el evento personalizado 'ViewCategory' cada vez que se carga una p&aacute;gina de categor&iacute;a de productos.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[view_category]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* EVENTO VIEWTAG */
function pfb_woo_conf_vct_input() {
	$options = get_option('pfb_woo_options');
	$id = $options['view_tag'];
	echo "<div id='col1'><label><strong>ViewTag</strong></label>
		  <div class='instruccion'>Incluye el evento personalizado 'ViewTag' cada vez que se carga una p&aacute;gina de etiqueta de productos.</div></div>";
	echo "<div id='col2'><input name='pfb_woo_options[view_tag]' type='checkbox' value='1' " . checked( $id , 1,false) . " /></div>";
}

/* GUARDAR OPCIONES */
function pfb_woo_validate($form){
	$options = get_option('pfb_woo_options');
	$updated = $options;
	$updated['id'] = $form['id'];
	$updated['include_snippet'] = $form['include_snippet'];
	$updated['product_ref'] = $form['product_ref'];
	$updated['currency'] = $form['currency'];
	$updated['taxes'] = $form['taxes'];
	$updated['view_content'] = $form['view_content'];
	$updated['view_shop'] = $form['view_shop'];
	$updated['view_category'] = $form['view_category'];
	$updated['view_tag'] = $form['view_tag'];
	$updated['add_to_cart'] = $form['add_to_cart'];
	$updated['initiate_checkout'] = $form['initiate_checkout'];
	$updated['purchase'] = $form['purchase'];
	return $updated;
}

?>
