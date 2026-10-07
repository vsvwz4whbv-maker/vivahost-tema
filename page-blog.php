<?php
/**
 * Template Name: Blog / Artigos
 * Template Post Type: page
 *
 * Página de arquivo do blog com design editorial limpo,
 * filtro por categorias e contato para proprietários.
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;

$site_name = get_bloginfo( 'name' );
$home_url  = home_url( '/' );

// Query de todos os artigos publicados
$blog_query = new WP_Query( [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => -1,
	'ignore_sticky_posts' => true,
] );

// Fallback de artigos caso o banco esteja vazio
$fallback_posts = [
	[
		'title'   => 'Como Maximizar a Performance da sua Hospedagem no Airbnb em Salvador',
		'excerpt' => 'Descubra as melhores práticas de precificação dinâmica, preparação da acomodação e hospitalidade para encantar hóspedes em Salvador.',
		'cat'     => 'Estratégia & Hospitalidade',
		'slug'    => 'estrategia',
		'img'     => 'https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?auto=format&fit=crop&w=800&q=80',
		'date'    => '24 de Setembro, 2026',
		'read'    => '4 min de leitura',
		'link'    => $home_url . '#contato',
	],
	[
		'title'   => 'Hospedagem por Temporada em Salvador: Boas Práticas e Regras de Condomínio',
		'excerpt' => 'Entenda como manter uma convivência harmônica com o condomínio, receber hóspedes com segurança e garantir tranquilidade para todos.',
		'cat'     => 'Hospitalidade & Convivência',
		'slug'    => 'convivencia',
		'img'     => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
		'date'    => '18 de Setembro, 2026',
		'read'    => '5 min de leitura',
		'link'    => $home_url . '#contato',
	],
	[
		'title'   => 'Os Melhores Bairros de Salvador para Hospedagem de Temporada em 2026',
		'excerpt' => 'Barra, Ondina, Rio Vermelho ou Costa Azul? Analisamos perfil de viajantes, demanda turística e fluxo de hóspedes nos principais bairros da capital baiana.',
		'cat'     => 'Destinos & Temporada',
		'slug'    => 'destinos',
		'img'     => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
		'date'    => '10 de Setembro, 2026',
		'read'    => '6 min de leitura',
		'link'    => $home_url . '#contato',
	],
];

// Coleta as categorias existentes para os filtros
$categories = get_categories( [
	'hide_empty' => true,
	'exclude'    => [ 1 ], // exclui Sem categoria / Uncategorized se id for 1
] );

// Configuração do link WhatsApp para proprietários
$wa_raw_digits = preg_replace( '/\D/', '', vh_mod( 'wa_number', '5571999999999' ) );
if ( strlen( $wa_raw_digits ) === 10 || strlen( $wa_raw_digits ) === 11 ) {
	$wa_raw_digits = '55' . $wa_raw_digits;
}
$wa_msg_owner = rawurlencode( 'Olá Márcia! Li os conteúdos no blog da VivaHost e gostaria de saber sobre a operação para o meu imóvel em Salvador.' );
$wa_owner_url = 'https://wa.me/' . ( $wa_raw_digits ?: '5571999999999' ) . '?text=' . $wa_msg_owner;
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
<body <?php body_class( 'vivahost-blog bg-base-100 text-neutral font-sans antialiased' ); ?>>
<a class="vh-skip-link" href="#blog-content">Ir para o conteúdo</a>

<?php get_template_part( 'template-parts/header-site' ); ?>

<main id="blog-content" class="pt-28 sm:pt-32 pb-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Top Header: Title & Filter Pills in one clean bar -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 pb-8 mb-8 border-b border-base-200">
      <div>
        <nav class="breadcrumb flex items-center gap-2 text-xs text-base-content/60 mb-3" aria-label="Navegação">
          <a href="<?php echo esc_url( $home_url ); ?>" class="hover:text-primary transition-colors">Início</a>
          <span>/</span>
          <span class="text-neutral font-medium">Blog</span>
        </nav>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-neutral tracking-tight">
          Blog VivaHost
        </h1>
        <p class="text-sm sm:text-base text-base-content/70 mt-1">
          Dicas de hospitalidade, estratégias operacionais e mercado de temporada em Salvador.
        </p>
      </div>

      <!-- Categories Filter Pills -->
      <div class="blog-filter-bar flex flex-wrap items-center gap-2">
        <button type="button" class="vh-blog-filter btn btn-sm rounded-full btn-primary text-white font-semibold transition-all active" data-filter="all">
          Todos
        </button>
        <?php if ( ! empty( $categories ) ) : ?>
          <?php foreach ( $categories as $cat ) : ?>
            <button type="button" class="vh-blog-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="<?php echo esc_attr( $cat->slug ); ?>">
              <?php echo esc_html( $cat->name ); ?>
            </button>
          <?php endforeach; ?>
        <?php else : ?>
          <button type="button" class="vh-blog-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="estrategia">
            Estratégia
          </button>
          <button type="button" class="vh-blog-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="convivencia">
            Hospitalidade
          </button>
          <button type="button" class="vh-blog-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="destinos">
            Destinos
          </button>
        <?php endif; ?>
      </div>
    </div>

    <!-- Articles Grid -->
    <div class="blog-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
      <?php
      if ( $blog_query->have_posts() ) :
        $idx = 0;
        while ( $blog_query->have_posts() ) :
          $blog_query->the_post();
          $p_id      = get_the_ID();
          $cats      = get_the_category();
          $cat_obj   = ! empty( $cats ) ? $cats[0] : null;
          $cat_title = ( $cat_obj && $cat_obj->name !== 'Uncategorized' && $cat_obj->name !== 'Sem categoria' ) ? $cat_obj->name : 'Dicas de Salvador';
          $cat_slug  = $cat_obj ? $cat_obj->slug : 'outros';
          $p_thumb   = vh_get_post_image( $p_id, 'medium_large' );
          $word_cnt  = str_word_count( strip_tags( get_the_content() ) );
          $read_time = max( 1, ceil( $word_cnt / 200 ) );
          $idx++;
      ?>
          <article class="blog-card card bg-white border border-base-200 shadow-sm hover:shadow-xl transition-all duration-300 rounded-3xl overflow-hidden group flex flex-col justify-between" data-filter="<?php echo esc_attr( $cat_slug ); ?>">
            <div>
              <a href="<?php the_permalink(); ?>" class="blog-photo-wrap block aspect-[16/10] overflow-hidden bg-base-200 relative">
                <img src="<?php echo esc_url( $p_thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="badge badge-primary text-white text-xs font-semibold py-2 px-3 shadow-sm absolute top-4 left-4">
                  <?php echo esc_html( $cat_title ); ?>
                </span>
              </a>
              <div class="p-6">
                <div class="blog-meta text-xs text-base-content/60 font-medium mb-2.5 flex items-center gap-2">
                  <span><?php echo esc_html( get_the_date( 'd \d\e F, Y' ) ); ?></span>
                  <span>·</span>
                  <span><?php echo esc_html( $read_time ); ?> min de leitura</span>
                </div>
                <h2 class="blog-title text-lg font-bold text-neutral group-hover:text-primary transition-colors leading-snug line-clamp-2 mb-3">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p class="blog-excerpt text-xs sm:text-sm text-base-content/75 leading-relaxed line-clamp-3">
                  <?php echo esc_html( get_the_excerpt() ); ?>
                </p>
              </div>
            </div>
            <div class="px-6 pb-6 pt-2 border-t border-base-200/60 mt-auto">
              <a href="<?php the_permalink(); ?>" class="btn btn-link btn-xs text-primary font-bold no-underline hover:underline p-0 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                <span>Ler artigo completo</span>
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
              </a>
            </div>
          </article>
      <?php
        endwhile;
        wp_reset_postdata();
      else :
        // Fallback nativo
        foreach ( $fallback_posts as $fpost ) :
      ?>
          <article class="blog-card card bg-white border border-base-200 shadow-sm hover:shadow-xl transition-all duration-300 rounded-3xl overflow-hidden group flex flex-col justify-between" data-filter="<?php echo esc_attr( $fpost['slug'] ); ?>">
            <div>
              <div class="blog-photo-wrap block aspect-[16/10] overflow-hidden bg-base-200 relative">
                <img src="<?php echo esc_url( $fpost['img'] ); ?>" alt="<?php echo esc_attr( $fpost['title'] ); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="badge badge-primary text-white text-xs font-semibold py-2 px-3 shadow-sm absolute top-4 left-4">
                  <?php echo esc_html( $fpost['cat'] ); ?>
                </span>
              </div>
              <div class="p-6">
                <div class="blog-meta text-xs text-base-content/60 font-medium mb-2.5 flex items-center gap-2">
                  <span><?php echo esc_html( $fpost['date'] ); ?></span>
                  <span>·</span>
                  <span><?php echo esc_html( $fpost['read'] ); ?></span>
                </div>
                <h2 class="blog-title text-lg font-bold text-neutral group-hover:text-primary transition-colors leading-snug line-clamp-2 mb-3">
                  <a href="<?php echo esc_url( $fpost['link'] ); ?>"><?php echo esc_html( $fpost['title'] ); ?></a>
                </h2>
                <p class="blog-excerpt text-xs sm:text-sm text-base-content/75 leading-relaxed line-clamp-3">
                  <?php echo esc_html( $fpost['excerpt'] ); ?>
                </p>
              </div>
            </div>
            <div class="px-6 pb-6 pt-2 border-t border-base-200/60 mt-auto">
              <a href="<?php echo esc_url( $fpost['link'] ); ?>" class="btn btn-link btn-xs text-primary font-bold no-underline hover:underline p-0 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                <span>Falar no WhatsApp →</span>
              </a>
            </div>
          </article>
      <?php
        endforeach;
      endif;
      ?>
    </div>

    <!-- Clean, Human Owner CTA Section -->
    <div class="mt-16 sm:mt-24 p-8 sm:p-12 rounded-3xl bg-base-200/60 border border-base-200 text-center max-w-3xl mx-auto">
      <span class="text-xs font-bold uppercase tracking-wider text-primary mb-2 block">Para Proprietários</span>
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

<script>
// Filter vanilla JS per categoria del blog
document.addEventListener('DOMContentLoaded', function () {
  var filterButtons = document.querySelectorAll('.vh-blog-filter');
  var articleCards  = document.querySelectorAll('.blog-card');
  if (!filterButtons.length || !articleCards.length) return;

  filterButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var filter = btn.getAttribute('data-filter');
      filterButtons.forEach(function (b) {
        b.classList.remove('btn-primary', 'text-white', 'active');
        b.classList.add('btn-outline', 'btn-neutral');
      });
      btn.classList.add('btn-primary', 'text-white', 'active');
      btn.classList.remove('btn-outline', 'btn-neutral');

      articleCards.forEach(function (card) {
        var cardFilter = card.getAttribute('data-filter');
        if (filter === 'all' || cardFilter === filter) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
});
</script>

<?php wp_footer(); ?>
</body>
</html>
