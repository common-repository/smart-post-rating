<?php

/*
Plugin Name: Smart Post Rating
Plugin URI: http://kabelpro.dk/
Description: A post rating system for wordpress.
Tags: Post rating, rating, post, page, star rating
Version: 1.0.0
Author: Kjeld Hansen
Author URI: #
Requires at least: 4.0
Tested up to: 4.7
Text Domain: smart-post-rating
*/

if ( ! defined( 'ABSPATH' ) ) exit; 
 
add_action('admin_menu','smart_post_rating_admin_menu');
function smart_post_rating_admin_menu() { 
    add_menu_page(
		"Smart Rating",
		" Post Rating",
		8,
		__FILE__,
		"smart_post_rating_admin_menu_list","","post-rating"/*,
		plugins_url( 'images/plugin-icon.png', __FILE__) */
	); 
}
function smart_post_rating_admin_menu_list(){
	?> <h2>Post Rating</h2>
	<div class="spr-wrap">	
		&lt;?php echo do_shortcode('[smart-post-rating]'); ?>
        <h2>Output : </h2>
	<?php
	 echo do_shortcode('[smart-post-rating]');
	echo '</div>';
}




	
function smart_post_rating_scripts_js() { 
	//wp_enqueue_script( 'spr-jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js', array( 'jquery' ), '1.0.0', true );
	wp_enqueue_script( 'smart-post-rating-jquery', plugins_url( '/spr.js', __FILE__), array( 'jquery' ), '1.0.0', true );
    wp_enqueue_style( 'smart-post-rating-css', plugins_url( '/spr.css', __FILE__) );
}
add_action( 'wp_enqueue_scripts', 'smart_post_rating_scripts_js' );

//intval($_POST['pcat']); sanitize_text_field($_POST['ftype']);

add_shortcode('smart-post-rating', 'smart_post_rating_fun');
function smart_post_rating_fun(){ 
$resp='';
	if(isset($_POST['smprt']) && $_POST['smprt']!=''){
		$rate = intval($_POST['smprt']); $userID = wp_get_current_user()->ID; 
		if(add_post_meta( get_the_ID(), 'ri_spost_rating',  $userID )){
			if(add_user_meta( $userID, 'ri_spost_rating_'.get_the_ID(),  $rate )){ $resp = 'Success!'; }
		}
	}
$postrating = '
<div class="riquickContact">
	<p>Rating : </p>';
	
	if(is_user_logged_in()){
		$rateMeta = get_post_meta( get_the_ID(), 'ri_spost_rating', false ); $f=0;
		foreach($rateMeta as $rmt){ if($rmt==wp_get_current_user()->ID){ $f=1; } }
		if($f == 1){
			//update_post_meta( get_the_ID(), 'ri_spost_rating',  wp_get_current_user()->ID );
			$resp = 'You have rated';
		}else{
			$postrating .= '
			<form id="smprtf" method="post" action="">
				<div class="star-rating">
					<input type="hidden" id="rateval" name="smprt" value="" />
					<div class="stars">
						<a class="star" title="1"></a><a class="star" title="2"></a><a class="star" title="3"></a><a class="star" title="4"></a><a class="star" title="5"></a>
					</div>
				</div>
				<input type="submit" name="sub" value="Rate" />
			</form>';
		}
	}else{
		$postrating .= '<a href="/login" class="rifancybox">Login</a>';
		$args = array(
			'echo'           => true,
			'remember'       => true,
			'redirect'       => ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'form_id'        => 'loginform_rate',
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Log In' ),
			'value_username' => '',
			'value_remember' => false
		);
		wp_login_form( $args );
	}
	
	
	$postrating .= $resp.'
</div>

';
/*<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript" src="'.plugins_url( '/spr.js', __FILE__).'"></script>*/


return $postrating;
}

function spr_add_to_content( $content ) {    
    if( is_single() ) {
        $content .= smart_post_rating_fun();
    }
    return $content;
}
add_filter( 'the_content', 'spr_add_to_content' );





