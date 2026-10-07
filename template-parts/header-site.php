<?php
/**
 * Template Part: Header del sito VivaHost
 * Incluso da front-page.php via get_template_part().
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;

$logo_src = '';
$logo_id  = get_theme_mod( 'custom_logo' );
if ( $logo_id ) {
	$img = wp_get_attachment_image_src( $logo_id, 'full' );
	if ( ! empty( $img[0] ) ) $logo_src = $img[0];
}
if ( ! $logo_src ) $logo_src = VH_URL . '/assets/images/vivahost-logo.png';

$cta_text = esc_html( vh_mod( 'header_cta_text', 'Avaliar minha hospedagem' ) );
$cta_link = esc_url( vh_mod( 'header_cta_link', '#contato' ) );
$home_url = esc_url( home_url( '/' ) );
?>
<header class="vivahost-header fixed top-0 left-0 right-0 z-50 transition-all duration-300" id="vivahost-header">
  <div class="header-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
    <div class="flex items-center">
      <a href="<?php echo $home_url; ?>" aria-label="<?php bloginfo( 'name' ); ?>" class="inline-flex items-center">
        <img src="<?php echo esc_url( $logo_src ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="brand-logo h-11 sm:h-12 w-auto object-contain transition-opacity hover:opacity-90" width="160" height="68">
      </a>
    </div>

    <nav class="vivahost-nav hidden md:flex items-center gap-1" aria-label="Navegação principal">
      <a href="<?php echo $home_url; ?>#como-funciona" class="btn btn-ghost btn-sm text-sm font-medium rounded-lg">Como funciona</a>
      <a href="<?php echo $home_url; ?>#servicos" class="btn btn-ghost btn-sm text-sm font-medium rounded-lg">Serviços</a>
      <a href="<?php echo esc_url( home_url( '/imoveis/' ) ); ?>" class="btn btn-ghost btn-sm text-sm font-medium rounded-lg">Imóveis</a>
      <a href="<?php echo $home_url; ?>#sobre-nos" class="btn btn-ghost btn-sm text-sm font-medium rounded-lg">Sobre</a>
      <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn-ghost btn-sm text-sm font-medium rounded-lg">Blog</a>
      <a href="<?php echo $home_url; ?>#contato" class="btn btn-ghost btn-sm text-sm font-medium rounded-lg">Contato</a>
    </nav>

    <div class="flex items-center gap-2">
      <a href="<?php echo $cta_link; ?>" class="btn btn-primary btn-brand-coral btn-sm rounded-full px-5 text-white font-bold shadow-md hover:shadow-lg transition-all header-cta" data-vh="header_cta_text"><?php echo $cta_text; ?></a>
      <button class="vivahost-nav-toggle md:hidden flex items-center justify-center w-10 h-10 rounded-lg hover:bg-black/5 transition-colors" id="vh-nav-toggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="vh-mobile-nav">
        <svg id="vh-icon-menu" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        <svg id="vh-icon-close" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
  </div>
</header>
<nav class="vivahost-mobile-nav fixed inset-0 z-40 bg-white/95 backdrop-blur-xl flex flex-col justify-center items-center gap-4" id="vh-mobile-nav" aria-hidden="true">
  <a href="<?php echo $home_url; ?>#como-funciona" class="vh-mobile-link text-2xl font-bold text-neutral hover:text-primary transition-colors py-2">Como funciona</a>
  <a href="<?php echo $home_url; ?>#servicos"      class="vh-mobile-link text-2xl font-bold text-neutral hover:text-primary transition-colors py-2">Serviços</a>
  <a href="<?php echo esc_url( home_url( '/imoveis/' ) ); ?>" class="vh-mobile-link text-2xl font-bold text-neutral hover:text-primary transition-colors py-2">Imóveis</a>
  <a href="<?php echo $home_url; ?>#sobre-nos"     class="vh-mobile-link text-2xl font-bold text-neutral hover:text-primary transition-colors py-2">Sobre</a>
  <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="vh-mobile-link text-2xl font-bold text-neutral hover:text-primary transition-colors py-2">Blog</a>
  <a href="<?php echo $home_url; ?>#contato"       class="vh-mobile-link text-2xl font-bold text-neutral hover:text-primary transition-colors py-2">Contato</a>
  <a href="<?php echo $cta_link; ?>" class="vh-mobile-link mobile-nav-cta btn btn-primary btn-brand-coral rounded-full px-8 py-3 text-white font-bold mt-4 shadow-lg" data-vh="header_cta_text"><?php echo $cta_text; ?> →</a>
</nav>