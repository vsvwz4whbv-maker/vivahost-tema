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
          <?php echo esc_html( vh_mod( 'hero_eyebrow', 'Gestão de Temporada — Salvador, BA' ) ); ?>
        </span>
      </div>
      <h1 class="hero-title text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight max-w-4xl mx-auto mb-6 drop-shadow-lg" data-vh="hero_title">
        <?php echo esc_html( vh_mod( 'hero_title', 'Rentabilidade máxima no Airbnb em Salvador — com tranquilidade total' ) ); ?>
      </h1>
      <p class="hero-sub text-base sm:text-lg lg:text-xl text-white/95 max-w-2xl mx-auto leading-relaxed mb-10 font-normal drop-shadow" data-vh="hero_subtitle">
        <?php echo esc_html( vh_mod( 'hero_subtitle', 'Administramos seu imóvel de temporada do início ao fim: precificação inteligente, anúncios em alta e cuidado presencial em Salvador. Você só acompanha os rendimentos na conta.' ) ); ?>
      </p>
      <div class="hero-actions flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="<?php echo esc_url( vh_mod( 'hero_btn1_link', '#contato' ) ); ?>" class="btn btn-primary btn-brand-coral btn-lg rounded-full px-8 text-white font-bold shadow-xl hover:shadow-2xl hover:scale-105 transition-all w-full sm:w-auto" data-vh="hero_btn1_text">
          <?php echo esc_html( vh_mod( 'hero_btn1_text', 'Avaliar meu imóvel' ) ); ?>
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
    <div class="stats-inner stats shadow-xl bg-white border border-base-200 rounded-3xl w-full grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-base-200">
      <?php
      $stat_fallbacks = [
        [ '+30%',   'Faturamento vs tradicional' ],
        [ '85%',    'Taxa média de ocupação' ],
        [ '4,88 ★', 'Avaliação dos hóspedes' ],
        [ '9 anos', 'Superhost em Salvador' ],
      ];
      for ( $i = 1; $i <= 4; $i++ ) :
      ?>
        <div class="stat place-items-center text-center p-6 anim-fade">
          <?php if ( $i === 3 ) : ?>
            <div class="stat-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-neutral tracking-tight">
              <span class="stat-num inline-flex items-center gap-1" data-vh="stat_<?php echo $i; ?>_num">
                <?php echo esc_html( str_replace( '★', '', vh_mod( "stat_{$i}_num", $stat_fallbacks[$i-1][0] ) ) ); ?>
                <span class="star-yellow text-amber-400">★</span>
              </span>
            </div>
          <?php else : ?>
            <div class="stat-value text-2xl sm:text-3xl lg:text-4xl font-extrabold text-neutral tracking-tight">
              <span class="stat-num" data-vh="stat_<?php echo $i; ?>_num">
                <?php echo esc_html( vh_mod( "stat_{$i}_num", $stat_fallbacks[$i-1][0] ) ); ?>
              </span>
            </div>
          <?php endif; ?>
          <div class="stat-title text-xs sm:text-sm font-medium text-base-content/70 mt-1 whitespace-normal">
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
            <?php echo esc_html( vh_mod( 'process_title', 'Como funciona a gestão do seu imóvel' ) ); ?>
          </h2>
          <p class="text-base text-base-content/80 leading-relaxed mb-6" data-vh="process_intro">
            <?php echo esc_html( vh_mod( 'process_intro', 'Do primeiro contato ao repasse dos lucros, assumimos toda a operação para você não se preocupar com nada.' ) ); ?>
          </p>
          <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-200/80 text-xs sm:text-sm font-semibold text-neutral border border-base-200">
            <span>⏱ Da vistoria ao anúncio ativo em até 7 dias</span>
          </div>
        </div>
        <div class="process-steps lg:col-span-7 flex flex-col gap-5">
          <?php
          $step_defs = [
            [ 'Diagnóstico gratuito', 'Avaliamos o perfil do seu imóvel e projetamos o faturamento real para a sua localização em Salvador.' ],
            [ 'Produção e anúncio',   'Sessão fotográfica profissional e cadastro estratégico nos principais canais de locação por temporada.' ],
            [ 'Operação 360°',        'Recepção de hóspedes, atendimento 24h e equipe dedicada para higienização e manutenção preventiva.' ],
            [ 'Repasse e extrato',    'Depósito pontual dos rendimentos e prestação de contas transparente todo mês.' ],
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
          <?php echo esc_html( vh_mod( 'compare_title', 'Alugar por conta própria vs Gestão VivaHost' ) ); ?>
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
                'Você resolve tudo — reservas, hóspedes, check-in, problemas 24h',
                'Preço baseado no chute — receita abaixo do potencial',
                'Limpeza, roupa de cama, reposição — tudo por sua conta',
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
                'A gente resolve — você só recebe o aluguel e dorme tranquilo',
                'Precificação dinâmica — ocupação máxima e +30% de receita',
                'Equipe própria de limpeza e amenities — imóvel sempre pronto',
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
            <a href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Vi a comparação no site e quero a gestão VivaHost no meu imóvel.' ) ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-block rounded-xl text-white font-bold shadow-md hover:shadow-lg compare-cta">Quero a VivaHost →</a>
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
            <?php echo esc_html( vh_mod( 'services_title', 'Serviços completos de gestão Airbnb em Salvador' ) ); ?>
          </h2>
        </div>
        <div class="services-intro-right max-w-md flex flex-col items-start md:items-end">
          <p class="text-sm sm:text-base text-base-content/80 leading-relaxed mb-4 md:text-right" data-vh="services_intro">
            <?php echo esc_html( vh_mod( 'services_intro', 'Da foto profissional ao suporte 24h, somos a única administradora de imóveis que você vai precisar para sua hospedagem por temporada.' ) ); ?>
          </p>
          <a href="#contato" class="btn btn-outline btn-sm rounded-full px-6 font-semibold hover:bg-neutral hover:text-white transition-all">Conhecer os planos →</a>
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
          [ 'Fotografia profissional',  'Ensaio fotográfico completo com equipamento profissional. Fotos que convertem visitas em reservas.' ],
          [ 'Precificação dinâmica',    'Algoritmo que ajusta a diária em tempo real conforme demanda, sazonalidade e concorrência local.' ],
          [ 'Check-in & Check-out',     'Recepção personalizada para cada hóspede, entrega de chaves e orientação completa sobre o imóvel.' ],
          [ 'Limpeza & Amenidades',     'Equipe própria de limpeza, troca de roupas de cama e reposição de amenidades a cada estadia.' ],
          [ 'Suporte 24 horas',         'Atendimento imediato para hóspedes e manutenção preventiva e corretiva em qualquer horário.' ],
          [ 'Relatórios mensais',       'Dashboard com receita, ocupação e avaliações. Transferência automática todo mês.' ],
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
        Comissão a partir de <strong class="text-neutral font-bold"><span data-vh="commission_rate"><?php echo esc_html( vh_mod( 'commission_rate', '20%' ) ); ?></span></strong> sobre o aluguel — sem taxa fixa, sem fidelidade.
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
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">Imóveis em destaque</span>
          <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight" data-vh="imoveis_title">
            <?php echo esc_html( vh_mod( 'imoveis_title', 'Conheça alguns dos imóveis que gerenciamos' ) ); ?>
          </h2>
        </div>
        <p class="max-w-md text-sm sm:text-base text-base-content/80 leading-relaxed md:text-right" data-vh="imoveis_desc">
          <?php echo esc_html( vh_mod( 'imoveis_desc', 'Cada propriedade é cuidada com o mesmo padrão — fotos profissionais, limpeza impecável e avaliações que falam por si.' ) ); ?>
        </p>
      </div>
      <!-- Filter tabs -->
      <div class="imoveis-filter-bar flex flex-wrap items-center justify-center gap-2 mb-10 anim-fade">
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="costa-azul">Costa Azul (6)</button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="barra">Barra (2)</button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="ondina">Ondina (1)</button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-outline btn-neutral font-semibold transition-all" data-filter="outros">Amaralina &amp; Outros (3)</button>
        <button type="button" class="vh-prop-filter btn btn-sm rounded-full btn-primary text-white font-semibold transition-all active" data-filter="all">Ver todos (12)</button>
      </div>

      <div class="imoveis-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php
        $all_properties = [
          [
            'name'    => 'Condomínio Ondina, vista para o mar',
            'loc'     => 'Ondina, Salvador',
            'filter'  => 'ondina',
            'rating'  => '4,90',
            'reviews' => '143',
            'photo'   => VH_URL . '/assets/images/prop-ondina-vista.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/42109543',
          ],
          [
            'name'    => 'Lar Lisboa — Costa Azul',
            'loc'     => 'Costa Azul, Salvador',
            'filter'  => 'costa-azul',
            'rating'  => '4,92',
            'reviews' => '83',
            'photo'   => VH_URL . '/assets/images/prop-lar-lisboa.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1197921583374763834',
          ],
          [
            'name'    => 'Apartamento em Salvador',
            'loc'     => 'Barra, Salvador',
            'filter'  => 'barra',
            'rating'  => '5,0',
            'reviews' => '59',
            'photo'   => VH_URL . '/assets/images/prop-apartamento-salvador.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1311644791404145582',
          ],
          [
            'name'    => 'Conforto / Temporada Costa Azul',
            'loc'     => 'Costa Azul, Salvador',
            'filter'  => 'costa-azul',
            'rating'  => '4,94',
            'reviews' => '18',
            'photo'   => VH_URL . '/assets/images/prop-conforto-costa-azul.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1064965973143964840',
          ],
          [
            'name'    => 'Studio Amaralina, Conforto e Mar',
            'loc'     => 'Amaralina, Salvador',
            'filter'  => 'outros',
            'rating'  => '5,0',
            'reviews' => '15',
            'photo'   => VH_URL . '/assets/images/prop-studio-amaralina.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1352825919438529862',
          ],
          [
            'name'    => 'Apto Moderno no Costa Azul, Vista Para o Mar',
            'loc'     => 'Costa Azul, Salvador',
            'filter'  => 'costa-azul',
            'rating'  => '4,92',
            'reviews' => '12',
            'photo'   => VH_URL . '/assets/images/prop-moderno-costa-azul.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1563394501361409294',
          ],
          [
            'name'    => 'Apart / 3 Quartos na Barra',
            'loc'     => 'Barra, Salvador',
            'filter'  => 'barra',
            'rating'  => '5,0',
            'reviews' => '8',
            'photo'   => VH_URL . '/assets/images/prop-barra-3-quartos.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1703976122563823281',
          ],
          [
            'name'    => 'Estilo e Conforto na Costa Azul',
            'loc'     => 'Costa Azul, Salvador',
            'filter'  => 'costa-azul',
            'rating'  => '5,0',
            'reviews' => '6',
            'photo'   => VH_URL . '/assets/images/prop-estilo-costa-azul.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1616242988721327670',
          ],
          [
            'name'    => 'Loft Aconchegante em Salvador',
            'loc'     => 'Salvador, Bahia',
            'filter'  => 'outros',
            'rating'  => '5,0',
            'reviews' => '5',
            'photo'   => VH_URL . '/assets/images/prop-loft-salvador.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1704553564833020627',
          ],
          [
            'name'    => 'Aconchego com Vista para o Mar',
            'loc'     => 'Costa Azul, Salvador',
            'filter'  => 'costa-azul',
            'rating'  => '5,0',
            'reviews' => '4',
            'photo'   => VH_URL . '/assets/images/prop-aconchego-mar.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1743632765287287212',
          ],
          [
            'name'    => 'Refúgio Praia do Flamengo',
            'loc'     => 'Praia do Flamengo, Salvador',
            'filter'  => 'outros',
            'rating'  => '5,0',
            'reviews' => '4',
            'photo'   => VH_URL . '/assets/images/prop-refugio-flamengo.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1636458869058323320',
          ],
          [
            'name'    => 'Stúdio Confortável Próximo ao Mar',
            'loc'     => 'Costa Azul, Salvador',
            'filter'  => 'costa-azul',
            'rating'  => '5,0',
            'reviews' => '3',
            'photo'   => VH_URL . '/assets/images/prop-studio-confortavel.webp',
            'link'    => 'https://www.airbnb.com.br/rooms/1735032299246360363',
          ],
        ];

        $ico_star = '<svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="w-3.5 h-3.5 text-primary"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>';

        foreach ( $all_properties as $idx => $prop ) :
          $i = $idx + 1;
          $ph_id  = (int) vh_mod( "prop_{$i}_photo", 0 );
          $ph_url = $ph_id ? wp_get_attachment_image_url( $ph_id, 'large' ) : ( isset( $prop_fallbacks[ $i ] ) ? $prop_fallbacks[ $i ] : $prop['photo'] );
          $p_name = esc_html( vh_mod( "prop_{$i}_name", '' ) ?: $prop['name'] );
          $p_loc  = esc_html( vh_mod( "prop_{$i}_loc",  '' ) ?: $prop['loc'] );
          $p_rate = esc_html( vh_mod( "prop_{$i}_rating", '' ) ?: $prop['rating'] );
          $p_rev  = esc_html( vh_mod( "prop_{$i}_reviews", '' ) ?: $prop['reviews'] );
          $p_link = esc_url( vh_mod( "prop_{$i}_link", '' ) ?: $prop['link'] );
          $bairro_short = explode( ',', $p_loc )[0];
        ?>
          <article class="imovel-card card bg-white border border-base-200 shadow-md hover:shadow-2xl transition-all duration-300 rounded-3xl overflow-hidden group anim-fade flex flex-col justify-between" itemscope itemtype="https://schema.org/Product" data-filter="<?php echo esc_attr( $prop['filter'] ); ?>">
            <div class="imovel-photo relative overflow-hidden aspect-[4/3] bg-base-200">
              <img src="<?php echo esc_url( $ph_url ); ?>" alt="<?php echo esc_attr( $p_name ); ?>" loading="lazy" width="600" height="450" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              <div class="imovel-badge absolute top-4 right-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-full flex items-center gap-1.5 shadow-sm font-bold text-xs text-neutral" <?php if ( $i <= 3 ) echo 'data-vh="prop_' . $i . '_rating"'; ?>>
                <?php echo $ico_star; ?><span><?php echo $p_rate; ?></span>
              </div>
              <div class="imovel-pill-loc absolute bottom-4 left-4 bg-black/65 backdrop-blur-md px-3 py-1 rounded-full text-white text-xs font-semibold">
                <?php echo esc_html( trim( $bairro_short ) ); ?>
              </div>
            </div>
            <div class="imovel-body p-6 flex flex-col justify-between flex-1">
              <div>
                <div class="imovel-location text-xs font-semibold uppercase tracking-wider text-primary mb-1.5" <?php if ( $i <= 3 ) echo 'data-vh="prop_' . $i . '_loc"'; ?>><?php echo $p_loc; ?></div>
                <h3 class="imovel-name text-lg font-bold text-neutral mb-3 line-clamp-2 leading-snug" itemprop="name" <?php if ( $i <= 3 ) echo 'data-vh="prop_' . $i . '_name"'; ?>><?php echo $p_name; ?></h3>
              </div>
              <div class="imovel-stats flex items-center justify-between pt-4 border-t border-base-200 text-xs mt-auto">
                <span class="imovel-reviews text-base-content/70 font-medium" <?php if ( $i <= 3 ) echo 'data-vh="prop_' . $i . '_reviews"'; ?>><?php echo $p_rev; ?> avaliações</span>
                <a href="<?php echo $p_link; ?>" target="_blank" rel="noopener noreferrer" class="imovel-airbnb btn btn-link btn-xs text-primary font-bold no-underline hover:underline p-0 flex items-center gap-1">
                  <span>Ver no Airbnb</span>
                  <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 14.78a.75.75 0 001.06 0l7.22-7.22v5.69a.75.75 0 001.5 0v-7.5a.75.75 0 00-.75-.75h-7.5a.75.75 0 000 1.5h5.69l-7.22 7.22a.75.75 0 000 1.06z" clip-rule="evenodd"/></svg>
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-12">
        <a href="<?php echo esc_url( vh_mod( 'footer_airbnb', 'https://www.airbnb.com.br/users/show/148412228' ) ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-primary rounded-full px-8 font-bold hover:btn-brand-coral hover:text-white transition-all">Ver perfil completo no Airbnb (15 anúncios) →</a>
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
  $testimonial_sub       = vh_mod( 'testimonial_banner_sub', 'Depoimento de Proprietária' );
  ?>
  <section class="vh-testimonial-section py-16 md:py-20 bg-white border-b border-base-200/60 scroll-mt-20"
    id="depoimento"
    aria-label="Depoimento de Proprietária"
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
        &ldquo;<?php echo esc_html( vh_mod( 'testimonial_text', 'Márcia e sua equipe cuidam do imóvel com dedicação, limpeza impecável e uma proatividade que poucas profissionais têm. Desde que confiei a gestão a ela, não tenho com o que me preocupar.' ) ); ?>&rdquo;
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
       HOST
  ════════════════════════════════════════ -->
  <section class="vh-section vh-section--off py-20 md:py-28 bg-base-200/40" id="sobre-nos" aria-label="Sobre a host" itemscope itemtype="https://schema.org/Person">
    <div class="inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="host-wrap grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center anim-fade">
        <div class="host-text lg:col-span-7">
          <span class="eyebrow badge badge-ghost text-xs font-bold uppercase tracking-wider text-primary mb-3">Presença Local & Confiança</span>
          <h2 class="host-name text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight mb-2" data-vh="host_name" itemprop="name">
            <?php echo esc_html( vh_mod( 'host_name', 'Marcia Sales' ) ); ?>
          </h2>
          <div class="host-subtitle text-sm sm:text-base font-semibold text-primary mb-5" data-vh="host_subtitle">
            <?php echo esc_html( vh_mod( 'host_subtitle', 'Gestão Operacional VivaHost em Salvador' ) ); ?>
          </div>
          <p class="host-bio text-base text-base-content/80 leading-relaxed mb-6" data-vh="host_bio" itemprop="description">
            <?php echo esc_html( vh_mod( 'host_bio', 'Ao contrário de plataformas impessoais, nossa gestão tem presença física diária em Salvador. Cuido de cada imóvel com equipe local de confiança, garantindo padrão de conservação e diálogo direto com você a qualquer momento.' ) ); ?>
          </p>
          <div class="host-bullets space-y-3 mb-8">
            <div class="host-bullet flex items-start gap-3 text-sm sm:text-base text-neutral font-medium">
              <span class="w-6 h-6 rounded-full bg-success/15 text-success flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span><strong>Olhar de dona:</strong> vistorias detalhadas e acompanhamento presencial para proteger seu patrimônio.</span>
            </div>
            <div class="host-bullet flex items-start gap-3 text-sm sm:text-base text-neutral font-medium">
              <span class="w-6 h-6 rounded-full bg-success/15 text-success flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span><strong>Interlocutora única:</strong> você fala diretamente com quem está no controle da sua operação, sem intermediários.</span>
            </div>
            <div class="host-bullet flex items-start gap-3 text-sm sm:text-base text-neutral font-medium">
              <span class="w-6 h-6 rounded-full bg-success/15 text-success flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" aria-hidden="true"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span><strong>Histórico comprovado:</strong> 9 anos consecutivos como Superhost e reputação consolidada no mercado.</span>
            </div>
          </div>
          <div class="host-actions flex items-center gap-4 flex-wrap">
            <a href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Gostaria de conversar sobre a gestão do meu imóvel.' ) ); ?>" target="_blank" rel="noopener" class="btn btn-primary rounded-full px-7 text-white font-bold shadow-md hover:shadow-lg flex items-center gap-2">
              <svg viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.103 1.523 5.824L.057 23.5a.5.5 0 0 0 .61.61l5.734-1.46A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.5-5.24-1.376l-.375-.213-3.882.99.998-3.795-.232-.387A9.946 9.946 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
              <span>Conversar com a Marcia</span>
            </a>
            <a href="<?php echo esc_url( vh_mod( 'host_airbnb', 'https://www.airbnb.com.br/users/show/148412228' ) ); ?>" target="_blank" rel="noopener" class="btn btn-ghost rounded-full px-6 font-semibold hover:bg-black/5">
              Ver perfil no Airbnb →
            </a>
          </div>
        </div>
        <div class="host-visual lg:col-span-5 flex justify-center">
          <div class="host-passport-card card bg-white border border-base-200 shadow-xl rounded-3xl p-6 sm:p-8 text-center max-w-sm w-full">
            <div class="host-avatar-wrap relative inline-block mx-auto mb-4">
              <div class="avatar">
                <div class="w-28 h-28 rounded-full ring-4 ring-primary/20 shadow-md overflow-hidden">
                  <?php if ( $host_photo_url ) : ?>
                    <img src="<?php echo esc_url( $host_photo_url ); ?>" alt="<?php echo esc_attr( vh_mod( 'host_name', 'Marcia Sales' ) ); ?>" class="host-avatar host-avatar--photo object-cover w-full h-full" itemprop="image">
                  <?php else : ?>
                    <div class="host-avatar w-full h-full bg-primary/10 flex items-center justify-center font-bold text-2xl text-primary" aria-hidden="true">MS</div>
                  <?php endif; ?>
                </div>
              </div>
              <?php if ( vh_mod( 'host_superhost', '1' ) ) : ?>
                <div class="host-avatar-badge host-superhost-medal absolute bottom-0 right-0 w-8 h-8 rounded-full bg-white shadow-md p-1 border border-base-200" aria-label="Superhost verificado" title="Superhost Verificado no Airbnb">
                  <img src="<?php echo esc_url( VH_URL . '/assets/images/badge_purple.png' ); ?>" alt="Superhost" class="w-full h-full object-contain">
                </div>
              <?php endif; ?>
            </div>
            
            <div class="host-passport-info">
              <div class="host-passport-name text-lg font-bold text-neutral mb-1" data-vh="host_name"><?php echo esc_html( vh_mod( 'host_name', 'Marcia Sales' ) ); ?></div>
              <div class="text-xs text-base-content/70 font-semibold mb-5 flex items-center justify-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                <span>Superhost no Airbnb · Salvador, BA</span>
              </div>

              <!-- Verified Airbnb Metrics Grid inside Card -->
              <div class="grid grid-cols-2 gap-3 text-center pt-2 border-t border-base-200">
                <div class="p-2.5 rounded-xl bg-base-200/50">
                  <div class="text-lg font-extrabold text-neutral" data-vh="host_stat_1_num">
                    <?php echo esc_html( vh_mod( 'host_stat_1_num', '469' ) ); ?>
                  </div>
                  <div class="text-[11px] text-base-content/70" data-vh="host_stat_1_label">
                    <?php echo esc_html( vh_mod( 'host_stat_1_label', 'Avaliações' ) ); ?>
                  </div>
                </div>
                <div class="p-2.5 rounded-xl bg-base-200/50">
                  <div class="text-lg font-extrabold text-neutral inline-flex items-center justify-center gap-1" data-vh="host_stat_2_num">
                    <?php
                    $stat2 = vh_mod( 'host_stat_2_num', '4,88 ★' );
                    $clean_stat2 = str_replace( '★', '', $stat2 );
                    echo esc_html( trim( $clean_stat2 ) );
                    ?><span class="text-amber-400">★</span>
                  </div>
                  <div class="text-[11px] text-base-content/70" data-vh="host_stat_2_label">
                    <?php echo esc_html( vh_mod( 'host_stat_2_label', 'Nota média' ) ); ?>
                  </div>
                </div>
                <div class="p-2.5 rounded-xl bg-base-200/50">
                  <div class="text-lg font-extrabold text-neutral" data-vh="host_stat_3_num">
                    <?php echo esc_html( vh_mod( 'host_stat_3_num', '9 anos' ) ); ?>
                  </div>
                  <div class="text-[11px] text-base-content/70" data-vh="host_stat_3_label">
                    <?php echo esc_html( vh_mod( 'host_stat_3_label', 'Hospedando' ) ); ?>
                  </div>
                </div>
                <div class="p-2.5 rounded-xl bg-base-200/50">
                  <div class="text-lg font-extrabold text-neutral" data-vh="host_properties_count">
                    <?php echo esc_html( vh_mod( 'host_properties_count', '15' ) ); ?>
                  </div>
                  <div class="text-[11px] text-base-content/70" data-vh="host_properties_label">
                    <?php echo esc_html( vh_mod( 'host_properties_label', 'Acomodações' ) ); ?>
                  </div>
                </div>
              </div>

              <div class="mt-4 pt-3 border-t border-base-200/70 text-xs text-base-content/65 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-primary fill-current" viewBox="0 0 16 16"><path d="m8.5 7.6 3.1-1.75 1.47-.82a.83.83 0 0 0 .43-.73V1.33a.83.83 0 0 0-.83-.83H3.33a.83.83 0 0 0-.83.83V4.3c0 .3.16.59.43.73l3 1.68 1.57.88c.35.2.65.2 1 0zm-.5.9a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7z"></path></svg>
                <span>Superhost no Airbnb desde 2017</span>
              </div>
            </div>
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
            <?php echo esc_html( vh_mod( 'blog_section_eyebrow', 'Blog & Mercado Salvador' ) ); ?>
          </span>
          <h2 class="section-title text-3xl sm:text-4xl font-extrabold text-neutral tracking-tight" data-vh="blog_section_title">
            <?php echo esc_html( vh_mod( 'blog_section_title', 'Dicas e novidades sobre aluguel por temporada em Salvador' ) ); ?>
          </h2>
        </div>
        <p class="max-w-md text-sm sm:text-base text-base-content/80 leading-relaxed md:text-right" data-vh="blog_section_intro">
          <?php echo esc_html( vh_mod( 'blog_section_intro', 'Estratégias de mercado, dicas de hospitalidade e orientações práticas para proprietários que desejam maximizar a rentabilidade no Airbnb na Bahia.' ) ); ?>
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
            'title'   => 'Como Maximizar o Faturamento do seu Imóvel no Airbnb em Salvador: O Guia Definitivo',
            'excerpt' => 'Descubra as melhores estratégias de precificação dinâmica, preparação do imóvel e sazonalidade para lucrar mais com aluguel de temporada em Salvador.',
            'cat'     => 'Estratégia & Rentabilidade',
            'img'     => 'https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?auto=format&fit=crop&w=800&q=80',
            'date'    => '24 de Setembro, 2026',
            'link'    => '#contato',
          ],
          [
            'title'   => 'Aluguel por Temporada em Salvador: Regras de Condomínio e Legislação Atualizada',
            'excerpt' => 'Entenda como funciona a legislação brasileira para locação de curta temporada, convenções de condomínio e como garantir tranquilidade jurídica para seu imóvel.',
            'cat'     => 'Legislação & Segurança',
            'img'     => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
            'date'    => '18 de Setembro, 2026',
            'link'    => '#contato',
          ],
          [
            'title'   => 'Os Melhores Bairros de Salvador para Investir em Imóveis de Temporada em 2026',
            'excerpt' => 'Barra, Ondina, Rio Vermelho ou Costa Azul? Analisamos taxa de ocupação, perfil de público e retorno sobre investimento nos principais bairros da capital baiana.',
            'cat'     => 'Mercado Imobiliário',
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
          [ 'Quanto custa o serviço de gestão?',
            'Nossa comissão é a partir de <strong data-vh="commission_rate">' . esc_html( $commission_rate ) . '</strong> sobre o valor do aluguel. Não cobramos taxa fixa, taxa de adesão nem multa por cancelamento. Você só paga quando seu imóvel aluga.' ],
          [ 'Preciso ter um imóvel já mobiliado?',
            'Sim, o imóvel precisa estar mobiliado e equipado. Mas não se preocupe — ajudamos com orientações sobre o que é essencial para começar a receber hóspedes com sucesso.' ],
          [ 'Quanto tempo leva para meu imóvel começar a gerar receita?',
            'Em média <strong>7 dias</strong> após a vistoria inicial. Fazemos fotografia profissional, otimizamos o anúncio e ajustamos a precificação antes de publicar.' ],
          [ 'Como recebo os pagamentos?',
            'O repasse é feito mensalmente, por transferência bancária, após o check-out dos hóspedes. Você recebe um relatório detalhado com todas as movimentações.' ],
          [ 'Posso cancelar quando quiser?',
            'Sim, sem multa nem fidelidade. Você pode cancelar a qualquer momento, sem burocracia. Devolvemos o imóvel no mesmo estado que recebemos.' ],
          [ 'O que acontece se meu imóvel ficar vago?',
            'Trabalhamos com precificação dinâmica para maximizar a ocupação. Mesmo assim, períodos de baixa são normais no turismo — ajustamos a estratégia conforme a sazonalidade.' ],
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
        Ainda com dúvidas? <a href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Tenho uma dúvida sobre a gestão da VivaHost.' ) ); ?>" target="_blank" rel="noopener" class="text-primary font-bold hover:underline">Fale com a Marcia no WhatsApp →</a>
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
          <span class="eyebrow badge badge-primary text-white text-xs font-bold uppercase tracking-wider mb-4 px-3 py-2">Avaliação gratuita</span>
          <h2 class="section-title text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight mb-4" data-vh="cta_title">
            <?php echo esc_html( vh_mod( 'cta_title', 'Descubra o potencial de faturamento do seu imóvel' ) ); ?>
          </h2>
          <p class="cta-lead text-base sm:text-lg text-white/80 leading-relaxed mb-8" data-vh="cta_lead">
            <?php echo esc_html( vh_mod( 'cta_lead', 'Receba uma estimativa gratuita de rentabilidade para o seu apartamento em Salvador, sem qualquer compromisso.' ) ); ?>
          </p>
          <ul class="cta-trust-list space-y-4 mb-8">
            <?php
            $ico_check = '<svg viewBox="0 0 24 24" class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
            $ico_shield = '<svg viewBox="0 0 24 24" class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
            $ico_clock = '<svg viewBox="0 0 24 24" class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
            $trust_icons = [ $ico_check, $ico_shield, $ico_clock ];
            $trust_keys = [ 'trust_1', 'trust_2', 'trust_3' ];
            $trust_defaults = [
              'Avaliação gratuita e sem compromisso',
              'Seus dados tratados com total sigilo',
              'Resposta em até 24 horas úteis',
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
              <a href="<?php echo esc_url( vh_wa_url( 'Olá! Gostaria de uma avaliação gratuita para meu imóvel.' ) ); ?>" target="_blank" rel="noopener" class="cta-wa-link inline-flex items-center gap-2 text-white font-bold hover:text-white/80 transition-colors">
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
            <p class="form-sub text-sm text-base-content/70 mb-6">Deixe seu contato que a Marcia responde em até 24 horas.</p>
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
                  <span class="btn-text">Quero saber mais →</span>
                  <span class="btn-loading flex items-center justify-center gap-2" style="display:none">
                    <span class="loading loading-spinner loading-sm"></span> Enviando…
                  </span>
                </button>
              </div>
              <p class="form-note text-xs text-center text-base-content/60 mt-4">🔒 Sem spam. Protegido contra robôs · Seus dados estão seguros (LGPD).</p>
              <div class="form-error alert alert-error text-xs rounded-xl mt-4" id="form-error" role="alert" aria-live="polite" style="display:none"></div>
            </form>
            <div class="form-success text-center py-6" id="form-success" role="status" aria-live="polite" tabindex="-1" aria-hidden="true" style="display:none">
              <div class="success-icon w-16 h-16 rounded-full bg-success/15 text-success flex items-center justify-center mx-auto mb-4">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-8 h-8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              </div>
              <h4 class="text-xl font-bold text-neutral mb-2">Recebemos sua mensagem!</h4>
              <p class="text-sm text-base-content/80 mb-6">Marcia vai entrar em contato pelo WhatsApp em até 24 horas para explicar tudo sobre a gestão VivaHost.</p>
              <a id="form-success-wa-btn" href="<?php echo esc_url( vh_wa_url( 'Olá Marcia! Acabei de preencher a avaliação do meu imóvel no site da VivaHost.' ) ); ?>" target="_blank" rel="noopener" class="btn btn-block bg-success hover:bg-success/90 text-white font-bold rounded-xl flex items-center justify-center gap-2 shadow-md">
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
<div id="vh-cookie-bar" class="vh-cookie-bar fixed bottom-4 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md z-50 alert bg-white/95 backdrop-blur-xl border border-base-200 shadow-2xl rounded-2xl p-4 flex-row items-center justify-between gap-4" role="dialog" aria-label="Aviso de cookies" style="display:none">
  <p class="text-xs text-neutral m-0">Usamos cookies essenciais para o funcionamento do site. Ao continuar, você aceita o uso de cookies.</p>
  <div class="vh-cookie-actions flex items-center gap-3 flex-shrink-0">
    <a href="<?php echo esc_url( home_url( '/privacidade/' ) ); ?>" class="text-xs font-semibold text-base-content/70 hover:text-neutral underline">Saiba mais</a>
    <button id="vh-cookie-accept" class="btn btn-primary btn-sm rounded-lg text-white font-bold px-4">Aceitar</button>
  </div>
</div>
<?php endif; ?>

</body>
</html>