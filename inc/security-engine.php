<?php
/**
 * VivaHost — Motore di Sicurezza Template, Hardening WordPress & Scudo Anti-Bot Form
 *
 * Protegge il tema e l'infrastruttura WordPress contro:
 * 1. Bot automatici, spam e flood sul form di contatto (Honeypot doppio, Timestamp firmato HMAC-SHA256,
 *    Token JS Proof-of-Interaction, Rate Limiting per IP via Transients, WAF anti-injection/spam e
 *    supporto opzionale Cloudflare Turnstile).
 * 2. Attacchi brute-force e amplificazione DDoS tramite XML-RPC e X-Pingback.
 * 3. Enumerazione degli utenti admin via scansioni /?author=N e endpoint REST /wp-json/wp/v2/users.
 * 4. Clickjacking, MIME-sniffing e XSS tramite HTTP Security Headers nativi.
 * 5. Fingerprinting della versione WordPress e leak di username negli errori di login.
 *
 * @package VivahostChild
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valori di default per le opzioni di sicurezza VivaHost.
 *
 * @return array
 */
function vh_sec_defaults() {
	return [
		'vh_sec_form_shield'       => '1',
		'vh_sec_rate_limit_max'    => 3,
		'vh_sec_rate_limit_window' => 15, // minuti
		'vh_sec_headers_enable'    => '1',
		'vh_sec_disable_xmlrpc'    => '1',
		'vh_sec_block_user_enum'   => '1',
		'vh_sec_hide_login_errors' => '1',
		'vh_sec_turnstile_site'    => '',
		'vh_sec_turnstile_secret'  => '',
	];
}

/**
 * Legge un'opzione di sicurezza con fallback al default stabile.
 *
 * @param string $key     Chiave opzione.
 * @param mixed  $default Default opzionale.
 * @return mixed
 */
function vh_sec_opt( $key, $default = null ) {
	$defaults = vh_sec_defaults();
	if ( null === $default && isset( $defaults[ $key ] ) ) {
		$default = $defaults[ $key ];
	}
	$val = get_option( $key, null );
	if ( null === $val || '' === $val ) {
		return $default;
	}
	return $val;
}

// ─── 1. CRITTOGRAFIA TOKEN ANTI-BOT & CHALLENGE FORM ────────────────────────

/**
 * Genera la firma HMAC-SHA256 per il timestamp del form.
 * Impedisce ai bot di falsificare il tempo di caricamento della pagina.
 *
 * @param int|string $timestamp Timestamp Unix.
 * @return string Firma esadecimale.
 */
function vh_sign_form_timestamp( $timestamp ) {
	return hash_hmac( 'sha256', (string) $timestamp, wp_salt( 'auth' ) . '|vh_form_ts' );
}

/**
 * Genera il token di challenge per la prova di interazione JavaScript.
 *
 * @param int|string $timestamp Timestamp Unix.
 * @return string Challenge string.
 */
function vh_get_form_js_challenge( $timestamp ) {
	return substr( hash_hmac( 'sha256', 'vh_js_' . (string) $timestamp, wp_salt( 'secure_auth' ) ), 0, 16 );
}

/**
 * Calcola la risposta attesa dal client JS per la challenge.
 *
 * @param string $challenge Challenge di 16 caratteri.
 * @return string Token base64 atteso.
 */
function vh_expected_js_token( $challenge ) {
	return base64_encode( strrev( (string) $challenge ) . ':vh-human' );
}

/**
 * Ottiene l'indirizzo IP reale del client (supportando Cloudflare / reverse proxy).
 *
 * @return string Indirizzo IP sanitizzato.
 */
function vh_get_client_ip() {
	$headers = [
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_REAL_IP',
		'HTTP_X_FORWARDED_FOR',
		'REMOTE_ADDR',
	];
	foreach ( $headers as $header ) {
		if ( ! empty( $_SERVER[ $header ] ) ) {
			$ip_list = explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) ) );
			$ip      = trim( $ip_list[0] );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
	}
	return '0.0.0.0';
}

/**
 * Maschera l'IP per il salvataggio conforme LGPD nel registro eventi di sicurezza.
 *
 * @param string $ip Indirizzo IP.
 * @return string IP parzialmente mascherato (es. 192.168.1.***).
 */
function vh_mask_ip( $ip ) {
	if ( strpos( $ip, ':' ) !== false ) {
		$parts = explode( ':', $ip );
		return implode( ':', array_slice( $parts, 0, 3 ) ) . ':***';
	}
	$parts = explode( '.', $ip );
	if ( count( $parts ) === 4 ) {
		return "{$parts[0]}.{$parts[1]}.{$parts[2]}.***";
	}
	return '***';
}

/**
 * Registra un tentativo di attacco o bot bloccato nelle statistiche di sicurezza.
 *
 * @param string $reason  Motivo del blocco (es. 'Honeypot', 'Rate Limit', 'WAF Injection').
 * @param string $details Dettaglio sintetico.
 */
function vh_log_security_block( $reason, $details = '' ) {
	$total = (int) get_option( 'vh_sec_blocked_total', 0 );
	update_option( 'vh_sec_blocked_total', $total + 1, false );

	$log = get_option( 'vh_sec_blocked_log', [] );
	if ( ! is_array( $log ) ) {
		$log = [];
	}

	array_unshift( $log, [
		'time'    => current_time( 'd/m/Y H:i:s' ),
		'ip'      => vh_mask_ip( vh_get_client_ip() ),
		'reason'  => sanitize_text_field( $reason ),
		'details' => sanitize_text_field( wp_strip_all_tags( $details ) ),
	] );

	// Conserva solo gli ultimi 20 eventi per non appesantire il DB
	$log = array_slice( $log, 0, 20 );
	update_option( 'vh_sec_blocked_log', $log, false );
}

// ─── 2. VERIFICA COMPLETA SCUDO ANTI-BOT E WAF SUL FORM CONTATTI ────────────

/**
 * Esegue tutti i controlli di sicurezza sul POST del form contatti prima dell'invio email.
 * Restituisce true se la richiesta è legittima, oppure invia direttamente risposta JSON e termina.
 */
function vh_verify_form_security_shield() {
	// 2.1 Verifica Nonce CSRF WordPress
	if ( ! check_ajax_referer( 'vh_form_nonce', 'nonce', false ) ) {
		vh_log_security_block( 'CSRF / Nonce Inválido', 'Tentativa de envio sem token de sessão válido.' );
		wp_send_json_error( [ 'message' => 'Sessão expirada por segurança. Recarregue a página e tente novamente.' ], 403 );
	}

	// 2.2 Verifica Origin / Referer (blocca richieste POST esterne cross-origin)
	$home_host = wp_parse_url( home_url(), PHP_URL_HOST );
	$origin    = ! empty( $_SERVER['HTTP_ORIGIN'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ), PHP_URL_HOST ) : '';
	$referer   = ! empty( $_SERVER['HTTP_REFERER'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), PHP_URL_HOST ) : '';

	if ( $home_host && $origin && strtolower( $origin ) !== strtolower( $home_host ) ) {
		vh_log_security_block( 'Origem Não Autorizada', "Origin: {$origin}" );
		wp_send_json_error( [ 'message' => 'Origem da requisição não autorizada.' ], 403 );
	}
	if ( $home_host && ! $origin && $referer && strtolower( $referer ) !== strtolower( $home_host ) ) {
		vh_log_security_block( 'Referer Inválido', "Referer: {$referer}" );
		wp_send_json_error( [ 'message' => 'Requisição externa bloqueada.' ], 403 );
	}

	// 2.3 Doppio Honeypot invisibile (website, vh_hp_check, email_confirm)
	if ( ! empty( $_POST['vh_hp_check'] ) || ! empty( $_POST['website'] ) || ! empty( $_POST['vh_email_confirm'] ) ) {
		vh_log_security_block( 'Bot Honeypot', 'Preenchimento automático de campo oculto anti-bot.' );
		// Silent drop: finge successo così il bot non riprova cambiando strategia
		wp_send_json_success( [ 'mode' => 'email', 'wa_url' => '' ] );
	}

	// Se lo scudo avanzato è attivo (default: 1)
	if ( '1' === (string) vh_sec_opt( 'vh_sec_form_shield', '1' ) ) {

		// 2.4 Verifica Timestamp Crittografico HMAC (blocca invii istantanei < 2s e replay > 12h)
		$ts  = isset( $_POST['_vh_ts'] ) ? (int) $_POST['_vh_ts'] : 0;
		$sig = isset( $_POST['_vh_sig'] ) ? sanitize_text_field( wp_unslash( $_POST['_vh_sig'] ) ) : '';

		if ( ! $ts || ! $sig || ! hash_equals( vh_sign_form_timestamp( $ts ), $sig ) ) {
			vh_log_security_block( 'Assinatura HMAC Inválida', 'Token temporal ausente ou adulterado.' );
			wp_send_json_error( [ 'message' => 'Validação de segurança falhou. Recarregue a página.' ], 403 );
		}

		$elapsed = time() - $ts;
		if ( $elapsed < 2 ) {
			vh_log_security_block( 'Bot Speed Trap (<2s)', "Formulário enviado em {$elapsed}s após carregar." );
			wp_send_json_success( [ 'mode' => 'email', 'wa_url' => '' ] ); // silent drop
		}
		if ( $elapsed > 12 * HOUR_IN_SECONDS ) {
			vh_log_security_block( 'Token Expirado (Replay)', 'Formulário aberto há mais de 12 horas.' );
			wp_send_json_error( [ 'message' => 'O formulário ficou aberto por muito tempo. Atualize a página para enviar.' ], 403 );
		}

		// 2.5 Verifica JS Proof-of-Human-Interaction Token
		$js_token  = isset( $_POST['_vh_js_token'] ) ? sanitize_text_field( wp_unslash( $_POST['_vh_js_token'] ) ) : '';
		$challenge = vh_get_form_js_challenge( $ts );
		$expected  = vh_expected_js_token( $challenge );

		if ( empty( $js_token ) || ! hash_equals( $expected, $js_token ) ) {
			vh_log_security_block( 'Bot Headless (Sem JS)', 'Ausência de interação humana real no navegador.' );
			wp_send_json_error( [ 'message' => 'Interação de segurança não validada. Certifique-se de que o JavaScript está ativo.' ], 403 );
		}

		// 2.6 Rate Limiting per IP tramite WordPress Transients
		$max_req    = max( 1, (int) vh_sec_opt( 'vh_sec_rate_limit_max', 3 ) );
		$window_min = max( 1, (int) vh_sec_opt( 'vh_sec_rate_limit_window', 15 ) );
		$client_ip  = vh_get_client_ip();
		$rl_key     = 'vh_rl_' . substr( hash( 'sha256', $client_ip . wp_salt( 'auth' ) ), 0, 20 );
		$attempts   = (int) get_transient( $rl_key );

		if ( $attempts >= $max_req ) {
			vh_log_security_block( 'Rate Limit Excedido', "Mais de {$max_req} envios em {$window_min} min." );
			wp_send_json_error( [
				'message' => "Muitas solicitações seguidas. Por segurança, aguarde {$window_min} minutos ou fale conosco diretamente pelo WhatsApp.",
			], 429 );
		}
		set_transient( $rl_key, $attempts + 1, $window_min * MINUTE_IN_SECONDS );
	}

	// 2.7 Verifica Cloudflare Turnstile (se configurato dall'admin)
	$ts_site   = trim( (string) vh_sec_opt( 'vh_sec_turnstile_site', '' ) );
	$ts_secret = trim( (string) vh_sec_opt( 'vh_sec_turnstile_secret', '' ) );
	if ( $ts_site && $ts_secret ) {
		$cf_resp = isset( $_POST['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( $_POST['cf-turnstile-response'] ) ) : '';
		if ( empty( $cf_resp ) ) {
			vh_log_security_block( 'Turnstile Ausente', 'Captcha Cloudflare não resolvido.' );
			wp_send_json_error( [ 'message' => 'Por favor, confirme a verificação de segurança anti-robô.' ], 403 );
		}
		$verify = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
			'timeout' => 8,
			'body'    => [
				'secret'   => $ts_secret,
				'response' => $cf_resp,
				'remoteip' => vh_get_client_ip(),
			],
		] );
		if ( ! is_wp_error( $verify ) ) {
			$body = json_decode( wp_remote_retrieve_body( $verify ), true );
			if ( empty( $body['success'] ) ) {
				vh_log_security_block( 'Turnstile Reprovado', 'Falha na validação Cloudflare Turnstile.' );
				wp_send_json_error( [ 'message' => 'Verificação anti-robô inválida. Tente novamente.' ], 403 );
			}
		}
	}
}

/**
 * Sanitizza e valida rigorosamente i campi del form contatti contro XSS, SQLi,
 * Email Header Injection e spam di link.
 *
 * @param array $raw Array $_POST.
 * @return array|WP_Error Array di campi puliti o WP_Error se rilevato payload malevolo.
 */
function vh_sanitize_and_validate_form_payload( $raw ) {
	// Funzione interna per rimuovere CR/LF (previene Email Header Injection) e tag HTML
	$clean_line = function ( $str, $max_len = 120 ) {
		$s = wp_strip_all_tags( wp_unslash( (string) $str ) );
		$s = str_replace( [ "\r", "\n", "%0a", "%0d", "%0A", "%0D" ], ' ', $s );
		$s = preg_replace( '/\s+/', ' ', trim( $s ) );
		return mb_substr( $s, 0, $max_len );
	};

	$nome    = $clean_line( $raw['nome'] ?? '', 80 );
	$cidade  = $clean_line( $raw['cidade'] ?? '', 80 );
	$tipo    = $clean_line( $raw['tipo'] ?? '', 40 );
	$quartos = $clean_line( $raw['quartos'] ?? '', 30 );
	$phone   = $clean_line( $raw['whatsapp'] ?? '', 30 );

	if ( ! $nome || ! $cidade || ! $phone ) {
		return new WP_Error( 'missing_fields', 'Por favor, preencha todos os campos obrigatórios (Nome, Cidade e WhatsApp).' );
	}

	// Controllo lunghezza minima nome e città
	if ( mb_strlen( $nome ) < 2 || mb_strlen( $cidade ) < 2 ) {
		return new WP_Error( 'invalid_length', 'Por favor, informe um nome e bairro válidos.' );
	}

	// WAF: Rilevamento URL, BBCode, SQL Injection, XSS o Email Header Injection nei campi di testo
	$combined = $nome . ' ' . $cidade . ' ' . $phone;
	$malicious_patterns = '/(https?:\/\/|www\.|\[url=|<script|javascript:|onload=|onerror=|union\s+select|insert\s+into|drop\s+table|base64_|bcc:|cc:|content-type:)/i';
	if ( preg_match( $malicious_patterns, $combined ) ) {
		vh_log_security_block( 'WAF Payload Bloqueado', "Padrão suspeito detectado em Nome/Cidade: {$nome}" );
		return new WP_Error( 'waf_blocked', 'Conteúdo não permitido detectado nos campos. Remova links ou caracteres especiais.' );
	}

	// Whitelist rigorosa per Tipo de Imóvel e Quartos
	$allowed_tipos   = [ '', 'Apartamento', 'Casa', 'Studio / Kitnet', 'Cobertura', 'Outro' ];
	$allowed_quartos = [ '', '1 quarto', '2 quartos', '3 quartos', '4 ou mais' ];

	if ( ! in_array( $tipo, $allowed_tipos, true ) ) {
		$tipo = 'Imóvel';
	}
	if ( ! in_array( $quartos, $allowed_quartos, true ) ) {
		$quartos = 'Não informado';
	}

	// Validazione numero WhatsApp (10–15 cifre, non tutte uguali es. 99999999999)
	$digits = preg_replace( '/\D/', '', $phone );
	if ( strlen( $digits ) < 10 || strlen( $digits ) > 15 || preg_match( '/^(\d)\1+$/', $digits ) ) {
		return new WP_Error( 'invalid_phone', 'Por favor, informe um número de WhatsApp válido com DDD (ex: 71 99999-9999).' );
	}

	return [
		'nome'    => $nome,
		'cidade'  => $cidade,
		'tipo'    => $tipo ?: 'Não informado',
		'quartos' => $quartos ?: 'Não informado',
		'phone'   => $phone,
		'digits'  => $digits,
	];
}

// ─── 3. HARDENING INFRASTRUTTURA WORDPRESS & TEMPLATE ────────────────────────

/**
 * 3.1 HTTP Security Headers contro Clickjacking, XSS e MIME-Sniffing.
 */
add_action( 'send_headers', 'vh_send_security_headers' );
function vh_send_security_headers() {
	if ( headers_sent() || '1' !== (string) vh_sec_opt( 'vh_sec_headers_enable', '1' ) ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'X-XSS-Protection: 1; mode=block' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );

	// Evita X-Frame-Options solo dentro l'anteprima del Customizer WordPress
	if ( ! is_customize_preview() ) {
		header( 'X-Frame-Options: SAMEORIGIN' );
	}
}

/**
 * 3.2 Disabilitazione XML-RPC e rimozione X-Pingback (previene attacchi brute-force e DDoS).
 */
add_action( 'init', 'vh_init_xmlrpc_hardening' );
function vh_init_xmlrpc_hardening() {
	if ( '1' !== (string) vh_sec_opt( 'vh_sec_disable_xmlrpc', '1' ) ) {
		return;
	}
	add_filter( 'xmlrpc_enabled', '__return_false' );
	add_filter( 'wp_headers', function ( $headers ) {
		unset( $headers['X-Pingback'], $headers['x-pingback'] );
		return $headers;
	} );
	add_filter( 'xmlrpc_methods', function ( $methods ) {
		unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'], $methods['system.multicall'] );
		return $methods;
	} );
}

/**
 * 3.3 Blocco Enumerazione Utenti (/?author=1 e /wp-json/wp/v2/users).
 * Impedisce agli scanner automatici di scoprire i nomi utente degli amministratori.
 */
add_action( 'template_redirect', 'vh_block_author_scan_enumeration', 1 );
function vh_block_author_scan_enumeration() {
	if ( is_admin() || '1' !== (string) vh_sec_opt( 'vh_sec_block_user_enum', '1' ) ) {
		return;
	}
	if ( ! empty( $_GET['author'] ) || is_author() ) {
		vh_log_security_block( 'User Enumeration Bloqueado', 'Tentativa de varredura de usuários via /?author=' );
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}

add_filter( 'rest_endpoints', 'vh_restrict_rest_users_endpoint' );
function vh_restrict_rest_users_endpoint( $endpoints ) {
	if ( '1' !== (string) vh_sec_opt( 'vh_sec_block_user_enum', '1' ) ) {
		return $endpoints;
	}
	if ( ! is_user_logged_in() || ! current_user_can( 'list_users' ) ) {
		unset( $endpoints['/wp/v2/users'] );
		unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
}

/**
 * 3.4 Offuscamento errori di Login e versione WordPress nei feed RSS.
 */
add_filter( 'login_errors', function ( $error ) {
	if ( '1' === (string) vh_sec_opt( 'vh_sec_hide_login_errors', '1' ) ) {
		return '<strong>Erro de acesso:</strong> Credenciais inválidas. Verifique seu usuário e senha.';
	}
	return $error;
} );

add_filter( 'the_generator', '__return_empty_string' );
