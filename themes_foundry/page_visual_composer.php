<?php 
	
//------------------------------------------------
// ПЕРЕАДРЕСАЦИЯ ПО ГОРОДАМ

//----------------------------
// Определяем бот это или нет
function is_bot(){
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $options = array(
            'YandexBot', 'YandexAccessibilityBot', 'YandexMobileBot','YandexDirectDyn',
            'YandexScreenshotBot', 'YandexImages', 'YandexVideo', 'YandexVideoParser',
            'YandexMedia', 'YandexBlogs', 'YandexFavicons', 'YandexWebmaster',
            'YandexPagechecker', 'YandexImageResizer','YandexAdNet', 'YandexDirect',
            'YaDirectFetcher', 'YandexCalendar', 'YandexSitelinks', 'YandexMetrika',
            'YandexNews', 'YandexNewslinks', 'YandexCatalog', 'YandexAntivirus',
            'YandexMarket', 'YandexVertis', 'YandexForDomain', 'YandexSpravBot',
            'YandexSearchShop', 'YandexMedianaBot', 'YandexOntoDB', 'YandexOntoDBAPI',
            'Googlebot', 'Googlebot-Image', 'Mediapartners-Google', 'AdsBot-Google',
            'Mail.RU_Bot', 'bingbot', 'Accoona', 'ia_archiver', 'Ask Jeeves',
            'OmniExplorer_Bot', 'W3C_Validator', 'WebAlta', 'YahooFeedSeeker', 'Yahoo!',
            'Ezooms', '', 'Tourlentabot', 'MJ12bot', 'AhrefsBot', 'SearchBot', 'SiteStatus',
            'Nigma.ru', 'Baiduspider', 'Statsbot', 'SISTRIX', 'AcoonBot', 'findlinks',
            'proximic', 'OpenindexSpider','statdom.ru', 'Exabot', 'Spider', 'SeznamBot',
            'oBot', 'C-T bot', 'Updownerbot', 'Snoopy', 'heritrix', 'Yeti',
            'DomainVader', 'DCPbot', 'PaperLiBot'
        );

        foreach($options as $row) {
            if (stripos($_SERVER['HTTP_USER_AGENT'], $row) !== false) {
                return true;
            }
        }
    }

    return false;
}
// Определяем бот это или нет
//--------------------------

// Ловим название родительской страницы
	global $wp_query;
	$pagename = get_query_var('pagename');
	$pagename_parent = get_permalink($post->post_parent);
	$parent = explode('/', $pagename_parent)[3];
	$pre_parent = explode('/', $pagename_parent)[4];
    // Если ЛИЦЕНЗИЯ МЧС - включаем вторую родительскую в редирект
    if($pre_parent == 'licenziya-mchs') {
        $super_parent = $parent .'/'. $pre_parent;
    } else {
        $super_parent = $parent;
    }

    $host  = $_SERVER['HTTP_HOST'];

// Находим в названии города пробел и заменяем его на _ и подставляем в ссылку

	function white_space_replace( $var_str ){

		$var_out = $var_str;
		$regexp_find = '/[\s\'\/]+/';
		$replace_str = '_';

		if( preg_match($regexp_find, $var_str) ){
			$var_out = preg_replace($regexp_find, $replace_str, $var_str);
		}
		return $var_out;
 	} // Проверяем есть ли пробел и заменяем на _


// Делаем переадресацию на город
//    $find_att = "/(itr-specialnosti|rabochie-specialnosti)/i";
//    $find_att = "/(itr-specialnosti|rabochie-specialnosti|licenzii)[\/A-Za-z-_]+/";
    $find_att = "/(itr-specialnosti|rabochie-specialnosti|licenzii)/i";
	if( preg_match( $find_att, $parent ) ){

        $city_name = htmlspecialchars(trim(  $wp_query->query_vars['cityname'] ));

		if( empty( $city_name ) ){

//		    $check_is_bot = is_bot();
            if ( !is_bot() ) {
            // Записываем данные города в сессию
                if( !isset($_COOKIE['geot_city_ru']) ){ // Если в сессии нет города
                    $city_name_go = geot_city_name(); // Запрашиваем город по IP
                    $city_name_go = white_space_replace($city_name_go); // Проверяем на пробелы и заменяем
                    setcookie('geot_city_ru', $city_name_go, time()+4320000 ); // Передаем город в сессию
                    $link_city = $_COOKIE['geot_city_ru'] . '/';
                } else {
                    $link_city = $_COOKIE['geot_city_ru'] . '/';
                }
                if ( !( $pagename == 'licenzii' || $pagename == 'itr-specialnosti' || $pagename == 'rabochie-specialnosti' ) ) {
                     header("Location: https://$host/$super_parent/$pagename/$link_city"); // Делаем редирект на новую ссылку
                }
            }
	    }

        //----------------------------------
        // Добавляем в title город
        //
        function register_my_plugin_extra_replacements() {
            wpseo_register_var_replacement( '%%showcity%%', 'show_city', 'advanced' );
        }
        add_action( 'wpseo_register_extra_replacements', 'register_my_plugin_extra_replacements' );
        //
        // Добавляем в title город
        //----------------------------------
	}


// ПЕРЕАДРЕСАЦИЯ ПО ГОРОДАМ
//--------------------------------------------------

//
// ACF Shortcodes
//-------------------------------------------------------



	get_header();
// <!--# include virtual="/wp-content/themes/foundry/ssi/uncached.php" wait="no" -->
    the_post();


$thumbnail = false;
	if( has_post_thumbnail() ){
		$thumbnail = wp_get_attachment_image( get_post_thumbnail_id(), 'full', 0, array('class' => 'background-image') );
	}
	
	echo ebor_get_page_title( 
		get_the_title(), 
		get_post_meta($post->ID, '_ebor_the_subtitle', 1), 
		get_post_meta($post->ID, '_ebor_page_title_icon', 1), 
		$thumbnail, 
		get_post_meta($post->ID, '_ebor_page_title_layout', 1) 
	);
?>

<div class="ebor-page-wrapper">
	<a id="home" class="in-page-link" href="#"></a>
	<?php the_content(); ?>
</div>

<?php
	get_footer();
