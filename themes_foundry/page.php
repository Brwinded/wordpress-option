<?php 
	get_header();


global $wp_query;
$pagename = get_query_var('pagename');
$city_name = htmlspecialchars(trim(  $wp_query->query_vars['cityname'] ));

if( preg_match("/(city|itr|rab)/i", $pagename) ){

    if( !empty( $city_name ) ){
     $city_name_go = geot_city_name();
     $site_url = '/' . $pagename .'/'. $city_name_go;
     exit("<meta http-equiv='refresh' content='0; url= ". $site_url ."'>"); 
    } else {
        //echo 'всё ок';
       //$query_post = get_post(205);
       //echo do_shortcode($query_post->post_content);             
    }
}



	while ( have_posts() ) : the_post();
	



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

	<section id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="container">
		    <div class="row">
		        <div class="col-sm-12 post-content">
		        	<?php
		        		the_content();
		        		wp_link_pages();
		        	?>
		        </div>
		    </div>
		</div>
	</section>
	
<?php 
	endwhile;
	get_footer();