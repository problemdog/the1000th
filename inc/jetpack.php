<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package the1000th LinkBlog
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function the1000th_link_blog_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'the1000th_link_blog_jetpack_setup' );
