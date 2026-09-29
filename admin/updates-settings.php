<?php
/**
 * VivaHost — Admin GitHub Updates Page
 * Raggiungibile da: Aparência → Atualizações VivaHost
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Accesso negato.' );

$saved = isset( $_GET['saved'] ) && $_GET['saved'] === '1';
$current_theme = wp_get_theme( 'vivahost-tema' );
$current_ver   = $current_theme->exists() ? $current_theme->get( 'Version' ) : '3.8.3';
$repo          = get_option( 'vh_github_repo', defined( 'VH_GITHUB_REPO' ) ? VH_GITHUB_REPO : '' );

// Se l'utente clicca "Verificar agora", pulisce la cache
if ( isset( $_POST['vh_check_now'] ) && check_admin_referer( 'vh_updates_save', 'vh_updates_nonce' ) ) {
	delete_transient( 'vh_gh_update_check' );
	delete_site_transient( 'update_themes' );
}

$release = ! empty( $repo ) ? get_transient( 'vh_gh_update_check' ) : false;
if ( ! empty( $repo ) && false === $release ) {
	$response = wp_remote_get( "https://api.github.com/repos/" . trim( $repo, '/' ) . "/releases/latest", [
		'headers' => [ 'Accept' => 'application/vnd.github.v3+json', 'User-Agent' => 'WordPress-VivaHost-Updater' ],
		'timeout' => 10,
	] );
	if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
		$release = json_decode( wp_remote_retrieve_body( $response ) );
		set_transient( 'vh_gh_update_check', $release, 4 * HOUR_IN_SECONDS );
	} else {
		set_transient( 'vh_gh_update_check', 'none', HOUR_IN_SECONDS );
		$release = 'none';
	}
}

$has_update = false;
$remote_ver = '';
if ( ! empty( $release ) && 'none' !== $release && ! empty( $release->tag_name ) ) {
	$remote_ver = ltrim( $release->tag_name, 'v' );
	$has_update = version_compare( $remote_ver, $current_ver, '>' );
}
?>
<style>
.vh-up-wrap { max-width: 780px; padding: 24px 0 60px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
.vh-up-wrap h1 { display: flex; align-items: center; gap: 10px; font-size: 1.5rem; margin-bottom: 8px; font-weight: 700; color: #111; }
.vh-up-intro { color: #555; font-size: 14px; margin-bottom: 28px; line-height: 1.6; max-width: 640px; }
.vh-up-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.vh-up-card h2 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; }
.vh-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.vh-field label { font-size: 12px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .04em; }
.vh-field input {
  font-size: 14px; padding: 10px 14px; border: 1.5px solid #cbd5e1;
  border-radius: 8px; background: #f8fafc; color: #0f172a;
  outline: none; transition: border-color .2s; width: 100%; max-width: 480px;
}
.vh-field input:focus { border-color: #FF5A5F; background: #fff; box-shadow: 0 0 0 3px rgba(255,90,95,0.15); }
.vh-hint { font-size: 12px; color: #64748b; line-height: 1.5; margin-top: 4px; }
.vh-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px; font-weight: 600; padding: 10px 22px; border-radius: 999px; border: none; cursor: pointer; text-decoration: none; transition: all .15s; }
.vh-btn-primary { background: #FF5A5F; color: #fff !important; }
.vh-btn-primary:hover { filter: brightness(.92); }
.vh-btn-outline { background: #fff; color: #334155 !important; border: 1.5px solid #cbd5e1; }
.vh-btn-outline:hover { border-color: #94a3b8; background: #f8fafc; }
.vh-notice { padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.vh-notice--ok   { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.vh-notice--info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.vh-notice--warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.vh-status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
.vh-badge--green { background: #dcfce7; color: #15803d; }
.vh-badge--amber { background: #fef3c7; color: #b45309; }
.vh-step-list { counter-reset: vh-step; margin: 16px 0; padding: 0; list-style: none; }
.vh-step-list li { position: relative; padding-left: 36px; margin-bottom: 14px; font-size: 13.5px; line-height: 1.6; color: #334155; }
.vh-step-list li::before {
  counter-increment: vh-step;
  content: counter(vh-step);
  position: absolute; left: 0; top: 0;
  width: 24px; height: 24px; border-radius: 50%;
  background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}
.vh-code { background: #0f172a; color: #f8fafc; padding: 10px 14px; border-radius: 8px; font-family: monospace; font-size: 12.5px; display: block; overflow-x: auto; margin-top: 6px; }
.vh-suite-nav { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 22px; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; }
.vh-suite-link { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; text-decoration: none; color: #475569; background: #fff; border: 1px solid #cbd5e1; transition: all .15s; }
.vh-suite-link:hover { color: #0f172a; border-color: #94a3b8; background: #f8fafc; }
.vh-suite-link.active { background: #0f172a; color: #fff; border-color: #0f172a; }
</style>

<div class="wrap vh-up-wrap">
  <div class="vh-suite-nav">
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-seo' ) ); ?>" class="vh-suite-link">🔍 SEO &amp; Opzioni Generali</a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-smtp' ) ); ?>" class="vh-suite-link">✉️ Email &amp; SMTP</a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-updates' ) ); ?>" class="vh-suite-link active">🔄 Atualizações GitHub</a>
    <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="vh-suite-link">🎨 Personalizar Tema</a>
  </div>

  <h1>
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FF5A5F" stroke-width="2" stroke-linecap="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
    VivaHost &mdash; Atualizações via GitHub
  </h1>
  <p class="vh-up-intro">
    Gerencie a conexão do tema com o seu repositório GitHub para receber atualizações automáticas e com 1 clique direto na bacheca do WordPress.
  </p>

  <?php if ( $saved ) : ?>
    <div class="vh-notice vh-notice--ok">
      ✓ Repositório salvo com sucesso! O WordPress verificou os dados no GitHub.
    </div>
  <?php endif; ?>

  <!-- STATUS DO TEMA -->
  <div class="vh-up-card">
    <h2>Status da Versão</h2>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px">
      <div>
        <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase">Versão instalada</div>
        <div style="font-size:1.3rem;font-weight:800;color:#0f172a">v<?php echo esc_html( $current_ver ); ?></div>
      </div>
      <div>
        <?php if ( empty( $repo ) ) : ?>
          <span class="vh-status-badge vh-badge--amber">Repositório não configurado</span>
        <?php elseif ( $has_update ) : ?>
          <span class="vh-status-badge vh-badge--amber">Nova versão disponível: v<?php echo esc_html( $remote_ver ); ?></span>
        <?php else : ?>
          <span class="vh-status-badge vh-badge--green">Tema atualizado (GitHub)</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ( $has_update ) : ?>
      <div class="vh-notice vh-notice--warn" style="margin-bottom:0">
        <div>
          <strong>Nova versão v<?php echo esc_html( $remote_ver ); ?> encontrada no GitHub!</strong><br>
          Você pode atualizar agora mesmo em <a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" style="font-weight:700;color:#92400e;text-decoration:underline">Aparência → Temas</a> ou clicar no botão abaixo.
        </div>
        <a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" class="vh-btn vh-btn-primary" style="margin-left:auto;white-space:nowrap">
          Ir para Aparência → Temas
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- CONFIGURAÇÃO DO REPOSITÓRIO -->
  <div class="vh-up-card">
    <h2>Repositório GitHub</h2>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-updates' ) ); ?>">
      <?php wp_nonce_field( 'vh_updates_save', 'vh_updates_nonce' ); ?>

      <div class="vh-field">
        <label for="vh_github_repo">Identificador do Repositório (usuário/nome-do-repo)</label>
        <input type="text" id="vh_github_repo" name="vh_github_repo"
          value="<?php echo esc_attr( $repo ); ?>"
          placeholder="ex: seunome/vivahost-tema">
        <span class="vh-hint">Exemplo: se o link do seu projeto for <code>https://github.com/marciasales/vivahost-tema</code>, digite apenas <strong>marciasales/vivahost-tema</strong>.</span>
      </div>

      <div class="vh-field" style="margin-top:16px">
        <label for="vh_github_token">GitHub Personal Access Token (necessário para repositórios PRIVADOS)</label>
        <input type="password" id="vh_github_token" name="vh_github_token"
          value="<?php echo esc_attr( get_option( 'vh_github_token', '' ) ); ?>"
          placeholder="ghp_... ou github_pat_...">
        <span class="vh-hint">Permite ao WordPress verificar e baixar as atualizações do tema mesmo com repositório privado. Se o repositório for público, este campo é opcional.</span>
      </div>

      <div style="display:flex;gap:12px;align-items:center;margin-top:24px">
        <button type="submit" name="vh_save_repo" class="vh-btn vh-btn-primary">
          Salvar Repositório
        </button>
        <?php if ( ! empty( $repo ) ) : ?>
          <button type="submit" name="vh_check_now" value="1" class="vh-btn vh-btn-outline">
            Verificar Atualizações Agora
          </button>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- GUIA DE USO -->
  <div class="vh-up-card">
    <h2>Como funciona o ciclo de atualização</h2>
    <ol class="vh-step-list">
      <li>
        <strong>Envie suas alterações para o GitHub:</strong>
        <span class="vh-code">git add . && git commit -m "Novos ajustes" && git push origin main</span>
      </li>
      <li>
        <strong>Quando quiser liberar uma atualização para o WordPress, crie uma Tag de versão:</strong>
        <span class="vh-code">git tag v3.8.4 && git push origin v3.8.4</span>
      </li>
      <li>
        <strong>O GitHub compila o tema automaticamente:</strong> A GitHub Action inclusa empacota o tema limpo e cria uma Release oficial.
      </li>
      <li>
        <strong>O WordPress detecta e atualiza:</strong> Em <em>Aparência → Temas</em> aparecerá o aviso <em>"Há uma nova versão disponível"</em>. Basta clicar em <strong>Atualizar agora</strong>!
      </li>
    </ol>
  </div>

  <div style="padding-top:16px;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8">
    VivaHost Theme &mdash; Sistema nativo de atualizações GitHub via <code>pre_set_site_transient_update_themes</code> e <code>themes_api</code>.
  </div>
</div>
