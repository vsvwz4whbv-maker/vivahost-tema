<?php
/**
 * Home template — carica la front-page canvas.
 * Serve per mostrare front-page.php con "Your latest posts" attivo.
 */
defined( 'ABSPATH' ) || exit;

if ( file_exists( get_stylesheet_directory() . '/front-page.php' ) ) {
	require get_stylesheet_directory() . '/front-page.php';
	exit;
}
get_header();
echo '<h1>' . esc_html( get_bloginfo( 'name' ) ) . '</h1>';
get_footer();