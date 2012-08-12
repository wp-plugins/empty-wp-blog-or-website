<?php
/*
Plugin Name: Empty WP Blog
Plugin URI: http://wordpress.org/extend/plugins/empty-wp-blog-or-website
Description: One click solution for make your blog/website empty. Delete all your posts, pages, media(images,videos,etc) , tags and categories.
Version: 1.0
Author: Anoop M C
Author Email: anoopmmc@gmail.com
Author URI: http://fb.com/anoopmc
*/
add_action('admin_menu', 'delete_menus');

function delete_menus() {
	add_options_page('Empty WP Blog Options', 'Empty WP Blog', 'manage_options', 'empty-wp-blog', 'empty_wp_options');
}

function empty_wp_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	if( isset( $_POST['delete_whole'] ) ) {

		echo "<h3>Deleting posts/pages.. please wait...</h3>";

		$options = array(
			'numberposts'     => 50,
			'offset'          => 0,
			'orderby'         => 'post_date',
			'order'           => 'DESC',
			'post_type'       => 'post',
			'post_status'     => 'publish'
		);

		$statuses = array ( 'publish', 'draft', 'trash', 'inherit' );
		$types = array( 'attachment', 'post', 'page');

		foreach( $types as $type ) {
			foreach( $statuses as $status ) {
				$options['post_type'] = $type;
				$options['post_status'] = $status;
				delete_posts( $options );
			}
		}
		echo "
<h3>Deleting categories.. please wait...</h3>";
		$cats = get_all_category_ids();
		foreach( $cats as $cat ) {
			wp_delete_category( $cat );
			
		}

		echo "
<h3>Deleting tags.. please wait...</h3>";
		$tags = get_terms( 'post_tag', array( 'hide_empty' => false, 'fields' => 'ids' ) );
		if ($tags) {
			foreach($tags as $tag) {
				echo "Tag: $tag";
				wp_delete_term( $tag, 'post_tag' );

			}
		}
		echo 'Congratulations all your posts, pages, media, tags and categories deleted. Start a fresh blog today!';
	}
	else {
		echo "
<h2>Empty WP Blog/Website</h2>";
		echo "<p>By pressing the button below you agree to delete your blog of pages, posts, attachments, comments, categories, and tags. Thie action can't be restored.<br /><b>Don't press unless you really want to empty your Website/Blog!</b></p>";
		?><input class="button" type="button" name="button_donate" value="Donate Me" onclick="document.location='https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=anoopmmc%40gmail%2ecom&lc=US&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest'" /><?php 
		echo '<form method="post" onsubmit="return confirm(\'Are you sure?\')">';
 echo '<input id="delete_whole" class="button" type="submit" name="delete_whole" value="Click here to Empty your Website/Blog" />';
 echo '</form>';
	}
}

function delete_posts( $options ) {
	$posts = get_posts( $options );
	$offset = 0;
	while( count( $posts ) > 0 ) {
		if( $offset == 10 ) {
			break;
		}
		$offset++;
		foreach( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
		$posts = get_posts( $options );
	}
}

?>