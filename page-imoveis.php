<?php
/**
 * Template Name: Catálogo de Imóveis
 * Template Post Type: page
 *
 * Catálogo limpo de propriedades com filtros por bairro
 * e contato para proprietários.
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;

$site_name = get_bloginfo( 'name' );
$home_url  = home_url( '/' );

$all_properties = vh_get_all_properties();

// Contadores por bairro para as pills
$counts = [
	'all'        => count( $all_properties ),
	'costa-azul' => 0,
	'barra'      => 0,
	'ondina'     => 0,
	'outros'     => 0,
];

foreach ( $all_properties as $p ) {
	$f = isset( $p['filter'] ) ? $p['filter'] : 'outros';
	if ( isset( $counts[ $f ] ) ) {
		$counts[ $f ]++;
	} else {
		$counts['outros']++;
	}
}

// WhatsApp para proprietários
$wa_raw_digits = preg_replace( '/\D/', '', vh_mod( 'wa_number', '5571999999999' ) );
if ( strlen( $wa_raw_digits ) === 10 || strlen( $wa_raw_digits ) === 11 ) {
	$wa_raw_digits = '55' . $wa_raw_digits;
}
$wa_msg_owner = rawurlencode( 'Olá Márcia! Gostaria de conversar sobre a gestão do meu imóvel com a VivaHost.' );
$wa_owner_url = 'https://wa.me/' . ( $wa_raw_digits ?: '5571999999999' ) . '?text=' . $wa_msg_owner;

$ico_star = '<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-3.5 h-3.5 text-primary"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="light" class="light">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light">

  <!-- Core Web Vitals Preconnects -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="preconnect" href="https://images.unsplash.com">

  <!-- daisyUI + Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.24/dist/full.min.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      daisyui: {
        themes: ['light'],
        darkTheme: false,
        base: true,
        styled: true,
      },
      theme: {
        extend: {
          colors: {
            'primary': 'var(--vh-primary)',
            'vh-primary': 'var(--vh-primary)',
            'vh-footer': 'var(--vh-footer-bg)',
            'vh-wa': '#25D366',
            'vh-dark': '#222222',
            'vh-body': '#484848',
          },
          fontFamily: {
            sans: ['var(--f)', 'Plus Jakarta Sans', 'sans-serif'],
          }
        }
      }
    };
  </script>

  <?php wp_head(); ?>
</head>
<body <?php body_class( 'vivahost-imoveis bg-base-100 text-neutral font-sans antialiased' ); ?>>
<a class="vh-skip-link" href="#imoveis-content">Ir para o conteúdo</a>

<?php get_template_part( 'template-parts/header-site' ); ?>

<main id="imoveis-content" class="pt-28 sm:pt-32 pb-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Top Header: Title & Filters in one clean bar -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 mb-8 border-b border-base-200">
      <div>
        <nav class="breadcrumb flex items-center gap-2 text-xs text-base-content/60 mb-3" aria-label="Navegação">
          <a href="<?php echo esc_url( $home_url ); ?>" class="hover:text-primary transition-colors">Início</a>
          <span>/</span>
          <span class="text-neutral font-medium">Imóveis</span>
        </nav>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-neutral tracking-tight">
          Nossos Imóveis
        </h1>
        <p class="text-sm sm:text-base text-base-content/70 mt-1">
          Acomodações selecionadas com operação e hospitalidade VivaHost em Salvador.
        </p>
      </div>

      <!-- Filters -->
      <div class="imoveis-filter-bar flex flex-wrap items-center gap-2">
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-primary text-white font-semibold transition-all active" data-filter="all">
          Todos (<?php echo esc_html( $counts['all'] ); ?>)
        </button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="costa-azul">
          Costa Azul (<?php echo esc_html( $counts['costa-azul'] ); ?>)
        </button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="barra">
          Barra (<?php echo esc_html( $counts['barra'] ); ?>)
        </button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="ondina">
          Ondina (<?php echo esc_html( $counts['ondina'] ); ?>)
        </button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="outros">
          Amaralina &amp; Outros (<?php echo esc_html( $counts['outros'] ); ?>)
        </button>
      </div>
    </div>

    <!-- Properties Grid -->
    <div class="imoveis-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      <?php
      foreach ( $all_properties as $prop ) :
        $bairro_short = explode( ',', $prop['loc'] )[0];
      ?>
        <article class="imovel-card card bg-white border border-base-200 shadow-sm hover:shadow-xl transition-all duration-300 rounded-3xl overflow-hidden group flex flex-col justify-between" itemscope itemtype="https://schema.org/Product" data-filter="<?php echo esc_attr( $prop['filter'] ); ?>">
          <div class="imovel-photo relative overflow-hidden aspect-[4/3] bg-base-200">
            <img src="<?php echo esc_url( $prop['photo'] ); ?>" alt="<?php echo esc_attr( $prop['name'] ); ?>" loading="lazy" width="600" height="450" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            <div class="imovel-badge absolute top-4 right-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-sm font-bold text-xs text-neutral">
              <?php echo $ico_star; ?><span><?php echo esc_html( $prop['rating'] ); ?></span>
            </div>
            <div class="imovel-pill-loc absolute bottom-4 left-4 bg-black/65 backdrop-blur-md px-3 py-1 rounded-full text-white text-xs font-semibold">
              <?php echo esc_html( trim( $bairro_short ) ); ?>
            </div>
          </div>
          <div class="imovel-body p-6 flex flex-col justify-between flex-1">
            <div>
              <div class="imovel-location text-xs font-semibold uppercase tracking-wider text-primary mb-1.5"><?php echo esc_html( $prop['loc'] ); ?></div>
              <h2 class="imovel-name text-base sm:text-lg font-bold text-neutral mb-3 line-clamp-2 leading-snug" itemprop="name"><?php echo esc_html( $prop['name'] ); ?></h2>
            </div>
            <div class="imovel-stats flex items-center justify-between pt-4 border-t border-base-200 text-xs mt-auto">
              <span class="imovel-reviews text-base-content/70 font-medium"><?php echo esc_html( $prop['reviews'] ); ?> avaliações</span>
              <a href="<?php echo esc_url( $prop['link'] ); ?>" target="_blank" rel="noopener noreferrer" class="imovel-airbnb btn btn-link btn-xs text-primary font-bold no-underline hover:underline p-0 flex items-center gap-1">
                <span>Ver no Airbnb</span>
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 001.06 0l7.22-7.22v5.69a.75.75 0 001.5 0v-7.5a.75.75 0 00-.75-.75h-7.5a.75.75 0 000 1.5h5.69l-7.22 7.22a.75.75 0 000 1.06z" clip-rule="evenodd"/></svg>
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <!-- Discreet Airbnb Link -->
    <div class="text-center mt-10">
      <a href="<?php echo esc_url( vh_mod( 'footer_airbnb', 'https://www.airbnb.com.br/users/show/148412228' ) ); ?>" target="_blank" rel="noopener noreferrer" class="text-xs text-base-content/60 hover:text-primary transition-colors underline">
        Ver perfil completo com 15 anúncios no Airbnb
      </a>
    </div>

    <!-- Clean, Human Owner CTA Section -->
    <div class="mt-16 sm:mt-24 p-8 sm:p-12 rounded-3xl bg-base-200/60 border border-base-200 text-center max-w-3xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral tracking-tight mb-3">
        Quer o seu imóvel operado com esse mesmo padrão?
      </h2>
      <p class="text-sm sm:text-base text-base-content/75 max-w-xl mx-auto mb-8 leading-relaxed">
        Cuidamos de tudo para você ter rentabilidade com tranquilidade — anúncio, precificação diária, limpeza profissional e atendimento aos hóspedes com padrão Superhost.
      </p>
      <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="<?php echo esc_url( $wa_owner_url ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-brand-coral rounded-full px-8 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all w-full sm:w-auto">
          Falar no WhatsApp
        </a>
        <a href="<?php echo esc_url( $home_url . '#contato' ); ?>" class="btn btn-outline btn-neutral rounded-full px-8 text-sm font-semibold w-full sm:w-auto">
          Solicitar avaliação gratuita
        </a>
      </div>
    </div>

  </div>
</main>

<!-- Full Footer -->
<?php get_template_part( 'template-parts/footer-site' ); ?>

<!-- ═══ Back to Top ═══ -->
<button id="vh-back-to-top" class="vh-back-to-top fixed bottom-7 right-7 z-40 btn btn-circle btn-primary text-white shadow-xl hover:shadow-2xl transition-all duration-300" aria-label="Voltar ao topo" title="Voltar ao topo">
  <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
