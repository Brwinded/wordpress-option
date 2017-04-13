<?php 

/**
 * Define theme folder URL, saves querying the template directory multiple times.
 */
define('EBOR_THEME_DIRECTORY', esc_url(trailingslashit( get_template_directory_uri() )));

/**
 * Ebor Framework
 * Queue Up Theme-Side Framework, everything else is loaded in the ebor-framework plugin.
 * 
 * You can install a child theme to modify all aspects of the theme, if you need to modify anything from the /admin/ folder
 * Just delete the require line below and move it to the functions.php of your child theme, make sure to copy over the entire /admin/ folder too.
 * It's very rare you'd need to do that, but if you do, you'll need to delete this require on every theme update.
 * 
 * Note that to override a function from the /admin/ folder, you don't need to copy the folder to your child theme, every function is wrapped
 * in a conditional so that it can be called directly from your child theme and ignored in the parent theme.
 * 
 * @since 1.0.0
 * @author TommusRhodus
 */
get_template_part("admin/init");

/**
 * If visual composer is installed, grab all required files.
 * Wrapped in an if statement so that we can save parsing this if visual composer is not used.
 * It's a speed boost basically.
 */
if( function_exists('vc_set_as_theme') ){
	get_template_part("vc_init");
}

/**
 * Please use a child theme if you need to modify any aspect of the theme, if you need to, you can add code
 * below here if you need to add extra functionality.
 * Be warned! Any code added here will be overwritten on theme update!
 * Add & modify code at your own risk & always use a child theme instead for this!
 */



// add_action('init', 'do_rewrite_test');
// function do_rewrite_test(){
//     // Правило перезаписи
//     add_rewrite_rule( '^city/([^/]*)/([^/]*)/?', 'index.php?p=12&food=$matches[1]&variety=$matches[2]', 'top' );

//     // скажем WP, что есть новые параметры запроса
//     add_filter( 'query_vars', function( $vars ){
//         $vars[] = 'food';
//         $vars[] = 'variety';
//         return $vars;
//     } );
// }

 function do_rewrite_myurl(){
  add_rewrite_tag('%cityname%', '([^&]+)');
  add_rewrite_rule('^((city|itr|rab|licenzii)(\/)?([A-Za-z_0-9]+)?)(\/)?([A-Za-z_0-9]+)?$', 'index.php?pagename=$matches[1]&cityname=$matches[6]', 'top');
//add_rewrite_rule('(city)/([A-Za-z_0-9]+)?(/)?([A-Za-z_0-9]+)?$', 'index.php?pagename=$matches[1]&country=$matches[2]&cityname=$matches[4]', 'top');
  //add_rewrite_rule('((city|itr|rab)(/[A-Za-z_0-9]+)?)(/)?([A-Za-z_0-9]+)?$', 'index.php?pagename=$matches[2]&cityname=$matches[4]', 'top');
  //add_rewrite_rule('(city/pages)/([A-Za-z_0-9]+)?$', 'index.php?cityname=$matches[1]&page=$matches[2]', 'top');
  flush_rewrite_rules();
  //city/bdd/
  //city/country/sity_name

}
add_action('init', 'do_rewrite_myurl');



/*
function citygeo(){
    // global $wp_query;
    // $pagename = htmlspecialchars(trim( $wp_query->query_vars['pagename']  ));
    // $city_name = htmlspecialchars(trim(  $wp_query->query_vars['cityname'] ));
  
  //$country = htmlspecialchars(trim( get_query_var('country') ));
  //$city_name = htmlspecialchars(trim( get_query_var('cityname') ));
  
  $err = 0;
//   $get_country = geot_country_code();
  $return_html = '';
  $err_html ='';
  
  //$page = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $city_name_go = geot_city_name();
  
  //$site_url = 'http://' . $_SERVER['HTTP_HOST'] .'/city/'. $country_go .'/'. $city_name_go;
  $site_url = '/' . $pagename .'/'. $city_name_go;
    $return_html .= '';
  //$return_html .= '$country: '. $country .', ';
  //$return_html .= '$city_name: '. $city_name .', ';

  //$get_country = 'RU'; //потом поменять на функцию
  
  if( isset($city_name) && $city_name != '' ){

    $return_html .= '<h1>'. $city_name .'</h1>';

    // а что надо сделать на все страницы одно и тоже только генерировать текст?
    
    //$return_html .= ' country: '. $country;
    //$return_html .= ' get_country: '. $get_country;
    
  } else {
    //header('Location: '. $site_url, true, 301 );
    exit("<meta http-equiv='refresh' content='0; url= ". $site_url ."'>"); // точки не нужны ? . $site . ?
    //exit();
    $err++;
    $err_html .='нет  города';
  }

  if( $err !== 0){
    $return_html .= $err_html;
  }
  return $return_html; //
}
add_shortcode( 'citygeoshow', 'citygeo');

*/

// ----------------------------------------------------

remove_action('wp_head', 'wp_generator');

add_action('init', 'optimize_fixwp_head', 100);
function optimize_fixwp_head() {
   remove_action('wp_head', array(visual_composer(), 'addMetaData')); 
}

/**
 * ДОБАВЛЯЕМ ФУНКЦИЮ ВЫДАЧИ: ID картинки по ее URL
 */

function get_img_id_for_Yarik( $url = null ){
    global $wpdb;

    if( ! $url )
        return false;

    // Получем имя + расширение файла
    $name = explode('/', $url);
    $name = end($name);

    // Избавляемся от расширения
    $name = preg_replace('~\.[^.]+$~', '', $name );
    $name = sanitize_file_name( $name );
    $name = sanitize_title( $name );

    // таблица постов, там же перечисленны и медиафайлы
    $table  = $wpdb->prefix . 'posts';

    // Запрос в базу по полю post_name и post_type. Поля индексирумые, а значит поиск по ним очень быстрый
    $attachment_id = $wpdb->get_var( 
      $wpdb->prepare( "SELECT ID FROM $table WHERE post_name = %s AND post_type = 'attachment'", $name )
    );

    return $attachment_id ? (int)$attachment_id : false;
}

// get_img_id_for_Yarik( http://web.com );