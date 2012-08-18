<?php
/*
Plugin Name: Empty WP Blog
Plugin URI: http://wordpress.org/extend/plugins/empty-wp-blog-or-website
Description: One click solution for make your blog/website empty. Delete all your posts, pages, media(images,videos,etc) , tags and categories.
Version: 1.1
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
/*Program Ends Here*/
if(is_admin()) {
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
		echo "<h3>Deleting categories.. please wait...</h3>";
		$cats = get_all_category_ids();
		foreach( $cats as $cat ) {
			wp_delete_category( $cat );
			
		}
		echo "<h3>Deleting Users.. please wait...</h3>";
		
		$users = get_users();
    foreach($users as $user) 
	{
			if($user->ID==1)
			{echo '<h3 style="color:red;">User '.$user->user_login.' and email: '.$user->user_email.' can\'t be deleted.</h3>';continue; }
			wp_delete_user( $user->ID,'' );
		}

		echo "<h3>Deleting tags.. please wait...</h3>";
		$tags = get_terms( 'post_tag', array( 'hide_empty' => false, 'fields' => 'ids' ) );
		if ($tags) {
			foreach($tags as $tag) {
				echo "Tag: $tag";
				wp_delete_term( $tag, 'post_tag' );

			}
		}
		echo 'Congratulations all your posts, pages, media, tags and categories deleted. Start a fresh blog today!';
	/*Program Ends Here*/
	}
	else
	{
	wp_die( 'You are not authorised to perform this action', 'Permission denied' );
	}
	}
	else {
		echo "
<h2>Empty WP Blog/Website</h2>";
		echo "<p>By pressing the button below you agree to delete your blog of pages, posts, attachments, comments, categories, and tags. Thie action can't be restored.<br /><b>Don't press unless you really want to empty your Website/Blog!</b></p>";
		?><?php 
		echo '<form method="post" onsubmit="return confirm(\'Are you sure?\')">';
 echo '<input id="delete_whole" class="button" type="submit" name="delete_whole" value="Click here to Empty your Website/Blog" />';
 echo '</form><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>';?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_donations">
<input type="hidden" name="business" value="anoopmmc@gmail.com">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="amount" value="10.00">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<?php
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