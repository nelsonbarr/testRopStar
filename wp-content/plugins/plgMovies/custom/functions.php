<?php
/**
 * Functions.php
 *
 * @package  PlgMovies
 * @author   Nelson Barraez
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


//AGREGO EL CAMPO PERSONALIZADO PARA PELICULA
add_action( 'woocommerce_before_add_to_cart_button', 'woocommerce_anadir_custom_field', 10 );
function woocommerce_anadir_custom_field() {
	global $woocommerce, $post;

	if(!function_exists('woocommerce_wp_text_input') && !is_admin())
		include_once(WC()->plugin_path() . '/includes/admin/wc-meta-box-functions.php');

    echo '<div class="product_custom_field">';
    
	// CREACION DE INPUT DE PELICULA 
    woocommerce_wp_text_input(
        array(
            'id' => '_cstmProductMovie',
            'placeholder' => 'Selecciona la pelicula en el listado de peliculas disponibles..',
            'label' => __('Pelicula Seleccionada: ', 'woocommerce'),
            'desc_tip' => 'true',
			'readonly'=>'true'
        )
    );
    
    echo '</div>';
}

// ALMACENO EL VALOR DE LA PELICULA SELECCIONADA
add_filter( 'woocommerce_add_cart_item_data', 'add_custom_cart_item_data', 20, 2 );
function add_custom_cart_item_data( $cart_item_data, $product_id ){
	//VERIFICO QUE EL CAMPO VENGA DISTINTO DE VACIO Y EXISTA
    if( isset($_POST['_cstmProductMovie']) && ! empty($_POST['_cstmProductMovie'])) {
        $cart_item_data['productMovie']= array(
            'value' => esc_attr($_POST['_cstmProductMovie']),
            'unique_key' => md5( microtime() . rand() ), // <= Make each cart item unique
        );
    }
    return $cart_item_data;
}


// MUESTRO EL VALOR DE LA PELICULA SELECCIONADA EN LA PAGINA DE CHECKOUT
add_filter( 'woocommerce_get_item_data', 'display_custom_cart_item_data', 10, 2 );
function display_custom_cart_item_data( $cart_item_data, $cart_item ) {
    if ( isset( $cart_item['productMovie']['value'] ) ){
        $cart_item_data[] = array(
            'name' => __( 'Pelicula Seleccionada', 'woocommerce' ),
            'value' => $cart_item['productMovie']['value'],
        );
    }
    return $cart_item_data;
}

//AGREGO EL VALOR DEL CUSTOM PELICULA SELECCIONADA AL DETALLE DE LA ORDEN
function cstm_order_meta_handler( $item_id, $values, $cart_item_key ) {	
    if( isset( $values['productMovie'] ) ) {
        wc_add_order_item_meta( $item_id, "productMovie", $values['productMovie'] );
    }
}
add_action( 'woocommerce_add_order_item_meta', 'cstm_order_meta_handler', 1, 3 );

//MUESTRA LA PELICULA SELECCIONADA EN EL DETALLE DE PRODUCTOS DE LA ORDEN
function cstm_woocommerce_order_item_name( $name, $item ){
	
    $productMovie = $item['productMovie'];
	
    $name .= ' <label>(<b>Pelicula Seleccionada:</b> ' . $productMovie["value"] . ') </label>';    

    return $name;
}
add_filter( 'woocommerce_order_item_name', 'cstm_woocommerce_order_item_name', 10, 2 );


//VALIDACION PARA LA SELECCION DE PELICULA CUANDO ALGUN GENERO ESTE SELECCIONADO
function cstm_movie_validation( $passed ) { 	
	if($_REQUEST['attribute_pa_generos']!='nothing' && !empty($_REQUEST['attribute_pa_generos'])){
		if ( empty( $_REQUEST['_cstmProductMovie'] )) {
			wc_add_notice( __( 'Debe seleccionar una pelicula.', 'woocommerce' ), 'error' );
			$passed = false;
		}
	}
	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'cstm_movie_validation', 10, 5 );  

//MUESTRO LA PELICULA SELECCIONADA EN EL RESUMEN DE LA ORDEN DEL PANEL ADMINISTRATIVO
add_action( 'woocommerce_before_order_itemmeta', 'cstm_before_order_itemmeta', 10, 3 );
function cstm_before_order_itemmeta( $item_id, $item, $product ){
    // Only "line" items and backend order pages
    if( ! ( is_admin() && $item->is_type('line_item') ) ) return;
    $productMovie = $item->get_meta('productMovie');
    if( ! empty($productMovie) ) {
        echo "Pelicula Seleccionada: ".$unit["productMovie"];
    }
}