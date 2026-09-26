<?php
/**
 * VivaHost Child Theme — functions.php v3.1
 *
 * Standalone WordPress landing page theme (no Elementor required).
 * Everything configurable from Aspetto → Personalizza.
 *
 * @package VivahostChild
 */

defined( 'ABSPATH' ) || exit;

// ─── CONSTANTS ────────────────────────────────────────────────────────────────
define( 'VH_VER',  '3.6.0' );
define( 'VH_PATH', get_stylesheet_directory() );
define( 'VH_URL',  get_stylesheet_directory_uri() );

// Default values per le impostazioni del Customizer
define( 'VH_DEFAULT_PRIMARY',   '#ff385c' );
define( 'VH_DEFAULT_FOOTER_BG', '#222222' );
define( 'VH_DEFAULT_FONT',      'Plus Jakarta Sans' );
define( 'VH_DEFAULT_WA_NUM',    '5571999999999' );

// ─── THEME SUPPORT ───────────────────────────────────────────────────────────
add_action( 'after_setup_theme', function () {
	add_theme_support( 'custom-logo', [
		'height'      => 80,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	] );
	add_theme_support( 'title-tag' );       // wp_get_document_title()
	add_theme_support( 'post-thumbnails' );  // Immagini in evidenza per articoli
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', [ 'script', 'style' ] );
} );

// ─── 1. ENQUEUE ──────────────────────────────────────────────────────────────
add_action( 'wp_enqueue_scripts', 'vh_enqueue' );
function vh_enqueue() {
	// Parent
	wp_enqueue_style( 'hello-elementor', get_template_directory_uri() . '/style.css',
		[], wp_get_theme( 'hello-elementor' )->get( 'Version' ) );

	// Google Font (dynamic choice)
	$font      = vh_mod( 'font_family', VH_DEFAULT_FONT );
	$font_slug = urlencode( $font );
	wp_enqueue_style( 'vh-gfont',
		"https://fonts.googleapis.com/css2?family={$font_slug}:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap",
		[], null );

	// Main stylesheet
	wp_enqueue_style( 'vh-style', VH_URL . '/assets/css/vivahost.css',
		[ 'hello-elementor' ], VH_VER );

	// Dynamic CSS vars (colours, font)
	wp_add_inline_style( 'vh-style', vh_dynamic_css() );

	// Main JS
	wp_enqueue_script( 'vh-script', VH_URL . '/assets/js/vivahost.js',
		[], VH_VER, true );

	// Pass AJAX url + nonce + form mode to JS
	$wa_raw_digits = preg_replace( '/\D/', '', vh_mod( 'wa_number', VH_DEFAULT_WA_NUM ) );
	if ( strlen( $wa_raw_digits ) === 10 || strlen( $wa_raw_digits ) === 11 ) {
		$wa_raw_digits = '55' . $wa_raw_digits;
	}
	wp_localize_script( 'vh-script', 'VH', [
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'vh_form_nonce' ),
		'formMode'  => vh_mod( 'form_mode', 'both' ),
		'waNumber'  => $wa_raw_digits ?: VH_DEFAULT_WA_NUM,
		'waMessage' => vh_mod( 'wa_message', 'Olá! Gostaria de saber mais sobre a gestão VivaHost.' ),
	] );

	// Customizer live-preview JS
	if ( is_customize_preview() ) {
		wp_enqueue_script( 'vh-preview', VH_URL . '/assets/js/customizer-preview.js',
			[ 'customize-preview' ], VH_VER, true );
	}
}

/** Build inline CSS variables from Customizer values with dynamic RGB triplet and daisyUI mapping. */
function vh_dynamic_css() {
	$primary = sanitize_hex_color( vh_mod( 'color_primary', VH_DEFAULT_PRIMARY ) ) ?: VH_DEFAULT_PRIMARY;
	$footer  = sanitize_hex_color( vh_mod( 'color_footer',  VH_DEFAULT_FOOTER_BG ) ) ?: VH_DEFAULT_FOOTER_BG;
	$font    = esc_attr( vh_mod( 'font_family', VH_DEFAULT_FONT ) );

	// Extract RGB channels for alpha blending in CSS
	$hex = ltrim( $primary, '#' );
	if ( strlen( $hex ) === 3 ) {
		$r = hexdec( $hex[0] . $hex[0] );
		$g = hexdec( $hex[1] . $hex[1] );
		$b = hexdec( $hex[2] . $hex[2] );
	} elseif ( strlen( $hex ) === 6 ) {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	} else {
		$r = 255;
		$g = 56;
		$b = 92;
	}

	// Calculate HSL for daisyUI theme variable (--p)
	$rf = $r / 255;
	$gf = $g / 255;
	$bf = $b / 255;
	$max = max( $rf, $gf, $bf );
	$min = min( $rf, $gf, $bf );
	$l   = ( $max + $min ) / 2;
	if ( $max === $min ) {
		$h = $s = 0;
	} else {
		$d = $max - $min;
		$s = $l > 0.5 ? $d / ( 2 - $max - $min ) : $d / ( $max + $min );
		switch ( $max ) {
			case $rf: $h = ( ( $gf - $bf ) / $d ) + ( $gf < $bf ? 6 : 0 ); break;
			case $gf: $h = ( ( $bf - $rf ) / $d ) + 2; break;
			case $bf: $h = ( ( $rf - $gf ) / $d ) + 4; break;
		}
		$h *= 60;
	}
	$h_deg = round( $h );
	$s_pct = round( $s * 100 );
	$l_pct = round( $l * 100 );
	$p_hsl = "{$h_deg} {$s_pct}% {$l_pct}%";

	return ":root{--vh-primary:{$primary};--vh-primary-rgb:{$r},{$g},{$b};--vh-footer-bg:{$footer};--f:'{$font}',-apple-system,BlinkMacSystemFont,sans-serif;--p:{$p_hsl};--pf:{$h_deg} {$s_pct}% " . max( 0, $l_pct - 6 ) . "%;--pc:0 0% 100%;--color-primary:{$primary};--color-primary-content:#ffffff;}";
}

/**
 * Reusable WhatsApp URL helper with Brazilian country code (+55) formatting.
 *
 * @param string $custom_msg Custom pre-filled message (optional).
 * @return string Full https://wa.me/ URL.
 */
function vh_wa_url( $custom_msg = '' ) {
	$number = preg_replace( '/\D/', '', vh_mod( 'wa_number', VH_DEFAULT_WA_NUM ) );
	if ( strlen( $number ) === 10 || strlen( $number ) === 11 ) {
		$number = '55' . $number;
	}
	if ( empty( $number ) ) {
		$number = VH_DEFAULT_WA_NUM;
	}
	$msg = $custom_msg !== '' ? $custom_msg : vh_mod( 'wa_message', 'Olá! Gostaria de saber mais sobre a gestão VivaHost.' );
	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $msg );
}

// Preconnect Google Fonts
add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

// Body class
add_filter( 'body_class', fn( $c ) => array_merge( $c, [ 'vivahost-child' ] ) );

// ─── 2. WA FLOAT INJECTION (Rimosso su richiesta utente) ─────────────────────
// add_action( 'wp_footer', 'vh_inject_wa_float' );

// ─── 3. CUSTOMIZER ───────────────────────────────────────────────────────────
add_action( 'customize_register', 'vh_customize_register' );
function vh_customize_register( WP_Customize_Manager $c ) {

	// ── Helper closures ──────────────────────────────────────────────────────
	$text = fn( $id, $section, $label, $default = '', $transport = 'postMessage' ) =>
		vh_add( $c, $id, $section, $label, $default, 'text', $transport );
	$area = fn( $id, $section, $label, $default = '', $transport = 'postMessage' ) =>
		vh_add( $c, $id, $section, $label, $default, 'textarea', $transport );
	$sel  = fn( $id, $section, $label, $default, $choices, $transport = 'postMessage' ) =>
		vh_add( $c, $id, $section, $label, $default, 'select', $transport, $choices );
	$chk  = fn( $id, $section, $label, $default = '1' ) =>
		vh_add( $c, $id, $section, $label, $default, 'checkbox', 'postMessage' );
	$col  = function ( $id, $section, $label, $default ) use ( $c ) {
		$c->add_setting( "vivahost_{$id}", [ 'default' => $default, 'transport' => 'postMessage',
			'sanitize_callback' => 'sanitize_hex_color' ] );
		$c->add_control( new WP_Customize_Color_Control( $c, "vivahost_{$id}",
			[ 'label' => $label, 'section' => "vivahost_{$section}" ] ) );
	};
	$img  = function ( $id, $section, $label ) use ( $c ) {
		$c->add_setting( "vivahost_{$id}", [ 'default' => '', 'transport' => 'refresh',
			'sanitize_callback' => 'absint' ] );
		$ctrl = new WP_Customize_Media_Control( $c, "vivahost_{$id}",
			[ 'label' => $label, 'section' => "vivahost_{$section}", 'mime_type' => 'image' ] );
		$c->add_control( $ctrl );
	};

	// ── Main panel ───────────────────────────────────────────────────────────
	$c->add_panel( 'vivahost_panel', [
		'title'    => '🏠 VivaHost',
		'priority' => 25,
	] );

	// ── STILE ────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_style', [ 'title' => 'Stile & Colori', 'panel' => 'vivahost_panel', 'priority' => 10 ] );
	$col( 'color_primary', 'style', 'Colore principale (CTA / coral)', VH_DEFAULT_PRIMARY );
	$col( 'color_footer',  'style', 'Colore footer (sfondo scuro)',   VH_DEFAULT_FOOTER_BG );
	$sel( 'font_family', 'style', 'Font', VH_DEFAULT_FONT, [
		VH_DEFAULT_FONT => VH_DEFAULT_FONT,
		'Inter'         => 'Inter',
		'Lato'          => 'Lato',
		'Open Sans'     => 'Open Sans',
	] );
	$text( 'header_cta_text', 'style', 'Testo bottone header',  'Quero saber mais' );
	$text( 'header_cta_link', 'style', 'Link bottone header',   '#contato' );

	// ── HERO ─────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_hero', [ 'title' => 'Hero', 'panel' => 'vivahost_panel', 'priority' => 20 ] );
	$text( 'hero_eyebrow',   'hero', 'Eyebrow (sopra titolo)', 'Gestão de Temporada — Salvador, BA' );
	$text( 'hero_title',     'hero', 'Titolo (H1)',            'Rentabilidade máxima no Airbnb em Salvador — com tranquilidade total' );
	$area( 'hero_subtitle',  'hero', 'Sottotitolo',            'Administramos seu imóvel de temporada do início ao fim: precificação inteligente, anúncios em alta e cuidado presencial em Salvador. Você só acompanha os rendimentos na conta.' );
	$text( 'hero_btn1_text', 'hero', 'Bottone 1 — testo',      'Avaliar meu imóvel' );
	$text( 'hero_btn1_link', 'hero', 'Bottone 1 — link',       '#contato' );
	$text( 'hero_btn2_text', 'hero', 'Bottone 2 — testo',      'Como funciona' );
	$text( 'hero_btn2_link', 'hero', 'Bottone 2 — link',       '#como-funciona' );
	$img(  'hero_bg',        'hero', 'Immagine di sfondo hero' );

	// ── STATS ────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_stats', [ 'title' => 'Barra Numeri (4 Stats)', 'panel' => 'vivahost_panel', 'priority' => 30 ] );
	$defs = [
		[ '+30%',    'Faturamento vs tradicional' ],
		[ '85%',     'Taxa média de ocupação' ],
		[ '4,88 ★',  'Avaliação dos hóspedes' ],
		[ '9 anos',  'Superhost em Salvador' ],
	];
	for ( $i = 1; $i <= 4; $i++ ) {
		$text( "stat_{$i}_num",   'stats', "Stat {$i} — Numero",   $defs[$i-1][0] );
		$text( "stat_{$i}_label", 'stats', "Stat {$i} — Etichetta", $defs[$i-1][1] );
	}

	// ── COME FUNZIONA ────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_process', [ 'title' => 'Come Funziona (4 Step)', 'panel' => 'vivahost_panel', 'priority' => 40 ] );
	$text( 'process_title', 'process', 'Titolo sezione', 'Como funciona a gestão do seu imóvel' );
	$area( 'process_intro', 'process', 'Testo intro',    'Do primeiro contato ao repasse dos lucros, assumimos toda a operação para você não se preocupar com nada.' );
	$step_defs = [
		[ 'Diagnóstico gratuito', 'Avaliamos o perfil do seu imóvel e projetamos o faturamento real para a sua localização em Salvador.' ],
		[ 'Produção e anúncio',   'Sessão fotográfica profissional e cadastro estratégico nos principais canais de locação por temporada.' ],
		[ 'Operação 360°',        'Recepção de hóspedes, atendimento 24h e equipe dedicada para higienização e manutenção preventiva.' ],
		[ 'Repasse e extrato',    'Depósito pontual dos rendimentos e prestação de contas transparente todo mês.' ],
	];
	for ( $i = 1; $i <= 4; $i++ ) {
		$text( "step_{$i}_title", 'process', "Step {$i} — Titolo", $step_defs[$i-1][0] );
		$area( "step_{$i}_text",  'process', "Step {$i} — Testo",  $step_defs[$i-1][1] );
	}

	// ── COMPARAÇÃO (Sozinho vs VivaHost) ──────────────────────────────────
	$c->add_section( 'vivahost_compare', [ 'title' => 'Comparação (Sozinho vs VivaHost)', 'panel' => 'vivahost_panel', 'priority' => 45 ] );
	$chk( 'compare_show', 'compare', 'Mostrar sezione', '1' );
	$text( 'compare_title', 'compare', 'Titolo sezione', 'Alugar por conta própria vs Gestão VivaHost' );
	$text( 'compare_left_title', 'compare', 'Titolo colonna sinistra', 'Por conta própria' );
	$area( 'compare_left_1', 'compare', 'Riga 1 — sinistra', 'Mensagens de madrugada, check-in no fim de semana e imprevistos diários' );
	$area( 'compare_left_2', 'compare', 'Riga 2 — sinistra', 'Preço fixo no chute que afasta viajantes ou vende barato na alta temporada' );
	$area( 'compare_left_3', 'compare', 'Riga 3 — sinistra', 'Preocupação constante com diaristas, lavanderia e compras de reposição' );
	$text( 'compare_right_title', 'compare', 'Titolo colonna destra', 'Com a VivaHost' );
	$area( 'compare_right_1', 'compare', 'Riga 1 — destra', 'Operação 24h para os hóspedes: cuidamos de tudo e você aproveita seu tempo' );
	$area( 'compare_right_2', 'compare', 'Riga 2 — destra', 'Precificação dinâmica: ocupação consistente e até +30% de receita líquida' );
	$area( 'compare_right_3', 'compare', 'Riga 3 — destra', 'Padrão hoteleiro garantido com governança própria e vistorias rigorosas' );

	// ── SERVIÇOS ─────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_services', [ 'title' => 'Serviços (6)', 'panel' => 'vivahost_panel', 'priority' => 50 ] );
		$text( 'services_title', 'services', 'Titolo sezione', 'Serviços completos de gestão Airbnb em Salvador' );
		$area( 'services_intro', 'services', 'Testo intro',    'Da foto profissional ao suporte 24h, cuidamos de cada detalhe da sua hospedagem por temporada.' );
		$text( 'commission_rate', 'services', 'Taxa de comissão padrão', '20%' );
		$svc_defs = [
			[ 'Fotografia profissional',  'Ensaio fotográfico completo. Fotos que convertem visitas em reservas.' ],
			[ 'Precificação dinâmica',    'Algoritmo que ajusta a diária conforme demanda, sazonalidade e concorrência.' ],
			[ 'Check-in & Check-out',     'Recepção personalizada, entrega de chaves e orientação completa do imóvel.' ],
			[ 'Limpeza & Amenidades',     'Equipe própria, troca de roupas de cama e reposição a cada estadia.' ],
			[ 'Suporte 24 horas',         'Atendimento imediato para hóspedes e manutenção em qualquer horário.' ],
			[ 'Relatórios mensais',       'Dashboard com receita, ocupação e avaliações. Transferência automática.' ],
		];
		for ( $i = 1; $i <= 6; $i++ ) {
			$text( "service_{$i}_title", 'services', "Card {$i} — Titolo", $svc_defs[$i-1][0] );
			$area( "service_{$i}_text",  'services', "Card {$i} — Testo",  $svc_defs[$i-1][1] );
		}

		// ── SERVIÇOS DE CONFIANÇA ─────────────────────────────────────────────
		$c->add_section( 'vivahost_trust_sec', [ 'title' => 'Serviços de Confiança', 'panel' => 'vivahost_panel', 'priority' => 55 ] );
		$chk( 'trust_section_show', 'trust_sec', 'Mostrar sezione', '1' );
		$text( 'trust_section_title', 'trust_sec', 'Titolo sezione', 'Serviços de confiança — sem estresse' );
		$area( 'trust_section_intro', 'trust_sec', 'Testo intro', 'Tem um problema no imóvel? A VivaHost cuida de tudo. Rede de profissionais parceiros verificados para qualquer eventualidade.' );
		$trust_defs = [
			[ 'Elétrica', 'Troca de lâmpadas, reparos em tomadas, instalação de chuveiros e muito mais.' ],
			[ 'Hidráulica', 'Vazamentos, entupimentos, instalação de torneiras e reparos em encanamentos.' ],
			[ 'Manutenção geral', 'Pintura, pequenos reparos, montagem de móveis e serviços de marcenaria.' ],
			[ 'Jardinagem & Limpeza', 'Manutenção de jardins, limpeza pós-obra e conservação externa.' ],
		];
		for ( $i = 1; $i <= 4; $i++ ) {
			$text( "trust_{$i}_title", 'trust_sec', "Serviço {$i} — Titolo", $trust_defs[$i-1][0] );
			$area( "trust_{$i}_text",  'trust_sec', "Serviço {$i} — Testo",  $trust_defs[$i-1][1] );
		}

	// ── IMÓVEIS ──────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_properties', [ 'title' => 'Imóveis (3 Proprietà)', 'panel' => 'vivahost_panel', 'priority' => 60 ] );
	$text( 'imoveis_title', 'properties', 'Titolo sezione', 'Conheça alguns dos imóveis que gerenciamos' );
	$area( 'imoveis_desc',  'properties', 'Descrizione',    'Cada propriedade é cuidada com o mesmo padrão — fotos profissionais, limpeza impecável e avaliações que falam por si.' );
	$prop_defs = [
		[ 'Condomínio Ondina, vista para o mar', 'Ondina, Salvador', '4,90', '143', 'https://www.airbnb.com.br/users/show/148412228' ],
		[ 'Lar Lisboa — Costa Azul', 'Costa Azul, Salvador', '4,92', '83', 'https://www.airbnb.com.br/users/show/148412228' ],
		[ 'Apartamento em Salvador', 'Barra, Salvador', '5,0', '59', 'https://www.airbnb.com.br/users/show/148412228' ],
	];
	for ( $i = 1; $i <= 3; $i++ ) {
		$img(  "prop_{$i}_photo",   'properties', "Proprietà {$i} — Foto" );
		$text( "prop_{$i}_name",    'properties', "Proprietà {$i} — Nome",     $prop_defs[$i-1][0] );
		$text( "prop_{$i}_loc",     'properties', "Proprietà {$i} — Bairro",   $prop_defs[$i-1][1] );
		$text( "prop_{$i}_rating",  'properties', "Proprietà {$i} — Rating",   $prop_defs[$i-1][2] );
		$text( "prop_{$i}_reviews", 'properties', "Proprietà {$i} — Recensioni", $prop_defs[$i-1][3] );
		$text( "prop_{$i}_link",    'properties', "Proprietà {$i} — Link Airbnb", $prop_defs[$i-1][4] );
	}

	// ── DEPOIMENTO ───────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_testimonial', [ 'title' => 'Depoimento & Banner Foto', 'panel' => 'vivahost_panel', 'priority' => 70 ] );
	$img( 'testimonial_banner_bg',    'testimonial', 'Foto di sfondo banner sopra depoimento (riempimento)' );
	$text( 'testimonial_banner_quote', 'testimonial', 'Frase banner foto sopra depoimento', 'Hospitalidade baiana autêntica com padrão Superhost internacional.' );
	$text( 'testimonial_banner_sub',   'testimonial', 'Sottotitolo banner foto', 'Salvador · Bahia' );
	$area( 'testimonial_text',   'testimonial', 'Citazione', 'Márcia e sua equipe cuidam do imóvel com dedicação, limpeza impecável e uma proatividade que poucas profissionais têm. Desde que confiei a gestão a ela, não tenho com o que me preocupar.' );
	$text( 'testimonial_author', 'testimonial', 'Nome autore', 'Ana Paula M.' );
	$text( 'testimonial_city',   'testimonial', 'Città / Ruolo', 'Proprietária · Salvador, BA' );
	$img( 'testimonial_photo',   'testimonial', 'Foto autore depoimento' );

	// ── HOST ─────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_host', [ 'title' => 'Host (Marcia)', 'panel' => 'vivahost_panel', 'priority' => 80 ] );
	$text( 'host_name',          'host', 'Nome',            'Marcia Sales' );
	$text( 'host_subtitle',      'host', 'Sottotitolo / Ruolo', 'Gestão Operacional VivaHost em Salvador' );
	$area( 'host_bio',           'host', 'Bio / Metodo de Gestao', 'Ao contrário de plataformas impessoais, nossa gestão tem presença física diária em Salvador. Cuido de cada imóvel com equipe local de confiança, garantindo padrão de conservação e diálogo direto com você a qualquer momento.' );
	$host_stat_defs = [ [ '469', 'Avaliações' ], [ '4,88 ★', 'Nota média' ], [ '9 anos', 'Hospedando' ] ];
	for ( $i = 1; $i <= 3; $i++ ) {
		$text( "host_stat_{$i}_num",   'host', "Stat {$i} — Valor",    $host_stat_defs[$i-1][0] );
		$text( "host_stat_{$i}_label", 'host', "Stat {$i} — Etichetta", $host_stat_defs[$i-1][1] );
	}
	$img(  'host_photo',         'host', 'Foto avatar host' );
	$text( 'host_airbnb',        'host', 'Link profilo Airbnb', 'https://www.airbnb.com.br/users/show/148412228' );
	$text( 'host_properties_count', 'host', 'N° imóveis attivi', '15' );
	$text( 'host_properties_label', 'host', 'Etichetta n° imóveis', 'Acomodações' );
	$chk(  'host_superhost',     'host', 'Mostra badge Superhost' );

	// ── REVIEWS ──────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_reviews', [ 'title' => 'Avaliações (Recensioni)', 'panel' => 'vivahost_panel', 'priority' => 90 ] );
	$text( 'reviews_score', 'reviews', 'Punteggio globale', '4,88' );
	$text( 'reviews_count', 'reviews', 'N° avaliações totali', '469' );
	$rev_defs = [
		[ 'O apartamento é bem localizado, super arrumado, seguro e a anfitriã é extraordinária. Me senti em casa, super indico.', 'Simone', 'Aracaju/SE' ],
		[ 'Localização perfeita, próximo dos principais pontos turísticos da cidade. Apartamento aconchegante como se estivesse em casa. Márcia muito atenciosa e prestativa. Vista maravilhosa do mar...', 'Ismael', 'Brasil' ],
		[ 'Muito obrigado pela estadia! Foi tudo maravilhoso e nos sentimos muito bem acolhidos. ❤️ A experiência foi incrível, o lugar é lindo e ficará guardado com muito carinho.', 'Cleiton', 'Recife/PE' ],
	];
	for ( $i = 1; $i <= 3; $i++ ) {
		$img(  "review_{$i}_photo",   'reviews', "Recensione {$i} — Foto autore" );
		$area( "review_{$i}_text",   'reviews', "Recensione {$i} — Testo",  $rev_defs[$i-1][0] );
		$text( "review_{$i}_author", 'reviews', "Recensione {$i} — Autore", $rev_defs[$i-1][1] );
		$text( "review_{$i}_city",   'reviews', "Recensione {$i} — Città",  $rev_defs[$i-1][2] );
	}

	// ── BLOG ─────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_blog_sec', [ 'title' => 'Blog', 'panel' => 'vivahost_panel', 'priority' => 95 ] );
	$chk( 'blog_section_show', 'blog_sec', 'Mostrar sezione', '1' );
	$text( 'blog_section_eyebrow', 'blog_sec', 'Eyebrow sopra titolo', 'Blog & Mercado Salvador' );
	$text( 'blog_section_title', 'blog_sec', 'Titolo sezione', 'Dicas e novidades sobre aluguel por temporada em Salvador' );
	$area( 'blog_section_intro', 'blog_sec', 'Testo intro',    'Estratégias de mercado, dicas de hospitalidade e orientações práticas para proprietários que desejam maximizar a rentabilidade no Airbnb na Bahia.' );
	$text( 'blog_section_link', 'blog_sec', 'Link "Ver todos"', '/blog/' );

	// ── CTA / FORM ───────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_cta', [ 'title' => 'CTA & Formulário', 'panel' => 'vivahost_panel', 'priority' => 100 ] );
	$text( 'cta_title', 'cta', 'Titolo sezione', 'Descubra o potencial de faturamento do seu imóvel' );
	$area( 'cta_lead',  'cta', 'Testo lead',     'Receba uma estimativa gratuita de rentabilidade para o seu apartamento em Salvador, sem qualquer compromisso.' );
	$text( 'trust_1',   'cta', 'Trust item 1',   'Avaliação gratuita e sem compromisso' );
	$text( 'trust_2',   'cta', 'Trust item 2',   'Seus dados tratados com total sigilo' );
	$text( 'trust_3',   'cta', 'Trust item 3',   'Resposta em até 24 horas úteis' );
	$sel(  'form_mode', 'cta', 'Modalità form',  'both', [
		'email'     => 'Solo email',
		'whatsapp'  => 'Solo WhatsApp',
		'both'      => 'Email + WhatsApp',
	], 'refresh' );
	$text( 'form_wa_cta', 'cta', 'Testo link WhatsApp alternativo', 'Conversar pelo WhatsApp' );

	// ── WHATSAPP ─────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_whatsapp', [ 'title' => 'WhatsApp', 'panel' => 'vivahost_panel', 'priority' => 110 ] );
	$text( 'wa_number',  'whatsapp', 'Numero WhatsApp (internazionale)', VH_DEFAULT_WA_NUM );
	$area( 'wa_message', 'whatsapp', 'Messaggio pre-compilato',          'Olá! Gostaria de saber mais sobre a gestão VivaHost.' );
	$chk(  'wa_float_show', 'whatsapp', 'Mostra bottone float WhatsApp' );

	// ── FOOTER ───────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_footer_sec', [ 'title' => 'Footer', 'panel' => 'vivahost_panel', 'priority' => 120 ] );
	$text( 'footer_tagline',   'footer_sec', 'Tagline footer',     'VivaHost — Superhost Airbnb em Salvador, Bahia. Gestão profissional de aluguel por temporada com 9 anos de experiência.' );
	$text( 'footer_copyright', 'footer_sec', 'Testo copyright',   '© 2026 VivaHost. Todos os direitos reservados.' );
	$text( 'footer_instagram', 'footer_sec', 'Link Instagram',    'https://www.instagram.com/vivahostbahia/' );
	$text( 'footer_airbnb',    'footer_sec', 'Link Airbnb',       'https://www.airbnb.com.br/p/vivahostbahia' );
	$text( 'footer_city',      'footer_sec', 'Città / Zona',      'Salvador, Bahia e região metropolitana' );
	$text( 'footer_hours',     'footer_sec', 'Orari',             '7 dias por semana, das 8h às 20h' );
	$text( 'footer_razao',     'footer_sec', 'Razão Social',      'Viva Host LTDA' );
	$text( 'footer_cnpj',      'footer_sec', 'CNPJ',              '27.447.686/0001-10' );
	$text( 'footer_address',   'footer_sec', 'Indirizzo (logradouro + n°)', 'Avenida Tancredo Neves, 002539' );
	$text( 'footer_complement','footer_sec', 'Complemento',       'Edif CEO Salvador Shopping Torre Londres Sala 2609' );
	$text( 'footer_bairro',    'footer_sec', 'Bairro',            'Caminho das Árvores' );
	$text( 'footer_cep',       'footer_sec', 'CEP',               '41820-021' );
	$text( 'footer_email',     'footer_sec', 'Email contato',     'marcia@meuvivahost.com.br' );
}

/**
 * Helper: add a Customizer setting + standard control.
 */
function vh_add( $c, $id, $section, $label, $default, $type = 'text', $transport = 'postMessage', $choices = [] ) {
	$full_id = "vivahost_{$id}";

	// Sanitize callback in base al tipo di campo
	switch ( $type ) {
		case 'checkbox':
			$sanitize = 'vh_sanitize_checkbox';
			break;
		case 'textarea':
			$sanitize = 'sanitize_textarea_field';
			break;
		default:
			$sanitize = 'sanitize_text_field';
			break;
	}

	$c->add_setting( $full_id, [
		'default'           => $default,
		'transport'         => $transport,
		'sanitize_callback' => $sanitize,
	] );
	$ctrl_args = [
		'label'   => $label,
		'section' => "vivahost_{$section}",
		'type'    => $type,
	];
	if ( $choices ) $ctrl_args['choices'] = $choices;
	$c->add_control( $full_id, $ctrl_args );
}

function vh_sanitize_checkbox( $v ) { return $v ? '1' : ''; }

/**
 * Shorthand get_theme_mod with prefix and legacy alias fallback resolution.
 * Guarantees 100% backward and forward compatibility.
 */
function vh_mod( $key, $default = '' ) {
	$clean_key = ( strpos( $key, 'vivahost_' ) === 0 ) ? substr( $key, 9 ) : $key;

	$val = get_theme_mod( "vivahost_{$clean_key}", null );
	if ( null !== $val && '' !== $val ) {
		return $val;
	}

	// Legacy alias bidirectional map
	$aliases = [
		'prop_1_name'           => 'property_1_name',
		'property_1_name'       => 'prop_1_name',
		'prop_1_loc'            => 'property_1_location',
		'property_1_location'   => 'prop_1_loc',
		'prop_1_rating'         => 'property_1_rating',
		'property_1_rating'     => 'prop_1_rating',
		'prop_1_reviews'        => 'property_1_reviews',
		'property_1_reviews'    => 'prop_1_reviews',
		'prop_1_link'           => 'property_1_link',
		'property_1_link'       => 'prop_1_link',
		'prop_1_photo'          => 'property_1_img',
		'property_1_img'        => 'prop_1_photo',

		'prop_2_name'           => 'property_2_name',
		'property_2_name'       => 'prop_2_name',
		'prop_2_loc'            => 'property_2_location',
		'property_2_location'   => 'prop_2_loc',
		'prop_2_rating'         => 'property_2_rating',
		'property_2_rating'     => 'prop_2_rating',
		'prop_2_reviews'        => 'property_2_reviews',
		'property_2_reviews'    => 'prop_2_reviews',
		'prop_2_link'           => 'property_2_link',
		'property_2_link'       => 'prop_2_link',
		'prop_2_photo'          => 'property_2_img',
		'property_2_img'        => 'prop_2_photo',

		'prop_3_name'           => 'property_3_name',
		'property_3_name'       => 'prop_3_name',
		'prop_3_loc'            => 'property_3_location',
		'property_3_location'   => 'prop_3_loc',
		'prop_3_rating'         => 'property_3_rating',
		'property_3_rating'     => 'prop_3_rating',
		'prop_3_reviews'        => 'property_3_reviews',
		'property_3_reviews'    => 'prop_3_reviews',
		'prop_3_link'           => 'property_3_link',
		'property_3_link'       => 'prop_3_link',
		'prop_3_photo'          => 'property_3_img',
		'property_3_img'        => 'prop_3_photo',

		'host_stat_1_num'       => 'host_reviews',
		'host_reviews'          => 'host_stat_1_num',
		'host_stat_2_num'       => 'host_score',
		'host_score'            => 'host_stat_2_num',
		'host_stat_3_num'       => 'host_years',
		'host_years'            => 'host_stat_3_num',
		'host_airbnb'           => 'host_profile',
		'host_profile'          => 'host_airbnb',
		'host_properties_count' => 'host_active',
		'host_active'           => 'host_properties_count',
	];

	if ( isset( $aliases[ $clean_key ] ) ) {
		$legacy = get_theme_mod( "vivahost_{$aliases[$clean_key]}", null );
		if ( null !== $legacy && '' !== $legacy ) {
			return $legacy;
		}
	}

	return $default;
}

// ─── 4. PHPMAILER / SMTP ─────────────────────────────────────────────────────
add_action( 'phpmailer_init', 'vh_configure_smtp' );
function vh_configure_smtp( $phpmailer ) {
	$host = get_option( 'vh_smtp_host', '' );
	if ( ! $host ) return;

	$phpmailer->isSMTP();
	$phpmailer->Host       = $host;
	$phpmailer->Port       = (int) get_option( 'vh_smtp_port', 587 );
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username   = get_option( 'vh_smtp_user', '' );
	$phpmailer->Password   = vh_decrypt( get_option( 'vh_smtp_pass', '' ) );
	$secure                = get_option( 'vh_smtp_secure', 'tls' );
	if ( $secure !== 'none' ) $phpmailer->SMTPSecure = $secure;
	$phpmailer->From       = get_option( 'vh_smtp_from_email', get_option( 'admin_email' ) );
	$phpmailer->FromName   = get_option( 'vh_smtp_from_name', get_bloginfo( 'name' ) );
	// NOTA: verify_peer non va mai disabilitato in produzione.
	// La verifica SSL/TLS è gestita automaticamente dal server.
}

/**
 * Crittografia AES-256-CBC con chiave derivata dal wp_salt('auth').
 * Sostituisce la precedente implementazione XOR insicura.
 */
function vh_encrypt( $v ) {
	if ( ! $v ) return '';
	$method = 'aes-256-cbc';
	$key    = wp_salt( 'auth' );
	$key    = hash( 'sha256', $key, true ); // 32 bytes per AES-256
	$ivlen  = openssl_cipher_iv_length( $method );
	$iv     = openssl_random_pseudo_bytes( $ivlen );
	$cipher = openssl_encrypt( $v, $method, $key, OPENSSL_RAW_DATA, $iv );
	return base64_encode( $iv . $cipher );
}

function vh_decrypt( $v ) {
	if ( ! $v ) return '';
	$data   = base64_decode( $v, true );
	if ( false === $data ) return '';
	$method = 'aes-256-cbc';
	$key    = wp_salt( 'auth' );
	$key    = hash( 'sha256', $key, true );
	$ivlen  = openssl_cipher_iv_length( $method );
	$iv     = substr( $data, 0, $ivlen );
	$cipher = substr( $data, $ivlen );
	return openssl_decrypt( $cipher, $method, $key, OPENSSL_RAW_DATA, $iv );
}

// ─── 5. AJAX FORM HANDLER ────────────────────────────────────────────────────
add_action( 'wp_ajax_vh_form',        'vh_form_handler' );
add_action( 'wp_ajax_nopriv_vh_form', 'vh_form_handler' );
function vh_form_handler() {
	// Security
	if ( ! check_ajax_referer( 'vh_form_nonce', 'nonce', false ) ) {
		wp_send_json_error( [ 'message' => 'Sessão expirada. Recarregue a página.' ], 403 );
	}
	// Honeypot check (reject if populated - checks both vh_hp_check and website)
	if ( ! empty( $_POST['vh_hp_check'] ) || ! empty( $_POST['website'] ) ) {
		wp_send_json_success( [ 'mode' => 'email', 'wa_url' => '' ] ); // silent drop
	}

	// Sanitize fields
	$nome     = sanitize_text_field( $_POST['nome']     ?? '' );
	$cidade   = sanitize_text_field( $_POST['cidade']   ?? '' );
	$tipo     = sanitize_text_field( $_POST['tipo']     ?? '' );
	$quartos  = sanitize_text_field( $_POST['quartos']  ?? '' );
	$phone    = sanitize_text_field( $_POST['whatsapp'] ?? '' );

	// Validate required fields (including phone number)
	if ( ! $nome || ! $cidade || ! $phone ) {
		wp_send_json_error( [ 'message' => 'Por favor, preencha todos os campos obrigatórios (Nome, Cidade e WhatsApp).' ], 422 );
	}

	$mode = vh_mod( 'form_mode', 'both' );

	// Build WhatsApp URL with formatted number and encoded message via helper
	$lead_msg = "Olá! Tenho interesse na avaliação gratuita.\n\nNome: {$nome}\nCidade/Bairro: {$cidade}\nTipo de imóvel: {$tipo}\nQuartos: {$quartos}\nMeu WhatsApp: {$phone}";
	$wa_url   = vh_wa_url( $lead_msg );

	// Send email
	$email_sent = false;
	if ( in_array( $mode, [ 'email', 'both' ], true ) ) {
		$to      = get_option( 'vh_smtp_to_email', get_option( 'admin_email' ) );
		$subject = "Nova avaliação de imóvel — {$nome}";
		$body    = "<h2>Nova solicitação de avaliação</h2>
<p><strong>Nome:</strong> {$nome}</p>
<p><strong>Cidade/Bairro:</strong> {$cidade}</p>
<p><strong>Tipo de imóvel:</strong> {$tipo}</p>
<p><strong>Nº de quartos:</strong> {$quartos}</p>
<p><strong>WhatsApp:</strong> {$phone}</p>";
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		$email_sent = wp_mail( $to, $subject, $body, $headers );
	}

	wp_send_json_success( [
		'mode'       => $mode,
		'wa_url'     => ( $mode !== 'email' ) ? $wa_url : '',
		'email_sent' => $email_sent,
		'message'    => $email_sent
			? 'Mensagem enviada! Entraremos em contato em breve.'
			: ( $mode === 'email' ? 'Erro ao enviar. Tente pelo WhatsApp.' : '' ),
	] );
}

// ─── 6. ADMIN: SMTP & THEME UPDATES PAGES ───────────────────────────────────
add_action( 'admin_menu', function () {
	// Impostazioni → VivaHost Email (solo SMTP ed invio email)
	add_options_page(
		'VivaHost — Email & SMTP', 'VivaHost Email', 'manage_options', 'vivahost-smtp',
		fn() => require VH_PATH . '/admin/smtp-settings.php'
	);

	// Aparência → Atualizações VivaHost (aggiornamenti del tema da GitHub)
	add_theme_page(
		'VivaHost — Atualizações via GitHub', 'Atualizações VivaHost', 'manage_options', 'vivahost-updates',
		fn() => require VH_PATH . '/admin/updates-settings.php'
	);
} );

add_action( 'admin_init', 'vh_updates_save' );
function vh_updates_save() {
	if ( ! isset( $_POST['vh_updates_nonce'] ) ) return;
	if ( ! wp_verify_nonce( $_POST['vh_updates_nonce'], 'vh_updates_save' ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	if ( isset( $_POST['vh_github_repo'] ) ) {
		update_option( 'vh_github_repo', sanitize_text_field( $_POST['vh_github_repo'] ) );
	}
	if ( isset( $_POST['vh_github_token'] ) ) {
		update_option( 'vh_github_token', sanitize_text_field( $_POST['vh_github_token'] ) );
	}
	delete_transient( 'vh_gh_update_check' );
	delete_site_transient( 'update_themes' );

	wp_safe_redirect( admin_url( 'themes.php?page=vivahost-updates&saved=1' ) );
	exit;
}

add_action( 'admin_init', 'vh_smtp_save' );
function vh_smtp_save() {
	if ( ! isset( $_POST['vh_smtp_nonce'] ) ) return;
	if ( ! wp_verify_nonce( $_POST['vh_smtp_nonce'], 'vh_smtp_save' ) ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;

	$fields = [ 'vh_smtp_to_email', 'vh_smtp_from_name', 'vh_smtp_from_email',
	            'vh_smtp_host', 'vh_smtp_port', 'vh_smtp_user', 'vh_smtp_secure' ];
	foreach ( $fields as $f ) {
		update_option( $f, sanitize_text_field( $_POST[ $f ] ?? '' ) );
	}
	if ( ! empty( $_POST['vh_smtp_pass'] ) ) {
		update_option( 'vh_smtp_pass', vh_encrypt( $_POST['vh_smtp_pass'] ) );
	}

	// SMTP test
	if ( ! empty( $_POST['vh_smtp_test'] ) ) {
		$to = sanitize_email( $_POST['vh_smtp_to_email'] );
		$ok = wp_mail( $to, 'VivaHost — Email di test', 'Configurazione SMTP funzionante ✓' );
		set_transient( 'vh_smtp_test_result', $ok ? 'ok' : 'fail', 30 );
	}

	wp_safe_redirect( admin_url( 'options-general.php?page=vivahost-smtp&saved=1' ) );
	exit;
}

// ─── 7. AFTER THEME SWITCH ───────────────────────────────────────────────────
add_action( 'after_switch_theme', 'vh_setup_homepage' );
function vh_setup_homepage() {
	if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) return;

	// Usiamo WP_Query invece di get_page_by_path() (deprecato da WP 6.7)
	$existing = new WP_Query( [
		'name'             => 'vivahost-home',
		'post_type'        => 'page',
		'posts_per_page'   => 1,
		'fields'           => 'ids',
		'no_found_rows'    => true,
		'suppress_filters' => true,
	] );
	$page_id = $existing->have_posts() ? $existing->posts[0] : 0;

	if ( ! $page_id ) {
		$page_id = wp_insert_post( [
			'post_title'     => 'Home',
			'post_name'      => 'vivahost-home',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_content'   => '',
			'comment_status' => 'closed',
		] );
	}

	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_id );
	}
}

// Admin notice if Hello Elementor missing
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'install_themes' ) ) return;
	if ( wp_get_theme( 'hello-elementor' )->exists() ) return;
	echo '<div class="notice notice-warning"><p><strong>VivaHost</strong>: tema pai <strong>Hello Elementor</strong> non trovato. <a href="' . esc_url( admin_url( 'theme-install.php?search=hello+elementor' ) ) . '">Installa →</a></p></div>';
} );

// ─── 8. POST IMAGE CONSISTENCY HELPER ──────────────────────────────────────
/**
 * Retorna a imagem do post garantindo 100% de consistência entre cards e single.php.
 */
function vh_get_post_image( $post_id = 0, $size = 'large' ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	if ( $post_id && has_post_thumbnail( $post_id ) ) {
		$url = get_the_post_thumbnail_url( $post_id, $size );
		if ( $url ) {
			return $url;
		}
	}
	if ( $post_id ) {
		$meta_img = get_post_meta( $post_id, '_vh_featured_image', true );
		if ( $meta_img ) {
			return $meta_img;
		}
		$slug = get_post_field( 'post_name', $post_id );
	} else {
		$slug = '';
	}

	$defaults = [
		'como-maximizar-faturamento-airbnb-salvador'               => 'https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?auto=format&fit=crop&w=1200&q=80',
		'aluguel-temporada-salvador-regras-condominio-legislacao'   => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
		'melhores-bairros-salvador-investimento-aluguel-temporada' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
	];

	if ( $slug && isset( $defaults[ $slug ] ) ) {
		return $defaults[ $slug ];
	}

	$pool = array_values( $defaults );
	return $pool[ abs( (int) $post_id ) % count( $pool ) ];
}

// ─── 9. AUTO-SEED & UPDATE 3 COMPREHENSIVE SEO GUIDES FOR SALVADOR ───────────
add_action( 'init', 'vh_seed_salvador_posts' );
function vh_seed_salvador_posts() {
	// Trash default "Hello world!" if present
	$hw = new WP_Query( [
		'name'           => 'hello-world',
		'post_type'      => 'post',
		'fields'         => 'ids',
		'posts_per_page' => 1,
	] );
	if ( $hw->have_posts() ) {
		wp_delete_post( $hw->posts[0], true );
	}

	if ( get_option( 'vh_posts_seeded_v5' ) ) {
		return;
	}

	$posts = [
		[
			'post_title'   => 'Como Maximizar o Faturamento do seu Imóvel no Airbnb em Salvador: O Guia Definitivo',
			'post_name'    => 'como-maximizar-faturamento-airbnb-salvador',
			'post_excerpt' => 'Descubra as estratégias comprovadas de precificação dinâmica, preparação do imóvel e sazonalidade para lucrar mais com aluguel de temporada em Salvador com a VivaHost.',
			'img'          => 'https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?auto=format&fit=crop&w=1200&q=80',
			'category'     => 'Estratégia & Rentabilidade',
			'post_content' => '<p class="lead font-medium text-lg text-neutral">Salvador é um dos polos turísticos e culturais mais procurados da América do Sul. Para proprietários e investidores imobiliários na capital baiana, transformar um apartamento em uma acomodação de temporada no Airbnb representa uma das formas mais inteligentes e lucrativas de monetização patrimonial — desde que conduzida com metodologia e disciplina profissional.</p><h2>1. A Curva de Sazonalidade em Salvador: Muito Além do Carnaval</h2><p>O maior equívoco de quem começa no aluguel por temporada é enxergar Salvador apenas através da lente do Carnaval e do Réveillon. Embora essas semanas de pico registrem diárias até 4 vezes superiores à média anual, o calendário da capital baiana oferece oportunidades de faturamento consistente durante os doze meses do ano:</p><ul><li><strong>Alta Temporada de Verão (Novembro a Março):</strong> Ocupação beirando 90% a 95% em bairros como Barra, Ondina e Rio Vermelho. O público busca sol, mar, ensaios de verão e turismo de lazer.</li><li><strong>Média Temporada e Festas Típicas (Junho e Julho):</strong> O período de São João e as férias escolares de inverno trazem famílias e viajantes de todo o Nordeste e Sudeste para curtir a gastronomia e o Centro Histórico.</li><li><strong>Temporada Corporativa e Turismo Médico (Abril a Outubro):</strong> Meses de grande movimentação de negócios, congressos médicos e eventos empresariais, especialmente para imóveis no Costa Azul, Caminho das Árvores e Armação.</li></ul><h2>2. Precificação Dinâmica vs. O Erro da Diária Fixa</h2><p>Cobrar a mesma diária em uma terça-feira chuvosa de maio e em um sábado ensolarado de janeiro é a receita certa para perder dinheiro — seja por vacância desnecessária ou por subprecificar o imóvel quando a demanda está no teto. Na VivaHost, monitoramos a curva de procura diária, a taxa de ocupação dos bairros vizinhos e grandes shows na cidade para calibrar o preço por noite de forma estratégica, maximizando o RevPAR (faturamento por noite disponível).</p><h2>3. Checklist de Preparação: O que Faz um Imóvel Alugar Mais Caro</h2><p>Os hóspedes em Salvador são exigentes e valorizam comodidades que garantam conforto térmico e conveniência total:</p><ul><li><strong>Climatização Eficiente:</strong> Ar-condicionado split em todos os quartos e, preferencialmente, na sala. É o item número 1 apontado nos filtros de busca em Salvador.</li><li><strong>Conexão de Alta Velocidade:</strong> Wi-Fi estável de no mínimo 300 Mbps para atrair nômades digitais e profissionais em trabalho remoto.</li><li><strong>Enxoval Padrão Hoteleiro:</strong> Roupas de cama 100% algodão ou percal 200 fios e toalhas brancas de alta gramatura, rigorosamente higienizadas a cada reserva.</li><li><strong>Fechadura Digital Inteligente:</strong> Check-in autônomo e seguro via senha, eliminando o estresse da troca de chaves física.</li><li><strong>Acolhimento com Identidade Baiana:</strong> Um mimo simples de boas-vindas — fitinhas de Nosso Senhor do Bonfim, café baiano ou petiscos locais — cria um vínculo emocional imediato e rende avaliações 5 estrelas entusiasmadas.</li></ul><h2>4. O Peso Real do Selo Superhost nas Reservas</h2><p>Propriedades com chancela de Superhost no Airbnb desfrutam de prioridade algorítmica nas buscas, gerando cerca de 35% mais visualizações orgânicas. Além disso, viajantes corporativos e turistas estrangeiros filtram ativamente apenas anúncios Superhost pela garantia de confiabilidade e limpeza impecável.</p><h2>5. Gestão Amadora vs. Gestão Profissional VivaHost</h2><p>Administrar um imóvel de temporada por conta própria consome entre 15 e 20 horas semanais com atendimento de mensagens, agendamento de diaristas, lavanderia, compras de insumos e pequenos consertos de emergência. A VivaHost assume 100% da operação — da curadoria do anúncio e fotos profissionais à manutenção preventiva e conciliação financeira —, entregando mais lucro líquido e zero dor de cabeça para você.</p>',
		],
		[
			'post_title'   => 'Aluguel por Temporada em Salvador: Regras de Condomínio e Legislação Atualizada',
			'post_name'    => 'aluguel-temporada-salvador-regras-condominio-legislacao',
			'post_excerpt' => 'Entenda o que diz a Lei do Inquilinato, as decisões dos tribunais superiores e como ter segurança jurídica e convivência harmônica com o condomínio ao alugar por temporada em Salvador.',
			'img'          => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
			'category'     => 'Legislação & Segurança',
			'post_content' => '<p class="lead font-medium text-lg text-neutral">A locação por temporada por meio de plataformas digitais consolidou-se como uma das atividades mais dinâmicas do mercado imobiliário em Salvador. Entretanto, muitos proprietários ainda têm receios sobre a legalidade da atividade perante a legislação federal e as convenções condominiais. Neste guia prático, esclarecemos as principais diretrizes jurídicas e operacionais para alugar com tranquilidade.</p><h2>1. A Lei do Inquilinato (Lei Federal nº 8.245/1991)</h2><p>O aluguel por temporada não é uma novidade no direito brasileiro nem uma área cinzenta: ele é expressamente regulamentado pelo <strong>Artigo 48 da Lei nº 8.245/1991</strong>. A legislação define a locação por temporada como aquela destinada à residência temporária do locatário para fins de lazer, turismo, estudos, tratamento de saúde ou obras, com prazo máximo legal de até <strong>90 dias</strong>.</p><p>Trata-se de um exercício legítimo do direito constitucional de propriedade (Artigo 5º, XXII da Constituição Federal), que faculta ao dono usar, fruir e dispor de seus bens.</p><h2>2. As Decisões do STJ e o Poder das Convenções de Condomínio</h2><p>Nos últimos anos, o Superior Tribunal de Justiça (STJ) julgou controvérsias envolvendo condomínios e plataformas digitais. O entendimento consolidado é o seguinte:</p><ul><li>O condomínio <strong>pode</strong> restringir ou proibir a locação de curta temporada em suas unidades autônomas, <strong>desde que</strong> haja previsão expressa em sua Convenção de Condomínio, aprovada pelo quórum qualificado de dois terços dos condôminos (Art. 1.351 do Código Civil).</li><li>Se a Convenção de Condomínio for omissa ou mencionar apenas que o prédio é de destinação "residencial", a locação por temporada permanece plenamente permitida, pois o hóspede utiliza o imóvel para residência temporária, e não como comércio.</li><li>Decisões unilaterais de síndicos ou avisos em circulares sem aprovação formal em assembleia não têm valor de lei e não podem violar o direito de propriedade.</li></ul><h2>3. Pilares Operacionais para Convivência Pacífica no Prédio</h2><p>Na prática, 99% das queixas em condomínios não são sobre a locação em si, mas sim sobre desorganização na portaria ou barulho. Para blindar o seu apartamento, a VivaHost adota um protocolo operacional preventivo e rigoroso:</p><ul><li><strong>Identificação Prévia Obrigatória:</strong> Coleta antecipada de nomes completos, números de documento (RG/CPF ou Passaporte) de todos os ocupantes e envio direto para a portaria 24 horas antes do check-in.</li><li><strong>Controle Estrito de Lotação:</strong> Nenhum imóvel recebe mais pessoas do que o número de camas anunciado. Visitas não cadastradas são expressamente vedadas.</li><li><strong>Termo de Compromisso e Regras da Casa:</strong> Todos os hóspedes concordam formalmente com as normas internas do condomínio, lei do silêncio após as 22h, regras de piscina e descarte correto de lixo.</li><li><strong>Canal Direto com Portaria e Síndico:</strong> Disponibilizamos o contato direto da gestão da VivaHost para que qualquer dúvida ou intercorrência seja solucionada imediatamente por nós, sem incomodar o proprietário.</li></ul><h2>Conclusão: Segurança se Constrói com Profissionalismo</h2><p>Alugar por temporada em Salvador é seguro e rentável quando conduzido com zelo, regras claras e gestão responsável. Com o acompanhamento da VivaHost, seu imóvel valoriza o edifício e mantém uma relação exemplar com a vizinhança.</p>',
		],
		[
			'post_title'   => 'Os Melhores Bairros de Salvador para Investir em Imóveis de Temporada em 2026',
			'post_name'    => 'melhores-bairros-salvador-investimento-aluguel-temporada',
			'post_excerpt' => 'Barra, Ondina, Rio Vermelho, Costa Azul ou Stella Maris? Análise aprofundada de taxa de ocupação, diária média, perfil de público e retorno sobre investimento em Salvador.',
			'img'          => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
			'category'     => 'Mercado Imobiliário',
			'post_content' => '<p class="lead font-medium text-lg text-neutral">Salvador vive um ciclo extraordinário de requalificação urbana e expansão da malha aérea, com voos diretos ligando a capital baiana aos principais centros do Brasil, Europa e América Latina. Para quem já possui um imóvel ou planeja investir em patrimônio imobiliário para locação por temporada, a escolha da localização dita o teto de rentabilidade e o perfil de ocupação ao longo do ano.</p><h2>1. Barra: O Epicentro Turístico com Ocupação Recorde</h2><p>A Barra é o endereço mais procurado e seguro para o investidor de temporada em Salvador. Com a orla totalmente requalificada, a praia protegida do Porto da Barra e o pôr do sol lendário no Farol da Barra, o bairro tem apelo universal para turistas do mundo todo.</p><ul><li><strong>Taxa de Ocupação Média:</strong> 78% a 88% ao ano.</li><li><strong>Tipologia Mais Rentável:</strong> Studios, lofts e apartamentos de 1 quarto.</li><li><strong>Perfil de Público:</strong> Casais jovens, viajantes internacionais, famílias pequenas e turistas de verão.</li></ul><h2>2. Ondina: Nobreza, Lazer e Localização Estratégica</h2><p>Vizinha à Barra, Ondina combina praias charmosas, condomínios com infraestrutura completa de lazer (piscinas com vista mar, academias e segurança 24h) e forte demanda durante todo o ano, impulsionada pelo fim do circuito de Carnaval e pela proximidade com grandes centros hospitalares e educacionais.</p><ul><li><strong>Taxa de Ocupação Média:</strong> 72% a 82% ao ano.</li><li><strong>Vantagem Competitiva:</strong> Diárias médias mais elevadas em condomínios de alto padrão frente-mar.</li></ul><h2>3. Rio Vermelho: O Polo Boêmio e Gastronômico da Cidade</h2><p>O Rio Vermelho é a alma boêmia de Salvador. Quem escolhe o Rio Vermelho busca vivenciar a cultura baiana autêntica, provar os lendários acarajés da Dinha e da Cira e curtir os melhores restaurantes e casas de show da capital.</p><ul><li><strong>Perfil do Hóspede:</strong> Viajantes individuais, turistas culturais, profissionais criativos e nômades digitais.</li><li><strong>Estadia Média:</strong> Ligeiramente mais longa que a da Barra, com excelente fluxo em fins de semana e feriados prolongados.</li></ul><h2>4. Costa Azul e Armação: O Melhor Custo de Entrada e Alta Rentabilidade</h2><p>Para investidores que buscam um preço por metro quadrado mais atrativo na compra, a região do Costa Azul e Armação desponta como uma das mais promissoras. Situada próxima ao polo financeiro da Avenida Tancredo Neves e do Salvador Shopping, atende com maestria o turismo de negócios e famílias que buscam praticidade.</p><ul><li><strong>Destaque:</strong> Menor investimento inicial e excelente retorno percentual sobre o capital investido (Yield líquido anual superior a 10%).</li></ul><h2>5. Praia do Flamengo e Stella Maris: O Refúgio Tropical Familiar</h2><p>Na zona norte de Salvador, próxima ao aeroporto, Flamengo e Stella Maris são imbatíveis para apartamentos maiores, casas de praia e vilas com piscina privativa, muito cobiçadas para estadias de férias em família e retiros de descanso.</p><h2>Comparativo Financeiro: Temporada vs. Aluguel Tradicional</h2><p>Enquanto o aluguel residencial convencional em Salvador rende em média <strong>0,4% a 0,5% ao mês</strong> (com alto risco de inadimplência e desgaste do imóvel ao longo de 30 meses), a locação por temporada bem administrada pela VivaHost costuma gerar entre <strong>0,9% e 1,4% ao mês líquido</strong>, com repasses mensais garantidos e o patrimônio sempre revisado e conservado.</p><h2>Traga seu Imóvel para a VivaHost</h2><p>Quer saber quanto o seu imóvel em Salvador pode faturar por mês no Airbnb? Entre em contato conosco pelo WhatsApp e solicite um estudo gratuito de potencial de rentabilidade.</p>',
		],
	];

	foreach ( $posts as $p ) {
		$existing = new WP_Query( [
			'name'             => $p['post_name'],
			'post_type'        => 'post',
			'post_status'      => 'any',
			'fields'           => 'ids',
			'posts_per_page'   => 1,
			'no_found_rows'    => true,
			'suppress_filters' => true,
		] );
		if ( $existing->have_posts() ) {
			$post_id = $existing->posts[0];
			wp_update_post( [
				'ID'           => $post_id,
				'post_title'   => $p['post_title'],
				'post_content' => $p['post_content'],
				'post_excerpt' => $p['post_excerpt'],
				'post_status'  => 'publish',
			] );
		} else {
			$post_id = wp_insert_post( [
				'post_title'   => $p['post_title'],
				'post_name'    => $p['post_name'],
				'post_excerpt' => $p['post_excerpt'],
				'post_content' => $p['post_content'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'post',
			] );
		}
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_vh_featured_image', $p['img'] );
			wp_set_object_terms( $post_id, $p['category'], 'category' );
		}
	}

	update_option( 'vh_posts_seeded_v5', 1 );
}

// ─── 9. GITHUB THEME AUTO-UPDATER ───────────────────────────────────────────
add_filter( 'pre_set_site_transient_update_themes', 'vh_github_check_theme_update' );
function vh_github_check_theme_update( $transient ) {
	if ( empty( $transient->checked ) ) {
		return $transient;
	}

	$repo = get_option( 'vh_github_repo', defined( 'VH_GITHUB_REPO' ) ? VH_GITHUB_REPO : '' );
	if ( empty( $repo ) ) {
		return $transient;
	}

	$repo = trim( $repo, '/' );
	$transient_key = 'vh_gh_update_check';
	$release = get_transient( $transient_key );

	if ( false === $release ) {
		$headers = [
			'Accept'     => 'application/vnd.github.v3+json',
			'User-Agent' => 'WordPress-VivaHost-Updater',
		];
		$token = get_option( 'vh_github_token', defined( 'VH_GITHUB_TOKEN' ) ? VH_GITHUB_TOKEN : '' );
		if ( ! empty( $token ) ) {
			$headers['Authorization'] = 'Bearer ' . trim( $token );
		}

		$response = wp_remote_get( "https://api.github.com/repos/{$repo}/releases/latest", [
			'headers' => $headers,
			'timeout' => 10,
		] );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			set_transient( $transient_key, 'none', HOUR_IN_SECONDS );
			return $transient;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ) );
		$release = ! empty( $body->tag_name ) ? $body : 'none';
		set_transient( $transient_key, $release, 4 * HOUR_IN_SECONDS );
	}

	if ( empty( $release ) || 'none' === $release || empty( $release->tag_name ) ) {
		return $transient;
	}

	$theme_slug  = 'vivahost-tema';
	$theme_obj   = wp_get_theme( $theme_slug );
	$current_ver = $theme_obj->exists() ? $theme_obj->get( 'Version' ) : '3.8.3';
	$remote_ver  = ltrim( $release->tag_name, 'v' );

	if ( version_compare( $remote_ver, $current_ver, '>' ) ) {
		$package_url = $release->zipball_url;
		if ( ! empty( $release->assets ) && is_array( $release->assets ) ) {
			foreach ( $release->assets as $asset ) {
				if ( isset( $asset->name ) && substr( $asset->name, -4 ) === '.zip' ) {
					$package_url = $asset->browser_download_url;
					break;
				}
			}
		}

		$transient->response[ $theme_slug ] = [
			'theme'       => $theme_slug,
			'new_version' => $remote_ver,
			'url'         => $release->html_url,
			'package'     => $package_url,
		];
	}

	return $transient;
}

// Assicura che la directory estratta dallo zip GitHub sia sempre vivahost-tema
add_filter( 'upgrader_source_selection', 'vh_github_fix_theme_dir', 10, 4 );
function vh_github_fix_theme_dir( $source, $remote_source, $upgrader, $hook_extra = [] ) {
	if ( isset( $hook_extra['theme'] ) && 'vivahost-tema' === $hook_extra['theme'] ) {
		global $wp_filesystem;
		$correct_dir = trailingslashit( $remote_source ) . 'vivahost-tema/';
		if ( $source !== $correct_dir ) {
			$wp_filesystem->move( $source, $correct_dir );
			return $correct_dir;
		}
	}
	return $source;
}

// Scheda popup dettagli con changelog GitHub
add_filter( 'themes_api', 'vh_github_theme_api_info', 20, 3 );
function vh_github_theme_api_info( $res, $action, $args ) {
	if ( 'theme_information' !== $action || empty( $args->slug ) || 'vivahost-tema' !== $args->slug ) {
		return $res;
	}

	$repo = get_option( 'vh_github_repo', defined( 'VH_GITHUB_REPO' ) ? VH_GITHUB_REPO : '' );
	if ( ! $repo ) return $res;

	$release = get_transient( 'vh_gh_update_check' );
	if ( ! empty( $release ) && 'none' !== $release ) {
		$res = (object) [
			'name'          => 'VivaHost',
			'slug'          => 'vivahost-tema',
			'version'       => ltrim( $release->tag_name, 'v' ),
			'author'        => 'VivaHost',
			'homepage'      => 'https://meuvivahost.com.br',
			'sections'      => [
				'description' => 'Tema VivaHost para gestão profissional de imóveis por temporada.',
				'changelog'   => nl2br( esc_html( $release->body ?? 'Melhorias de desempenho e novas funcionalidades.' ) ),
			],
			'download_link' => $release->zipball_url,
		];
	}
	return $res;
}

// Autorizzazione HTTP per download pacchetto ZIP da repository GitHub privati
add_filter( 'http_request_args', 'vh_github_download_auth', 10, 2 );
function vh_github_download_auth( $args, $url ) {
	if ( strpos( $url, 'api.github.com' ) !== false || strpos( $url, 'codeload.github.com' ) !== false ) {
		$token = get_option( 'vh_github_token', defined( 'VH_GITHUB_TOKEN' ) ? VH_GITHUB_TOKEN : '' );
		if ( ! empty( $token ) ) {
			$args['headers']['Authorization'] = 'Bearer ' . trim( $token );
		}
	}
	return $args;
}