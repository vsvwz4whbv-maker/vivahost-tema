<?php
/**
 * Single Post Template — VivaHost Blog
 *
 * Visualizzazione dettagliata dell'articolo con layout pulito,
 * autore Superhost, call-to-action per valutazione immobile e articoli correlati.
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;

$site_name = get_bloginfo( 'name' );
$home_url  = home_url( '/' );

// Footer & contact data
$razao     = vh_mod( 'footer_razao', 'Viva Host LTDA' );
$cnpj      = vh_mod( 'footer_cnpj', '27.447.686/0001-10' );
$address   = vh_mod( 'footer_address', 'Avenida Tancredo Neves, 002539' );
$comp      = vh_mod( 'footer_complement', '' );
$bairro    = vh_mod( 'footer_bairro', 'Caminho das Árvores' );
$cep       = vh_mod( 'footer_cep', '41820-021' );
$email     = vh_mod( 'footer_email', 'marcia@meuvivahost.com.br' );
$instagram = vh_mod( 'footer_instagram', 'https://www.instagram.com/vivahostbahia/' );
$airbnb    = vh_mod( 'footer_airbnb', 'https://www.airbnb.com.br/users/show/148412228' );
$host_name = vh_mod( 'host_name', 'Marcia Sales' );
$host_photo_id  = (int) vh_mod( 'host_photo', 0 );
$host_photo_url = $host_photo_id ? wp_get_attachment_image_url( $host_photo_id, 'large' ) : VH_URL . '/assets/images/marcia-sales.jpg';
$wa_url = vh_wa_url();

while ( have_posts() ) :
	the_post();
	$post_id    = get_the_ID();
	$categories = get_the_category();
	$cat_name   = ( ! empty( $categories ) && $categories[0]->name !== 'Uncategorized' && $categories[0]->name !== 'Sem categoria' ) ? $categories[0]->name : 'Dicas de Salvador';
	$word_count = str_word_count( strip_tags( get_the_content() ) );
	$read_time  = max( 1, ceil( $word_count / 200 ) );
	$thumb_url  = vh_get_post_image( $post_id, 'large' );
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
<body <?php body_class( 'vivahost-single bg-base-100 text-neutral font-sans antialiased' ); ?>>
<a class="vh-skip-link" href="#single-content">Ir para o conteúdo</a>

<?php get_template_part( 'template-parts/header-site' ); ?>

<main id="single-content" class="pt-28 pb-20">
  <!-- Article Header -->
  <div class="article-hero bg-base-200/40 border-b border-base-200 py-12 sm:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="breadcrumb flex items-center gap-2 text-xs sm:text-sm text-base-content/60 mb-6" aria-label="Navegação estrutural">
        <a href="<?php echo esc_url( $home_url ); ?>" class="hover:text-primary transition-colors">Início</a>
        <span>/</span>
        <a href="<?php echo esc_url( $home_url ); ?>#blog" class="hover:text-primary transition-colors">Blog</a>
        <span>/</span>
        <span class="text-neutral font-medium truncate max-w-[200px] sm:max-w-none"><?php the_title(); ?></span>
      </nav>

      <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4">
        <span class="badge badge-ghost h-auto whitespace-nowrap text-xs font-semibold py-1.5 px-3 border border-base-300 bg-base-200/80 text-base-content/80"><?php echo esc_html( $cat_name ); ?></span>
        <span class="text-xs text-base-content/60 font-medium flex items-center gap-1 whitespace-nowrap">
          <svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
          <?php echo esc_html( $read_time ); ?> min de leitura
        </span>
        <span class="text-xs text-base-content/60 font-medium whitespace-nowrap">· <?php echo esc_html( get_the_date( 'd \d\e F, Y' ) ); ?></span>
      </div>

      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-neutral tracking-tight leading-tight mb-6">
        <?php the_title(); ?>
      </h1>

      <p class="text-lg sm:text-xl text-base-content/80 leading-relaxed font-normal">
        <?php echo esc_html( get_the_excerpt() ); ?>
      </p>
    </div>
  </div>

  <!-- Featured Image & Content Wrap -->
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
    <div class="featured-image rounded-3xl overflow-hidden shadow-2xl aspect-[16/9] mb-12 bg-base-300">
      <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
    </div>

    <!-- Article Content -->
    <article class="prose prose-lg max-w-none text-base-content/85 leading-relaxed space-y-6 text-base sm:text-lg">
      <?php the_content(); ?>
    </article>

    <!-- Author Box -->
    <div class="author-box card bg-base-200/50 border border-base-200 rounded-3xl p-6 sm:p-8 mt-14 flex flex-col sm:flex-row items-center sm:items-start gap-6">
      <div class="avatar flex-shrink-0">
        <div class="w-20 h-20 rounded-full ring-2 ring-base-300 shadow-md overflow-hidden">
          <img src="<?php echo esc_url( $host_photo_url ); ?>" alt="<?php echo esc_attr( $host_name ); ?>" class="w-full h-full object-cover">
        </div>
      </div>
      <div>
        <div class="flex items-center gap-2 mb-1 justify-center sm:justify-start">
          <h4 class="font-bold text-lg text-neutral"><?php echo esc_html( $host_name ); ?></h4>
          <span class="badge border border-amber-300/80 bg-amber-50 text-amber-800 text-xs font-semibold gap-1 px-2.5 py-2">
            <svg class="w-3 h-3 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            Superhost Airbnb
          </span>
        </div>
        <p class="text-xs sm:text-sm text-base-content/75 leading-relaxed mb-4 text-center sm:text-left">
          Gestora da VivaHost e anfitriã profissional com 9 anos de experiência e mais de 460 avaliações 5 estrelas em Salvador, Bahia.
        </p>
        <div class="flex justify-center sm:justify-start">
          <a href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Li seu artigo no blog da VivaHost e gostaria de tirar uma dúvida.' ) ); ?>" target="_blank" rel="noopener" class="btn bg-[#25D366] hover:bg-[#20ba5a] text-white border-none btn-sm rounded-full font-bold gap-2 shadow-sm">
            <svg viewBox="0 0 24 24" class="w-4 h-4 fill-current"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.103 1.523 5.824L.057 23.5a.5.5 0 0 0 .61.61l5.734-1.46A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.5-5.24-1.376l-.375-.213-3.882.99.998-3.795-.232-.387A9.946 9.946 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            <span>Conversar com a Marcia no WhatsApp</span>
          </a>
        </div>
      </div>
    </div>

    <!-- Valuation CTA Box -->
    <div class="valuation-cta card bg-neutral text-white rounded-3xl p-8 sm:p-12 mt-12 text-center shadow-xl relative overflow-hidden">
      <div class="relative z-10 max-w-xl mx-auto">
        <span class="badge badge-outline border-white/30 text-white/90 text-xs font-semibold uppercase tracking-wider mb-4 px-3 py-2">Avaliação Gratuita</span>
        <h3 class="text-2xl sm:text-3xl font-extrabold text-white mb-4">Descubra o potencial de faturamento do seu imóvel em Salvador</h3>
        <p class="text-sm sm:text-base text-white/80 mb-6">
          Avaliamos a localização, o perfil do imóvel e apresentamos uma estimativa realista de receita com gestão profissional Superhost.
        </p>
        <a href="<?php echo esc_url( $home_url ); ?>#contato" class="btn bg-white hover:bg-white/90 text-neutral font-bold btn-lg rounded-full px-8 border-none shadow-lg hover:shadow-xl hover:scale-105 transition-all">
          Solicitar avaliação gratuita →
        </a>
      </div>
    </div>

    <!-- Related Articles -->
    <?php
    $related = new WP_Query( [
      'post_type'      => 'post',
      'posts_per_page' => 2,
      'post__not_in'   => [ $post_id ],
    ] );
    if ( $related->have_posts() ) :
    ?>
      <div class="related-posts mt-16 pt-12 border-t border-base-200">
        <h3 class="text-2xl font-bold text-neutral mb-8">Outros artigos recomendados</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
          <?php while ( $related->have_posts() ) : $related->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="card bg-white border border-base-200 shadow-sm hover:shadow-xl transition-all duration-300 rounded-3xl overflow-hidden group">
              <div class="p-6">
                <span class="text-xs font-bold text-base-content/60 uppercase tracking-wider mb-2 block">Dicas de Gestão</span>
                <h4 class="font-bold text-neutral group-hover:text-primary transition-colors text-lg leading-snug line-clamp-2 mb-3">
                  <?php the_title(); ?>
                </h4>
                <p class="text-xs text-base-content/70 line-clamp-2 mb-4">
                  <?php echo esc_html( get_the_excerpt() ); ?>
                </p>
                <span class="text-xs font-bold text-neutral group-hover:text-primary flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                  Ler artigo →
                </span>
              </div>
            </a>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </div>
    <?php endif; ?>
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
<?php endwhile; ?>
