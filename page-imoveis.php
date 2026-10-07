<?php
/**
 * Template Name: Catálogo de Imóveis
 * Template Post Type: page
 *
 * Catálogo completo de propriedades com filtros dinâmicos por bairro
 * e seção final de conversão para proprietários de imóveis.
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;

$site_name = get_bloginfo( 'name' );
$home_url  = home_url( '/' );

$all_properties = vh_get_all_properties();

// Contadores por filtro para as tags de filtro
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

// Configurações do WhatsApp para a CTA de proprietários
$wa_raw_digits = preg_replace( '/\D/', '', vh_mod( 'wa_number', '5571999999999' ) );
if ( strlen( $wa_raw_digits ) === 10 || strlen( $wa_raw_digits ) === 11 ) {
	$wa_raw_digits = '55' . $wa_raw_digits;
}
$wa_msg_owner = rawurlencode( 'Olá Márcia! Vi o portfólio de imóveis da VivaHost e gostaria de saber como funciona a gestão para o meu imóvel em Salvador.' );
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

<main id="imoveis-content" class="pt-24 sm:pt-28 pb-20">
  <!-- ═══════════════════════════════════════
       HERO & INTRODUÇÃO DO CATÁLOGO
  ════════════════════════════════════════ -->
  <section class="catalog-hero bg-base-200/50 border-b border-base-200 py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="breadcrumb flex items-center gap-2 text-xs sm:text-sm text-base-content/60 mb-6" aria-label="Navegação estrutural">
        <a href="<?php echo esc_url( $home_url ); ?>" class="hover:text-primary transition-colors">Início</a>
        <span>/</span>
        <span class="text-neutral font-medium">Imóveis em Salvador</span>
      </nav>

      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
        <div class="max-w-3xl">
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">Portfólio Exclusivo VivaHost</span>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-neutral tracking-tight mb-4 leading-tight">
            Acomodações geridas com padrão Superhost em Salvador
          </h1>
          <p class="text-base sm:text-lg text-base-content/80 leading-relaxed">
            Apartamentos e studios selecionados, preparados com zelo presencial, limpeza 5 estrelas e localização estratégica nos bairros mais procurados da Bahia. Escolha seu destino ou filtre por região.
          </p>
        </div>

        <!-- Mini Stats Badge -->
        <div class="flex flex-wrap items-center gap-3">
          <div class="bg-white border border-base-200 rounded-2xl px-5 py-3 shadow-sm flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">⭐</span>
            <div>
              <div class="text-base font-extrabold text-neutral leading-none">4.9+ Média</div>
              <div class="text-[11px] text-base-content/70 mt-1">+300 Avaliações 5 estrelas</div>
            </div>
          </div>
          <div class="bg-white border border-base-200 rounded-2xl px-5 py-3 shadow-sm flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">🏠</span>
            <div>
              <div class="text-base font-extrabold text-neutral leading-none">15 Imóveis</div>
              <div class="text-[11px] text-base-content/70 mt-1">Gestão 100% profissional</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       BARRA DE FILTROS & GRID DE IMÓVEIS
  ════════════════════════════════════════ -->
  <section class="catalog-grid-section py-12 sm:py-16 bg-base-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Filter Bar -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-10 pb-6 border-b border-base-200">
        <div class="text-sm font-semibold text-base-content/70">
          Filtrar por região:
        </div>
        <div class="imoveis-filter-bar flex flex-wrap items-center justify-center gap-2">
          <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-primary text-white font-semibold transition-all active" data-filter="all">
            Ver todos (<?php echo esc_html( $counts['all'] ); ?>)
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
      <div class="imoveis-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        foreach ( $all_properties as $idx => $prop ) :
          $bairro_short = explode( ',', $prop['loc'] )[0];
        ?>
          <article class="imovel-card card bg-white border border-base-200 shadow-md hover:shadow-2xl transition-all duration-300 rounded-3xl overflow-hidden group flex flex-col justify-between" itemscope itemtype="https://schema.org/Product" data-filter="<?php echo esc_attr( $prop['filter'] ); ?>">
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
                <h2 class="imovel-name text-lg font-bold text-neutral mb-3 line-clamp-2 leading-snug" itemprop="name"><?php echo esc_html( $prop['name'] ); ?></h2>
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

      <!-- Airbnb External Profile Link -->
      <div class="text-center mt-12 mb-6">
        <a href="<?php echo esc_url( vh_mod( 'footer_airbnb', 'https://www.airbnb.com.br/users/show/148412228' ) ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-neutral rounded-full px-8 font-bold hover:btn-primary hover:text-white transition-all text-sm">
          Ver perfil completo de anfitriã no Airbnb (15 anúncios) →
        </a>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       SEÇÃO CTA CONVERSÃO PROPRIETÁRIOS
       (Afie seu imóvel com quem entende)
  ════════════════════════════════════════ -->
  <section class="owner-cta-section max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-12 sm:my-16">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-neutral to-neutral-900 text-white p-8 sm:p-12 lg:p-16 shadow-2xl border border-neutral-700/50">
      
      <!-- Decorative background glow -->
      <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-primary/20 blur-3xl pointer-events-none" aria-hidden="true"></div>
      <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-primary/10 blur-3xl pointer-events-none" aria-hidden="true"></div>

      <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
        <div class="max-w-2xl text-center lg:text-left">
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-bold uppercase tracking-wider mb-4 backdrop-blur-sm border border-white/15">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
            Para Proprietários de Imóveis
          </span>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-5 leading-tight">
            Tem um imóvel em Salvador? Deixe a gestão completa com a VivaHost
          </h2>
          <p class="text-base sm:text-lg text-white/80 leading-relaxed mb-8">
            Transforme seu apartamento em uma fonte de renda previsível e até 40% mais rentável que a locação tradicional. Cuidamos de todo o ciclo operacional com padrão Superhost — anúncio, precificação inteligente, check-in, suporte 24h e conservação impecável.
          </p>

          <!-- 4 Pillars Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-left mb-8">
            <div class="flex items-start gap-3 bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
              <span class="w-8 h-8 rounded-xl bg-primary/20 text-primary flex items-center justify-center flex-shrink-0 text-base font-bold">⭐</span>
              <div>
                <h3 class="font-bold text-sm text-white">Selo Superhost</h3>
                <p class="text-xs text-white/70 mt-0.5">Maior visibilidade orgânica e reservas no Airbnb e plataformas.</p>
              </div>
            </div>

            <div class="flex items-start gap-3 bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
              <span class="w-8 h-8 rounded-xl bg-success/20 text-success flex items-center justify-center flex-shrink-0 text-base font-bold">📈</span>
              <div>
                <h3 class="font-bold text-sm text-white">Precificação Dinâmica</h3>
                <p class="text-xs text-white/70 mt-0.5">Algoritmos inteligentes para maximizar sua diária e taxa de ocupação.</p>
              </div>
            </div>

            <div class="flex items-start gap-3 bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
              <span class="w-8 h-8 rounded-xl bg-primary/20 text-primary flex items-center justify-center flex-shrink-0 text-base font-bold">🧹</span>
              <div>
                <h3 class="font-bold text-sm text-white">Equipe Própria &amp; Zelo</h3>
                <p class="text-xs text-white/70 mt-0.5">Limpeza profissional, vistorias rigorosas e conservação do patrimônio.</p>
              </div>
            </div>

            <div class="flex items-start gap-3 bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur-sm">
              <span class="w-8 h-8 rounded-xl bg-info/20 text-info flex items-center justify-center flex-shrink-0 text-base font-bold">📱</span>
              <div>
                <h3 class="font-bold text-sm text-white">Transparência Total</h3>
                <p class="text-xs text-white/70 mt-0.5">Acesso direto à gestora Márcia Sales e repasses pontuais sem burocracia.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Card / Buttons -->
        <div class="w-full lg:w-auto flex-shrink-0 bg-white/10 border border-white/15 rounded-3xl p-6 sm:p-8 backdrop-blur-md text-center max-w-md flex flex-col items-center">
          <div class="w-16 h-16 rounded-2xl bg-white/15 flex items-center justify-center text-3xl mb-4 shadow-inner">
            🔑
          </div>
          <h3 class="text-xl font-bold text-white mb-2">Quer saber o potencial do seu imóvel?</h3>
          <p class="text-xs text-white/75 mb-6 leading-relaxed">
            Faça uma simulação gratuita e receba uma análise de rentabilidade personalizada para sua acomodação em Salvador.
          </p>

          <div class="w-full space-y-3">
            <a href="<?php echo esc_url( $wa_owner_url ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-block bg-[#25D366] hover:bg-[#1EBE5D] text-white border-0 font-bold rounded-full py-3.5 shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2">
              <svg viewBox="0 0 24 24" class="w-5 h-5 fill-current" aria-hidden="true"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.634.053-.984.053-1.636 0-3.32-1.424-3.32-1.424s-1.844-1.92-1.844-3.834c0-1.29.67-1.928.908-2.176.239-.249.524-.312.698-.312.174 0 .349.002.5.011.161.009.378-.061.591.45.222.532.756 1.844.823 1.977.066.133.11.288.022.464-.088.177-.132.287-.264.442-.132.155-.278.347-.397.465-.133.133-.272.278-.117.544.155.266.69 1.136 1.48 1.84.805.719 1.482.941 1.748 1.052.266.111.421.089.576-.089.155-.177.665-.774.842-1.04.177-.266.354-.221.598-.133.244.089 1.547.73 1.813.863.266.133.443.2.51.31.066.111.066.643-.078 1.048z"/></svg>
              <span>Falar no WhatsApp agora</span>
            </a>

            <a href="<?php echo esc_url( $home_url . '#contato' ); ?>" class="btn btn-block btn-outline text-white hover:bg-white hover:text-neutral border-white/30 rounded-full font-bold text-xs py-3 transition-all">
              Preencher formulário de avaliação →
            </a>
          </div>

          <div class="mt-4 text-[11px] text-white/60 flex items-center gap-1.5 justify-center">
            <span>🔒 Atendimento rápido e sem compromisso</span>
          </div>
        </div>
      </div>
    </div>
  </section>
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
