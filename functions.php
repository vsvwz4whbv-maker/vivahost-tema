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
define( 'VH_VER',  '3.9.9' );
define( 'VH_PATH', get_stylesheet_directory() );
define( 'VH_URL',  get_stylesheet_directory_uri() );

// Default values per le impostazioni del Customizer
define( 'VH_DEFAULT_PRIMARY',   '#ff385c' );
define( 'VH_DEFAULT_FOOTER_BG', '#222222' );
define( 'VH_DEFAULT_FONT',      'Plus Jakarta Sans' );
define( 'VH_DEFAULT_WA_NUM',    '5571999999999' );

// ─── 0. MOTORE SEO WORDPRESS-NATIVE, SICUREZZA & OPZIONI GENERALI ────────────
require_once VH_PATH . '/inc/seo-engine.php';
require_once VH_PATH . '/inc/security-engine.php';

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
	$text( 'header_cta_text', 'style', 'Testo bottone header',  'Avaliar minha hospedagem' );
	$text( 'header_cta_link', 'style', 'Link bottone header',   '#contato' );

	// ── HERO ─────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_hero', [ 'title' => 'Hero', 'panel' => 'vivahost_panel', 'priority' => 20 ] );
	$text( 'hero_eyebrow',   'hero', 'Eyebrow (sopra titolo)', 'Operação de Hospedagem & Hospitalidade — Salvador, BA' );
	$text( 'hero_title',     'hero', 'Titolo (H1)',            'Alta performance e hospitalidade no Airbnb em Salvador — com tranquilidade para você' );
	$area( 'hero_subtitle',  'hero', 'Sottotitolo',            'A VivaHost cuida da operação da sua hospedagem em Salvador de ponta a ponta: dos anúncios e reservas ao atendimento, limpeza e acompanhamento presencial. Você acompanha os resultados com total transparência.' );
	$text( 'hero_btn1_text', 'hero', 'Bottone 1 — testo',      'Avaliar minha hospedagem' );
	$text( 'hero_btn1_link', 'hero', 'Bottone 1 — link',       '#contato' );
	$text( 'hero_btn2_text', 'hero', 'Bottone 2 — testo',      'Como funciona' );
	$text( 'hero_btn2_link', 'hero', 'Bottone 2 — link',       '#como-funciona' );
	$img(  'hero_bg',        'hero', 'Immagine di sfondo hero' );

	// ── STATS ────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_stats', [ 'title' => 'Barra Numeri (4 Stats)', 'panel' => 'vivahost_panel', 'priority' => 30 ] );
	$defs = [
		[ '+30%',    'Média de faturamento vs tradicional' ],
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
	$text( 'process_title', 'process', 'Titolo sezione', 'Como funciona a operação da sua hospedagem' );
	$area( 'process_intro', 'process', 'Testo intro',    'Do alinhamento inicial ao acolhimento dos hóspedes, cuidamos de toda a rotina operacional para proporcionar a melhor estadia com tranquilidade para você.' );
	$step_defs = [
		[ 'Análise de potencial', 'Avaliamos o perfil da sua acomodação e projetamos o faturamento estimado com base no histórico da região em Salvador.' ],
		[ 'Produção e posicionamento', 'Sessão fotográfica profissional e criação de anúncios atrativos nas principais plataformas de hospedagem.' ],
		[ 'Operação e hospitalidade 360°', 'Check-in, suporte aos hóspedes, governança, higienização impecável e manutenção preventiva contínua.' ],
		[ 'Relatórios e acompanhamento', 'Prestação de contas detalhada todo mês e acompanhamento transparente do desempenho da sua hospedagem.' ],
	];
	for ( $i = 1; $i <= 4; $i++ ) {
		$text( "step_{$i}_title", 'process', "Step {$i} — Titolo", $step_defs[$i-1][0] );
		$area( "step_{$i}_text",  'process', "Step {$i} — Testo",  $step_defs[$i-1][1] );
	}

	// ── COMPARAÇÃO (Sozinho vs VivaHost) ──────────────────────────────────
	$c->add_section( 'vivahost_compare', [ 'title' => 'Comparação (Sozinho vs VivaHost)', 'panel' => 'vivahost_panel', 'priority' => 45 ] );
	$chk( 'compare_show', 'compare', 'Mostrar sezione', '1' );
	$text( 'compare_title', 'compare', 'Titolo sezione', 'Hospedar por conta própria vs Operação VivaHost' );
	$text( 'compare_left_title', 'compare', 'Titolo colonna sinistra', 'Por conta própria' );
	$area( 'compare_left_1', 'compare', 'Riga 1 — sinistra', 'Você resolve toda a rotina — mensagens, reservas, check-in e chamados 24h' );
	$area( 'compare_left_2', 'compare', 'Riga 2 — sinistra', 'Preço sem dados de mercado — receita e ocupação abaixo do potencial' );
	$area( 'compare_left_3', 'compare', 'Riga 3 — sinistra', 'Limpeza, enxoval, amenidades e reparos — tudo sob sua responsabilidade direta' );
	$text( 'compare_right_title', 'compare', 'Titolo colonna destra', 'Com a VivaHost' );
	$area( 'compare_right_1', 'compare', 'Riga 1 — destra', 'Equipe local cuida de tudo — você acompanha os resultados da sua hospedagem com tranquilidade' );
	$area( 'compare_right_2', 'compare', 'Riga 2 — destra', 'Estratégia de preços dinâmica — busca por ocupação máxima e histórico de até +30% de receita' );
	$area( 'compare_right_3', 'compare', 'Riga 3 — destra', 'Higienização hoteleira e amenidades — acomodação sempre pronta e impecável para o próximo hóspede' );

	// ── SERVIÇOS ─────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_services', [ 'title' => 'Serviços (6)', 'panel' => 'vivahost_panel', 'priority' => 50 ] );
		$text( 'services_title', 'services', 'Titolo sezione', 'Serviços completos de operação e hospitalidade em Salvador' );
		$area( 'services_intro', 'services', 'Testo intro',    'Da produção fotográfica ao suporte presencial 24h, oferecemos toda a estrutura operacional e de hospitalidade para o sucesso da sua hospedagem por temporada.' );
		$text( 'commission_rate', 'services', 'Taxa de serviço padrão', '20%' );
		$svc_defs = [
			[ 'Fotografia profissional',       'Ensaio completo com produção visual dedicada. Imagens atraentes que valorizam os diferenciais da acomodação.' ],
			[ 'Estratégia de preços dinâmica', 'Monitoramento contínuo de demanda, sazonalidade e concorrência local para otimizar o valor das diárias.' ],
			[ 'Check-in & Check-out',          'Acolhimento atencioso para cada hóspede, instruções claras de acesso e suporte presencial na chegada.' ],
			[ 'Governança & Higienização',     'Equipe dedicada de limpeza, troca de enxoval de qualidade e reposição de amenidades a cada reserva.' ],
			[ 'Suporte e Acompanhamento 24h',  'Atendimento rápido aos hóspedes durante toda a estadia e suporte operacional para qualquer imprevisto.' ],
			[ 'Relatórios de desempenho',      'Demonstrativo claro com ocupação, diárias e avaliações recebidas, com repasse mensal pontual.' ],
		];
		for ( $i = 1; $i <= 6; $i++ ) {
			$text( "service_{$i}_title", 'services', "Card {$i} — Titolo", $svc_defs[$i-1][0] );
			$area( "service_{$i}_text",  'services', "Card {$i} — Testo",  $svc_defs[$i-1][1] );
		}

		// ── SERVIÇOS DE CONFIANÇA ─────────────────────────────────────────────
		$c->add_section( 'vivahost_trust_sec', [ 'title' => 'Serviços de Confiança', 'panel' => 'vivahost_panel', 'priority' => 55 ] );
		$chk( 'trust_section_show', 'trust_sec', 'Mostrar sezione', '1' );
		$text( 'trust_section_title', 'trust_sec', 'Titolo sezione', 'Serviços de confiança — sem estresse' );
		$area( 'trust_section_intro', 'trust_sec', 'Testo intro', 'Necessita de apoio com a acomodação? A VivaHost cuida de tudo. Rede de profissionais parceiros verificados para qualquer necessidade de manutenção preventiva ou corretiva.' );
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
	$text( 'imoveis_title', 'properties', 'Titolo sezione', 'Conheça algumas das propriedades atendidas pela VivaHost' );
	$area( 'imoveis_desc',  'properties', 'Descrizione',    'Cada acomodação recebe o mesmo padrão de cuidado operacional — fotos de qualidade, limpeza rigorosa e experiência do hóspede comprovada em avaliações.' );
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
	$text( 'testimonial_banner_sub',   'testimonial', 'Sottotitolo banner foto', 'Experiência de Proprietária' );
	$area( 'testimonial_text',   'testimonial', 'Citazione', 'Márcia e sua equipe cuidam da hospedagem com dedicação, limpeza impecável e uma proatividade exemplar. Desde que comecei a contar com a VivaHost para a operação da hospedagem, não tenho com o que me preocupar.' );
	$text( 'testimonial_author', 'testimonial', 'Nome autore', 'Ana Paula M.' );
	$text( 'testimonial_city',   'testimonial', 'Città / Ruolo', 'Proprietária · Salvador, BA' );
	$img( 'testimonial_photo',   'testimonial', 'Foto autore depoimento' );

	// ── HOST ─────────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_host', [ 'title' => 'Host (Marcia)', 'panel' => 'vivahost_panel', 'priority' => 80 ] );
	$text( 'host_name',          'host', 'Nome',            'Marcia Sales' );
	$text( 'host_subtitle',      'host', 'Sottotitolo / Ruolo', 'Especialista em Hospitalidade & Operação de Hospedagens em Salvador' );
	$area( 'host_bio',           'host', 'Bio / Metodo de Gestao', 'Com 9 anos de dedicação à hospitalidade em Salvador como Superhost, ofereço uma operação próxima e presencial. Cuidamos de cada acomodação com equipe própria de confiança, zelo rigoroso na conservação e comunicação direta e transparente com os proprietários a qualquer momento.' );
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
	$text( 'blog_section_eyebrow', 'blog_sec', 'Eyebrow sopra titolo', 'Hospitalidade & Temporada' );
	$text( 'blog_section_title', 'blog_sec', 'Titolo sezione', 'Dicas e novidades sobre hospitalidade e temporada em Salvador' );
	$area( 'blog_section_intro', 'blog_sec', 'Testo intro',    'Estratégias operacionais, dicas de hospitalidade e orientações práticas para proprietários que desejam maximizar a performance no Airbnb na Bahia.' );
	$text( 'blog_section_link', 'blog_sec', 'Link "Ver todos"', '/blog/' );

	// ── CTA / FORM ───────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_cta', [ 'title' => 'CTA & Formulário', 'panel' => 'vivahost_panel', 'priority' => 100 ] );
	$text( 'cta_title', 'cta', 'Titolo sezione', 'Descubra o potencial de faturamento da sua hospedagem' );
	$area( 'cta_lead',  'cta', 'Testo lead',     'Receba uma projeção personalizada de desempenho para a sua acomodação em Salvador, com base em dados de mercado e sem compromisso.' );
	$text( 'trust_1',   'cta', 'Trust item 1',   'Análise de potencial gratuita e sem compromisso' );
	$text( 'trust_2',   'cta', 'Trust item 2',   'Seus dados tratados com total privacidade' );
	$text( 'trust_3',   'cta', 'Trust item 3',   'Retorno em até 24 horas úteis por WhatsApp' );
	$sel(  'form_mode', 'cta', 'Modalità form',  'both', [
		'email'     => 'Solo email',
		'whatsapp'  => 'Solo WhatsApp',
		'both'      => 'Email + WhatsApp',
	], 'refresh' );
	$text( 'form_wa_cta', 'cta', 'Testo link WhatsApp alternativo', 'Conversar pelo WhatsApp' );

	// ── WHATSAPP ─────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_whatsapp', [ 'title' => 'WhatsApp', 'panel' => 'vivahost_panel', 'priority' => 110 ] );
	$text( 'wa_number',  'whatsapp', 'Numero WhatsApp (internazionale)', VH_DEFAULT_WA_NUM );
	$area( 'wa_message', 'whatsapp', 'Messaggio pre-compilato',          'Olá! Gostaria de saber mais sobre os serviços da VivaHost.' );
	$chk(  'wa_float_show', 'whatsapp', 'Mostra bottone float WhatsApp' );

	// ── FOOTER ───────────────────────────────────────────────────────────────
	$c->add_section( 'vivahost_footer_sec', [ 'title' => 'Footer', 'panel' => 'vivahost_panel', 'priority' => 120 ] );
	$text( 'footer_tagline',   'footer_sec', 'Tagline footer',     'VivaHost — Superhost Airbnb em Salvador, Bahia. Operação de hospedagens e aluguel por temporada com foco em hospitalidade e excelência.' );
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

// ─── 5. AJAX FORM HANDLER (PROTETTO DA SCUDO ANTI-BOT & WAF) ─────────────────
add_action( 'wp_ajax_vh_form',        'vh_form_handler' );
add_action( 'wp_ajax_nopriv_vh_form', 'vh_form_handler' );
function vh_form_handler() {
	// 1. Verifica Scudo Anti-Bot (CSRF Nonce, Origin, Doppio Honeypot, HMAC Timestamp, JS Token, IP Rate Limit, Turnstile)
	vh_verify_form_security_shield();

	// 2. Sanitizzazione rigorosa, Whitelist e WAF anti-injection
	$clean = vh_sanitize_and_validate_form_payload( $_POST );
	if ( is_wp_error( $clean ) ) {
		wp_send_json_error( [ 'message' => $clean->get_error_message() ], 422 );
	}

	$nome    = $clean['nome'];
	$cidade  = $clean['cidade'];
	$tipo    = $clean['tipo'];
	$quartos = $clean['quartos'];
	$phone   = $clean['phone'];

	$mode = vh_mod( 'form_mode', 'both' );

	// Build WhatsApp URL with formatted number and encoded message via helper
	$lead_msg = "Olá! Tenho interesse na avaliação gratuita.\n\nNome: {$nome}\nCidade/Bairro: {$cidade}\nTipo de imóvel: {$tipo}\nQuartos: {$quartos}\nMeu WhatsApp: {$phone}";
	$wa_url   = vh_wa_url( $lead_msg );

	// Send email (con tutti i campi escapati contro HTML/Header Injection)
	$email_sent = false;
	if ( in_array( $mode, [ 'email', 'both' ], true ) ) {
		$to      = sanitize_email( get_option( 'vh_smtp_to_email', get_option( 'admin_email' ) ) );
		$subject = 'Nova avaliação de imóvel — ' . wp_strip_all_tags( $nome );
		$body    = '<h2>Nova solicitação de avaliação (VivaHost)</h2>'
			. '<p><strong>Nome:</strong> ' . esc_html( $nome ) . '</p>'
			. '<p><strong>Cidade/Bairro:</strong> ' . esc_html( $cidade ) . '</p>'
			. '<p><strong>Tipo de imóvel:</strong> ' . esc_html( $tipo ) . '</p>'
			. '<p><strong>Nº de quartos:</strong> ' . esc_html( $quartos ) . '</p>'
			. '<p><strong>WhatsApp:</strong> ' . esc_html( $phone ) . '</p>'
			. '<hr style="border:none;border-top:1px solid #eee;margin:16px 0">'
			. '<p style="font-size:12px;color:#777">Enviado com proteção anti-bot VivaHost em ' . esc_html( current_time( 'd/m/Y H:i' ) ) . '</p>';
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

// ─── 6. ADMIN: MENU DEDICATO VIVAHOST (SEO, SICUREZZA, SMTP, GITHUB) ─────────
function vh_render_seo_settings_page() {
	require_once VH_PATH . '/admin/seo-settings.php';
}
function vh_render_smtp_settings_page() {
	require_once VH_PATH . '/admin/smtp-settings.php';
}

add_action( 'admin_menu', function () {
	// Menu Top-Level "VivaHost" nella barra laterale di WordPress
	add_menu_page(
		'VivaHost — Central SEO, Segurança & Opções',
		'VivaHost',
		'manage_options',
		'vivahost-seo',
		'vh_render_seo_settings_page',
		'dashicons-admin-home',
		58
	);

	// 1. Sottomenu principale: SEO, Segurança & Opções (rinomina la prima voce senza duplicare l'hook)
	add_submenu_page(
		'vivahost-seo',
		'VivaHost — Central SEO, Segurança & Opções',
		'SEO & Segurança',
		'manage_options',
		'vivahost-seo',
		'vh_render_seo_settings_page'
	);

	// 2. Sottomenu: Email & SMTP
	add_submenu_page(
		'vivahost-seo',
		'VivaHost — Email & SMTP',
		'Email & SMTP',
		'manage_options',
		'vivahost-smtp',
		'vh_render_smtp_settings_page'
	);

	// 3. Sottomenu: Collegamento rapido al Customizer (Personalizar Tema)
	global $submenu;
	if ( current_user_can( 'customize' ) ) {
		$submenu['vivahost-seo'][] = [
			'Personalizar Tema',
			'customize',
			admin_url( 'customize.php' ),
		];
	}
} );

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

	wp_safe_redirect( admin_url( 'admin.php?page=vivahost-smtp&saved=1' ) );
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

	if ( get_option( 'vh_posts_seeded_v6' ) ) {
		return;
	}

	$posts = [
		[
			'post_title'   => 'Como Maximizar a Performance da sua Hospedagem no Airbnb em Salvador: O Guia Definitivo',
			'post_name'    => 'como-maximizar-faturamento-airbnb-salvador',
			'post_excerpt' => 'Descubra as estratégias comprovadas de precificação dinâmica, preparação da acomodação e hospitalidade para encantar hóspedes em Salvador com a VivaHost.',
			'seo_title'    => 'Como Maximizar a Performance no Airbnb em Salvador — Guia VivaHost',
			'seo_desc'     => 'Guia completo para anfitriões e proprietários em Salvador: precificação dinâmica, sazonalidade na Bahia, enxoval hoteleiro e hospitalidade com a VivaHost.',
			'seo_keyword'  => 'performance airbnb salvador, hospitalidade airbnb salvador, temporada salvador',
			'img'          => 'https://images.unsplash.com/photo-1590523277543-a94d2e4eb00b?auto=format&fit=crop&w=1200&q=80',
			'category'     => 'Estratégia & Hospitalidade',
			'post_content' => '<p class="lead font-medium text-lg text-neutral">Salvador é um dos polos turísticos e culturais mais procurados da América do Sul. Para proprietários na capital baiana, disponibilizar uma acomodação para estadias de temporada no Airbnb representa uma oportunidade expressiva — desde que conduzida com metodologia, atenção aos detalhes e hospitalidade de excelência.</p><h2>1. A Curva de Sazonalidade em Salvador: Muito Além do Carnaval</h2><p>O maior equívoco de quem começa na hospedagem por temporada é enxergar Salvador apenas através da lente do Carnaval e do Réveillon. Embora essas semanas de pico registrem diárias até 4 vezes superiores à média anual, o calendário da capital baiana oferece oportunidades de ocupação consistente durante os doze meses do ano:</p><ul><li><strong>Alta Temporada de Verão (Novembro a Março):</strong> Ocupação elevada em bairros litorâneos como Barra, Ondina e Rio Vermelho. O público busca sol, mar, ensaios de verão e turismo cultural.</li><li><strong>Média Temporada e Festas Típicas (Junho e Julho):</strong> O período de São João e as férias escolares de inverno trazem famílias e viajantes de todo o país para vivenciar a gastronomia e o Centro Histórico.</li><li><strong>Temporada Corporativa e Eventos (Abril a Outubro):</strong> Meses de grande movimentação de negócios, congressos médicos e eventos empresariais, especialmente para acomodações no Costa Azul, Caminho das Árvores e Armação.</li></ul><h2>2. Estratégia de Preços Dinâmica vs. O Erro da Diária Fixa</h2><p>Manter a mesma diária em uma terça-feira de baixa procura e em um sábado ensolarado de alta temporada reduz o potencial da sua hospedagem — seja por vacância ou por cobrar abaixo do mercado quando a procura atinge o ápice. Na VivaHost, monitoramos a curva de procura diária, a taxa de ocupação dos bairros vizinhos e grandes eventos na cidade para calibrar o valor das diárias com inteligência.</p><h2>3. Preparação e Conforto: O que Encanta os Hóspedes</h2><p>Os viajantes em Salvador valorizam comodidades que garantam conforto térmico, segurança e bem-estar:</p><ul><li><strong>Climatização Eficiente:</strong> Ar-condicionado split em todos os quartos e, preferencialmente, na sala de estar.</li><li><strong>Conexão de Alta Velocidade:</strong> Wi-Fi rápido e estável para atender nômades digitais e quem viaja a trabalho.</li><li><strong>Enxoval Padrão Hoteleiro:</strong> Roupas de cama de toque suave e toalhas de alta gramatura, rigorosamente higienizadas a cada reserva.</li><li><strong>Acesso Facilitado:</strong> Fechadura eletrônica ou recepção acolhedora com instruções claras de chegada.</li><li><strong>Acolhimento com Identidade Local:</strong> Mimos de boas-vindas com referências baianas criam conexão imediata e impulsionam avaliações 5 estrelas.</li></ul><h2>4. A Importância da Reputação de Superhost</h2><p>Anúncios com reconhecimento de Superhost no Airbnb conquistam destaque nas buscas e inspiram máxima confiança nos viajantes, que priorizam anfitriões com histórico comprovado de hospitalidade e limpeza impecável.</p><h2>5. Operação por Conta Própria vs. Operação Profissional VivaHost</h2><p>Cuidar de uma acomodação de temporada por conta própria demanda dezenas de horas semanais com mensagens, governança, lavanderia, recepção e imprevistos. A VivaHost assume toda a rotina operacional — da produção fotográfica ao suporte presencial e prestação de contas mensal —, proporcionando tranquilidade para o proprietário e experiências memoráveis para os hóspedes.</p>',
		],
		[
			'post_title'   => 'Hospedagem por Temporada em Salvador: Boas Práticas e Regras de Condomínio',
			'post_name'    => 'aluguel-temporada-salvador-regras-condominio-legislacao',
			'post_excerpt' => 'Entenda as melhores práticas de convivência em condomínio, segurança e harmonia ao disponibilizar sua acomodação para hospedagem de temporada em Salvador.',
			'seo_title'    => 'Hospedagem por Temporada em Salvador: Boas Práticas e Condomínios',
			'seo_desc'     => 'Diretrizes práticas de convivência e protocolos de segurança da VivaHost para hospedar por temporada em condomínios de Salvador com tranquilidade.',
			'seo_keyword'  => 'hospedagem temporada salvador, condomínio temporada salvador, convivência airbnb',
			'img'          => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80',
			'category'     => 'Hospitalidade & Convivência',
			'post_content' => '<p class="lead font-medium text-lg text-neutral">A hospedagem de curta duração consolidou-se como uma das formas mais dinâmicas e acolhedoras de receber viajantes em Salvador. Para os proprietários, o segredo de uma operação bem-sucedida está na harmonia contínua com as normas do condomínio e no respeito aos vizinhos. Conheça as práticas operacionais essenciais para garantir tranquilidade a todos.</p><h2>1. Regras Claras e Comunicação Transparente</h2><p>A harmonia em ambientes condominiais depende de regras transparentes e comunicação preventiva antes mesmo da chegada do hóspede:</p><ul><li><strong>Identificação Prévia Obrigatória:</strong> Coleta antecipada de nomes completos e documentos de identificação de todos os ocupantes, enviada com antecedência para a portaria.</li><li><strong>Capacidade Máxima Respeitada:</strong> Limite rigoroso de pessoas de acordo com o número de camas anunciado, sem permissão de festas ou visitas não cadastradas.</li><li><strong>Respeito à Lei do Silêncio:</strong> Orientação detalhada aos viajantes sobre horários de silêncio, uso de áreas comuns e normas internas do edifício.</li></ul><h2>2. Protocolo Operacional Preventivo da VivaHost</h2><p>Para assegurar que cada estadia ocorra com ordem e zelo, a equipe local da VivaHost mantém acompanhamento próximo:</p><ul><li><strong>Acompanhamento Presencial:</strong> Instruções claras de check-in e check-out, com canal de suporte 24h para esclarecer qualquer dúvida de imediato.</li><li><strong>Canal Direto com a Portaria:</strong> A equipe da VivaHost fica à disposição direta da portaria e equipe do condomínio para atender prontamente a qualquer necessidade.</li><li><strong>Vistorias Periódicas:</strong> Verificação rigorosa do estado do imóvel após cada saída, mantendo a acomodação sempre bem cuidada.</li></ul><h2>Conclusão: Zelo e Cuidado em Primeiro Lugar</h2><p>Receber hóspedes em Salvador é uma experiência gratificante quando realizada com responsabilidade, protocolos claros e dedicação contínua. Com o suporte operacional da VivaHost, sua acomodação contribui positivamente para o condomínio e acolhe viajantes com excelência.</p>',
		],
		[
			'post_title'   => 'Os Melhores Bairros de Salvador para Hospedagem de Temporada em 2026',
			'post_name'    => 'melhores-bairros-salvador-investimento-aluguel-temporada',
			'post_excerpt' => 'Barra, Ondina, Rio Vermelho, Costa Azul ou Stella Maris? Análise sobre perfil de público, fluxo de viajantes e atratividade das principais regiões de Salvador.',
			'seo_title'    => 'Melhores Bairros de Salvador para Hospedagem por Temporada em 2026',
			'seo_desc'     => 'Conheça os bairros com maior procura para hospedagem em Salvador: Barra, Ondina, Rio Vermelho, Costa Azul e praias do norte.',
			'seo_keyword'  => 'melhores bairros salvador hospedagem, airbnb barra ondina rio vermelho, turismo salvador',
			'img'          => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
			'category'     => 'Destinos & Temporada',
			'post_content' => '<p class="lead font-medium text-lg text-neutral">Salvador atrai visitantes de todas as partes do Brasil e do mundo, motivados pela rica cena cultural, gastronomia marcante, praias encantadoras e eventos ao longo do ano. Para quem disponibiliza acomodações na capital baiana, conhecer as particularidades de cada bairro é fundamental para posicionar a hospedagem com sucesso.</p><h2>1. Barra: O Epicentro Turístico</h2><p>A Barra é o endereço mais tradicional e procurado pelos viajantes. Com praias calmas no Porto da Barra e o icônico pôr do sol no Farol, a região possui procura constante tanto no verão quanto ao longo do ano.</p><h2>2. Ondina: Conforto e Localização Estratégica</h2><p>Vizinha à Barra, Ondina reúne condomínios modernos com estrutura completa de lazer e vista para o mar, atraindo viajantes que buscam comodidade e fácil acesso aos principais pontos da orla.</p><h2>3. Rio Vermelho: Cultura e Gastronomia</h2><p>O coração boêmio de Salvador atrai quem valoriza experiências gastronômicas, vida cultural vibrante e proximidade com artistas e a autêntica vida noturna soteropolitana.</p><h2>4. Costa Azul e Armação: Conveniência e Praticidade</h2><p>Regiões valorizadas pela proximidade com centros de convenções, polos empresariais e shoppings, além do Parque dos Ventos e da orla, perfeitas para viagens a trabalho e famílias.</p><h2>5. Stella Maris e Flamengo: Refúgio de Lazer</h2><p>Praias amplas, ideais para estadias de descanso em família e quem procura tranquilidade e contato com a natureza perto da capital.</p><h2>Acompanhe o Desempenho da sua Hospedagem com a VivaHost</h2><p>Quer entender como a sua acomodação em Salvador pode se destacar no mercado de hospedagem? Fale conosco pelo WhatsApp e converse com a Marcia sobre as soluções operacionais da VivaHost.</p>',
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
			if ( ! get_post_meta( $post_id, '_vh_seo_title', true ) ) {
				update_post_meta( $post_id, '_vh_seo_title', $p['seo_title'] );
			}
			if ( ! get_post_meta( $post_id, '_vh_seo_desc', true ) ) {
				update_post_meta( $post_id, '_vh_seo_desc', $p['seo_desc'] );
			}
			if ( ! get_post_meta( $post_id, '_vh_seo_keyword', true ) ) {
				update_post_meta( $post_id, '_vh_seo_keyword', $p['seo_keyword'] );
			}
			wp_set_object_terms( $post_id, $p['category'], 'category' );
		}
	}

	update_option( 'vh_posts_seeded_v6', 1 );
}

/**
 * Restituisce l'elenco completo di tutte le 12 proprietà in gestione VivaHost,
 * applicando gli override del Customizer (prop_1_*, prop_2_*, prop_3_*).
 *
 * @return array
 */
function vh_get_all_properties() {
	$prop_fallbacks = [
		1 => VH_URL . '/assets/images/prop-ondina-vista.webp',
		2 => VH_URL . '/assets/images/prop-lar-lisboa.webp',
		3 => VH_URL . '/assets/images/prop-apartamento-salvador.webp',
	];

	$properties = [
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

	// Applica gli override Customizer per i primi 3 immobili
	for ( $i = 1; $i <= 3; $i++ ) {
		$idx = $i - 1;
		$ph_id  = (int) vh_mod( "prop_{$i}_photo", 0 );
		if ( $ph_id ) {
			$img_url = wp_get_attachment_image_url( $ph_id, 'large' );
			if ( $img_url ) {
				$properties[ $idx ]['photo'] = $img_url;
			}
		} elseif ( isset( $prop_fallbacks[ $i ] ) ) {
			$properties[ $idx ]['photo'] = $prop_fallbacks[ $i ];
		}

		$custom_name = vh_mod( "prop_{$i}_name", '' );
		if ( $custom_name ) {
			$properties[ $idx ]['name'] = $custom_name;
		}

		$custom_loc = vh_mod( "prop_{$i}_loc", '' );
		if ( $custom_loc ) {
			$properties[ $idx ]['loc'] = $custom_loc;
		}

		$custom_rate = vh_mod( "prop_{$i}_rating", '' );
		if ( $custom_rate ) {
			$properties[ $idx ]['rating'] = $custom_rate;
		}

		$custom_rev = vh_mod( "prop_{$i}_reviews", '' );
		if ( $custom_rev ) {
			$properties[ $idx ]['reviews'] = $custom_rev;
		}

		$custom_link = vh_mod( "prop_{$i}_link", '' );
		if ( $custom_link ) {
			$properties[ $idx ]['link'] = $custom_link;
		}
	}

	return $properties;
}

/**
 * Assicura che le pagine 'imoveis' e 'blog' esistano nel database WordPress.
 */
add_action( 'init', 'vh_setup_theme_pages' );
function vh_setup_theme_pages() {
	// Pagina Imóveis
	if ( ! get_option( 'vh_imoveis_page_created' ) ) {
		$existing_imoveis = new WP_Query( [
			'name'             => 'imoveis',
			'post_type'        => 'page',
			'post_status'      => 'any',
			'fields'           => 'ids',
			'posts_per_page'   => 1,
			'no_found_rows'    => true,
			'suppress_filters' => true,
		] );

		if ( ! $existing_imoveis->have_posts() ) {
			$page_id = wp_insert_post( [
				'post_title'     => 'Imóveis',
				'post_name'      => 'imoveis',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'page_template'  => 'page-imoveis.php',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			] );
			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_post_meta( $page_id, '_vh_seo_title', 'Imóveis em Salvador | Acomodações Superhost VivaHost' );
				update_post_meta( $page_id, '_vh_seo_desc', 'Conheça nossos apartamentos e studios para aluguel por temporada em Salvador nos melhores bairros: Costa Azul, Barra, Ondina e Amaralina.' );
			}
		}
		update_option( 'vh_imoveis_page_created', 1 );
	}

	// Pagina Blog
	if ( ! get_option( 'vh_blog_page_created' ) ) {
		$existing_blog = new WP_Query( [
			'name'             => 'blog',
			'post_type'        => 'page',
			'post_status'      => 'any',
			'fields'           => 'ids',
			'posts_per_page'   => 1,
			'no_found_rows'    => true,
			'suppress_filters' => true,
		] );

		if ( ! $existing_blog->have_posts() ) {
			$blog_id = wp_insert_post( [
				'post_title'     => 'Blog',
				'post_name'      => 'blog',
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'page_template'  => 'page-blog.php',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			] );
			if ( $blog_id && ! is_wp_error( $blog_id ) ) {
				update_post_meta( $blog_id, '_vh_seo_title', 'Blog VivaHost | Dicas de Hospitalidade e Temporada em Salvador' );
				update_post_meta( $blog_id, '_vh_seo_desc', 'Artigos práticos, estratégias de precificação e orientações de hospitalidade para anfitriões e viajantes em Salvador, Bahia.' );
			}
		}
		update_option( 'vh_blog_page_created', 1 );
	}
}

/**
 * Routing trasparente: carica page-imoveis.php per /imoveis e page-blog.php per /blog.
 */
add_filter( 'template_include', 'vh_route_custom_pages' );
function vh_route_custom_pages( $template ) {
	$req_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$req_path = trim( (string) parse_url( $req_uri, PHP_URL_PATH ), '/' );

	if ( is_page( 'imoveis' ) || $req_path === 'imoveis' ) {
		$tpl = locate_template( 'page-imoveis.php' );
		if ( $tpl ) {
			return $tpl;
		}
	}

	if ( is_page( 'blog' ) || $req_path === 'blog' ) {
		$tpl = locate_template( 'page-blog.php' );
		if ( $tpl ) {
			return $tpl;
		}
	}

	return $template;
}