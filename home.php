<?php
/**
 * Home template — carica la front-page canvas.
 * Serve per mostrare front-page.php con "Your latest posts" attivo.
 */
defined( 'ABSPATH' ) || exit;

// Se stiamo visualizzando l'archivio del blog, carica il template page-blog.php
if ( is_home() && ! is_front_page() && file_exists( get_stylesheet_directory() . '/page-blog.php' ) ) {
	require get_stylesheet_directory() . '/page-blog.php';
	exit;
}

if ( file_exists( get_stylesheet_directory() . '/front-page.php' ) ) {
	require get_stylesheet_directory() . '/front-page.php';
	exit;
}
get_header();
echo '<h1>' . esc_html( get_bloginfo( 'name' ) ) . '</h1>';
get_footer();