<?php
/*
Plugin Name: Simple Likes
Description: 
Version: 1.0
Author: Mild Media
Author URI: 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

function simple_ajax_like() {
	if ( isset( $_POST['pid'] ) && is_numeric( $_POST['pid'] ) ) {
		$pid = (int)$_POST['pid'];
        $likes = (int)get_post_meta( $pid, 'likes', true ) > 0 ? (int)get_post_meta( $pid, 'likes', true ) : 0;

        $decoded = is_array(json_decode($_COOKIE['simple_likes'])) ? json_decode($_COOKIE['simple_likes']) : array();
        if( !in_array($pid, $decoded) ) {
            //set
            $expiry = strtotime("+1 months");
            array_push($decoded, $pid);
            setcookie('simple_likes', json_encode($decoded), $expiry, '/', '');

            //increase
            update_post_meta( $pid, 'likes', ++$likes );

            echo json_encode( array( 'likes' => $likes, 'status' => 'success', 'action' => 'like' ) );
        } else {
            echo json_encode( array( 'status' => 'error' ) );
        }
	}
	die;
}
add_action( 'wp_ajax_simple-like', 'simple_ajax_like' );
add_action( 'wp_ajax_nopriv_simple-like', 'simple_ajax_like' );


function simple_ajax_unlike() {
	if ( isset( $_POST['pid'] ) && is_numeric( $_POST['pid'] ) ) {
		$pid = (int)$_POST['pid'];
        $likes = (int)get_post_meta( $pid, 'likes', true );

        $decoded = json_decode($_COOKIE['simple_likes']);
        if( in_array($pid, $decoded) ) {
            //remove
            $expiry = strtotime("first day of last month");
            setcookie('simple_likes', '', $expiry, '/', '');

            update_post_meta( $pid, 'likes', --$likes );

            echo json_encode( array( 'status' => 'success', 'action' => 'unlike' ) );
        } else {
            echo json_encode( array( 'status' => 'error' ) );
        }
	}
	die;
}
add_action( 'wp_ajax_simple-unlike', 'simple_ajax_unlike' );
add_action( 'wp_ajax_nopriv_simple-unlike', 'simple_ajax_unlike' );


function simple_add_button( $content ) {
	global $post;
	$post_id = $post->ID;
    $likes = (int)get_post_meta( $post_id, 'likes', true );

    $output = '<div class="likes-container">';

    $decoded = json_decode($_COOKIE['simple_likes']);
    if( !in_array($post_id, $decoded) ) {
        $output .= '<a data-action="simple-like" data-pid="' . $post_id . '" class="simple-like-btn like" title="Like" href="#">Like</a>';
	} else {
        if($likes == 1) {
            $output .= '<a data-action="simple-unlike" data-pid="' . $post_id . '" class="simple-like-btn liked" title="Like" href="#">You like this post</a>';
        } else {
            $output .= '<a data-action="simple-unlike" data-pid="' . $post_id . '" class="simple-like-btn liked" title="Like" href="#">You and '.--$likes.' other people like this post</a>';
        }
    }
    $output .= '</div>';

    if(is_single() && 'post' == get_post_type()) {
        return $content . $output;
    } else {
        return $content;
    }
}
add_filter( 'the_content', 'simple_add_button' );

function simple_head() {	
    wp_enqueue_style( 'simple-like-styles', plugin_dir_url( __FILE__ ) . '/css/style.css',false,'1.0.4','all');

	wp_enqueue_script( 'simple-like-actions', plugin_dir_url( __FILE__ ) . 'js/like-actions.js', 'jquery', '1.0.9' );
	wp_localize_script( 'simple-like-actions', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'simple_head', 80 );
