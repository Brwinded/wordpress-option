<?php

/**
 * Plugin Name: OWN_PLUGIN
 */

/**
 * Личный плагин - дополнительные поля и функции
 */


//------------------------------------------------
// Проверка по городам

function white_space_replace_reverce( $var_str ){
		$var_out = $var_str;

		$regexp_find = '/[_]+/';
		$replace_str = ' ';

		if( preg_match($regexp_find, $var_str) ){
			$var_out = preg_replace($regexp_find, $replace_str, $var_str);
		}
		return $var_out;
 	}

function show_city(){
global $wp_query;
$city_name = htmlspecialchars(trim(  $wp_query->query_vars['cityname'] ));

	include 'citylist.php';

	if( empty($city_name) ){
		$this_geo_city_name = geot_city_name();
	} else {
		$this_geo_city_name = white_space_replace_reverce($city_name);
	}

	$out_city_name = 'в вашем городе';

	if( !empty($cityList[$this_geo_city_name]) ) {
		$out_city_name = $cityList[$this_geo_city_name];
	}

	return $out_city_name;
}
add_shortcode( 'showcity', 'show_city');



// if (empty($_SESSION['geo']) {
//   // get geo
// }
// // $_SESSION['geo'] exist


//------------------------------------------------
// Вставляем разметку schema.org на каждую страницу

function getproductbysku_campusboy( $atts ){

$city_for_title = show_city(); // DELETE

// Проверка по городам
//-------------------------------

    $html = '';
  
    if(!empty($atts['sku'])){
      
      global $post;
	  $title = stripslashes(get_post_meta($post->ID, '_yoast_wpseo_title', true));
	  
	  $title_city = $title . ' ' . $city_for_title;

      $seodesc = stripslashes(get_post_meta($post->ID, '_yoast_wpseo_metadesc', true));
      
      $product_id = wc_get_product_id_by_sku( $atts['sku'] );
      $new_product = new WC_Product($product_id);
      $price = $new_product->get_regular_price();
      
      $html = '
      	<script type="application/ld+json">
        {
         "@context": "http://schema.org/",
         "@type": "Product",
         "name": "' . $title_city .'",
         "description": "' . $seodesc . '",
         "sku": "' . $atts['sku'] . '",
         "aggregateRating": {
		    "@type": "AggregateRating",
		    "ratingValue": "4.7",
		    "reviewCount": "129"
		  },
         "offers": {
           "@type": "Offer",
           "priceCurrency": "RUB",
           "price": "' . $price . '",
           "priceValidUntil": "2020-10-01",
		   "itemCondition": "http://schema.org/UsedCondition",
           "availability": "http://schema.org/InStock",
           "seller": {
				"@type": "Organization",
				"name": "Строй Аттестат Мск"
			}
         }
        }
        </script>
      ' . $price;
    }
  
    return $html;
}
add_shortcode( 'getpid', 'getproductbysku_campusboy');

// Добавляем разметку schema.org на каждую страницу
//-------------------------------------------------------


//-------------------------------------------------------
// Добавляем новое поле
//

// Display Fields
add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );
// Save Fields
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

function woo_add_custom_general_fields() {

	global $woocommerce, $post;
	
	// СРОКИ
	woocommerce_wp_text_input(
		array(
		'id'          => '_text_field',
		'label'       => __( 'Сроки', 'woocommerce' ),
		'placeholder' => '',
		'desc_tip'    => 'true',
		'description' => __( 'Введите сроки', 'woocommerce' )
		)
	);
	// AmoCRM
	woocommerce_wp_text_input(
		array(
		'id'          => '_text_field_amo',
		'label'       => __( 'AmoCRM', 'woocommerce' ),
		'placeholder' => '',
		'desc_tip'    => 'true',
		'description' => __( 'Заявка AmoCRM', 'woocommerce' )
		)
	);
}

function woo_add_custom_general_fields_save( $post_id ){
	// Text Field
	$woocommerce_text_field = $_POST['_text_field'];
	if( !empty( $woocommerce_text_field ) )
		update_post_meta( $post_id, '_text_field', esc_attr( $woocommerce_text_field ) );

	// AmoCRP
	$woocommerce_text_field = $_POST['_text_field_amo'];
	if( !empty( $woocommerce_text_field ) )
		update_post_meta( $post_id, '_text_field_amo', esc_attr( $woocommerce_text_field ) );
}

//
// Добавляем новое поле
//-------------------------------------------------------



//-------------------------------------------------------
// Выводим строки из товаров на страницу
//

// СРОКИ
function getproducttime( $atts ){
    $product_id = null;
    $time = '';
    if(!empty($atts['sku'])){
        $product_id = wc_get_product_id_by_sku( $atts['sku'] );
    	// $new_product = new WC_Product($product_id);
    	$time = get_post_meta( $product_id, '_text_field', true);

    }
    return $time;
}
add_shortcode( 'time', 'getproducttime');

// Заявки AmoCRM
function getamobid( $atts ){
    $product_id = null;
    $amobid = '';

    if(!empty($atts['sku'])){
        $product_id = wc_get_product_id_by_sku( $atts['sku'] );
    	// $new_product = new WC_Product($product_id);
    	$amo_code = get_post_meta( $product_id, '_text_field_amo', true);
    	$amobid = sprintf('[amoforms id="%d"]', $amo_code);

    	// $amobid = str_replace("&quot;", "'", $amobid);
    	//[amoforms id=&quot;3&quot;]
    	// $amobid = do_shortcode( $amobid );
    	// var_dump($amobid);
    }

return $amobid;
}

add_shortcode( 'amobid', 'getamobid');


//-------------------------------------------------------
// ПЛАГИН НА СЛАЙДЕР ОТЗЫВОВ
//

function wpis_enqueue_scripts() {
	if (!is_admin()) {
		wp_enqueue_script('wpis-slick-js', plugins_url('assets/js/slick.min.js', __FILE__),array('jquery'),'1.6.0', false);
		wp_enqueue_script('wpis-front-js', plugins_url('assets/js/wpis.front.js', __FILE__),array('jquery'),'1.0', true);
		wp_enqueue_style('wpis-front-css', plugins_url('assets/css/wpis-front.css', __FILE__),'1.0', true);
	}
}
add_action( 'wp_enqueue_scripts', 'wpis_enqueue_scripts' ); 


function ebor_image_carou_shortcode_vc() {
	vc_map(
		array(
		'name' => __( 'Product Images Carousel', 'js_composer' ),
		'base' => 'vc_images_carou',
		'icon' => 'icon-wpb-images-carousel',
		'category' => __( 'Content', 'js_composer' ),
		'description' => __( 'Animated carousel for Product', 'js_composer' ),

		'params' => array(
				array(
					"type" => "textfield",
					"heading" => __("Subtitle", 'foundry'),
					"param_name" => "subtitle",
					'holder' => 'div',
					'description' => 'Default layout only.'
				),
				array(
					"type" => "textfield",
					"heading" => __("Main Class", 'foundry'),
					"param_name" => "mainclass"
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Widget title', 'js_composer' ),
					'param_name' => 'sku',
					'description' => __( 'Введите Артикул товара для слайдера).', 'js_composer' )
				)
			)
		) 
	);
}
add_action( 'vc_before_init', 'ebor_image_carou_shortcode_vc' );




function ebor_image_carou_shortcode( $atts, $content = null ) {
	extract( 
		shortcode_atts( 
			array(
				'subtitle' => '',
				'mainclass' => '',
				'sku' => ''
			), $atts 
		) 
	);

        $product_id = wc_get_product_id_by_sku( $atts['sku'] );
    	$new_product = new WC_Product($product_id);

		$image_title = esc_attr( get_the_title( get_post_thumbnail_id($product_id) ) );
		// $image_link  = wp_get_attachment_url( get_post_thumbnail_id($product_id) );
		$image       = get_the_post_thumbnail( $new_product, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
		// $image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
			'title' 		  => $image_title,
			// 'data-zoom-image' => $image_link,
			'id' 		      => 'zoom-image',
		));
	


		$attachment_ids   = $new_product->get_gallery_attachment_ids();
		$attachment_count = count( $attachment_ids);

		if ( $attachment_count > 0 ) {
			$gallery = '[wpis-gallery]';
		} else {
			$gallery = '';
		}
		

		ob_start();


		// WPIS FOR SLIDER
		echo '<div class="slider_about_us'.$mainclass.'">';
		echo '<h6 class="slider_about_us-feedback">' .$subtitle. '</h6>';
		echo '<div class="slider wpis-slider-for">';
		
		foreach( $attachment_ids as $attachment_id ) {
		   $imgfull_src = wp_get_attachment_image_src( $attachment_id,'full');
		   $image_src   = wp_get_attachment_image_src( $attachment_id,'shop_single');
		   echo '<div><img src="'.$image_src[0].'" /><a href="'.$imgfull_src[0].'" class="wpis-popup" data-rel="prettyPhoto' . $gallery . '">popup</a></div>';
		}
		
		echo '</div>';

		// WPIS NAV SLIDER
		if($gallery)
		{	
			echo '<div id="wpis-gallery" class="slider wpis-slider-nav">';
		
			// $fimgfull_src  = wp_get_attachment_image_src( $attachment_id,'full');
			// $fimgthumb_src = wp_get_attachment_image_src( $attachment_id,'shop_thumbnail');
			
			// echo '<div><a data-zoom-image="'.$fimgfull_src[0].'"><img src="'.$fimgthumb_src[0].'" /></a></div>';
			// echo '<p  class="HELLO1">'. var_dump($attachment_ids) .'</p>';

			foreach( $attachment_ids as $attachment_id ){
			   $fullimg_src  = wp_get_attachment_url( $attachment_id );
			   $thumbimg_src = wp_get_attachment_image_src( $attachment_id,'shop_thumbnail');
			   echo '<div><a data-zoom-image="'.$fullimg_src.'"><img src="'.$thumbimg_src[0].'" /></a></div>';
			}
			echo '</div>';
		}
		echo '</div>';

		$content = ob_get_contents();
		ob_end_clean();

		return $content;

}
add_shortcode( 'vc_images_carou', 'ebor_image_carou_shortcode' );

//
// ПЛАГИН НА СЛАЙДЕР ОТЗЫВОВ
//-------------------------------------------------------



//*******************************
// Добавляем в title город
//

function register_my_plugin_extra_replacements() {
    wpseo_register_var_replacement( '%%showcity%%', 'show_city', 'advanced' );
}
add_action( 'wpseo_register_extra_replacements', 'register_my_plugin_extra_replacements' );

//
// Добавляем в title город
//*******************************
