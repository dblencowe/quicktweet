<?php
/**
 * @package Twitter_new_posts
 * @author Dave Blencowe
 * @version 1.0.0
 */
/**
 * @Plugin Name: Post new posts to twitter
 * @Plugin URI: http://www.syntaxmonster.net
 * @Description: This plugin will allow you to post updates to your twitter account
 * whenever you publish a post.
 * Remember to insert your username/password in the plugin file.
 * Old posts will be twittered on activation
 * @Author URI: http://www.syntaxmonster.net
*/

/* Add the hook for posting
 * Wordpress triggers "actions" as it processes data. The below directive
 * means that when a user publishes a post the function listed will be executed.
 * in this case it is the new_post_to_twitter function
*/
add_action('publish_post', 'post_to_twitter');

/**
 *
 * @param Int $post_ID
 * This function forms the core of the plugin.
 */
function post_to_twitter($post_ID)
{
	// The username and password of the twitter account you would like to Tweet to
	$username = 'USERNAME';
	$password = 'PASSWORD';

	// Load the wordpress query functions
	global $wp_query, $wpdb;

	// Get the post that we just published from the database
	$post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '$post_ID'", ARRAY_A);

	// The message that will appear on Twitter
	$msg = 'New post: '.$post['post_title'].' @ '.$post['guid'].'';

	// Construct the data that we will send to Twitter
	// This has to be layed out in a specific way and include authorization information
	$out  = "POST http://twitter.com/statuses/update.json HTTP/1.1\r\n";
	$out .= "Host: twitter.com\r\n";
	$out .= "Authorization: Basic ".base64_encode (''.$username.':'.$password.'')."\r\n";
	$out .= "Content-type: application/x-www-form-urlencoded\r\n";
	$out .= "Content-length: ".strlen ("status=$msg")."\r\n";
	$out .= "Connection: Close\r\n\r\n";
	$out .= "status=$msg";

	// Open a socket to Twitter
	$fp = fsockopen ('twitter.com', 80);

	// Write the data in the socket, this is our message ($out)
	fwrite ($fp, $out);

	// Close the socket
	fclose ($fp);
}
?>
