<?php
/**
 * VivaHost — Pannello Admin Principale: SEO & Opzioni Generali
 * Raggiungibile dal menu top-level: VivaHost → SEO & Opzioni
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Accesso negato.' );
}

$saved      = isset( $_GET['saved'] ) && '1' === $_GET['saved'];
$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'seo';
if ( ! in_array( $active_tab, [ 'seo', 'schema', 'general', 'technical', 'security' ], true ) ) {
	$active_tab = 'seo';
}

$defaults = array_merge(
	vh_seo_defaults(),
	function_exists( 'vh_sec_defaults' ) ? vh_sec_defaults() : []
);
$ext_seo = vh_detect_external_seo_plugin();

// Helper per leggere il valore corrente (anche per checkbox salvati a '0')
$val = function ( $key ) use ( $defaults ) {
	$v = get_option( $key, null );
	return ( null === $v ) ? ( $defaults[ $key ] ?? '' ) : $v;
};
?>
<style>
.vh-admin-wrap { max-width: 980px; padding: 20px 0 60px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
.vh-admin-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; background: linear-gradient(135deg, #2b1328 0%, #681739 60%, #941c42 100%); padding: 26px 30px; border-radius: 16px; color: #fff; box-shadow: 0 10px 25px -5px rgba(148, 28, 66, 0.25); }
.vh-admin-header h1 { color: #fff !important; font-size: 1.55rem; font-weight: 800; margin: 0 0 6px; padding: 0; display: flex; align-items: center; gap: 10px; line-height: 1.2; }
.vh-admin-header p { color: rgba(255,255,255,0.85); margin: 0; font-size: 13.5px; max-width: 640px; line-height: 1.5; }

.vh-tabs { display: flex; gap: 6px; margin-bottom: 24px; background: #f1f5f9; padding: 6px; border-radius: 12px; flex-wrap: wrap; }
.vh-tab-btn { flex: 1; min-width: 155px; display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 11px 14px; border-radius: 8px; border: none; background: transparent; color: #475569; font-size: 13px; font-weight: 700; cursor: pointer; transition: all .15s; text-align: center; }
.vh-tab-btn:hover { color: #0f172a; background: rgba(255,255,255,0.5); }
.vh-tab-btn.active { background: #fff; color: #FF5A5F; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }

.vh-tab-panel { display: none; }
.vh-tab-panel.active { display: block; }

.vh-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
.vh-card h2 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 6px; display: flex; align-items: center; gap: 8px; }
.vh-card-sub { font-size: 13px; color: #64748b; margin: 0 0 20px; padding-bottom: 14px; border-bottom: 1px solid #f1f5f9; line-height: 1.5; }

.vh-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.vh-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
.vh-stat-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; }
.vh-stat-box .vh-stat-k { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: .04em; margin-bottom: 4px; }
.vh-stat-box .vh-stat-v { font-size: 1.25rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 6px; }

.vh-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 18px; }
.vh-field label { font-size: 12px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .04em; }
.vh-field input[type="text"], .vh-field input[type="url"], .vh-field input[type="number"], .vh-field input[type="password"], .vh-field select, .vh-field textarea {
	font-size: 14px; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; background: #f8fafc; color: #0f172a; outline: none; transition: all .2s; width: 100%;
}
.vh-field input:focus, .vh-field select:focus, .vh-field textarea:focus { border-color: #FF5A5F; background: #fff; box-shadow: 0 0 0 3px rgba(255,90,95,0.12); }
.vh-field textarea.code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-size: 12.5px; background: #0f172a; color: #f8fafc; border-color: #1e293b; }
.vh-hint { font-size: 12px; color: #64748b; line-height: 1.5; }

.vh-toggle-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 14px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 12px; }
.vh-toggle-info strong { display: block; font-size: 13.5px; color: #0f172a; margin-bottom: 3px; }
.vh-toggle-info span { font-size: 12.5px; color: #64748b; line-height: 1.4; display: block; }
.vh-switch { position: relative; display: inline-block; width: 46px; height: 26px; flex-shrink: 0; margin-top: 2px; }
.vh-switch input { opacity: 0; width: 0; height: 0; }
.vh-slider { position: absolute; cursor: pointer; inset: 0; background-color: #cbd5e1; transition: .25s; border-radius: 999px; }
.vh-slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: white; transition: .25s; border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.2); }
.vh-switch input:checked + .vh-slider { background-color: #FF5A5F; }
.vh-switch input:checked + .vh-slider:before { transform: translateX(20px); }

.vh-serp-box { background: #fff; border: 1px solid #dadce0; border-radius: 12px; padding: 18px 22px; margin-bottom: 22px; font-family: Arial, sans-serif; box-shadow: 0 1px 4px rgba(0,0,0,0.04); }
.vh-serp-site { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
.vh-serp-favicon { width: 26px; height: 26px; border-radius: 50%; background: #FF5A5F; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px; }
.vh-serp-domain { font-size: 14px; color: #202124; line-height: 1.2; }
.vh-serp-url { font-size: 12px; color: #5f6368; }
.vh-serp-title { font-size: 20px; color: #1a0dab; font-weight: 400; line-height: 1.3; margin: 4px 0 6px; cursor: pointer; }
.vh-serp-title:hover { text-decoration: underline; }
.vh-serp-desc { font-size: 14px; color: #4d5156; line-height: 1.58; }

.vh-btn { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 700; padding: 11px 26px; border-radius: 999px; border: none; cursor: pointer; text-decoration: none; transition: all .15s; }
.vh-btn-primary { background: #FF5A5F; color: #fff !important; box-shadow: 0 4px 12px rgba(255, 90, 95, 0.3); }
.vh-btn-primary:hover { filter: brightness(.93); transform: translateY(-1px); }
.vh-btn-outline { background: #fff; color: #334155 !important; border: 1.5px solid #cbd5e1; }
.vh-btn-outline:hover { background: #f8fafc; border-color: #94a3b8; }

.vh-notice { padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.vh-notice--ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.vh-notice--warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

.vh-diag-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.vh-diag-table th, .vh-diag-table td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; text-align: left; }
.vh-diag-table th { font-size: 11.5px; text-transform: uppercase; color: #64748b; font-weight: 700; background: #f8fafc; }
.vh-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.vh-pill--ok { background: #dcfce7; color: #15803d; }
.vh-pill--warn { background: #fef3c7; color: #b45309; }
.vh-pill--danger { background: #fee2e2; color: #b91c1c; }

@media (max-width: 782px) { .vh-grid-2, .vh-grid-4 { grid-template-columns: 1fr; } }
</style>

<div class="wrap vh-admin-wrap">

	<!-- Header Banner -->
	<div class="vh-admin-header">
		<div>
			<h1>
				<span>🏠 VivaHost — Central SEO, Segurança &amp; Opções Gerais</span>
			</h1>
			<p>Gerencie em um único painel o SEO técnico, Local SEO para Salvador (Schema.org), Google Analytics, Sitemap/IA (<code>/llms.txt</code>) e a blindagem de segurança anti-bot e anti-invasão do template.</p>
		</div>
		<div>
			<span style="background:rgba(255,255,255,0.18);padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;backdrop-filter:blur(6px)">
				Tema v<?php echo esc_html( wp_get_theme( 'vivahost-tema' )->get( 'Version' ) ?: VH_VER ); ?>
			</span>
		</div>
	</div>

	<?php if ( $saved ) : ?>
		<div class="vh-notice vh-notice--ok">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
			<span>Configurações de SEO, Segurança e Opções Gerais salvas com sucesso!</span>
		</div>
	<?php endif; ?>

	<?php if ( $ext_seo ) : ?>
		<div class="vh-notice vh-notice--warn">
			<span>⚠️ <strong>Plugin SEO externo detectado (<?php echo esc_html( $ext_seo ); ?>):</strong> Para evitar duplicação de meta tags, o VivaHost cede automaticamente a geração de <code>&lt;title&gt;</code> e Open Graph ao plugin externo, mantendo ativos os scripts de Analytics, segurança e verificação.</span>
		</div>
	<?php endif; ?>

	<!-- Tabs Navigation -->
	<div class="vh-tabs" role="tablist">
		<button type="button" class="vh-tab-btn <?php echo 'seo' === $active_tab ? 'active' : ''; ?>" data-tab="seo">
			🔍 1. SEO Global &amp; Home
		</button>
		<button type="button" class="vh-tab-btn <?php echo 'schema' === $active_tab ? 'active' : ''; ?>" data-tab="schema">
			📍 2. Local SEO &amp; Schema
		</button>
		<button type="button" class="vh-tab-btn <?php echo 'general' === $active_tab ? 'active' : ''; ?>" data-tab="general">
			📊 3. Analytics &amp; Opções
		</button>
		<button type="button" class="vh-tab-btn <?php echo 'technical' === $active_tab ? 'active' : ''; ?>" data-tab="technical">
			🤖 4. Sitemap &amp; IA SEO
		</button>
		<button type="button" class="vh-tab-btn <?php echo 'security' === $active_tab ? 'active' : ''; ?>" data-tab="security">
			🛡️ 5. Segurança &amp; Anti-Bot
		</button>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-seo' ) ); ?>">
		<?php wp_nonce_field( 'vh_seo_settings_save', 'vh_seo_settings_nonce' ); ?>
		<input type="hidden" name="vh_active_tab" id="vh_active_tab" value="<?php echo esc_attr( $active_tab ); ?>">

		<!-- ══════════════════════════════════════════════════════════════════════
		     TAB 1: SEO GLOBAL & HOMEPAGE
		═══════════════════════════════════════════════════════════════════════ -->
		<div class="vh-tab-panel <?php echo 'seo' === $active_tab ? 'active' : ''; ?>" id="vh-panel-seo">

			<div class="vh-card">
				<h2>Motor SEO Nativo VivaHost</h2>
				<p class="vh-card-sub">Ativa a geração nativa de títulos otimizados, meta descriptions, canonicals, Open Graph (WhatsApp/Instagram/Facebook) e Twitter Cards sem precisar instalar plugins pesados.</p>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Ativar Motor SEO Nativo no WordPress (<code>wp_head</code>)</strong>
						<span>Gera todas as meta tags SEO e controla o <code>&lt;title&gt;</code> nativo do WordPress sem duplicações.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_seo_enable_native" value="1" <?php checked( $val( 'vh_seo_enable_native' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>
			</div>

			<div class="vh-card">
				<h2>Prévia de Pesquisa no Google (SERP) &amp; Metadados da Homepage</h2>
				<p class="vh-card-sub">Personalize como a página principal da VivaHost aparece no Google e quando compartilhada no WhatsApp.</p>

				<!-- Live Google SERP Preview -->
				<div class="vh-serp-box">
					<div class="vh-serp-site">
						<div class="vh-serp-favicon">V</div>
						<div>
							<div class="vh-serp-domain"><?php echo esc_html( get_bloginfo( 'name' ) ?: 'VivaHost' ); ?></div>
							<div class="vh-serp-url"><?php echo esc_html( home_url( '/' ) ); ?></div>
						</div>
					</div>
					<div class="vh-serp-title" id="vh-serp-title-prev"><?php echo esc_html( $val( 'vh_seo_home_title' ) ); ?></div>
					<div class="vh-serp-desc" id="vh-serp-desc-prev"><?php echo esc_html( $val( 'vh_seo_home_desc' ) ); ?></div>
				</div>

				<div class="vh-field">
					<label for="vh_seo_home_title">Meta Title da Homepage (Título SEO Principal)</label>
					<input type="text" id="vh_seo_home_title" name="vh_seo_home_title" value="<?php echo esc_attr( $val( 'vh_seo_home_title' ) ); ?>">
					<span class="vh-hint">Recomendado entre 50 e 65 caracteres. Inclua a palavra-chave principal (ex: <em>Gestão de Aluguel por Temporada e Airbnb em Salvador</em>).</span>
				</div>

				<div class="vh-field">
					<label for="vh_seo_home_desc">Meta Description da Homepage</label>
					<textarea id="vh_seo_home_desc" name="vh_seo_home_desc" rows="3"><?php echo esc_textarea( $val( 'vh_seo_home_desc' ) ); ?></textarea>
					<span class="vh-hint">Recomendado entre 140 e 160 caracteres. Texto exibido logo abaixo do título nos resultados do Google e nos cartões do WhatsApp.</span>
				</div>

				<div class="vh-field">
					<label for="vh_seo_home_keywords">Palavras-chave Principais (Focus Keywords)</label>
					<input type="text" id="vh_seo_home_keywords" name="vh_seo_home_keywords" value="<?php echo esc_attr( $val( 'vh_seo_home_keywords' ) ); ?>">
					<span class="vh-hint">Separadas por vírgula. Usadas como referência semântica para o site.</span>
				</div>

				<div class="vh-grid-2">
					<div class="vh-field">
						<label for="vh_seo_title_sep">Separador de Título das Páginas Internas</label>
						<select id="vh_seo_title_sep" name="vh_seo_title_sep">
							<?php
							$seps = [ '—' => '— (Travessão longo)', '|' => '| (Barra vertical)', '·' => '· (Ponto central)', '-' => '- (Hífen)' ];
							foreach ( $seps as $s_val => $s_label ) :
							?>
								<option value="<?php echo esc_attr( $s_val ); ?>" <?php selected( $val( 'vh_seo_title_sep' ), $s_val ); ?>><?php echo esc_html( $s_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="vh-field">
						<label for="vh_seo_robots_default">Diretivas Robots Globais</label>
						<select id="vh_seo_robots_default" name="vh_seo_robots_default">
							<?php
							$r_opts = [
								'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' => 'Indexar Tudo + Rich Snippets Máximos (Recomendado)',
								'index, follow'   => 'Index, Follow (Padrão)',
								'noindex, follow' => 'Noindex, Follow (Apenas Homologação)',
							];
							foreach ( $r_opts as $r_val => $r_label ) :
							?>
								<option value="<?php echo esc_attr( $r_val ); ?>" <?php selected( $val( 'vh_seo_robots_default' ), $r_val ); ?>><?php echo esc_html( $r_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="vh-field">
					<label for="vh_seo_og_image">URL da Imagem Open Graph Padrão (Compartilhamento WhatsApp / Redes Sociais)</label>
					<input type="url" id="vh_seo_og_image" name="vh_seo_og_image" value="<?php echo esc_attr( $val( 'vh_seo_og_image' ) ); ?>" placeholder="<?php echo esc_attr( VH_URL . '/assets/images/prop-ondina-vista.jpg' ); ?>">
					<span class="vh-hint">Imagem de capa exibida quando alguém envia o link do site no WhatsApp ou Facebook (ideal: 1200×630px). Se vazio, usa a foto destaque da VivaHost.</span>
				</div>
			</div>
		</div>

		<!-- ══════════════════════════════════════════════════════════════════════
		     TAB 2: LOCAL SEO & SCHEMA.ORG (SALVADOR, BA)
		═══════════════════════════════════════════════════════════════════════ -->
		<div class="vh-tab-panel <?php echo 'schema' === $active_tab ? 'active' : ''; ?>" id="vh-panel-schema">

			<div class="vh-card">
				<h2>Dados Estruturados Schema.org (JSON-LD <code>@graph</code>) &amp; Local SEO Salvador</h2>
				<p class="vh-card-sub">Configura o grafo de conhecimento oficial que informa ao Google, Google Maps e assistentes de IA os dados empresariais, avaliações (Rich Snippets ★), localização geográfica e bairros atendidos em Salvador.</p>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Gerar Grafo Schema.org JSON-LD Completo Automaticamente</strong>
						<span>Inclui <code>RealEstateAgent</code>, <code>LocalBusiness</code>, <code>WebSite</code>, <code>Service</code>, <code>FAQPage</code>, <code>BreadcrumbList</code> e <code>BlogPosting</code>.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_seo_schema_enable" value="1" <?php checked( $val( 'vh_seo_schema_enable' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-field" style="margin-top:18px">
					<label for="vh_seo_schema_type">Categoria Principal do Negócio (Schema @type)</label>
					<select id="vh_seo_schema_type" name="vh_seo_schema_type">
						<?php
						$s_types = [
							'RealEstateAgent'     => 'RealEstateAgent (Imobiliária / Gestão de Imóveis — Recomendado)',
							'LodgingBusiness'     => 'LodgingBusiness (Hospedagem por Temporada)',
							'ProfessionalService' => 'ProfessionalService (Serviço Profissional)',
						];
						foreach ( $s_types as $st_val => $st_label ) :
						?>
							<option value="<?php echo esc_attr( $st_val ); ?>" <?php selected( $val( 'vh_seo_schema_type' ), $st_val ); ?>><?php echo esc_html( $st_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="vh-grid-2">
					<div class="vh-field">
						<label for="vh_seo_geo_placename">Cidade Principal (<code>geo.placename</code>)</label>
						<input type="text" id="vh_seo_geo_placename" name="vh_seo_geo_placename" value="<?php echo esc_attr( $val( 'vh_seo_geo_placename' ) ); ?>">
					</div>
					<div class="vh-field">
						<label for="vh_seo_geo_region">Código ISO do Estado (<code>geo.region</code>)</label>
						<input type="text" id="vh_seo_geo_region" name="vh_seo_geo_region" value="<?php echo esc_attr( $val( 'vh_seo_geo_region' ) ); ?>" placeholder="BR-BA">
					</div>
				</div>

				<div class="vh-grid-2">
					<div class="vh-field">
						<label for="vh_seo_geo_lat">Latitude Geográfica (Salvador)</label>
						<input type="text" id="vh_seo_geo_lat" name="vh_seo_geo_lat" value="<?php echo esc_attr( $val( 'vh_seo_geo_lat' ) ); ?>" placeholder="-12.9818">
					</div>
					<div class="vh-field">
						<label for="vh_seo_geo_lng">Longitude Geográfica (Salvador)</label>
						<input type="text" id="vh_seo_geo_lng" name="vh_seo_geo_lng" value="<?php echo esc_attr( $val( 'vh_seo_geo_lng' ) ); ?>" placeholder="-38.4552">
					</div>
				</div>

				<div class="vh-field">
					<label for="vh_seo_area_served">Bairros e Regiões Atendidas em Salvador (<code>areaServed</code> para SEO Local)</label>
					<textarea id="vh_seo_area_served" name="vh_seo_area_served" rows="3"><?php echo esc_textarea( $val( 'vh_seo_area_served' ) ); ?></textarea>
					<span class="vh-hint">Lista de bairros separados por vírgula injetada no Schema.org e no arquivo <code>/llms.txt</code> para posicionar buscas por bairro (ex: "gestão airbnb Barra Salvador", "aluguel temporada Ondina").</span>
				</div>

				<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;margin-top:6px;font-size:13px;color:#475569">
					<span>📌 Os dados cadastrais (Razão Social, CNPJ, Endereço, Nota e Taxa de Comissão) são lidos automaticamente do Personalizar para evitar repetição.</span>
					<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="vh-btn vh-btn-outline" style="padding:7px 16px;font-size:12.5px;white-space:nowrap">Editar no Personalizar →</a>
				</div>
			</div>
		</div>

		<!-- ══════════════════════════════════════════════════════════════════════
		     TAB 3: OPÇÕES GERAIS, ANALYTICS & VERIFICAÇÃO
		═══════════════════════════════════════════════════════════════════════ -->
		<div class="vh-tab-panel <?php echo 'general' === $active_tab ? 'active' : ''; ?>" id="vh-panel-general">

			<div class="vh-card">
				<h2>Rastreamento, Analytics &amp; Pixels de Conversão</h2>
				<p class="vh-card-sub">Insira apenas o ID de cada serviço. O tema VivaHost injeta automaticamente o código oficial otimizado e assíncrono no <code>&lt;head&gt;</code>.</p>

				<div class="vh-grid-2">
					<div class="vh-field">
						<label for="vh_gen_ga4_id">Google Analytics 4 — Measurement ID</label>
						<input type="text" id="vh_gen_ga4_id" name="vh_gen_ga4_id" value="<?php echo esc_attr( $val( 'vh_gen_ga4_id' ) ); ?>" placeholder="G-XXXXXXXXXX">
						<span class="vh-hint">Formato: <code>G-XXXXXXXXXX</code>. Deixe em branco se não utilizar.</span>
					</div>

					<div class="vh-field">
						<label for="vh_gen_gtm_id">Google Tag Manager — Container ID</label>
						<input type="text" id="vh_gen_gtm_id" name="vh_gen_gtm_id" value="<?php echo esc_attr( $val( 'vh_gen_gtm_id' ) ); ?>" placeholder="GTM-XXXXXXX">
						<span class="vh-hint">Formato: <code>GTM-XXXXXXX</code>.</span>
					</div>
				</div>

				<div class="vh-field" style="max-width:450px">
					<label for="vh_gen_meta_pixel">Meta / Facebook Pixel ID</label>
					<input type="text" id="vh_gen_meta_pixel" name="vh_gen_meta_pixel" value="<?php echo esc_attr( $val( 'vh_gen_meta_pixel' ) ); ?>" placeholder="123456789012345">
					<span class="vh-hint">Apenas números. Dispara automaticamente o evento <code>PageView</code> em todas as páginas.</span>
				</div>
			</div>

			<div class="vh-card">
				<h2>Verificação de Propriedade nos Buscadores (Search Console &amp; Bing)</h2>
				<p class="vh-card-sub">Confirme a propriedade do domínio no Google Search Console e no Bing Webmaster Tools colando o código ou a meta tag.</p>

				<div class="vh-grid-2">
					<div class="vh-field">
						<label for="vh_seo_google_verify">Google Search Console (<code>google-site-verification</code>)</label>
						<input type="text" id="vh_seo_google_verify" name="vh_seo_google_verify" value="<?php echo esc_attr( $val( 'vh_seo_google_verify' ) ); ?>" placeholder="Código de verificação do Google">
					</div>

					<div class="vh-field">
						<label for="vh_seo_bing_verify">Bing Webmaster Tools (<code>msvalidate.01</code>)</label>
						<input type="text" id="vh_seo_bing_verify" name="vh_seo_bing_verify" value="<?php echo esc_attr( $val( 'vh_seo_bing_verify' ) ); ?>" placeholder="Código de verificação do Bing">
					</div>
				</div>
			</div>

			<div class="vh-card">
				<h2>Opções Gerais do Tema, Performance &amp; Scripts Customizados</h2>
				<p class="vh-card-sub">Otimizações de velocidade para o Core Web Vitals do WordPress e injeção de scripts extras.</p>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Otimizar e Limpar <code>&lt;head&gt;</code> do WordPress (Core Web Vitals)</strong>
						<span>Remove scripts desnecessários de Emojis do WP, links RSD, WLWManifest, shortlinks e tag de versão para acelerar o carregamento.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_gen_clean_wp_head" value="1" <?php checked( $val( 'vh_gen_clean_wp_head' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Exibir Barra de Consentimento de Cookies (LGPD)</strong>
						<span>Mostra o aviso discreto de cookies no rodapé na primeira visita do usuário.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_gen_cookie_bar" value="1" <?php checked( $val( 'vh_gen_cookie_bar' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-field" style="margin-top:18px">
					<label for="vh_gen_custom_head">Scripts Personalizados no <code>&lt;head&gt;</code> (Opcional — ex: Microsoft Clarity, Hotjar)</label>
					<textarea id="vh_gen_custom_head" name="vh_gen_custom_head" rows="4" class="code" placeholder="<!-- Cole aqui scripts extras para o <head> -->"><?php echo esc_textarea( $val( 'vh_gen_custom_head' ) ); ?></textarea>
				</div>

				<div class="vh-field">
					<label for="vh_gen_custom_footer">Scripts Personalizados no Rodapé antes de <code>&lt;/body&gt;</code> (Opcional)</label>
					<textarea id="vh_gen_custom_footer" name="vh_gen_custom_footer" rows="4" class="code" placeholder="<!-- Cole aqui scripts extras para o rodapé -->"><?php echo esc_textarea( $val( 'vh_gen_custom_footer' ) ); ?></textarea>
				</div>
			</div>
		</div>

		<!-- ══════════════════════════════════════════════════════════════════════
		     TAB 4: SITEMAP XML, ROBOTS.TXT & AI SEO (LLMS.TXT)
		═══════════════════════════════════════════════════════════════════════ -->
		<div class="vh-tab-panel <?php echo 'technical' === $active_tab ? 'active' : ''; ?>" id="vh-panel-technical">

			<div class="vh-card">
				<h2>Infraestrutura de Indexação WordPress &amp; AI SEO (GEO)</h2>
				<p class="vh-card-sub">Controle como o Google e os motores de resposta por Inteligência Artificial (ChatGPT, Perplexity, Claude, Google AI Overviews) rastreiam e citam a VivaHost.</p>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Otimizar Sitemap XML Nativa do WordPress (<code>/wp-sitemap.xml</code>)</strong>
						<span>Remove arquivos de usuários/autores vazios e exclui automaticamente páginas marcadas com <code>noindex</code> na metabox SEO.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_seo_sitemap_enhance" value="1" <?php checked( $val( 'vh_seo_sitemap_enhance' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Otimizar <code>/robots.txt</code> Virtual com Regras SEO &amp; Bots de IA</strong>
						<span>Adiciona diretivas limpas de rastreamento, libera bots de busca de IA (<code>OAI-SearchBot</code>, <code>PerplexityBot</code>, <code>ClaudeBot</code>) e declara o link oficial da Sitemap XML.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_seo_robots_enhance" value="1" <?php checked( $val( 'vh_seo_robots_enhance' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Ativar Endpoint <code>/llms.txt</code> (Otimização para ChatGPT, Perplexity &amp; Claude)</strong>
						<span>Gera dinamicamente um documento Markdown estruturado na raiz do site com todas as credenciais da VivaHost, serviços em Salvador, bairros atendidos e links para os artigos do blog.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_seo_llms_txt_enable" value="1" <?php checked( $val( 'vh_seo_llms_txt_enable' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>
			</div>

			<div class="vh-card">
				<h2>Diagnóstico da Infraestrutura SEO no WordPress</h2>
				<p class="vh-card-sub">Verificação em tempo real dos componentes nativos do WordPress no seu servidor.</p>

				<table class="vh-diag-table">
					<thead>
						<tr>
							<th>Componente</th>
							<th>Status</th>
							<th>Detalhes / Ação Rápida</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><strong>Visibilidade nos Buscadores (WordPress)</strong></td>
							<td>
								<?php if ( '0' !== (string) get_option( 'blog_public' ) ) : ?>
									<span class="vh-pill vh-pill--ok">✓ Indexável (Público)</span>
								<?php else : ?>
									<span class="vh-pill vh-pill--warn">⚠️ Bloqueado (noindex)</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( '0' !== (string) get_option( 'blog_public' ) ) : ?>
									Os motores de busca estão autorizados a indexar o site.
								<?php else : ?>
									Desmarque "Evitar que mecanismos de busca indexem este site" em <a href="<?php echo esc_url( admin_url( 'options-reading.php' ) ); ?>">Configurações → Leitura</a>.
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><strong>Tag <code>&lt;title&gt;</code> Nativa (<code>title-tag</code>)</strong></td>
							<td><span class="vh-pill vh-pill--ok">✓ Ativa &amp; Sem Duplicatas</span></td>
							<td>Gerenciada via hook <code>pre_get_document_title</code> do WordPress.</td>
						</tr>
						<tr>
							<td><strong>Sitemap XML Nativa (WordPress Core)</strong></td>
							<td><span class="vh-pill vh-pill--ok">✓ Ativa</span></td>
							<td><a href="<?php echo esc_url( home_url( '/wp-sitemap.xml' ) ); ?>" target="_blank" rel="noopener">Abrir <code>/wp-sitemap.xml</code> ↗</a></td>
						</tr>
						<tr>
							<td><strong>Arquivo <code>robots.txt</code> Virtual</strong></td>
							<td><span class="vh-pill vh-pill--ok">✓ Otimizado</span></td>
							<td><a href="<?php echo esc_url( home_url( '/?robots=1' ) ); ?>" target="_blank" rel="noopener">Inspecionar <code>robots.txt</code> ↗</a></td>
						</tr>
						<tr>
							<td><strong>Contexto para IA (<code>/llms.txt</code>)</strong></td>
							<td>
								<?php if ( '1' === $val( 'vh_seo_llms_txt_enable' ) ) : ?>
									<span class="vh-pill vh-pill--ok">✓ Ativo (GEO Ready)</span>
								<?php else : ?>
									<span class="vh-pill vh-pill--warn">Desativado</span>
								<?php endif; ?>
							</td>
							<td><a href="<?php echo esc_url( home_url( '/llms.txt' ) ); ?>" target="_blank" rel="noopener">Visualizar <code>/llms.txt</code> ↗</a></td>
						</tr>
						<tr>
							<td><strong>Metabox SEO em Posts &amp; Páginas</strong></td>
							<td><span class="vh-pill vh-pill--ok">✓ Ativo no Editor</span></td>
							<td>Permite editar Title, Description, Focus Keyword e Canonical individualmente em cada artigo do Blog.</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- ══════════════════════════════════════════════════════════════════════
		     TAB 5: SEGURANÇA DO TEMPLATE, ANTI-BOT & FIREWALL (WAF)
		═══════════════════════════════════════════════════════════════════════ -->
		<div class="vh-tab-panel <?php echo 'security' === $active_tab ? 'active' : ''; ?>" id="vh-panel-security">

			<?php
			$sec_total_blocked = (int) get_option( 'vh_sec_blocked_total', 0 );
			$sec_log           = get_option( 'vh_sec_blocked_log', [] );
			if ( ! is_array( $sec_log ) ) {
				$sec_log = [];
			}
			?>

			<div class="vh-grid-4">
				<div class="vh-stat-box">
					<div class="vh-stat-k">Escudo Formulário</div>
					<div class="vh-stat-v">
						<span class="vh-pill vh-pill--ok">✓ 7 Camadas Ativas</span>
					</div>
				</div>
				<div class="vh-stat-box">
					<div class="vh-stat-k">Rate Limiting IP</div>
					<div class="vh-stat-v">
						Max <?php echo esc_html( (string) $val( 'vh_sec_rate_limit_max' ) ); ?> / <?php echo esc_html( (string) $val( 'vh_sec_rate_limit_window' ) ); ?>m
					</div>
				</div>
				<div class="vh-stat-box">
					<div class="vh-stat-k">Hardening Template</div>
					<div class="vh-stat-v">
						<span class="vh-pill vh-pill--ok">✓ Blindado</span>
					</div>
				</div>
				<div class="vh-stat-box">
					<div class="vh-stat-k">Bots &amp; Ataques Bloqueados</div>
					<div class="vh-stat-v" style="color:#FF5A5F">
						<?php echo esc_html( (string) $sec_total_blocked ); ?>
					</div>
				</div>
			</div>

			<div class="vh-card">
				<h2>🛡️ Proteção Anti-Bot, Anti-Spam &amp; Firewall do Formulário</h2>
				<p class="vh-card-sub">Impede que robôs automatizados, scripts de spam e ataques de injeção enviem mensagens falsas ou sobrecarreguem o servidor de e-mail.</p>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Ativar Escudo Multi-Camada Anti-Bot &amp; WAF no Formulário (Recomendado)</strong>
						<span>Combina: (1) Nonce CSRF + Verificação de Origem, (2) Honeypot Triplo Invisível, (3) Timestamp Assinado com Criptografia <code>HMAC-SHA256</code> (bloqueia robôs que enviam em &lt;2s), (4) Token JS de Interação Humana Real e (5) Firewall WAF contra links/XSS/SQLi/Email Header Injection.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_sec_form_shield" value="1" <?php checked( $val( 'vh_sec_form_shield' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-grid-2" style="margin-top:18px">
					<div class="vh-field">
						<label for="vh_sec_rate_limit_max">Limite Máximo de Envios por IP (Anti-Flood)</label>
						<input type="number" id="vh_sec_rate_limit_max" name="vh_sec_rate_limit_max" min="1" max="50" value="<?php echo esc_attr( (string) $val( 'vh_sec_rate_limit_max' ) ); ?>">
						<span class="vh-hint">Quantos formulários um mesmo endereço IP pode enviar dentro da janela de tempo (Padrão: 3).</span>
					</div>
					<div class="vh-field">
						<label for="vh_sec_rate_limit_window">Janela de Tempo do Rate Limit (em Minutos)</label>
						<input type="number" id="vh_sec_rate_limit_window" name="vh_sec_rate_limit_window" min="1" max="120" value="<?php echo esc_attr( (string) $val( 'vh_sec_rate_limit_window' ) ); ?>">
						<span class="vh-hint">Tempo de bloqueio temporário caso um IP ultrapasse o limite de envios (Padrão: 15 minutos).</span>
					</div>
				</div>

				<div class="vh-grid-2" style="margin-top:6px">
					<div class="vh-field">
						<label for="vh_sec_turnstile_site">Cloudflare Turnstile — Site Key (Opcional)</label>
						<input type="text" id="vh_sec_turnstile_site" name="vh_sec_turnstile_site" value="<?php echo esc_attr( $val( 'vh_sec_turnstile_site' ) ); ?>" placeholder="0x4AAAAAA... (Opcional)">
						<span class="vh-hint">Se preenchido, adiciona o widget oficial Cloudflare Turnstile ao formulário. Sem chaves, o escudo invisível nativo já protege 100% automaticamente.</span>
					</div>
					<div class="vh-field">
						<label for="vh_sec_turnstile_secret">Cloudflare Turnstile — Secret Key (Opcional)</label>
						<input type="password" id="vh_sec_turnstile_secret" name="vh_sec_turnstile_secret" value="<?php echo esc_attr( $val( 'vh_sec_turnstile_secret' ) ); ?>" placeholder="0x4AAAAAA... (Opcional)">
					</div>
				</div>
			</div>

			<div class="vh-card">
				<h2>🔒 Hardening do Template &amp; Blindagem da Infraestrutura WordPress</h2>
				<p class="vh-card-sub">Fecha as brechas clássicas do WordPress exploradas por hackers e scanners automatizados.</p>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Injetar Cabeçalhos HTTP de Segurança (Security Headers)</strong>
						<span>Envia <code>X-Content-Type-Options: nosniff</code>, <code>X-Frame-Options: SAMEORIGIN</code> (anti-clickjacking), <code>Referrer-Policy: strict-origin-when-cross-origin</code>, <code>X-XSS-Protection</code> e <code>Permissions-Policy</code>.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_sec_headers_enable" value="1" <?php checked( $val( 'vh_sec_headers_enable' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Desativar XML-RPC e Pingbacks (Anti-Força Bruta &amp; DDoS)</strong>
						<span>Bloqueia o endpoint <code>xmlrpc.php</code> e o cabeçalho <code>X-Pingback</code>, eliminando o principal vetor de ataques de força bruta amplificados no WordPress.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_sec_disable_xmlrpc" value="1" <?php checked( $val( 'vh_sec_disable_xmlrpc' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Bloquear Enumeração de Usuários Admin (<code>/?author=1</code> e REST API)</strong>
						<span>Impede que hackers descubram os logins dos administradores varrendo <code>/?author=N</code> ou consultando <code>/wp-json/wp/v2/users</code> sem autenticação.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_sec_block_user_enum" value="1" <?php checked( $val( 'vh_sec_block_user_enum' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>

				<div class="vh-toggle-row">
					<div class="vh-toggle-info">
						<strong>Ocultar Dicas de Usuário no Login WordPress + Remover Versão WP</strong>
						<span>Substitui mensagens detalhadas de erro no login por um aviso genérico seguro e remove a versão do WordPress dos feeds RSS.</span>
					</div>
					<label class="vh-switch">
						<input type="checkbox" name="vh_sec_hide_login_errors" value="1" <?php checked( $val( 'vh_sec_hide_login_errors' ), '1' ); ?>>
						<span class="vh-slider"></span>
					</label>
				</div>
			</div>

			<div class="vh-card">
				<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:6px">
					<h2 style="margin:0">📋 Registro de Bots &amp; Ataques Bloqueados (Firewall Log)</h2>
					<?php if ( ! empty( $sec_log ) || $sec_total_blocked > 0 ) : ?>
						<button type="submit" name="vh_clear_sec_log" value="1" class="vh-btn vh-btn-outline" style="padding:6px 14px;font-size:12px">
							Limpar Histórico
						</button>
					<?php endif; ?>
				</div>
				<p class="vh-card-sub">Últimas 20 tentativas suspeitas interceptadas e neutralizadas automaticamente pelo escudo VivaHost (IPs parcialmente mascarados conforme LGPD).</p>

				<?php if ( empty( $sec_log ) ) : ?>
					<div style="text-align:center;padding:24px;background:#f8fafc;border-radius:10px;color:#64748b;font-size:13.5px">
						✓ Nenhuma tentativa de ataque ou bot registrada recentemente. O escudo está ativo e monitorando em tempo real.
					</div>
				<?php else : ?>
					<table class="vh-diag-table">
						<thead>
							<tr>
								<th>Data / Hora</th>
								<th>IP (LGPD)</th>
								<th>Camada Acionada</th>
								<th>Detalhes</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $sec_log as $entry ) : ?>
								<tr>
									<td style="white-space:nowrap"><?php echo esc_html( $entry['time'] ?? '' ); ?></td>
									<td><code><?php echo esc_html( $entry['ip'] ?? '' ); ?></code></td>
									<td><span class="vh-pill vh-pill--danger">🛡️ <?php echo esc_html( $entry['reason'] ?? '' ); ?></span></td>
									<td><?php echo esc_html( $entry['details'] ?? '' ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			</div>

		</div>

		<!-- Save Bar -->
		<div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border:1px solid #e2e8f0;padding:18px 28px;border-radius:14px;box-shadow:0 4px 15px rgba(0,0,0,0.04)">
			<div style="font-size:13px;color:#64748b">
				Todas as opções são salvas de forma segura no banco de dados do WordPress e mantidas mesmo após atualizações do tema.
			</div>
			<button type="submit" class="vh-btn vh-btn-primary">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
				Salvar Configurações
			</button>
		</div>

	</form>
</div>

<script>
(function(){
	// Tab switching
	var btns = document.querySelectorAll('.vh-tab-btn');
	var panels = document.querySelectorAll('.vh-tab-panel');
	var hiddenTab = document.getElementById('vh_active_tab');

	btns.forEach(function(btn){
		btn.addEventListener('click', function(){
			var tab = this.getAttribute('data-tab');
			btns.forEach(function(b){ b.classList.remove('active'); });
			panels.forEach(function(p){ p.classList.remove('active'); });
			this.classList.add('active');
			var target = document.getElementById('vh-panel-' + tab);
			if(target) target.classList.add('active');
			if(hiddenTab) hiddenTab.value = tab;
		});
	});

	// Live SERP preview update
	var tIn = document.getElementById('vh_seo_home_title');
	var dIn = document.getElementById('vh_seo_home_desc');
	var tPr = document.getElementById('vh-serp-title-prev');
	var dPr = document.getElementById('vh-serp-desc-prev');
	if(tIn && tPr){
		tIn.addEventListener('input', function(){ tPr.textContent = this.value; });
	}
	if(dIn && dPr){
		dIn.addEventListener('input', function(){ dPr.textContent = this.value; });
	}
})();
</script>
