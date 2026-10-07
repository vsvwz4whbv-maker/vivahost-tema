<?php
/**
 * Front Page Template — VivaHost Landing Page
 *
 * Template Name: Canvas Home
 * Standalone canvas (no Hello Elementor header/footer).
 * Ogni stringa proviene da get_theme_mod() via vh_mod().
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;

// ── Read all mods upfront ─────────────────────────────────────────────────────
$hero_bg_id  = (int) vh_mod( 'hero_bg', 0 );
$hero_bg_url = $hero_bg_id
	? wp_get_attachment_image_url( $hero_bg_id, 'full' )
	: '';

$prop_fallbacks = [
	1 => VH_URL . '/assets/images/prop-ondina-vista.webp',
	2 => VH_URL . '/assets/images/prop-lar-lisboa.webp',
	3 => VH_URL . '/assets/images/prop-apartamento-salvador.webp',
];

$host_photo_id  = (int) vh_mod( 'host_photo', 0 );
$host_photo_url = $host_photo_id ? wp_get_attachment_image_url( $host_photo_id, 'large' ) : VH_URL . '/assets/images/marcia-sales.webp';

$wa_url = vh_wa_url();

$form_mode = vh_mod( 'form_mode', 'both' );
$host_name = esc_html( vh_mod( 'host_name', 'Marcia Sales' ) );
$comm_rate = esc_html( vh_mod( 'commission_rate', '20%' ) );
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
            'vh-muted': '#717171',
            'vh-off': '#F7F7F7',
            'vh-border': '#EBEBEB',
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
<body <?php body_class( 'vivahost-landing bg-base-100 text-neutral font-sans antialiased' ); ?>>
<a class="vh-skip-link" href="#vivahost-main">Ir para o conteúdo</a>
<?php
// Header personalizzato (da template-parts/header-site.php) — sostituisce vh_inject_header()
get_template_part( 'template-parts/header-site' );
?>

<main id="vivahost-main">

  <!-- ═══════════════════════════════════════
       HERO
  ════════════════════════════════════════ -->
  <section class="vh-hero relative min-h-[85vh] md:min-h-[90vh] flex items-center justify-center text-center text-white bg-cover bg-center <?php echo $hero_bg_url ? 'has-custom-bg' : 'vh-hero--gradient'; ?>" id="hero"
    <?php if ( $hero_bg_url ) : ?>style="background-image:url('<?php echo esc_url( $hero_bg_url ); ?>')"<?php endif; ?>
    aria-label="Introdução">
    <div class="vh-hero__overlay absolute inset-0 <?php echo $hero_bg_url ? 'bg-gradient-to-b from-black/75 via-black/60 to-black/80' : 'bg-gradient-to-b from-black/15 via-transparent to-black/25'; ?> z-10" aria-hidden="true"></div>
    <div class="vh-hero__content inner relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-24">
      <div class="inline-block mb-5">
        <span class="hero-eyebrow badge badge-lg border-white/30 bg-black/25 text-white font-medium tracking-wide backdrop-blur-md px-5 py-3 rounded-full text-xs sm:text-sm shadow-sm" data-vh="hero_eyebrow">
          <?php echo esc_html( vh_mod( 'hero_eyebrow', 'Operação de Hospedagem & Hospitalidade — Salvador, BA' ) ); ?>
        </span>
      </div>
      <h1 class="hero-title text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight max-w-4xl mx-auto mb-6 drop-shadow-lg" data-vh="hero_title">
        <?php echo esc_html( vh_mod( 'hero_title', 'Alta performance e hospitalidade no Airbnb em Salvador — com tranquilidade para você' ) ); ?>
      </h1>
      <p class="hero-sub text-base sm:text-lg lg:text-xl text-white/95 max-w-2xl mx-auto leading-relaxed mb-10 font-normal drop-shadow" data-vh="hero_subtitle">
        <?php echo esc_html( vh_mod( 'hero_subtitle', 'A VivaHost cuida da operação da sua hospedagem em Salvador de ponta a ponta: dos anúncios e reservas ao atendimento, limpeza e acompanhamento presencial. Você acompanha os resultados com total transparência.' ) ); ?>
      </p>
      <div class="hero-actions flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="<?php echo esc_url( vh_mod( 'hero_btn1_link', '#contato' ) ); ?>" class="btn btn-primary btn-brand-coral btn-lg rounded-full px-8 text-white font-bold shadow-xl hover:shadow-2xl hover:scale-105 transition-all w-full sm:w-auto" data-vh="hero_btn1_text">
          <?php echo esc_html( vh_mod( 'hero_btn1_text', 'Avaliar minha hospedagem' ) ); ?>
        </a>
        <a href="<?php echo esc_url( vh_mod( 'hero_btn2_link', '#como-funciona' ) ); ?>" class="btn btn-hero-secondary btn-lg rounded-full px-8 font-bold hover:scale-105 transition-all w-full sm:w-auto" data-vh="hero_btn2_text">
          <?php echo esc_html( vh_mod( 'hero_btn2_text', 'Como funciona' ) ); ?>
        </a>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       STATS BAR
  ════════════════════════════════════════ -->
  <section class="vh-stats relative z-30 -mt-12 sm:-mt-14 px-4 max-w-6xl mx-auto" aria-label="Números">
    <div class="stats-inner shadow-xl bg-white border border-base-200 rounded-3xl w-full grid grid-cols-2 md:grid-cols-4 overflow-hidden">
      <?php
      $stat_fallbacks = [
        [ '+30%',   'Média de faturamento vs tradicional' ],
        [ '85%',    'Taxa média de ocupação' ],
        [ '4,88 ★', 'Avaliação dos hóspedes' ],
        [ '9 anos', 'Superhost em Salvador' ],
      ];
      for ( $i = 1; $i <= 4; $i++ ) :
      ?>
        <div class="stat vh-stat-cell flex flex-col items-center justify-center text-center p-4 sm:p-6 anim-fade">
          <?php if ( $i === 3 ) : ?>
            <div class="stat-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-neutral tracking-tight leading-tight">
              <span class="stat-num inline-flex items-center gap-1" data-vh="stat_<?php echo $i; ?>_num">
                <?php echo esc_html( str_replace( '★', '', vh_mod( "stat_{$i}_num", $stat_fallbacks[$i-1][0] ) ) ); ?>
                <span class="star-yellow text-amber-400">★</span>
              </span>
            </div>
          <?php else : ?>
            <div class="stat-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-neutral tracking-tight leading-tight">
              <span class="stat-num" data-vh="stat_<?php echo $i; ?>_num">
                <?php echo esc_html( vh_mod( "stat_{$i}_num", $stat_fallbacks[$i-1][0] ) ); ?>
              </span>
            </div>
          <?php endif; ?>
          <div class="stat-title text-xs sm:text-sm font-medium text-base-content/70 mt-1 whitespace-normal leading-snug">
            <span class="stat-label" data-vh="stat_<?php echo $i; ?>_label">
              <?php echo esc_html( vh_mod( "stat_{$i}_label", $stat_fallbacks[$i-1][1] ) ); ?>
            </span>
          </div>
        </div>
      <?php endfor; ?>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       COMO FUNCIONA
  ════════════════════════════════════════ -->
  <!-- ═══════════════════════════════════════
       COMO FUNCIONA
  ════════════════════════════════════════ -->
  <section class="vh-section py-20 md:py-28 bg-base-100" id="como-funciona" aria-label="Como funciona">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="process-wrap grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <div class="process-intro lg:col-span-5 anim-fade lg:sticky lg:top-28">
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">Como funciona</span>
          <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight leading-tight mb-4" data-vh="process_title">
            <?php echo esc_html( vh_mod( 'process_title', 'Como funciona a operação da sua hospedagem' ) ); ?>
          </h2>
          <p class="text-base text-base-content/80 leading-relaxed mb-6" data-vh="process_intro">
            <?php echo esc_html( vh_mod( 'process_intro', 'Do alinhamento inicial ao acolhimento dos hóspedes, cuidamos de toda a rotina operacional para proporcionar a melhor estadia com tranquilidade para você.' ) ); ?>
          </p>
          <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-200/80 text-xs sm:text-sm font-semibold text-neutral border border-base-200">
            <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-primary flex-shrink-0" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
            <span>Da visita técnica inicial ao anúncio ativo em até 7 dias</span>
          </div>
        </div>
        <div class="process-steps lg:col-span-7 flex flex-col gap-5">
          <?php
          $step_defs = [
            [ 'Análise de potencial', 'Avaliamos o perfil da sua acomodação e projetamos o faturamento estimado com base no histórico da região em Salvador.' ],
            [ 'Produção e posicionamento', 'Sessão fotográfica profissional e criação de anúncios atrativos nas principais plataformas de hospedagem.' ],
            [ 'Operação e hospitalidade 360°', 'Check-in, suporte aos hóspedes, governança, higienização impecável e manutenção preventiva contínua.' ],
            [ 'Relatórios e acompanhamento', 'Prestação de contas detalhada todo mês e acompanhamento transparente do desempenho da sua hospedagem.' ],
          ];
          for ( $i = 1; $i <= 4; $i++ ) :
          ?>
            <div class="process-step card bg-white border border-base-200 shadow-sm hover:shadow-md transition-all duration-200 rounded-2xl p-6 sm:p-7 flex flex-col sm:flex-row items-start gap-5 anim-fade">
              <span class="step-num flex-shrink-0 w-12 h-12 rounded-xl bg-primary/10 text-primary font-black text-lg flex items-center justify-center">0<?php echo $i; ?></span>
              <div class="step-body flex-1">
                <h3 class="text-lg sm:text-xl font-bold text-neutral mb-2" data-vh="step_<?php echo $i; ?>_title">
                  <?php echo esc_html( vh_mod( "step_{$i}_title", '' ) ?: $step_defs[ $i - 1 ][0] ); ?>
                </h3>
                <p class="text-sm sm:text-base text-base-content/80 leading-relaxed" data-vh="step_<?php echo $i; ?>_text">
                  <?php echo esc_html( vh_mod( "step_{$i}_text", '' ) ?: $step_defs[ $i - 1 ][1] ); ?>
                </p>
              </div>
            </div>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       COMPARAÇÃO (Sozinho vs VivaHost)
  ════════════════════════════════════════ -->
  <?php if ( vh_mod( 'compare_show', '1' ) ) : ?>
  <section class="vh-section vh-section--compare py-20 md:py-28 bg-base-200/50" aria-label="Comparação: sozinho vs VivaHost">
    <div class="inner max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12 sm:mb-16">
        <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">Por que escolher a VivaHost?</span>
        <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight" data-vh="compare_title">
          <?php echo esc_html( vh_mod( 'compare_title', 'Hospedar por conta própria vs Operação VivaHost' ) ); ?>
        </h2>
      </div>
      <div class="compare-grid grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
        <div class="compare-card compare-card--bad card bg-white border border-base-200 shadow-sm rounded-3xl p-7 sm:p-9 flex flex-col justify-between">
          <div>
            <div class="compare-card__header flex items-center gap-3 pb-6 border-b border-base-200 mb-6">
              <span class="compare-icon-wrap compare-icon-wrap--bad w-10 h-10 rounded-xl bg-error/10 text-error flex items-center justify-center flex-shrink-0">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </span>
              <h3 class="text-xl font-bold text-neutral" data-vh="compare_left_title"><?php echo esc_html( vh_mod( 'compare_left_title', 'Por conta própria' ) ); ?></h3>
            </div>
            <ul class="compare-card__list space-y-4">
              <?php
              $left_defs = [
                'Você resolve toda a rotina — mensagens, reservas, check-in e chamados 24h',
                'Preço sem dados de mercado — receita e ocupação abaixo do potencial',
                'Limpeza, enxoval, amenidades e reparos — tudo sob sua responsabilidade direta',
              ];
              for ( $i = 1; $i <= 3; $i++ ) :
              ?>
                <li class="compare-card__item flex items-start gap-3 text-sm sm:text-base text-base-content/80 leading-relaxed" data-vh="compare_left_<?php echo $i; ?>">
                  <span class="compare-icon compare-icon--bad text-error mt-1 flex-shrink-0">
                    <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="4" x2="4" y2="12"/><line x1="4" y1="4" x2="12" y2="12"/></svg>
                  </span>
                  <span><?php echo esc_html( vh_mod( "compare_left_{$i}", $left_defs[ $i - 1 ] ) ); ?></span>
                </li>
              <?php endfor; ?>
            </ul>
          </div>
        </div>

        <div class="compare-card compare-card--good card bg-white border-2 border-primary/30 shadow-xl rounded-3xl p-7 sm:p-9 relative ring-1 ring-primary/20 flex flex-col justify-between">
          <div>
            <div class="compare-card__header flex items-center justify-between gap-3 pb-6 border-b border-base-200 mb-6">
              <div class="flex items-center gap-3">
                <span class="compare-icon-wrap compare-icon-wrap--good w-10 h-10 rounded-xl bg-success/15 text-success flex items-center justify-center flex-shrink-0">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <h3 class="text-xl font-bold text-neutral" data-vh="compare_right_title"><?php echo esc_html( vh_mod( 'compare_right_title', 'Com a VivaHost' ) ); ?></h3>
              </div>
              <span class="compare-badge badge badge-primary text-white text-xs font-semibold gap-1 py-3 px-3">
                <svg viewBox="0 0 16 16" class="w-3 h-3 fill-current" aria-hidden="true"><path d="m8.5 7.6 3.1-1.75 1.47-.82a.83.83 0 0 0 .43-.73V1.33a.83.83 0 0 0-.83-.83H3.33a.83.83 0 0 0-.83.83V4.3c0 .3.16.59.43.73l3 1.68 1.57.88c.35.2.65.2 1 0zm-.5.9a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7z"></path></svg>
                Superhost
              </span>
            </div>
            <ul class="compare-card__list space-y-4 mb-8">
              <?php
              $right_defs = [
                'Equipe local cuida de tudo — você acompanha os resultados da sua hospedagem com tranquilidade',
                'Estratégia de preços dinâmica — busca por ocupação máxima e histórico de até +30% de receita',
                'Higienização hoteleira e amenidades — acomodação sempre pronta e impecável para o próximo hóspede',
              ];
              for ( $i = 1; $i <= 3; $i++ ) :
              ?>
                <li class="compare-card__item flex items-start gap-3 text-sm sm:text-base text-neutral font-medium leading-relaxed" data-vh="compare_right_<?php echo $i; ?>">
                  <span class="compare-icon compare-icon--good text-success mt-1 flex-shrink-0">
                    <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="13 4 6 11 3 8"/></svg>
                  </span>
                  <span><?php echo esc_html( vh_mod( "compare_right_{$i}", $right_defs[ $i - 1 ] ) ); ?></span>
                </li>
              <?php endfor; ?>
            </ul>
          </div>
          <div class="compare-card__footer pt-2">
            <a href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Vi a comparação no site e quero a operação da VivaHost na minha hospedagem.' ) ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-block rounded-xl text-white font-bold shadow-md hover:shadow-lg compare-cta">Quero a operação VivaHost →</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ═══════════════════════════════════════
       SERVIÇOS
  ════════════════════════════════════════ -->
  <!-- ═══════════════════════════════════════
       SERVIÇOS
  ════════════════════════════════════════ -->
  <section class="vh-section vh-section--off py-20 md:py-28 bg-base-200/40" id="servicos" aria-label="Serviços">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="services-intro anim-fade flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-16">
        <div class="max-w-2xl">
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">Serviços incluídos</span>
          <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight" data-vh="services_title">
            <?php echo esc_html( vh_mod( 'services_title', 'Serviços completos de operação e hospitalidade em Salvador' ) ); ?>
          </h2>
        </div>
        <div class="services-intro-right max-w-md flex flex-col items-start md:items-end">
          <p class="text-sm sm:text-base text-base-content/80 leading-relaxed mb-4 md:text-right" data-vh="services_intro">
            <?php echo esc_html( vh_mod( 'services_intro', 'Da produção fotográfica ao suporte presencial 24h, oferecemos toda a estrutura operacional e de hospitalidade para o sucesso da sua hospedagem por temporada.' ) ); ?>
          </p>
          <a href="#contato" class="btn btn-outline btn-sm rounded-full px-6 font-semibold hover:bg-neutral hover:text-white transition-all">Solicitar proposta para meu imóvel →</a>
        </div>
      </div>
      <div class="services-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        <?php
        $svc_icons = [
          '<svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>',
          '<svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>',
          '<svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>',
          '<svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
          '<svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
          '<svg viewBox="0 0 24 24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
        ];
        $svc_defs = [
          [ 'Fotografia profissional',       'Ensaio completo com produção visual dedicada. Imagens atraentes que valorizam os diferenciais da acomodação.' ],
          [ 'Estratégia de preços dinâmica', 'Monitoramento contínuo de demanda, sazonalidade e concorrência local para otimizar o valor das diárias.' ],
          [ 'Check-in & Check-out',          'Acolhimento atencioso para cada hóspede, instruções claras de acesso e suporte presencial na chegada.' ],
          [ 'Governança & Higienização',     'Equipe dedicada de limpeza, troca de enxoval de qualidade e reposição de amenidades a cada reserva.' ],
          [ 'Suporte e Acompanhamento 24h',  'Atendimento rápido aos hóspedes durante toda a estadia e suporte operacional para qualquer imprevisto.' ],
          [ 'Relatórios de desempenho',      'Demonstrativo claro com ocupação, diárias e avaliações recebidas, com repasse mensal pontual.' ],
        ];
        for ( $i = 1; $i <= 6; $i++ ) :
        ?>
          <div class="service-card card bg-white border border-base-200 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 rounded-3xl p-7 anim-fade">
            <div class="service-icon w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mb-5">
              <?php echo $svc_icons[ $i - 1 ]; ?>
            </div>
            <h3 class="text-xl font-bold text-neutral mb-2" data-vh="service_<?php echo $i; ?>_title">
              <?php echo esc_html( vh_mod( "service_{$i}_title", '' ) ?: $svc_defs[ $i - 1 ][0] ); ?>
            </h3>
            <p class="text-sm text-base-content/80 leading-relaxed" data-vh="service_<?php echo $i; ?>_text">
              <?php echo esc_html( vh_mod( "service_{$i}_text", '' ) ?: $svc_defs[ $i - 1 ][1] ); ?>
            </p>
          </div>
        <?php endfor; ?>
      </div>
      <p class="services-commission text-center mt-10 text-sm text-base-content/70">
        Taxa a partir de <strong class="text-neutral font-bold"><span data-vh="commission_rate"><?php echo esc_html( vh_mod( 'commission_rate', '20%' ) ); ?></span></strong> sobre o valor das reservas pelos serviços operacionais — sem taxa de adesão, sem fidelidade.
      </p>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       IMÓVEIS
  ════════════════════════════════════════ -->
  <section class="vh-section py-20 md:py-28 bg-base-100" id="imoveis" aria-label="Imóveis">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="imoveis-header anim-fade flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-16">
        <div>
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">Imóveis em operação</span>
          <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight" data-vh="imoveis_title">
            <?php echo esc_html( vh_mod( 'imoveis_title', 'Conheça algumas das propriedades atendidas pela VivaHost' ) ); ?>
          </h2>
        </div>
        <p class="max-w-md text-sm sm:text-base text-base-content/80 leading-relaxed md:text-right" data-vh="imoveis_desc">
          <?php echo esc_html( vh_mod( 'imoveis_desc', 'Cada acomodação recebe o mesmo padrão de cuidado operacional — fotos de qualidade, limpeza rigorosa e experiência do hóspede comprovada em avaliações.' ) ); ?>
        </p>
      </div>
      <div class="imoveis-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        $featured_properties = array_slice( vh_get_all_properties(), 0, 3 );
        $ico_star = '<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-3.5 h-3.5 text-primary"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';

        foreach ( $featured_properties as $idx => $prop ) :
          $i = $idx + 1;
          $bairro_short = explode( ',', $prop['loc'] )[0];
        ?>
          <article class="imovel-card card bg-white border border-base-200 shadow-md hover:shadow-2xl transition-all duration-300 rounded-3xl overflow-hidden group anim-fade flex flex-col justify-between" itemscope itemtype="https://schema.org/Product">
            <div class="imovel-photo relative overflow-hidden aspect-[4/3] bg-base-200">
              <img src="<?php echo esc_url( $prop['photo'] ); ?>" alt="<?php echo esc_attr( $prop['name'] ); ?>" loading="lazy" width="600" height="450" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              <div class="imovel-badge absolute top-4 right-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-sm font-bold text-xs text-neutral" data-vh="prop_<?php echo $i; ?>_rating">
                <?php echo $ico_star; ?><span><?php echo esc_html( $prop['rating'] ); ?></span>
              </div>
              <div class="imovel-pill-loc absolute bottom-4 left-4 bg-black/65 backdrop-blur-md px-3 py-1 rounded-full text-white text-xs font-semibold">
                <?php echo esc_html( trim( $bairro_short ) ); ?>
              </div>
            </div>
            <div class="imovel-body p-6 flex flex-col justify-between flex-1">
              <div>
                <div class="imovel-location text-xs font-semibold uppercase tracking-wider text-primary mb-1.5" data-vh="prop_<?php echo $i; ?>_loc"><?php echo esc_html( $prop['loc'] ); ?></div>
                <h3 class="imovel-name text-lg font-bold text-neutral mb-3 line-clamp-2 leading-snug" itemprop="name" data-vh="prop_<?php echo $i; ?>_name"><?php echo esc_html( $prop['name'] ); ?></h3>
              </div>
              <div class="imovel-stats flex items-center justify-between pt-4 border-t border-base-200 text-xs mt-auto">
                <span class="imovel-reviews text-base-content/70 font-medium" data-vh="prop_<?php echo $i; ?>_reviews"><?php echo esc_html( $prop['reviews'] ); ?> avaliações</span>
                <a href="<?php echo esc_url( $prop['link'] ); ?>" target="_blank" rel="noopener noreferrer" class="imovel-airbnb btn btn-link btn-xs text-primary font-bold no-underline hover:underline p-0 flex items-center gap-1">
                  <span>Ver no Airbnb</span>
                  <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 001.06 0l7.22-7.22v5.69a.75.75 0 001.5 0v-7.5a.75.75 0 00-.75-.75h-7.5a.75.75 0 000 1.5h5.69l-7.22 7.22a.75.75 0 000 1.06z" clip-rule="evenodd"/></svg>
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-12 flex flex-col items-center justify-center gap-3">
        <a href="<?php echo esc_url( home_url( '/imoveis/' ) ); ?>" class="btn btn-primary btn-brand-coral rounded-full px-8 sm:px-10 py-3.5 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all">Ver todos os imóveis (12) →</a>
        <a href="<?php echo esc_url( vh_mod( 'footer_airbnb', 'https://www.airbnb.com.br/users/show/148412228' ) ); ?>" target="_blank" rel="noopener noreferrer" class="text-xs text-base-content/60 hover:text-primary transition-colors underline">Ver perfil no Airbnb</a>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       PANORAMA SALVADOR — FAROL & PALMEIRA (2.SVG)
  ════════════════════════════════════════ -->
  <div class="vh-svg-banner vh-svg-banner--farol border-y border-base-200/50" aria-hidden="true">
    <img src="<?php echo esc_url( VH_URL . '/assets/images/2.svg' ); ?>" alt="Farol da Barra e Palmeira — Salvador" loading="lazy" width="1920" height="1080">
  </div>

  <!-- ═══════════════════════════════════════
       DEPOIMENTO (MINIMAL & ELEGANTE)
  ════════════════════════════════════════ -->
  <?php
  $testimonial_photo_id  = (int) vh_mod( 'testimonial_photo', 0 );
  $testimonial_photo_url = $testimonial_photo_id ? wp_get_attachment_image_url( $testimonial_photo_id, 'thumbnail' ) : '';
  $testimonial_sub       = vh_mod( 'testimonial_banner_sub', 'Experiência de Proprietária' );
  ?>
  <section class="vh-testimonial-section py-16 md:py-20 bg-white border-b border-base-200/60 scroll-mt-20"
    id="depoimento"
    aria-label="Experiência de Proprietária"
    itemscope itemtype="https://schema.org/Review">
    
    <meta itemprop="itemReviewed" content="VivaHost">
    <span itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating" style="display:none">
      <meta itemprop="ratingValue" content="5">
      <meta itemprop="bestRating" content="5">
    </span>

    <div class="inner max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center anim-fade">
      
      <!-- Minimal Eyebrow -->
      <div class="inline-flex items-center gap-2 mb-6">
        <span class="badge badge-ghost text-[11px] font-semibold tracking-wider uppercase py-2 px-3 border border-base-300 bg-white/80 text-base-content/70 shadow-sm" data-vh="testimonial_banner_sub">
          <?php echo esc_html( $testimonial_sub ); ?>
        </span>
      </div>

      <!-- 5 Golden Stars -->
      <div class="flex items-center justify-center gap-1.5 text-amber-400 mb-6" aria-label="5 estrelas">
        <?php for ( $s = 0; $s < 5; $s++ ) echo '<svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>'; ?>
      </div>

      <!-- Editorial Minimal Quote -->
      <blockquote class="quote-text text-xl sm:text-2xl md:text-3xl font-medium text-neutral tracking-tight leading-relaxed mb-8" data-vh="testimonial_text" itemprop="reviewBody">
        &ldquo;<?php echo esc_html( vh_mod( 'testimonial_text', 'Márcia e sua equipe cuidam da hospedagem com dedicação, limpeza impecável e uma proatividade exemplar. Desde que comecei a contar com a VivaHost para a operação da hospedagem, não tenho com o que me preocupar.' ) ); ?>&rdquo;
      </blockquote>

      <!-- Minimal Author Details -->
      <div class="quote-author flex items-center justify-center gap-3.5">
        <div class="avatar">
          <?php if ( $testimonial_photo_url ) : ?>
            <div class="w-12 h-12 rounded-full ring-1 ring-base-300 shadow-sm overflow-hidden">
              <img src="<?php echo esc_url( $testimonial_photo_url ); ?>" alt="<?php echo esc_attr( vh_mod( 'testimonial_author', 'Ana Paula M.' ) ); ?>" class="author-avatar author-avatar--photo object-cover w-full h-full">
            </div>
          <?php else : ?>
            <div class="w-12 h-12 rounded-full bg-base-200 text-neutral font-bold text-sm flex items-center justify-center ring-1 ring-base-300 shadow-sm" aria-hidden="true">AP</div>
          <?php endif; ?>
        </div>
        <div class="author-info text-left">
          <div class="author-name font-bold text-sm sm:text-base text-neutral flex items-center gap-1.5" data-vh="testimonial_author" itemprop="author">
            <span><?php echo esc_html( vh_mod( 'testimonial_author', 'Ana Paula M.' ) ); ?></span>
            <svg class="w-4 h-4 text-primary fill-current" viewBox="0 0 20 20" title="Verificada"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          </div>
          <div class="author-city text-xs text-base-content/65" data-vh="testimonial_city">
            <?php echo esc_html( vh_mod( 'testimonial_city', 'Proprietária · Salvador, BA' ) ); ?>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ═══════════════════════════════════════
       HOST (MÁRCIA SALES) — HUMAN, EDITORIAL & MINIMAL
  ════════════════════════════════════════ -->
  <section class="vh-section py-20 md:py-28 bg-base-200/30" id="sobre-nos" aria-label="Sobre a anfitriã" itemscope itemtype="https://schema.org/Person">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="host-wrap grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
        
        <!-- Large Organic Photo with Integrated Superhost Badge (No enclosing card/box) -->
        <div class="host-visual lg:col-span-5 flex justify-center order-1 lg:order-2">
          <div class="host-avatar-container relative inline-block">
            <!-- Free-standing Circular Portrait (Compact & without white border) -->
            <div class="w-52 h-52 sm:w-64 sm:h-64 lg:w-72 lg:h-72 rounded-full overflow-hidden shadow-xl aspect-square">
              <?php if ( $host_photo_url ) : ?>
                <img src="<?php echo esc_url( $host_photo_url ); ?>" alt="<?php echo esc_attr( vh_mod( 'host_name', 'Márcia Sales' ) ); ?>" class="host-avatar host-avatar--photo object-cover w-full h-full" itemprop="image">
              <?php else : ?>
                <div class="host-avatar w-full h-full bg-primary/10 flex items-center justify-center font-bold text-3xl text-primary" aria-hidden="true">MS</div>
              <?php endif; ?>
            </div>

            <!-- Integrated Superhost Badge (Directly anchored on photo edge, NO white circle or border) -->
            <?php if ( vh_mod( 'host_superhost', '1' ) ) : ?>
              <div class="host-superhost-medal absolute bottom-1 right-1 sm:bottom-2 sm:right-2 z-10 transition-transform hover:scale-105 duration-300" aria-label="Superhost verificado no Airbnb" title="Superhost Verificado no Airbnb">
                <img src="<?php echo esc_url( VH_URL . '/assets/images/badge_purple.png' ); ?>" alt="Superhost Airbnb" class="w-14 h-14 sm:w-16 sm:h-16 drop-shadow-xl object-contain">
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Human, Editorial Bio & Metrics -->
        <div class="host-text lg:col-span-7 order-2 lg:order-1">
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">
            Quem cuida do seu imóvel
          </span>
          <h2 class="host-name text-3xl sm:text-4xl lg:text-5xl font-extrabold text-neutral tracking-tight mb-2" data-vh="host_name" itemprop="name">
            <?php echo esc_html( vh_mod( 'host_name', 'Márcia Sales' ) ); ?>
          </h2>
          <div class="host-subtitle text-base sm:text-lg font-semibold text-primary mb-5" data-vh="host_subtitle">
            <?php echo esc_html( vh_mod( 'host_subtitle', 'Anfitriã em Salvador & Superhost Airbnb há 9 anos' ) ); ?>
          </div>
          <p class="host-bio text-base sm:text-lg text-base-content/85 leading-relaxed mb-6" data-vh="host_bio" itemprop="description">
            <?php echo esc_html( vh_mod( 'host_bio', 'Moro em Salvador e recebo viajantes há quase uma década. Acredito que hospitalidade de verdade é estar perto: conhecer cada detalhe do imóvel, receber quem chega com carinho e cuidar da conservação como se fosse a minha própria casa. Para você, proprietário, isso significa máxima rentabilidade com tranquilidade e transparência absoluta.' ) ); ?>
          </p>

          <!-- Sleek Horizontal Metrics Row -->
          <div class="host-metrics grid grid-cols-2 sm:grid-cols-4 gap-4 py-5 border-y border-base-200/80 my-6">
            <div>
              <div class="text-2xl sm:text-3xl font-black text-neutral" data-vh="host_stat_1_num">
                <?php echo esc_html( vh_mod( 'host_stat_1_num', '469' ) ); ?>
              </div>
              <div class="text-xs text-base-content/65 font-medium mt-0.5" data-vh="host_stat_1_label">
                <?php echo esc_html( vh_mod( 'host_stat_1_label', 'Avaliações' ) ); ?>
              </div>
            </div>
            <div>
              <div class="text-2xl sm:text-3xl font-black text-neutral inline-flex items-center gap-1" data-vh="host_stat_2_num">
                <?php
                $stat2 = vh_mod( 'host_stat_2_num', '4,88 ★' );
                $clean_stat2 = str_replace( '★', '', $stat2 );
                echo esc_html( trim( $clean_stat2 ) );
                ?><span class="text-amber-400 text-xl">★</span>
              </div>
              <div class="text-xs text-base-content/65 font-medium mt-0.5" data-vh="host_stat_2_label">
                <?php echo esc_html( vh_mod( 'host_stat_2_label', 'Nota média' ) ); ?>
              </div>
            </div>
            <div>
              <div class="text-2xl sm:text-3xl font-black text-neutral" data-vh="host_stat_3_num">
                <?php echo esc_html( vh_mod( 'host_stat_3_num', '9 anos' ) ); ?>
              </div>
              <div class="text-xs text-base-content/65 font-medium mt-0.5" data-vh="host_stat_3_label">
                <?php echo esc_html( vh_mod( 'host_stat_3_label', 'Hospedando' ) ); ?>
              </div>
            </div>
            <div>
              <div class="text-2xl sm:text-3xl font-black text-neutral" data-vh="host_properties_count">
                <?php echo esc_html( vh_mod( 'host_properties_count', '15' ) ); ?>
              </div>
              <div class="text-xs text-base-content/65 font-medium mt-0.5" data-vh="host_properties_label">
                <?php echo esc_html( vh_mod( 'host_properties_label', 'Acomodações' ) ); ?>
              </div>
            </div>
          </div>

          <!-- CTAs -->
          <div class="host-actions flex items-center gap-4 flex-wrap pt-2">
            <a href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Gostaria de conversar sobre a operação da minha hospedagem.' ) ); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-brand-coral rounded-full px-7 text-white font-bold shadow-md hover:shadow-lg flex items-center gap-2">
              <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.103 1.523 5.824L.057 23.5a.5.5 0 0 0 .61.61l5.734-1.46A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.5-5.24-1.376l-.375-.213-3.882.99.998-3.795-.232-.387A9.946 9.946 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
              <span>Conversar com a Marcia</span>
            </a>
            <a href="<?php echo esc_url( vh_mod( 'host_airbnb', 'https://www.airbnb.com.br/users/show/148412228' ) ); ?>" target="_blank" rel="noopener" class="btn btn-ghost rounded-full px-6 font-semibold hover:bg-black/5">
              Ver perfil no Airbnb →
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       REVIEWS
  ════════════════════════════════════════ -->
  <section class="vh-section py-20 md:py-28 bg-base-100" aria-label="Avaliações">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="reviews-header anim-fade flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-16">
        <div>
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">O que dizem os hóspedes</span>
          <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight">Avaliações reais do Airbnb</h2>
        </div>
        <div class="reviews-score flex items-center gap-4 bg-base-200/60 p-4 rounded-2xl border border-base-200">
          <span class="reviews-score-num text-3xl sm:text-4xl font-black text-neutral" data-vh="reviews_score">
            <?php echo esc_html( vh_mod( 'reviews_score', '4,88' ) ); ?>
          </span>
          <div class="reviews-score-detail">
            <div class="score-stars flex items-center gap-0.5 text-amber-400" aria-label="5 estrelas">
              <?php for ( $s = 0; $s < 5; $s++ ) echo '<svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" aria-hidden="true"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>'; ?>
            </div>
            <span class="score-count text-xs font-semibold text-base-content/70 mt-1 block" data-vh="reviews_count">
              <?php echo esc_html( vh_mod( 'reviews_count', '469' ) ); ?> avaliações verificadas
            </span>
          </div>
        </div>
      </div>
      <div class="reviews-grid grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        <?php
        $av_classes = [ 'av-1', 'av-2', 'av-3' ];
        $rev_defs = [
          [ 'O apartamento é bem localizado, super arrumado, seguro e a anfitriã é extraordinária. Me senti em casa, super indico.', 'Simone', 'Aracaju, Brasil', VH_URL . '/assets/images/reviewer-simone.webp' ],
          [ 'Localização perfeita, próximo dos principais pontos turísticos da cidade. Apartamento aconchegante como se estivesse em casa. Márcia muito atenciosa e prestativa. Uma vista maravilhosa do mar...', 'Ismael', 'São Paulo, Brasil', VH_URL . '/assets/images/reviewer-ismael.webp' ],
          [ 'Muito obrigado pela estadia! Foi tudo maravilhoso e nos sentimos muito bem acolhidos. A experiência foi incrível, o lugar é lindo e ficará guardado com carinho na memória.', 'Cleiton', 'Recife, Brasil', VH_URL . '/assets/images/reviewer-cleiton.webp' ],
        ];
        for ( $i = 1; $i <= 3; $i++ ) :
          $rev_photo_id  = (int) vh_mod( "review_{$i}_photo", 0 );
          $rev_photo_url = $rev_photo_id ? wp_get_attachment_image_url( $rev_photo_id, 'thumbnail' ) : $rev_defs[ $i - 1 ][3];
          $rev_text      = vh_mod( "review_{$i}_text", '' ) ?: $rev_defs[ $i - 1 ][0];
          $rev_author    = vh_mod( "review_{$i}_name", '' ) ?: ( vh_mod( "review_{$i}_author", '' ) ?: $rev_defs[ $i - 1 ][1] );
          $rev_city      = vh_mod( "review_{$i}_loc", '' ) ?: ( vh_mod( "review_{$i}_city", '' ) ?: $rev_defs[ $i - 1 ][2] );
        ?>
          <article class="review-card card bg-white border border-base-200 shadow-sm hover:shadow-xl transition-all duration-300 rounded-3xl p-7 flex flex-col justify-between anim-fade" itemscope itemtype="https://schema.org/Review">
            <div>
              <meta itemprop="itemReviewed" content="VivaHost">
              <span itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating" style="display:none">
                <meta itemprop="ratingValue" content="5">
                <meta itemprop="bestRating" content="5">
              </span>
              <div class="review-stars flex items-center gap-1 text-amber-400 mb-4" aria-label="5 estrelas">
                <?php for ( $s = 0; $s < 5; $s++ ) echo '<svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" aria-hidden="true"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>'; ?>
              </div>
              <p class="review-text text-sm sm:text-base text-base-content/85 leading-relaxed italic mb-6" data-vh="review_<?php echo $i; ?>_text" itemprop="reviewBody">
                &ldquo;<?php echo esc_html( $rev_text ); ?>&rdquo;
              </p>
            </div>
            <div class="review-author flex items-center gap-3 pt-4 border-t border-base-200">
              <div class="avatar">
                <?php if ( $rev_photo_url ) : ?>
                  <div class="w-11 h-11 rounded-full overflow-hidden ring-1 ring-base-300">
                    <img src="<?php echo esc_url( $rev_photo_url ); ?>" alt="<?php echo esc_attr( $rev_author ); ?>" class="review-avatar review-avatar--photo object-cover w-full h-full">
                  </div>
                <?php else : ?>
                  <div class="w-11 h-11 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm review-avatar <?php echo $av_classes[ $i - 1 ]; ?>" aria-hidden="true">
                    <?php echo mb_substr( $rev_author, 0, 1 ); ?>
                  </div>
                <?php endif; ?>
              </div>
              <div>
                <div class="review-name font-bold text-sm text-neutral" data-vh="review_<?php echo $i; ?>_name" itemprop="author">
                  <?php echo esc_html( $rev_author ); ?>
                </div>
                <div class="review-city text-xs text-base-content/70" data-vh="review_<?php echo $i; ?>_loc">
                  <?php echo esc_html( $rev_city ); ?>
                </div>
              </div>
            </div>
          </article>
        <?php endfor; ?>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       BLOG & DICAS (INTEGRADO WORDPRESS)
  ════════════════════════════════════════ -->
  <?php if ( vh_mod( 'blog_section_show', '1' ) ) : ?>
  <section class="vh-section py-20 md:py-28 bg-base-100" id="blog" aria-label="Blog e Dicas de Salvador">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="blog-header anim-fade flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 sm:mb-16">
        <div>
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3" data-vh="blog_section_eyebrow">
            <?php echo esc_html( vh_mod( 'blog_section_eyebrow', 'Hospitalidade & Temporada' ) ); ?>
          </span>
          <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight" data-vh="blog_section_title">
            <?php echo esc_html( vh_mod( 'blog_section_title', 'Dicas e novidades sobre hospitalidade e temporada em Salvador' ) ); ?>
          </h2>
        </div>
        <p class="max-w-md text-sm sm:text-base text-base-content/80 leading-relaxed md:text-right" data-vh="blog_section_intro">
          <?php echo esc_html( vh_mod( 'blog_section_intro', 'Estratégias operacionais, dicas de hospitalidade e orientações práticas para proprietários que desejam maximizar a performance no Airbnb na Bahia.' ) ); ?>
        </p>
      </div>

      <div class="blog-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        $blog_query = new WP_Query( [
          'post_type'           => 'post',
          'posts_per_page'      => 3,
          'post_status'         => 'publish',
          'ignore_sticky_posts' => true,
        ] );

        $fallback_posts = [
          [
            'title'   => 'Como Maximizar a Performance da sua Hospedagem no Airbnb em Salvador',
            'excerpt' => 'Descubra as melhores práticas de precificação dinâmica, preparação da acomodação e hospitalidade para encantar hóspedes em Salvador.',
            'cat'     => 'Estratégia & Hospitalidade',
            'img'     => 'https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?auto=format&fit=crop&w=800&q=80',
            'date'    => '24 de Setembro, 2026',
            'link'    => '#contato',
          ],
          [
            'title'   => 'Hospedagem por Temporada em Salvador: Boas Práticas e Regras de Condomínio',
            'excerpt' => 'Entenda como manter uma convivência harmônica com o condomínio, receber hóspedes com segurança e garantir tranquilidade para todos.',
            'cat'     => 'Hospitalidade & Convivência',
            'img'     => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
            'date'    => '18 de Setembro, 2026',
            'link'    => '#contato',
          ],
          [
            'title'   => 'Os Bairros mais Procurados de Salvador para Hospedagem de Temporada em 2026',
            'excerpt' => 'Barra, Ondina, Rio Vermelho ou Costa Azul? Analisamos perfil de viajantes, demanda turística e fluxo de hóspedes nos principais bairros da capital baiana.',
            'cat'     => 'Destinos & Temporada',
            'img'     => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
            'date'    => '10 de Setembro, 2026',
            'link'    => '#contato',
          ],
        ];

        if ( $blog_query->have_posts() ) :
          $idx = 0;
          while ( $blog_query->have_posts() ) : $blog_query->the_post();
            $p_id       = get_the_ID();
            $cats       = get_the_category();
            $cat_title  = ( ! empty( $cats ) && $cats[0]->name !== 'Uncategorized' && $cats[0]->name !== 'Sem categoria' ) ? $cats[0]->name : $fallback_posts[ $idx % 3 ]['cat'];
            $p_thumb    = vh_get_post_image( $p_id, 'medium_large' );
            $idx++;
        ?>
            <article class="blog-card card bg-white border border-base-200 shadow-md hover:shadow-2xl transition-all duration-300 rounded-3xl overflow-hidden group flex flex-col anim-fade">
              <a href="<?php the_permalink(); ?>" class="blog-photo-wrap block aspect-[16/10] overflow-hidden bg-base-200 relative">
                <img src="<?php echo esc_url( $p_thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="badge badge-primary text-white text-xs font-semibold py-3 px-3 shadow-md absolute top-4 left-4">
                  <?php echo esc_html( $cat_title ); ?>
                </span>
              </a>
              <div class="blog-body p-6 flex flex-col flex-grow justify-between">
                <div>
                  <div class="blog-meta text-xs text-base-content/60 font-medium mb-2.5 flex items-center gap-2">
                    <span><?php echo esc_html( get_the_date( 'd \d\e F, Y' ) ); ?></span>
                  </div>
                  <h3 class="blog-title text-lg font-bold text-neutral group-hover:text-primary transition-colors leading-snug line-clamp-2 mb-3">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h3>
                  <p class="blog-excerpt text-xs sm:text-sm text-base-content/75 leading-relaxed line-clamp-3 mb-4">
                    <?php echo esc_html( get_the_excerpt() ); ?>
                  </p>
                </div>
                <div class="pt-4 border-t border-base-200 flex items-center justify-between">
                  <a href="<?php the_permalink(); ?>" class="btn btn-link btn-xs text-primary font-bold no-underline hover:underline p-0 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    <span>Ler artigo completo</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                  </a>
                </div>
              </div>
            </article>
        <?php
          endwhile;
          wp_reset_postdata();
        else :
          // Fallback nativo
          foreach ( $fallback_posts as $fpost ) :
        ?>
            <article class="blog-card card bg-white border border-base-200 shadow-md hover:shadow-2xl transition-all duration-300 rounded-3xl overflow-hidden group flex flex-col anim-fade">
              <div class="blog-photo-wrap block aspect-[16/10] overflow-hidden bg-base-200 relative">
                <img src="<?php echo esc_url( $fpost['img'] ); ?>" alt="<?php echo esc_attr( $fpost['title'] ); ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <span class="badge badge-primary text-white text-xs font-semibold py-3 px-3 shadow-md absolute top-4 left-4">
                  <?php echo esc_html( $fpost['cat'] ); ?>
                </span>
              </div>
              <div class="blog-body p-6 flex flex-col flex-grow justify-between">
                <div>
                  <div class="blog-meta text-xs text-base-content/60 font-medium mb-2.5">
                    <span><?php echo esc_html( $fpost['date'] ); ?></span>
                  </div>
                  <h3 class="blog-title text-lg font-bold text-neutral group-hover:text-primary transition-colors leading-snug line-clamp-2 mb-3">
                    <a href="#contato"><?php echo esc_html( $fpost['title'] ); ?></a>
                  </h3>
                  <p class="blog-excerpt text-xs sm:text-sm text-base-content/75 leading-relaxed line-clamp-3 mb-4">
                    <?php echo esc_html( $fpost['excerpt'] ); ?>
                  </p>
                </div>
                <div class="pt-4 border-t border-base-200 flex items-center justify-between">
                  <a href="#contato" class="btn btn-link btn-xs text-primary font-bold no-underline hover:underline p-0 flex items-center gap-1">
                    <span>Saber mais no WhatsApp →</span>
                  </a>
                </div>
              </div>
            </article>
        <?php
          endforeach;
        endif;
        ?>
      </div>

      <div class="text-center mt-12 flex justify-center">
        <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="btn btn-outline btn-neutral rounded-full px-8 py-3 text-sm font-bold hover:btn-primary hover:text-white transition-all shadow-sm">Ver todos os artigos do blog →</a>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ═══════════════════════════════════════
       PANORAMA SALVADOR — SKYLINE PELOURINHO (1.SVG)
  ════════════════════════════════════════ -->
  <div class="vh-svg-banner vh-svg-banner--skyline border-y border-base-200/50" aria-hidden="true">
    <img src="<?php echo esc_url( VH_URL . '/assets/images/1.svg' ); ?>" alt="Skyline Salvador — Pelourinho" loading="lazy" width="1920" height="1080">
  </div>

  <!-- ═══════════════════════════════════════
       FAQ
  ════════════════════════════════════════ -->
  <section class="vh-section vh-section--off py-20 md:py-28 bg-base-200/40 vh-faq" id="faq" aria-label="Perguntas frequentes" itemscope itemtype="https://schema.org/FAQPage">
    <div class="inner max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">FAQ</span>
        <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight">Perguntas frequentes</h2>
      </div>
      <div class="join join-vertical w-full bg-white border border-base-200 rounded-3xl overflow-hidden shadow-sm divide-y divide-base-200">
        <?php
        $commission_rate = vh_mod( 'commission_rate', '20%' );
        $faqs = [
          [ 'Quanto custam os serviços de operação?',
            'Nossa taxa de serviço é a partir de <strong data-vh="commission_rate">' . esc_html( $commission_rate ) . '</strong> sobre o valor das reservas confirmadas. Não cobramos taxa de adesão, mensalidade fixa nem multa de fidelidade. Nossa remuneração está diretamente atrelada ao sucesso das suas estadias.' ],
          [ 'O imóvel precisa estar mobiliado e equipado?',
            'Sim, a acomodação deve estar mobiliada e preparada para estadias de curta temporada. Orientamos detalhadamente sobre utensílios, roupas de cama e itens indispensáveis para garantir avaliações 5 estrelas.' ],
          [ 'Quanto tempo leva para iniciar as reservas?',
            'Em média <strong>7 dias</strong> após a visita técnica e alinhamento inicial. Realizamos as fotografias profissionais, configuramos os anúncios e calibramos a estratégia de preços antes de abrir o calendário.' ],
          [ 'Como ocorrem os repasses e a prestação de contas?',
            'O repasse é realizado mensalmente, acompanhado de um relatório detalhado com todas as reservas do período, diárias médias, ocupação e avaliações recebidas.' ],
          [ 'Existe período mínimo de contrato ou fidelidade?',
            'Não há fidelidade nem multas rescisórias. Prezamos pela parceria e transparência: você pode interromper os serviços operacionais a qualquer momento com prévio aviso.' ],
          [ 'Como é tratada a sazonalidade e períodos com menor fluxo?',
            'Utilizamos estratégias de precificação dinâmica para estimular reservas mesmo em períodos de menor demanda turística, equilibrando taxa de ocupação e receita líquida ao longo do ano.' ],
        ];
        foreach ( $faqs as $i => $faq ) :
        ?>
        <details class="collapse collapse-arrow join-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
          <summary class="collapse-title text-base sm:text-lg font-bold text-neutral py-5 px-6 sm:px-8 cursor-pointer hover:bg-base-200/40 transition-colors" itemprop="name">
            <span><?php echo esc_html( $faq[0] ); ?></span>
          </summary>
          <div class="collapse-content px-6 sm:px-8 pb-6 text-sm sm:text-base text-base-content/80 leading-relaxed" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
            <p itemprop="text" class="m-0 pt-2 border-t border-base-200/60"><?php echo wp_kses_post( $faq[1] ); ?></p>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
      <p class="text-center text-sm text-base-content/70 mt-8">
        Ainda com dúvidas? <a href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Tenho uma dúvida sobre os serviços da VivaHost.' ) ); ?>" target="_blank" rel="noopener" class="text-primary font-bold hover:underline">Fale com a Marcia no WhatsApp →</a>
      </p>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       CTA + FORM
  ════════════════════════════════════════ -->
  <section class="vh-section vh-section--cta py-20 md:py-28 bg-gradient-to-br from-neutral via-neutral/95 to-neutral/90 text-white relative overflow-hidden" id="contato" aria-label="Formulário de avaliação">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="cta-wrap grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
        <div class="cta-left lg:col-span-6 anim-fade">
          <span class="eyebrow badge badge-primary text-white text-xs font-bold uppercase tracking-wider mb-4 px-3 py-2">Análise de Potencial</span>
          <h2 class="section-title text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight mb-4" data-vh="cta_title">
            <?php echo esc_html( vh_mod( 'cta_title', 'Descubra o potencial de faturamento da sua hospedagem' ) ); ?>
          </h2>
          <p class="cta-lead text-base sm:text-lg text-white/80 leading-relaxed mb-8" data-vh="cta_lead">
            <?php echo esc_html( vh_mod( 'cta_lead', 'Receba uma projeção personalizada de desempenho para a sua acomodação em Salvador, com base em dados de mercado e sem compromisso.' ) ); ?>
          </p>
          <ul class="cta-trust-list space-y-4 mb-8">
            <?php
            $ico_check = '<svg viewBox="0 0 24 24" class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
            $ico_shield = '<svg viewBox="0 0 24 24" class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
            $ico_clock = '<svg viewBox="0 0 24 24" class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
            $trust_icons = [ $ico_check, $ico_shield, $ico_clock ];
            $trust_keys = [ 'trust_1', 'trust_2', 'trust_3' ];
            $trust_defaults = [
              'Análise de potencial gratuita e sem compromisso',
              'Seus dados tratados com total privacidade',
              'Retorno em até 24 horas úteis por WhatsApp',
            ];
            foreach ( $trust_keys as $idx => $key ) :
            ?>
              <li class="flex items-center gap-3 text-sm sm:text-base text-white/90">
                <span class="trust-icon w-8 h-8 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0"><?php echo $trust_icons[ $idx ]; ?></span>
                <span data-vh="<?php echo $key; ?>">
                  <?php echo esc_html( vh_mod( $key, '' ) ?: $trust_defaults[ $idx ] ); ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php if ( $form_mode !== 'email' ) : ?>
            <div class="pt-4 border-t border-white/15">
              <p class="text-xs text-white/60 mb-2 font-medium">Prefere falar diretamente?</p>
              <a href="<?php echo esc_url( vh_wa_url( 'Olá! Gostaria de uma análise de potencial para minha acomodação.' ) ); ?>" target="_blank" rel="noopener" class="cta-wa-link inline-flex items-center gap-2 text-white font-bold hover:text-white/80 transition-colors">
                <span class="w-8 h-8 rounded-full bg-success flex items-center justify-center text-white">
                  <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.103 1.523 5.824L.057 23.5a.5.5 0 0 0 .61.61l5.734-1.46A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.5-5.24-1.376l-.375-.213-3.882.99.998-3.795-.232-.387A9.946 9.946 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                </span>
                <span data-vh="form_wa_cta">
                  <?php echo esc_html( vh_mod( 'form_wa_cta', 'Conversar pelo WhatsApp' ) ); ?>
                </span>
              </a>
            </div>
          <?php endif; ?>
        </div>

        <div class="lg:col-span-6 anim-fade">
          <div class="form-card card bg-white text-neutral shadow-2xl rounded-3xl p-7 sm:p-10 border border-white/20 w-full max-w-lg mx-auto">
            <h3 class="text-2xl font-bold text-neutral mb-1">Quer saber mais?</h3>
            <p class="form-sub text-sm text-base-content/70 mb-6">Deixe seu contato que a Marcia responde em até 24 horas para apresentar o potencial da sua hospedagem.</p>
            <?php
            $vh_form_ts     = time();
            $vh_form_sig    = function_exists( 'vh_sign_form_timestamp' ) ? vh_sign_form_timestamp( $vh_form_ts ) : '';
            $vh_form_chal   = function_exists( 'vh_get_form_js_challenge' ) ? vh_get_form_js_challenge( $vh_form_ts ) : '';
            $vh_cf_site_key = function_exists( 'vh_sec_opt' ) ? trim( (string) vh_sec_opt( 'vh_sec_turnstile_site', '' ) ) : '';
            ?>
            <form id="estimate-form" method="post" novalidate data-vh-challenge="<?php echo esc_attr( $vh_form_chal ); ?>">
              <?php wp_nonce_field( 'vh_form_nonce', 'nonce' ); ?>
              <input type="hidden" name="_vh_ts" value="<?php echo esc_attr( (string) $vh_form_ts ); ?>">
              <input type="hidden" name="_vh_sig" value="<?php echo esc_attr( $vh_form_sig ); ?>">
              <input type="hidden" name="_vh_js_token" id="vh_js_token" value="">
              <!-- Triple Honeypot (invisible to humans, bots fill it) -->
              <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden" aria-hidden="true">
                <label for="vh-website">Website</label>
                <input type="text" id="vh-website" name="website" tabindex="-1" autocomplete="off">
                <label for="vh_hp_check">Deixe em branco</label>
                <input type="text" id="vh_hp_check" name="vh_hp_check" tabindex="-1" autocomplete="off">
                <label for="vh_email_confirm">Confirme seu email</label>
                <input type="email" id="vh_email_confirm" name="vh_email_confirm" tabindex="-1" autocomplete="off">
              </div>
              <div class="form-control mb-4">
                <label class="label pb-1.5" for="f-nome">
                  <span class="label-text font-bold text-xs uppercase tracking-wider text-base-content/70">Nome completo</span>
                </label>
                <input type="text" id="f-nome" name="nome" placeholder="Seu nome" required maxlength="80" autocomplete="name" class="input input-bordered w-full rounded-xl focus:border-primary focus:outline-none bg-base-100">
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div class="form-control">
                  <label class="label pb-1.5" for="f-cidade">
                    <span class="label-text font-bold text-xs uppercase tracking-wider text-base-content/70">Cidade / Bairro</span>
                  </label>
                  <input type="text" id="f-cidade" name="cidade" placeholder="Salvador — Barra" required maxlength="80" class="input input-bordered w-full rounded-xl focus:border-primary focus:outline-none bg-base-100">
                </div>
                <div class="form-control">
                  <label class="label pb-1.5" for="f-tipo">
                    <span class="label-text font-bold text-xs uppercase tracking-wider text-base-content/70">Tipo de imóvel</span>
                  </label>
                  <select id="f-tipo" name="tipo" required class="select select-bordered w-full rounded-xl focus:border-primary focus:outline-none bg-base-100 font-normal">
                    <option value="" disabled selected>Selecione</option>
                    <option value="Apartamento">Apartamento</option>
                    <option value="Casa">Casa</option>
                    <option value="Studio / Kitnet">Studio / Kitnet</option>
                    <option value="Cobertura">Cobertura</option>
                    <option value="Outro">Outro</option>
                  </select>
                </div>
              </div>
              <div class="form-control mb-4">
                <label class="label pb-1.5" for="f-quartos">
                  <span class="label-text font-bold text-xs uppercase tracking-wider text-base-content/70">Número de quartos</span>
                </label>
                <select id="f-quartos" name="quartos" required class="select select-bordered w-full rounded-xl focus:border-primary focus:outline-none bg-base-100 font-normal">
                  <option value="" disabled selected>Quantos quartos?</option>
                  <option value="1 quarto">1 quarto</option>
                  <option value="2 quartos">2 quartos</option>
                  <option value="3 quartos">3 quartos</option>
                  <option value="4 ou mais">4 ou mais</option>
                </select>
              </div>
              <div class="form-control mb-6">
                <label class="label pb-1.5" for="f-whatsapp">
                  <span class="label-text font-bold text-xs uppercase tracking-wider text-base-content/70">Seu WhatsApp</span>
                </label>
                <input type="tel" id="f-whatsapp" name="whatsapp" placeholder="(71) 99999-9999" required maxlength="22" autocomplete="tel" class="input input-bordered w-full rounded-xl focus:border-primary focus:outline-none bg-base-100">
              </div>
              <?php if ( $vh_cf_site_key ) : ?>
                <div class="mb-4 flex justify-center">
                  <div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $vh_cf_site_key ); ?>" data-theme="light"></div>
                </div>
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
              <?php endif; ?>
              <div class="form-submit">
                <button type="submit" class="btn btn-primary btn-block rounded-xl text-white font-bold py-3.5 shadow-lg hover:shadow-xl transition-all" id="form-submit-btn">
                  <span class="btn-text">Solicitar análise de potencial →</span>
                  <span class="btn-loading flex items-center justify-center gap-2" style="display:none">
                    <span class="loading loading-spinner loading-sm"></span> Enviando…
                  </span>
                </button>
              </div>
              <p class="form-note text-xs text-center text-base-content/60 mt-4 flex items-center justify-center gap-1.5"><svg viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-base-content/50" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg><span>Sem spam. Protegido contra robôs · Seus dados estão seguros (LGPD).</span></p>
              <div class="form-error alert alert-error text-xs rounded-xl mt-4" id="form-error" role="alert" aria-live="polite" style="display:none"></div>
            </form>
            <div class="form-success text-center py-6" id="form-success" role="status" aria-live="polite" tabindex="-1" aria-hidden="true" style="display:none">
              <div class="success-icon w-16 h-16 rounded-full bg-success/15 text-success flex items-center justify-center mx-auto mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-8 h-8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <h4 class="text-xl font-bold text-neutral mb-2">Recebemos sua mensagem!</h4>
              <p class="text-sm text-base-content/80 mb-6">Marcia vai entrar em contato pelo WhatsApp em até 24 horas para apresentar o potencial da sua hospedagem e explicar a operação da VivaHost.</p>
              <a id="form-success-wa-btn" href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Acabei de solicitar a análise da minha acomodação no site da VivaHost.' ) ); ?>" target="_blank" rel="noopener" class="btn btn-block bg-success hover:bg-success/90 text-white font-bold rounded-xl flex items-center justify-center gap-2 shadow-md">
                <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.103 1.523 5.824L.057 23.5a.5.5 0 0 0 .61.61l5.734-1.46A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.5-5.24-1.376l-.375-.213-3.882.99.998-3.795-.232-.387A9.946 9.946 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                <span>Continuar no WhatsApp agora</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ═══════════════════════════════════════
       FOOTER
  ════════════════════════════════════════ -->
  <?php get_template_part( 'template-parts/footer-site' ); ?>

</main>

<?php wp_footer(); // injects WA float + scripts ?>

<!-- ═══ Back to Top ═══ -->
<button id="vh-back-to-top" class="vh-back-to-top fixed bottom-7 right-7 z-40 btn btn-circle btn-primary text-white shadow-xl hover:shadow-2xl transition-all duration-300" aria-label="Voltar ao topo" title="Voltar ao topo">
  <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<?php if ( '0' !== get_option( 'vh_gen_cookie_bar', '1' ) ) : ?>
<!-- ═══ Cookie Consent Minimal ═══ -->
<div id="vh-cookie-bar" class="vh-cookie-bar fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 bg-white/95 backdrop-blur-xl border border-base-200 shadow-2xl rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4" role="dialog" aria-label="Aviso de cookies" style="display:none">
  <p class="text-xs text-neutral m-0 leading-relaxed">Usamos cookies essenciais para o funcionamento do site. Ao continuar, você aceita o uso de cookies.</p>
  <div class="vh-cookie-actions flex items-center justify-end gap-3 w-full sm:w-auto flex-shrink-0">
    <a href="<?php echo esc_url( home_url( '/privacidade/' ) ); ?>" class="text-xs font-semibold text-base-content/70 hover:text-neutral underline">Saiba mais</a>
    <button id="vh-cookie-accept" class="btn btn-primary btn-sm rounded-lg text-white font-bold px-4">Aceitar</button>
  </div>
</div>
<?php endif; ?>

</body>
</html>