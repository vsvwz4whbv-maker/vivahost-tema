<?php
/**
 * VivaHost — Motore SEO WordPress-Native & Opzioni Generali
 *
 * Gestisce in modo centralizzato tramite gli hook nativi di WordPress:
 * - Document Title (pre_get_document_title / document_title_parts)
 * - Meta Description, Robots, Keywords, Canonical, Hreflang, Geo Tags
 * - Open Graph (Facebook/WhatsApp/LinkedIn) & Twitter Cards
 * - Schema.org JSON-LD @graph (RealEstateAgent, WebSite, Service, FAQPage, BlogPosting, BreadcrumbList)
 * - Metabox SEO per Articoli e Pagine (_vh_seo_title, _vh_seo_desc, _vh_seo_keyword, _vh_seo_canonical, _vh_seo_og_image, _vh_seo_noindex)
 * - Ottimizzazione Sitemap XML nativa (wp-sitemap.xml), robots.txt e /llms.txt (AI SEO / GEO)
 * - Integrazione Google Analytics 4 (GA4), Google Tag Manager (GTM), Meta Pixel e Webmaster Verification
 * - Pulizia <head> WordPress per Core Web Vitals
 *
 * @package VivahostChild
 */

defined( 'ABSPATH' ) || exit;

/**
 * Helper per leggere le opzioni SEO/Generali con default stabili.
 *
 * @param string $key     Chiave dell'opzione (es. 'vh_seo_home_title').
 * @param mixed  $default Valore di default se l'opzione è vuota o non salvata.
 * @return mixed
 */
function vh_seo_opt( $key, $default = '' ) {
	$val = get_option( $key, null );
	if ( null === $val || '' === $val ) {
		return $default;
	}
	return $val;
}

/**
 * Restituisce tutti i valori di default delle opzioni SEO e Generali.
 *
 * @return array
 */
function vh_seo_defaults() {
	$site_name = get_bloginfo( 'name' ) ?: 'VivaHost';
	return [
		// Tab 1: SEO Globale & Homepage
		'vh_seo_enable_native'   => '1',
		'vh_seo_title_sep'       => '—',
		'vh_seo_home_title'      => 'VivaHost — Gestão Profissional de Aluguel por Temporada e Airbnb em Salvador, BA',
		'vh_seo_home_desc'       => 'Especialista em administração de imóveis por temporada e Airbnb em Salvador, Bahia. Precificação dinâmica, limpeza padrão hoteleiro e suporte 24h com Superhost.',
		'vh_seo_home_keywords'   => 'gestão airbnb salvador, administração de imóveis por temporada salvador, aluguel por temporada salvador bahia, superhost airbnb salvador, vivahost',
		'vh_seo_og_image'        => '',
		'vh_seo_robots_default'  => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',

		// Tab 2: Local SEO & Schema.org
		'vh_seo_schema_enable'   => '1',
		'vh_seo_schema_type'     => 'RealEstateAgent',
		'vh_seo_geo_lat'         => '-12.9818',
		'vh_seo_geo_lng'         => '-38.4552',
		'vh_seo_geo_region'      => 'BR-BA',
		'vh_seo_geo_placename'   => 'Salvador',
		'vh_seo_area_served'     => 'Barra, Ondina, Rio Vermelho, Costa Azul, Armação, Caminho das Árvores, Pituba, Itapuã, Stella Maris, Praia do Flamengo, Pelourinho, Santo Antônio Além do Carmo',
		'vh_seo_price_range'     => vh_mod( 'commission_rate', '20%' ),

		// Tab 3: Opzioni Generali, Analytics & Verifica
		'vh_gen_ga4_id'          => '',
		'vh_gen_gtm_id'          => '',
		'vh_gen_meta_pixel'      => '',
		'vh_seo_google_verify'   => '',
		'vh_seo_bing_verify'     => '',
		'vh_gen_clean_wp_head'   => '1',
		'vh_gen_cookie_bar'      => '1',
		'vh_gen_custom_head'     => '',
		'vh_gen_custom_footer'   => '',

		// Tab 4: Sitemap, Robots.txt & AI SEO (llms.txt)
		'vh_seo_sitemap_enhance' => '1',
		'vh_seo_robots_enhance'  => '1',
		'vh_seo_llms_txt_enable' => '1',
	];
}

/**
 * Rileva se è attivo un plugin SEO di terze parti (Yoast, Rank Math, SEOPress, AIOSEO).
 *
 * @return string|false Nome del plugin attivo o false.
 */
function vh_detect_external_seo_plugin() {
	if ( defined( 'WPSEO_VERSION' ) ) {
		return 'Yoast SEO';
	}
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		return 'Rank Math SEO';
	}
	if ( defined( 'SEOPRESS_VERSION' ) ) {
		return 'SEOPress';
	}
	if ( defined( 'AIOSEO_VERSION' ) ) {
		return 'All in One SEO';
	}
	return false;
}

/**
 * Verifica se il motore SEO nativo VivaHost deve generare i tag nel <head>.
 * Se è presente un plugin SEO esterno e l'utente non ha forzato il motore interno,
 * evita qualsiasi duplicazione di meta tag.
 *
 * @return bool
 */
function vh_is_native_seo_active() {
	$defaults = vh_seo_defaults();
	$enabled  = get_option( 'vh_seo_enable_native', $defaults['vh_seo_enable_native'] );
	if ( '1' !== $enabled ) {
		return false;
	}
	return true;
}

/**
 * Restituisce il nome del brand pulito (sostituisce il placeholder predefinito di WP Playground/Fresh install).
 *
 * @return string
 */
function vh_get_brand_name() {
	$name = get_bloginfo( 'name' );
	if ( empty( $name ) || in_array( $name, [ 'My WordPress Website', 'My Blog', 'WordPress' ], true ) ) {
		return 'VivaHost';
	}
	return $name;
}

// ─── 1. PULIZIA <HEAD> WORDPRESS (PERFORMANCE & CORE WEB VITALS) ─────────────
add_action( 'init', 'vh_clean_wp_head_init' );
function vh_clean_wp_head_init() {
	// Imposta automaticamente il nome del sito se è ancora il placeholder di default WordPress
	if ( in_array( get_option( 'blogname' ), [ 'My WordPress Website', 'My Blog' ], true ) ) {
		update_option( 'blogname', 'VivaHost' );
		update_option( 'blogdescription', 'Gestão Profissional de Aluguel por Temporada em Salvador, BA' );
	}

	$defaults = vh_seo_defaults();
	if ( '1' === get_option( 'vh_gen_clean_wp_head', $defaults['vh_gen_clean_wp_head'] ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
	}

	// Se il motore SEO nativo è attivo, gestiamo noi il tag canonical senza duplicati
	if ( vh_is_native_seo_active() && ! vh_detect_external_seo_plugin() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
}

// ─── 2. GESTIONE NATIVA DEL <TITLE> WORDPRESS ────────────────────────────────
add_filter( 'document_title_separator', 'vh_filter_title_separator', 20 );
function vh_filter_title_separator( $sep ) {
	if ( ! vh_is_native_seo_active() || vh_detect_external_seo_plugin() ) {
		return $sep;
	}
	$defaults = vh_seo_defaults();
	return vh_seo_opt( 'vh_seo_title_sep', $defaults['vh_seo_title_sep'] );
}

add_filter( 'pre_get_document_title', 'vh_filter_document_title', 20 );
function vh_filter_document_title( $title ) {
	if ( ! vh_is_native_seo_active() || vh_detect_external_seo_plugin() ) {
		return $title;
	}

	$defaults  = vh_seo_defaults();
	$sep       = vh_seo_opt( 'vh_seo_title_sep', $defaults['vh_seo_title_sep'] );
	$site_name = get_bloginfo( 'name' ) ?: 'VivaHost';

	if ( is_front_page() ) {
		$front_id = (int) get_option( 'page_on_front' );
		if ( $front_id ) {
			$custom_front_title = get_post_meta( $front_id, '_vh_seo_title', true );
			if ( ! empty( $custom_front_title ) ) {
				return $custom_front_title;
			}
		}
		return vh_seo_opt( 'vh_seo_home_title', $defaults['vh_seo_home_title'] );
	}

	if ( is_singular() ) {
		$post_id      = get_queried_object_id();
		$custom_title = get_post_meta( $post_id, '_vh_seo_title', true );
		if ( ! empty( $custom_title ) ) {
			return $custom_title;
		}
		return single_post_title( '', false ) . " {$sep} {$site_name}";
	}

	if ( is_home() ) {
		return "Blog & Mercado de Temporada em Salvador {$sep} {$site_name}";
	}

	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false ) . " {$sep} {$site_name}";
	}

	return $title;
}

// ─── 3. CALCOLO METADATI CONTESTUALI (PER PAGINA CORRENTE) ───────────────────
/**
 * Costruisce il pacchetto completo di metadati SEO per la richiesta corrente.
 *
 * @return array
 */
function vh_get_current_seo_data() {
	$defaults  = vh_seo_defaults();
	$site_name = get_bloginfo( 'name' ) ?: 'VivaHost';
	$sep       = vh_seo_opt( 'vh_seo_title_sep', $defaults['vh_seo_title_sep'] );

	// Immagine OG di fallback
	$default_og = vh_seo_opt( 'vh_seo_og_image', '' );
	if ( empty( $default_og ) ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		if ( $logo_id ) {
			$default_og = wp_get_attachment_image_url( $logo_id, 'full' );
		}
	}
	if ( empty( $default_og ) ) {
		$default_og = VH_URL . '/assets/images/prop-ondina-vista.jpg';
	}

	$data = [
		'title'       => wp_get_document_title(),
		'description' => vh_seo_opt( 'vh_seo_home_desc', $defaults['vh_seo_home_desc'] ),
		'keywords'    => vh_seo_opt( 'vh_seo_home_keywords', $defaults['vh_seo_home_keywords'] ),
		'canonical'   => home_url( '/' ),
		'og_type'     => 'website',
		'og_image'    => $default_og,
		'robots'      => vh_seo_opt( 'vh_seo_robots_default', $defaults['vh_seo_robots_default'] ),
		'published'   => '',
		'modified'    => '',
		'section'     => '',
	];

	// Rispetta l'impostazione nativa di WordPress "Scoraggia i motori di ricerca"
	if ( '0' === (string) get_option( 'blog_public' ) ) {
		$data['robots'] = 'noindex, nofollow';
	}

	if ( is_front_page() ) {
		$data['canonical'] = home_url( '/' );
		$front_id          = (int) get_option( 'page_on_front' );
		if ( $front_id ) {
			$m_desc = get_post_meta( $front_id, '_vh_seo_desc', true );
			$m_can  = get_post_meta( $front_id, '_vh_seo_canonical', true );
			$m_og   = get_post_meta( $front_id, '_vh_seo_og_image', true );
			$m_noin = get_post_meta( $front_id, '_vh_seo_noindex', true );
			if ( $m_desc ) $data['description'] = $m_desc;
			if ( $m_can )  $data['canonical']   = $m_can;
			if ( $m_og )   $data['og_image']    = $m_og;
			if ( '1' === $m_noin ) $data['robots'] = 'noindex, nofollow';
		}
	} elseif ( is_singular() ) {
		$post_id      = get_queried_object_id();
		$post_obj     = get_post( $post_id );
		$m_desc       = get_post_meta( $post_id, '_vh_seo_desc', true );
		$m_kw         = get_post_meta( $post_id, '_vh_seo_keyword', true );
		$m_can        = get_post_meta( $post_id, '_vh_seo_canonical', true );
		$m_og         = get_post_meta( $post_id, '_vh_seo_og_image', true );
		$m_noin       = get_post_meta( $post_id, '_vh_seo_noindex', true );

		if ( ! empty( $m_desc ) ) {
			$data['description'] = $m_desc;
		} elseif ( $post_obj ) {
			$excerpt = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( $post_obj->post_content ), 28, '...' );
			if ( $excerpt ) {
				$data['description'] = wp_strip_all_tags( $excerpt );
			}
		}

		$data['keywords']  = ! empty( $m_kw ) ? $m_kw : '';
		$data['canonical'] = ! empty( $m_can ) ? $m_can : get_permalink( $post_id );
		$data['og_type']   = is_single() ? 'article' : 'website';

		if ( ! empty( $m_og ) ) {
			$data['og_image'] = $m_og;
		} elseif ( function_exists( 'vh_get_post_image' ) ) {
			$data['og_image'] = vh_get_post_image( $post_id, 'large' );
		}

		if ( is_single() && $post_obj ) {
			$data['published'] = get_the_date( 'c', $post_id );
			$data['modified']  = get_the_modified_date( 'c', $post_id );
			$cats              = get_the_category( $post_id );
			if ( ! empty( $cats ) && ! in_array( $cats[0]->name, [ 'Uncategorized', 'Sem categoria' ], true ) ) {
				$data['section'] = $cats[0]->name;
			} else {
				$data['section'] = 'Mercado Imobiliário & Temporada';
			}
		}

		if ( '1' === $m_noin ) {
			$data['robots'] = 'noindex, nofollow';
		}
	} elseif ( is_home() ) {
		$blog_page_id      = (int) get_option( 'page_for_posts' );
		$data['canonical'] = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
		$data['description'] = 'Guias práticos, legislação e análises de rentabilidade sobre aluguel por temporada e gestão Airbnb em Salvador, Bahia.';
	} elseif ( is_archive() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) && isset( $term->term_id ) ) {
			$term_link = get_term_link( $term );
			if ( ! is_wp_error( $term_link ) ) {
				$data['canonical'] = $term_link;
			}
			if ( ! empty( $term->description ) ) {
				$data['description'] = wp_strip_all_tags( $term->description );
			}
		}
	}

	return $data;
}

// ─── 4. OUTPUT CENTRALIZZATO NEL <HEAD> (META, OG, TWITTER, GEO, SCHEMA) ────
add_action( 'wp_head', 'vh_render_seo_head', 1 );
function vh_render_seo_head() {
	$defaults = vh_seo_defaults();
	$ext_seo  = vh_detect_external_seo_plugin();

	// 4.1 Search Console & Bing Verification (sempre attivi se compilati)
	$google_verify = trim( vh_seo_opt( 'vh_seo_google_verify', '' ) );
	$bing_verify   = trim( vh_seo_opt( 'vh_seo_bing_verify', '' ) );
	if ( $google_verify ) {
		// Se l'utente ha incollato l'intero tag <meta>, estraiamo solo il content
		if ( preg_match( '/content=["\']([^"\']+)["\']/', $google_verify, $m ) ) {
			$google_verify = $m[1];
		}
		echo '<meta name="google-site-verification" content="' . esc_attr( $google_verify ) . '">' . "\n";
	}
	if ( $bing_verify ) {
		if ( preg_match( '/content=["\']([^"\']+)["\']/', $bing_verify, $m ) ) {
			$bing_verify = $m[1];
		}
		echo '<meta name="msvalidate.01" content="' . esc_attr( $bing_verify ) . '">' . "\n";
	}

	// 4.2 Se non c'è un plugin SEO esterno e il motore nativo è attivo, stampa i tag SEO
	if ( vh_is_native_seo_active() && ! $ext_seo ) {
		$seo       = vh_get_current_seo_data();
		$site_name = get_bloginfo( 'name' ) ?: 'VivaHost';
		$lat       = vh_seo_opt( 'vh_seo_geo_lat', $defaults['vh_seo_geo_lat'] );
		$lng       = vh_seo_opt( 'vh_seo_geo_lng', $defaults['vh_seo_geo_lng'] );
		$region    = vh_seo_opt( 'vh_seo_geo_region', $defaults['vh_seo_geo_region'] );
		$placename = vh_seo_opt( 'vh_seo_geo_placename', $defaults['vh_seo_geo_placename'] );

		echo "\n<!-- VivaHost Native SEO Engine v" . esc_attr( VH_VER ) . " -->\n";
		echo '<meta name="description" content="' . esc_attr( $seo['description'] ) . '">' . "\n";
		if ( ! empty( $seo['keywords'] ) ) {
			echo '<meta name="keywords" content="' . esc_attr( $seo['keywords'] ) . '">' . "\n";
		}
		echo '<meta name="robots" content="' . esc_attr( $seo['robots'] ) . '">' . "\n";
		echo '<link rel="canonical" href="' . esc_url( $seo['canonical'] ) . '">' . "\n";

		// Hreflang per Brasile e fallback internazionale
		echo '<link rel="alternate" hreflang="pt-BR" href="' . esc_url( $seo['canonical'] ) . '">' . "\n";
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $seo['canonical'] ) . '">' . "\n";

		// Local SEO Geo Meta Tags (Salvador, BA)
		if ( $region && $placename ) {
			echo '<meta name="geo.region" content="' . esc_attr( $region ) . '">' . "\n";
			echo '<meta name="geo.placename" content="' . esc_attr( $placename ) . '">' . "\n";
		}
		if ( $lat && $lng ) {
			echo '<meta name="geo.position" content="' . esc_attr( "{$lat};{$lng}" ) . '">' . "\n";
			echo '<meta name="ICBM" content="' . esc_attr( "{$lat}, {$lng}" ) . '">' . "\n";
		}

		// Open Graph
		echo '<meta property="og:locale" content="pt_BR">' . "\n";
		echo '<meta property="og:type" content="' . esc_attr( $seo['og_type'] ) . '">' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( $seo['title'] ) . '">' . "\n";
		echo '<meta property="og:description" content="' . esc_attr( $seo['description'] ) . '">' . "\n";
		echo '<meta property="og:url" content="' . esc_url( $seo['canonical'] ) . '">' . "\n";
		echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
		if ( ! empty( $seo['og_image'] ) ) {
			echo '<meta property="og:image" content="' . esc_url( $seo['og_image'] ) . '">' . "\n";
		}
		if ( 'article' === $seo['og_type'] ) {
			if ( $seo['published'] ) echo '<meta property="article:published_time" content="' . esc_attr( $seo['published'] ) . '">' . "\n";
			if ( $seo['modified'] )  echo '<meta property="article:modified_time" content="' . esc_attr( $seo['modified'] ) . '">' . "\n";
			if ( $seo['section'] )   echo '<meta property="article:section" content="' . esc_attr( $seo['section'] ) . '">' . "\n";
		}

		// Twitter Card
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
		echo '<meta name="twitter:title" content="' . esc_attr( $seo['title'] ) . '">' . "\n";
		echo '<meta name="twitter:description" content="' . esc_attr( $seo['description'] ) . '">' . "\n";
		if ( ! empty( $seo['og_image'] ) ) {
			echo '<meta name="twitter:image" content="' . esc_url( $seo['og_image'] ) . '">' . "\n";
		}

		// Unified Schema.org JSON-LD @graph
		if ( '1' === vh_seo_opt( 'vh_seo_schema_enable', $defaults['vh_seo_schema_enable'] ) ) {
			$schema = vh_build_schema_graph( $seo );
			if ( ! empty( $schema ) ) {
				echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
			}
		}

		// Link a llms.txt per AI Search / GEO
		if ( '1' === vh_seo_opt( 'vh_seo_llms_txt_enable', $defaults['vh_seo_llms_txt_enable'] ) ) {
			echo '<link rel="alternate" type="text/markdown" title="LLMs Context (AI SEO)" href="' . esc_url( home_url( '/llms.txt' ) ) . '">' . "\n";
		}
		echo "<!-- /VivaHost Native SEO Engine -->\n\n";
	}

	// 4.3 Analytics & Tracking Scripts (GA4, GTM, Meta Pixel, Custom Head)
	$gtm_id     = trim( vh_seo_opt( 'vh_gen_gtm_id', '' ) );
	$ga4_id     = trim( vh_seo_opt( 'vh_gen_ga4_id', '' ) );
	$meta_pixel = trim( vh_seo_opt( 'vh_gen_meta_pixel', '' ) );
	$custom_hd  = vh_seo_opt( 'vh_gen_custom_head', '' );

	if ( $gtm_id ) {
		$gtm_clean = esc_js( $gtm_id );
		echo "<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$gtm_clean}');</script>\n<!-- End Google Tag Manager -->\n";
	}

	if ( $ga4_id ) {
		$ga4_clean = esc_attr( $ga4_id );
		echo "<!-- Google Analytics 4 (GA4) -->\n<script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga4_clean}\"></script>\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . esc_js( $ga4_id ) . "');</script>\n";
	}

	if ( $meta_pixel ) {
		$px_clean = esc_js( $meta_pixel );
		echo "<!-- Meta Pixel Code -->\n<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{$px_clean}');fbq('track','PageView');</script>\n<!-- End Meta Pixel Code -->\n";
	}

	if ( ! empty( $custom_hd ) ) {
		echo "\n<!-- VivaHost Custom Head Scripts -->\n" . $custom_hd . "\n";
	}
}

add_action( 'wp_footer', 'vh_render_custom_footer_scripts', 99 );
function vh_render_custom_footer_scripts() {
	$custom_ft = vh_seo_opt( 'vh_gen_custom_footer', '' );
	if ( ! empty( $custom_ft ) ) {
		echo "\n<!-- VivaHost Custom Footer Scripts -->\n" . $custom_ft . "\n";
	}
}

// ─── 5. COSTRUZIONE GRAFO SCHEMA.ORG JSON-LD (@graph) ───────────────────────
/**
 * Genera il grafo JSON-LD completo e interconnesso per la pagina corrente.
 *
 * @param array $seo Dati SEO correnti da vh_get_current_seo_data().
 * @return array
 */
function vh_build_schema_graph( $seo ) {
	$defaults   = vh_seo_defaults();
	$home_url   = home_url( '/' );
	$site_name  = get_bloginfo( 'name' ) ?: 'VivaHost';
	$razao      = vh_mod( 'footer_razao', 'Viva Host LTDA' );
	$cnpj       = vh_mod( 'footer_cnpj', '27.447.686/0001-10' );
	$address    = vh_mod( 'footer_address', 'Avenida Tancredo Neves, 002539' );
	$comp       = vh_mod( 'footer_complement', 'Edif CEO Salvador Shopping Torre Londres Sala 2609' );
	$bairro     = vh_mod( 'footer_bairro', 'Caminho das Árvores' );
	$cep        = vh_mod( 'footer_cep', '41820-021' );
	$email      = vh_mod( 'footer_email', 'marcia@meuvivahost.com.br' );
	$instagram  = vh_mod( 'footer_instagram', 'https://www.instagram.com/vivahostbahia/' );
	$airbnb     = vh_mod( 'footer_airbnb', 'https://www.airbnb.com.br/users/show/148412228' );
	$host_name  = vh_mod( 'host_name', 'Marcia Sales' );
	$comm_rate  = vh_seo_opt( 'vh_seo_price_range', vh_mod( 'commission_rate', '20%' ) );
	$biz_type   = vh_seo_opt( 'vh_seo_schema_type', $defaults['vh_seo_schema_type'] );
	$lat        = (float) vh_seo_opt( 'vh_seo_geo_lat', $defaults['vh_seo_geo_lat'] );
	$lng        = (float) vh_seo_opt( 'vh_seo_geo_lng', $defaults['vh_seo_geo_lng'] );

	$host_photo_id  = (int) vh_mod( 'host_photo', 0 );
	$host_photo_url = $host_photo_id ? wp_get_attachment_image_url( $host_photo_id, 'large' ) : VH_URL . '/assets/images/marcia-sales.webp';

	$wa_digits = preg_replace( '/\D/', '', vh_mod( 'wa_number', '5571999999999' ) );
	if ( strlen( $wa_digits ) === 10 || strlen( $wa_digits ) === 11 ) {
		$wa_digits = '55' . $wa_digits;
	}

	// Costruzione AreaServed (Salvador + quartieri chiave per Local SEO)
	$areas_raw  = vh_seo_opt( 'vh_seo_area_served', $defaults['vh_seo_area_served'] );
	$area_items = [
		[
			'@type'  => 'City',
			'name'   => 'Salvador',
			'sameAs' => 'https://pt.wikipedia.org/wiki/Salvador_(Bahia)',
		],
	];
	foreach ( array_filter( array_map( 'trim', explode( ',', $areas_raw ) ) ) as $neighborhood ) {
		$area_items[] = [
			'@type' => 'Place',
			'name'  => $neighborhood . ', Salvador - BA',
		];
	}

	$graph = [];

	// 1. Organization / RealEstateAgent
	$graph[] = [
		'@type'                     => [ $biz_type, 'LocalBusiness', 'Organization' ],
		'@id'                       => $home_url . '#organization',
		'name'                      => $site_name,
		'legalName'                 => $razao,
		'description'               => vh_seo_opt( 'vh_seo_home_desc', $defaults['vh_seo_home_desc'] ),
		'url'                       => $home_url,
		'email'                     => $email,
		'telephone'                 => '+' . $wa_digits,
		'priceRange'                => $comm_rate,
		'currenciesAccepted'        => 'BRL',
		'paymentAccepted'           => 'PIX, Transferência Bancária',
		'taxID'                     => $cnpj,
		'vatID'                     => $cnpj,
		'logo'                      => [
			'@type' => 'ImageObject',
			'@id'   => $home_url . '#logo',
			'url'   => VH_URL . '/assets/images/vivahost-logo.png',
		],
		'image'                     => VH_URL . '/assets/images/prop-ondina-vista.jpg',
		'geo'                       => [
			'@type'     => 'GeoCoordinates',
			'latitude'  => $lat,
			'longitude' => $lng,
		],
		'address'                   => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => $address . ( $comp ? ' - ' . $comp : '' ),
			'addressLocality' => 'Salvador',
			'addressRegion'   => 'BA',
			'addressCountry'  => 'BR',
			'postalCode'      => $cep,
		],
		'areaServed'                => $area_items,
		'openingHoursSpecification' => [
			[
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ],
				'opens'     => '08:00',
				'closes'    => '20:00',
			],
		],
		'sameAs'                    => array_values( array_filter( [ $instagram, $airbnb ] ) ),
		'founder'                   => [
			'@type'       => 'Person',
			'@id'         => $home_url . '#marcia',
			'name'        => $host_name,
			'jobTitle'    => 'Gestora Operacional VivaHost & Superhost Airbnb',
			'image'       => $host_photo_url,
			'worksFor'    => [ '@id' => $home_url . '#organization' ],
			'sameAs'      => array_values( array_filter( [ $airbnb, $instagram ] ) ),
		],
		'aggregateRating'           => [
			'@type'       => 'AggregateRating',
			'ratingValue' => str_replace( ',', '.', vh_mod( 'reviews_score', '4,88' ) ),
			'bestRating'  => '5',
			'worstRating' => '1',
			'ratingCount' => preg_replace( '/\D/', '', vh_mod( 'reviews_count', '469' ) ) ?: '469',
		],
		'review'                    => [
			[
				'@type'        => 'Review',
				'author'       => [ '@type' => 'Person', 'name' => 'Simone' ],
				'reviewRating' => [ '@type' => 'Rating', 'ratingValue' => '5' ],
				'reviewBody'   => 'O apartamento é bem localizado, super arrumado, seguro e a anfitriã é extraordinária. Me senti em casa, super indico.',
			],
			[
				'@type'        => 'Review',
				'author'       => [ '@type' => 'Person', 'name' => 'Ismael' ],
				'reviewRating' => [ '@type' => 'Rating', 'ratingValue' => '5' ],
				'reviewBody'   => 'Localização perfeita, próximo dos principais pontos turísticos da cidade. Apartamento aconchegante como se estivesse em casa. Márcia muito atenciosa e prestativa.',
			],
			[
				'@type'        => 'Review',
				'author'       => [ '@type' => 'Person', 'name' => 'Cleiton' ],
				'reviewRating' => [ '@type' => 'Rating', 'ratingValue' => '5' ],
				'reviewBody'   => 'Muito obrigado pela estadia! Foi tudo maravilhoso e nos sentimos muito bem acolhidos. A experiência foi incrível.',
			],
			[
				'@type'        => 'Review',
				'author'       => [ '@type' => 'Person', 'name' => 'Pedro Bessa' ],
				'reviewRating' => [ '@type' => 'Rating', 'ratingValue' => '5' ],
				'reviewBody'   => 'Excelente! Apartamento muito bem cuidado, cheiroso e com muitas coisas úteis dentro dele. Comunicação impecável!',
			],
		],
	];

	// 2. WebSite
	$graph[] = [
		'@type'       => 'WebSite',
		'@id'         => $home_url . '#website',
		'url'         => $home_url,
		'name'        => $site_name,
		'description' => vh_seo_opt( 'vh_seo_home_desc', $defaults['vh_seo_home_desc'] ),
		'publisher'   => [ '@id' => $home_url . '#organization' ],
		'inLanguage'  => 'pt-BR',
	];

	// 3. Contestuale: Front Page (WebPage + Service + FAQPage)
	if ( is_front_page() ) {
		$graph[] = [
			'@type'       => 'WebPage',
			'@id'         => $home_url . '#webpage',
			'url'         => $home_url,
			'name'        => $seo['title'],
			'description' => $seo['description'],
			'isPartOf'    => [ '@id' => $home_url . '#website' ],
			'about'       => [ '@id' => $home_url . '#organization' ],
			'inLanguage'  => 'pt-BR',
		];

		$graph[] = [
			'@type'       => 'Service',
			'@id'         => $home_url . '#service',
			'name'        => 'Gestão Completa de Aluguel por Temporada e Airbnb em Salvador',
			'serviceType' => 'Administração de Imóveis por Temporada',
			'provider'    => [ '@id' => $home_url . '#organization' ],
			'areaServed'  => [
				'@type' => 'City',
				'name'  => 'Salvador',
			],
			'description' => 'Gestão 360° de imóveis no Airbnb e Booking em Salvador: fotografia profissional, precificação dinâmica, check-in/out, limpeza hoteleira, manutenção e repasse mensal.',
			'offers'      => [
				'@type'         => 'Offer',
				'priceCurrency' => 'BRL',
				'description'   => 'Comissão a partir de ' . $comm_rate . ' apenas sobre as reservas confirmadas. Sem taxa de adesão ou fidelidade.',
			],
		];

		$faqs = [
			[
				'Quanto custa o serviço de gestão?',
				'Nossa comissão é a partir de ' . $comm_rate . ' sobre o valor do aluguel. Não cobramos taxa fixa, taxa de adesão nem multa por cancelamento. Você só paga quando seu imóvel aluga.',
			],
			[
				'Preciso ter um imóvel já mobiliado?',
				'Sim, o imóvel precisa estar mobiliado e equipado. Mas não se preocupe — ajudamos com orientações sobre o que é essencial para começar a receber hóspedes com sucesso.',
			],
			[
				'Quanto tempo leva para meu imóvel começar a gerar receita?',
				'Em média 7 dias após a vistoria inicial. Fazemos fotografia profissional, otimizamos o anúncio e ajustamos a precificação antes de publicar.',
			],
			[
				'Como recebo os pagamentos?',
				'O repasse é feito mensalmente, por transferência bancária, após o check-out dos hóspedes. Você recebe um relatório detalhado com todas as movimentações.',
			],
			[
				'Posso cancelar quando quiser?',
				'Sim, sem multa nem fidelidade. Você pode cancelar a qualquer momento, sem burocracia. Devolvemos o imóvel no mesmo estado que recebemos.',
			],
			[
				'O que acontece se meu imóvel ficar vago?',
				'Trabalhamos com precificação dinâmica para maximizar a ocupação. Mesmo assim, períodos de baixa são normais no turismo — ajustamos a estratégia conforme a sazonalidade.',
			],
		];

		$graph[] = [
			'@type'      => 'FAQPage',
			'@id'        => $home_url . '#faq',
			'isPartOf'   => [ '@id' => $home_url . '#webpage' ],
			'mainEntity' => array_map( function ( $f ) {
				return [
					'@type'          => 'Question',
					'name'           => $f[0],
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( $f[1] ),
					],
				];
			}, $faqs ),
		];
	}

	// 4. Contestuale: Single Post (WebPage + BreadcrumbList + BlogPosting)
	if ( is_single() ) {
		$post_id    = get_queried_object_id();
		$post_obj   = get_post( $post_id );
		$permalink  = $seo['canonical'];
		$word_count = $post_obj ? str_word_count( wp_strip_all_tags( $post_obj->post_content ) ) : 0;

		$graph[] = [
			'@type'       => 'WebPage',
			'@id'         => $permalink . '#webpage',
			'url'         => $permalink,
			'name'        => $seo['title'],
			'description' => $seo['description'],
			'isPartOf'    => [ '@id' => $home_url . '#website' ],
			'breadcrumb'  => [ '@id' => $permalink . '#breadcrumb' ],
			'inLanguage'  => 'pt-BR',
		];

		$graph[] = [
			'@type'           => 'BreadcrumbList',
			'@id'             => $permalink . '#breadcrumb',
			'itemListElement' => [
				[
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'Início',
					'item'     => $home_url,
				],
				[
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Blog & Dicas',
					'item'     => $home_url . '#blog',
				],
				[
					'@type'    => 'ListItem',
					'position' => 3,
					'name'     => get_the_title( $post_id ),
					'item'     => $permalink,
				],
			],
		];

		$graph[] = [
			'@type'            => 'BlogPosting',
			'@id'              => $permalink . '#article',
			'isPartOf'         => [ '@id' => $permalink . '#webpage' ],
			'mainEntityOfPage' => [ '@id' => $permalink . '#webpage' ],
			'headline'         => get_the_title( $post_id ),
			'description'      => $seo['description'],
			'image'            => [ $seo['og_image'] ],
			'datePublished'    => $seo['published'],
			'dateModified'     => $seo['modified'],
			'wordCount'        => $word_count,
			'articleSection'   => $seo['section'],
			'inLanguage'       => 'pt-BR',
			'author'           => [ '@id' => $home_url . '#marcia' ],
			'publisher'        => [ '@id' => $home_url . '#organization' ],
		];
	}

	return [
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	];
}

// ─── 6. METABOX SEO PER ARTICOLI E PAGINE ────────────────────────────────────
add_action( 'add_meta_boxes', 'vh_register_seo_metabox' );
function vh_register_seo_metabox() {
	$screens = [ 'post', 'page' ];
	foreach ( $screens as $screen ) {
		add_meta_box(
			'vh_seo_metabox',
			'🔍 VivaHost SEO — Otimização para Buscadores (Google & Redes Sociais)',
			'vh_render_seo_metabox',
			$screen,
			'normal',
			'high'
		);
	}
}

function vh_render_seo_metabox( $post ) {
	wp_nonce_field( 'vh_save_post_seo', 'vh_post_seo_nonce' );

	$seo_title     = get_post_meta( $post->ID, '_vh_seo_title', true );
	$seo_desc      = get_post_meta( $post->ID, '_vh_seo_desc', true );
	$seo_keyword   = get_post_meta( $post->ID, '_vh_seo_keyword', true );
	$seo_canonical = get_post_meta( $post->ID, '_vh_seo_canonical', true );
	$seo_og_image  = get_post_meta( $post->ID, '_vh_seo_og_image', true );
	$seo_noindex   = get_post_meta( $post->ID, '_vh_seo_noindex', true );

	$default_title = get_the_title( $post->ID ) . ' — ' . ( get_bloginfo( 'name' ) ?: 'VivaHost' );
	$default_desc  = has_excerpt( $post->ID ) ? wp_strip_all_tags( get_the_excerpt( $post->ID ) ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 25, '...' );
	?>
	<style>
		.vh-mb-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px; }
		.vh-mb-field { margin-bottom: 14px; }
		.vh-mb-field label { display: block; font-weight: 600; font-size: 12px; text-transform: uppercase; color: #334155; margin-bottom: 5px; letter-spacing: .03em; }
		.vh-mb-field input[type="text"], .vh-mb-field input[type="url"], .vh-mb-field textarea {
			width: 100%; padding: 8px 12px; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;
		}
		.vh-mb-field input:focus, .vh-mb-field textarea:focus { border-color: #FF5A5F; outline: none; box-shadow: 0 0 0 2px rgba(255,90,95,0.15); }
		.vh-mb-hint { font-size: 11.5px; color: #64748b; margin-top: 4px; display: flex; justify-content: space-between; }
		.vh-serp-preview { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px; font-family: Arial, sans-serif; }
		.vh-serp-url { font-size: 12px; color: #202124; margin-bottom: 2px; }
		.vh-serp-title { font-size: 18px; color: #1a0dab; font-weight: 400; line-height: 1.3; margin-bottom: 4px; text-decoration: none; }
		.vh-serp-desc { font-size: 13px; color: #4d5156; line-height: 1.5; }
		@media (max-width: 782px) { .vh-mb-grid { grid-template-columns: 1fr; } }
	</style>

	<div class="vh-serp-preview">
		<div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:6px">Prévia no Google (SERP)</div>
		<div class="vh-serp-url"><?php echo esc_html( get_permalink( $post->ID ) ?: home_url( '/' ) ); ?></div>
		<div class="vh-serp-title" id="vh-mb-preview-title"><?php echo esc_html( $seo_title ?: $default_title ); ?></div>
		<div class="vh-serp-desc" id="vh-mb-preview-desc"><?php echo esc_html( $seo_desc ?: $default_desc ); ?></div>
	</div>

	<div class="vh-mb-field">
		<label for="vh_seo_title">Título SEO (Meta Title)</label>
		<input type="text" id="vh_seo_title" name="vh_seo_title" value="<?php echo esc_attr( $seo_title ); ?>" placeholder="<?php echo esc_attr( $default_title ); ?>">
		<div class="vh-mb-hint">
			<span>Deixe em branco para usar automaticamente o título do post + VivaHost.</span>
			<span>Ideal: 50–60 caracteres</span>
		</div>
	</div>

	<div class="vh-mb-field">
		<label for="vh_seo_desc">Meta Descrição (Meta Description)</label>
		<textarea id="vh_seo_desc" name="vh_seo_desc" rows="3" placeholder="<?php echo esc_attr( $default_desc ); ?>"><?php echo esc_textarea( $seo_desc ); ?></textarea>
		<div class="vh-mb-hint">
			<span>Resumo persuasivo exibido nos resultados do Google e ao compartilhar no WhatsApp.</span>
			<span>Ideal: 140–160 caracteres</span>
		</div>
	</div>

	<div class="vh-mb-grid">
		<div class="vh-mb-field">
			<label for="vh_seo_keyword">Palavra-chave Principal (Focus Keyword)</label>
			<input type="text" id="vh_seo_keyword" name="vh_seo_keyword" value="<?php echo esc_attr( $seo_keyword ); ?>" placeholder="ex: gestão airbnb salvador">
		</div>
		<div class="vh-mb-field">
			<label for="vh_seo_canonical">URL Canonical Personalizado (Opcional)</label>
			<input type="url" id="vh_seo_canonical" name="vh_seo_canonical" value="<?php echo esc_attr( $seo_canonical ); ?>" placeholder="<?php echo esc_attr( get_permalink( $post->ID ) ); ?>">
		</div>
	</div>

	<div class="vh-mb-grid">
		<div class="vh-mb-field">
			<label for="vh_seo_og_image">Imagem Open Graph / Social (URL Opcional)</label>
			<input type="url" id="vh_seo_og_image" name="vh_seo_og_image" value="<?php echo esc_attr( $seo_og_image ); ?>" placeholder="Usa a imagem destacada por padrão">
		</div>
		<div class="vh-mb-field" style="display:flex;align-items:center;padding-top:18px">
			<label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none;font-size:13px">
				<input type="checkbox" name="vh_seo_noindex" value="1" <?php checked( $seo_noindex, '1' ); ?>>
				<span>Ocultar esta página dos motores de busca (<code>noindex, nofollow</code>)</span>
			</label>
		</div>
	</div>
	<script>
	(function(){
		var tInput = document.getElementById('vh_seo_title');
		var dInput = document.getElementById('vh_seo_desc');
		var tPrev  = document.getElementById('vh-mb-preview-title');
		var dPrev  = document.getElementById('vh-mb-preview-desc');
		if(tInput && tPrev){
			tInput.addEventListener('input', function(){ tPrev.textContent = this.value || tInput.getAttribute('placeholder'); });
		}
		if(dInput && dPrev){
			dInput.addEventListener('input', function(){ dPrev.textContent = this.value || dInput.getAttribute('placeholder'); });
		}
	})();
	</script>
	<?php
}

add_action( 'save_post', 'vh_save_seo_metabox_data' );
function vh_save_seo_metabox_data( $post_id ) {
	if ( ! isset( $_POST['vh_post_seo_nonce'] ) || ! wp_verify_nonce( $_POST['vh_post_seo_nonce'], 'vh_save_post_seo' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$text_fields = [
		'vh_seo_title'   => '_vh_seo_title',
		'vh_seo_keyword' => '_vh_seo_keyword',
	];
	foreach ( $text_fields as $post_key => $meta_key ) {
		if ( isset( $_POST[ $post_key ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
		}
	}

	if ( isset( $_POST['vh_seo_desc'] ) ) {
		update_post_meta( $post_id, '_vh_seo_desc', sanitize_textarea_field( $_POST['vh_seo_desc'] ) );
	}
	if ( isset( $_POST['vh_seo_canonical'] ) ) {
		update_post_meta( $post_id, '_vh_seo_canonical', esc_url_raw( $_POST['vh_seo_canonical'] ) );
	}
	if ( isset( $_POST['vh_seo_og_image'] ) ) {
		update_post_meta( $post_id, '_vh_seo_og_image', esc_url_raw( $_POST['vh_seo_og_image'] ) );
	}

	$noindex = isset( $_POST['vh_seo_noindex'] ) && '1' === $_POST['vh_seo_noindex'] ? '1' : '';
	update_post_meta( $post_id, '_vh_seo_noindex', $noindex );
}

// ─── 7. OTTIMIZZAZIONE SITEMAP XML NATIVA WORDPRESS (wp-sitemap.xml) ────────
add_filter( 'wp_sitemaps_add_provider', 'vh_optimize_wp_sitemap_providers', 10, 2 );
function vh_optimize_wp_sitemap_providers( $provider, $name ) {
	$defaults = vh_seo_defaults();
	if ( '1' === vh_seo_opt( 'vh_seo_sitemap_enhance', $defaults['vh_seo_sitemap_enhance'] ) ) {
		// Rimuove l'archivio autori dalla sitemap per evitare enumerazione utenti e thin content
		if ( 'users' === $name ) {
			return false;
		}
	}
	return $provider;
}

add_filter( 'wp_sitemaps_posts_query_args', 'vh_exclude_noindex_from_sitemap', 10, 2 );
function vh_exclude_noindex_from_sitemap( $args, $post_type ) {
	$defaults = vh_seo_defaults();
	if ( '1' === vh_seo_opt( 'vh_seo_sitemap_enhance', $defaults['vh_seo_sitemap_enhance'] ) ) {
		$args['meta_query'] = [
			'relation' => 'OR',
			[
				'key'     => '_vh_seo_noindex',
				'compare' => 'NOT EXISTS',
			],
			[
				'key'     => '_vh_seo_noindex',
				'value'   => '1',
				'compare' => '!=',
			],
		];
	}
	return $args;
}

// ─── 8. OTTIMIZZAZIONE ROBOTS.TXT VIRTUALE WORDPRESS ────────────────────────
add_filter( 'robots_txt', 'vh_custom_robots_txt', 20, 2 );
function vh_custom_robots_txt( $output, $public ) {
	if ( '0' === (string) $public ) {
		return $output;
	}
	$defaults = vh_seo_defaults();
	if ( '1' !== vh_seo_opt( 'vh_seo_robots_enhance', $defaults['vh_seo_robots_enhance'] ) ) {
		return $output;
	}

	$home = trailingslashit( home_url() );
	$rules  = "User-agent: *\n";
	$rules .= "Disallow: /wp-admin/\n";
	$rules .= "Allow: /wp-admin/admin-ajax.php\n";
	$rules .= "Allow: /wp-content/uploads/\n";
	$rules .= "Disallow: /?s=\n";
	$rules .= "Disallow: /search/\n\n";
	$rules .= "# AI Search & Answer Engines (GEO Optimization)\n";
	$rules .= "User-agent: OAI-SearchBot\nAllow: /\n\n";
	$rules .= "User-agent: ChatGPT-User\nAllow: /\n\n";
	$rules .= "User-agent: PerplexityBot\nAllow: /\n\n";
	$rules .= "User-agent: ClaudeBot\nAllow: /\n\n";
	$rules .= "User-agent: Google-Extended\nAllow: /\n\n";
	$rules .= "Sitemap: {$home}wp-sitemap.xml\n";

	return $rules;
}

// ─── 9. ENDPOINT /llms.txt PER AI SEO / GEO (ChatGPT, Perplexity, Claude) ───
add_action( 'init', 'vh_handle_llms_txt_request', 1 );
function vh_handle_llms_txt_request() {
	$defaults = vh_seo_defaults();
	if ( '1' !== vh_seo_opt( 'vh_seo_llms_txt_enable', $defaults['vh_seo_llms_txt_enable'] ) ) {
		return;
	}

	$req_uri = isset( $_SERVER['REQUEST_URI'] ) ? strtok( $_SERVER['REQUEST_URI'], '?' ) : '';
	$is_llms = ( '/llms.txt' === $req_uri || substr( $req_uri, -9 ) === '/llms.txt' || isset( $_GET['llms_txt'] ) );
	if ( ! $is_llms ) {
		return;
	}

	header( 'Content-Type: text/markdown; charset=UTF-8' );
	header( 'X-Robots-Tag: index, follow' );

	$site_name = get_bloginfo( 'name' ) ?: 'VivaHost';
	$home_url  = home_url( '/' );
	$razao     = vh_mod( 'footer_razao', 'Viva Host LTDA' );
	$cnpj      = vh_mod( 'footer_cnpj', '27.447.686/0001-10' );
	$address   = vh_mod( 'footer_address', 'Avenida Tancredo Neves, 002539' );
	$bairro    = vh_mod( 'footer_bairro', 'Caminho das Árvores' );
	$cep       = vh_mod( 'footer_cep', '41820-021' );
	$email     = vh_mod( 'footer_email', 'marcia@meuvivahost.com.br' );
	$host_name = vh_mod( 'host_name', 'Marcia Sales' );
	$comm_rate = vh_mod( 'commission_rate', '20%' );
	$score     = vh_mod( 'reviews_score', '4,88' );
	$rev_count = vh_mod( 'reviews_count', '469' );
	$areas     = vh_seo_opt( 'vh_seo_area_served', $defaults['vh_seo_area_served'] );

	echo "# {$site_name} — Gestão Profissional de Imóveis por Temporada e Airbnb em Salvador, Bahia\n\n";
	echo "> A {$site_name} ({$razao}, CNPJ {$cnpj}) é especialista na administração completa (360°) de apartamentos, studios e casas para aluguel por temporada no Airbnb e Booking em Salvador, Bahia, Brasil.\n\n";
	echo "## Principais Indicadores e Credenciais\n";
	echo "- **Gestora Responsável:** {$host_name} (Superhost no Airbnb desde 2017 — 9 anos de experiência)\n";
	echo "- **Avaliação Média dos Hóspedes:** {$score} / 5.0 estrelas ({$rev_count}+ avaliações verificadas)\n";
	echo "- **Taxa Média de Ocupação:** 85% ao ano em Salvador\n";
	echo "- **Aumento Médio de Receita:** +30% de faturamento líquido frente ao aluguel tradicional\n";
	echo "- **Modelo Comercial:** Comissão a partir de {$comm_rate} sobre o valor das reservas confirmadas. Sem taxa fixa, sem taxa de adesão e sem multa de fidelidade.\n\n";
	echo "## Serviços Inclusos na Gestão 360°\n";
	echo "1. **Diagnóstico e Consultoria de Imóvel:** Vistoria técnica em Salvador e projeção de rentabilidade gratuita.\n";
	echo "2. **Fotografia Profissional e Anúncio Otimizado:** Produção visual e copywriting para destaque algorítmico no Airbnb.\n";
	echo "3. **Precificação Dinâmica Diária:** Ajuste de tarifas conforme sazonalidade de Salvador (Verão, Carnaval, São João, Congressos e Turismo Corporativo).\n";
	echo "4. **Atendimento e Suporte 24h aos Hóspedes:** Triagem de perfil, check-in/check-out e comunicação imediata.\n";
	echo "5. **Governança Padrão Hoteleiro e Manutenção:** Equipe própria de limpeza, lavanderia de enxoval, reposição de insumos e reparos preventivos (elétrica, hidráulica, ar-condicionado).\n";
	echo "6. **Gestão Financeira e Repasse Mensal:** Extrato transparente e transferência bancária pontual ao proprietário.\n\n";
	echo "## Bairros Atendidos em Salvador (Bahia)\n";
	echo "{$areas}\n\n";
	echo "## Guias e Artigos Publicados (Blog VivaHost)\n";

	$posts = get_posts( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 20,
	] );
	if ( ! empty( $posts ) ) {
		foreach ( $posts as $p ) {
			$url     = get_permalink( $p->ID );
			$excerpt = has_excerpt( $p->ID ) ? wp_strip_all_tags( get_the_excerpt( $p->ID ) ) : wp_trim_words( wp_strip_all_tags( $p->post_content ), 25, '...' );
			echo "- [{$p->post_title}]({$url}): {$excerpt}\n";
		}
	}
	echo "\n## Contato Oficial e Endereço\n";
	echo "- **Website:** {$home_url}\n";
	echo "- **E-mail:** {$email}\n";
	echo "- **Endereço:** {$address}, {$bairro}, Salvador - BA, CEP {$cep}, Brasil\n";
	exit;
}

// ─── 10. SALVATAGGIO IMPOSTAZIONI ADMIN SEO & OPZIONI GENERALI ──────────────
add_action( 'admin_init', 'vh_seo_settings_save' );
function vh_seo_settings_save() {
	if ( ! isset( $_POST['vh_seo_settings_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['vh_seo_settings_nonce'], 'vh_seo_settings_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Se l'admin ha cliccato "Limpar Histórico de Bloqueios"
	if ( ! empty( $_POST['vh_clear_sec_log'] ) ) {
		update_option( 'vh_sec_blocked_log', [], false );
		update_option( 'vh_sec_blocked_total', 0, false );
		wp_safe_redirect( admin_url( 'admin.php?page=vivahost-seo&tab=security&saved=1' ) );
		exit;
	}

	// Checkbox booleani (SEO, Generali & Sicurezza)
	$checkboxes = [
		'vh_seo_enable_native',
		'vh_seo_schema_enable',
		'vh_gen_clean_wp_head',
		'vh_gen_cookie_bar',
		'vh_seo_sitemap_enhance',
		'vh_seo_robots_enhance',
		'vh_seo_llms_txt_enable',
		'vh_sec_form_shield',
		'vh_sec_headers_enable',
		'vh_sec_disable_xmlrpc',
		'vh_sec_block_user_enum',
		'vh_sec_hide_login_errors',
	];
	foreach ( $checkboxes as $chk ) {
		update_option( $chk, isset( $_POST[ $chk ] ) && '1' === $_POST[ $chk ] ? '1' : '0' );
	}

	// Campi testo singoli
	$text_fields = [
		'vh_seo_title_sep',
		'vh_seo_home_title',
		'vh_seo_home_keywords',
		'vh_seo_robots_default',
		'vh_seo_schema_type',
		'vh_seo_geo_lat',
		'vh_seo_geo_lng',
		'vh_seo_geo_region',
		'vh_seo_geo_placename',
		'vh_seo_price_range',
		'vh_gen_ga4_id',
		'vh_gen_gtm_id',
		'vh_gen_meta_pixel',
		'vh_seo_google_verify',
		'vh_seo_bing_verify',
		'vh_sec_turnstile_site',
		'vh_sec_turnstile_secret',
	];
	foreach ( $text_fields as $tf ) {
		if ( isset( $_POST[ $tf ] ) ) {
			update_option( $tf, sanitize_text_field( wp_unslash( $_POST[ $tf ] ) ) );
		}
	}

	// Campi numerici (Rate Limiting)
	if ( isset( $_POST['vh_sec_rate_limit_max'] ) ) {
		update_option( 'vh_sec_rate_limit_max', max( 1, min( 50, absint( $_POST['vh_sec_rate_limit_max'] ) ) ) );
	}
	if ( isset( $_POST['vh_sec_rate_limit_window'] ) ) {
		update_option( 'vh_sec_rate_limit_window', max( 1, min( 120, absint( $_POST['vh_sec_rate_limit_window'] ) ) ) );
	}

	// Campi textarea
	$textarea_fields = [
		'vh_seo_home_desc',
		'vh_seo_area_served',
	];
	foreach ( $textarea_fields as $ta ) {
		if ( isset( $_POST[ $ta ] ) ) {
			update_option( $ta, sanitize_textarea_field( wp_unslash( $_POST[ $ta ] ) ) );
		}
	}

	// Campi URL
	if ( isset( $_POST['vh_seo_og_image'] ) ) {
		update_option( 'vh_seo_og_image', esc_url_raw( wp_unslash( $_POST['vh_seo_og_image'] ) ) );
	}

	// Script custom (solo per amministratori con unfiltered_html)
	if ( current_user_can( 'unfiltered_html' ) ) {
		if ( isset( $_POST['vh_gen_custom_head'] ) ) {
			update_option( 'vh_gen_custom_head', wp_unslash( $_POST['vh_gen_custom_head'] ) );
		}
		if ( isset( $_POST['vh_gen_custom_footer'] ) ) {
			update_option( 'vh_gen_custom_footer', wp_unslash( $_POST['vh_gen_custom_footer'] ) );
		}
	}

	$active_tab = isset( $_POST['vh_active_tab'] ) ? sanitize_key( $_POST['vh_active_tab'] ) : 'seo';
	wp_safe_redirect( admin_url( 'admin.php?page=vivahost-seo&tab=' . $active_tab . '&saved=1' ) );
	exit;
}
