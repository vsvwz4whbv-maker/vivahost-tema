<?php
/**
 * VivaHost — Admin GitHub Updates Page
 * Raggiungibile da: VivaHost → Atualizações GitHub
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Accesso negato.' );

$saved         = isset( $_GET['saved'] ) && $_GET['saved'] === '1';
$current_theme = wp_get_theme( 'vivahost-tema' );
$current_ver   = $current_theme->exists() ? $current_theme->get( 'Version' ) : VH_VER;
$repo          = get_option( 'vh_github_repo', defined( 'VH_GITHUB_REPO' ) ? VH_GITHUB_REPO : '' );

// Se l'utente clicca "Verificar agora", pulisce la cache
if ( isset( $_POST['vh_check_now'] ) && check_admin_referer( 'vh_updates_save', 'vh_updates_nonce' ) ) {
	delete_transient( 'vh_gh_update_check' );
	delete_site_transient( 'update_themes' );
}

$release = ! empty( $repo ) ? get_transient( 'vh_gh_update_check' ) : false;
if ( ! empty( $repo ) && false === $release ) {
	$headers = [
		'Accept'     => 'application/vnd.github.v3+json',
		'User-Agent' => 'WordPress-VivaHost-Updater',
	];
	$token = get_option( 'vh_github_token', defined( 'VH_GITHUB_TOKEN' ) ? VH_GITHUB_TOKEN : '' );
	if ( ! empty( $token ) ) {
		$headers['Authorization'] = 'Bearer ' . trim( $token );
	}
	$response = wp_remote_get( 'https://api.github.com/repos/' . trim( $repo, '/' ) . '/releases/latest', [
		'headers' => $headers,
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
.vh-up-wrap { max-width: 980px; padding: 20px 0 60px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
.vh-admin-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; background: linear-gradient(135deg, #2b1328 0%, #681739 60%, #941c42 100%); padding: 26px 30px; border-radius: 16px; color: #fff; box-shadow: 0 10px 25px -5px rgba(148, 28, 66, 0.25); }
.vh-admin-header h1 { color: #fff !important; font-size: 1.55rem; font-weight: 800; margin: 0 0 6px; padding: 0; display: flex; align-items: center; gap: 10px; line-height: 1.2; }
.vh-admin-header p { color: rgba(255,255,255,0.85); margin: 0; font-size: 13.5px; max-width: 640px; line-height: 1.5; }
.vh-up-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
.vh-up-card h2 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; }
.vh-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.vh-field label { font-size: 12px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .04em; }
.vh-field input {
  font-size: 14px; padding: 10px 14px; border: 1.5px solid #cbd5e1;
  border-radius: 8px; background: #f8fafc; color: #0f172a;
  outline: none; transition: border-color .2s; width: 100%; max-width: 540px;
}
.vh-field input:focus { border-color: #FF5A5F; background: #fff; box-shadow: 0 0 0 3px rgba(255,90,95,0.12); }
.vh-hint { font-size: 12px; color: #64748b; line-height: 1.5; margin-top: 4px; }
.vh-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 700; padding: 11px 24px; border-radius: 999px; border: none; cursor: pointer; text-decoration: none; transition: all .15s; }
.vh-btn-primary { background: #FF5A5F; color: #fff !important; box-shadow: 0 4px 12px rgba(255, 90, 95, 0.3); }
.vh-btn-primary:hover { filter: brightness(.93); transform: translateY(-1px); }
.vh-btn-outline { background: #fff; color: #334155 !important; border: 1.5px solid #cbd5e1; }
.vh-btn-outline:hover { border-color: #94a3b8; background: #f8fafc; }
.vh-notice { padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.vh-notice--ok   { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.vh-notice--warn { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
.vh-status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 999px; font-size: 12.5px; font-weight: 700; }
.vh-badge--green { background: #dcfce7; color: #15803d; }
.vh-badge--amber { background: #fef3c7; color: #b45309; }
</style>

<div class="wrap vh-up-wrap">
  <div class="vh-admin-header">
    <div>
      <h1>
        <span>🔄 VivaHost — Atualizações Automáticas via GitHub</span>
      </h1>
      <p>Conecte o tema ao seu repositório GitHub para verificar novas releases e atualizar o template com 1 clique sem perder nenhuma configuração do banco de dados.</p>
    </div>
    <div>
      <span style="background:rgba(255,255,255,0.18);padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;backdrop-filter:blur(6px)">
        v<?php echo esc_html( $current_ver ); ?>
      </span>
    </div>
  </div>

  <?php if ( $saved ) : ?>
    <div class="vh-notice vh-notice--ok">
      ✓ Conexão com o repositório GitHub salva e sincronizada com sucesso!
    </div>
  <?php endif; ?>

  <!-- STATUS DO TEMA -->
  <div class="vh-up-card">
    <h2>Status da Versão Instalada</h2>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <div>
        <div style="font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase">Versão ativa no WordPress</div>
        <div style="font-size:1.4rem;font-weight:800;color:#0f172a">v<?php echo esc_html( $current_ver ); ?></div>
      </div>
      <div>
        <?php if ( empty( $repo ) ) : ?>
          <span class="vh-status-badge vh-badge--amber">Repositório não configurado</span>
        <?php elseif ( $has_update ) : ?>
          <span class="vh-status-badge vh-badge--amber">Nova versão disponível: v<?php echo esc_html( $remote_ver ); ?></span>
        <?php else : ?>
          <span class="vh-status-badge vh-badge--green">✓ Tema atualizado com o GitHub</span>
        <?php endif; ?>
      </div>
    </div>

    <?php if ( $has_update ) : ?>
      <div class="vh-notice vh-notice--warn" style="margin-top:16px;margin-bottom:0">
        <div>
          <strong>Nova versão v<?php echo esc_html( $remote_ver ); ?> encontrada no GitHub!</strong><br>
          Você pode atualizar com 1 clique em <a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" style="font-weight:700;color:#92400e;text-decoration:underline">Aparência → Temas</a>.
        </div>
        <a href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>" class="vh-btn vh-btn-primary" style="margin-left:auto;white-space:nowrap">
          Atualizar Tema →
        </a>
      </div>
    <?php endif; ?>
  </div>

  <!-- CONFIGURAÇÃO DO REPOSITÓRIO -->
  <div class="vh-up-card">
    <h2>Conexão com o Repositório GitHub</h2>
    <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-updates' ) ); ?>">
      <?php wp_nonce_field( 'vh_updates_save', 'vh_updates_nonce' ); ?>

      <div class="vh-field">
        <label for="vh_github_repo">Identificador do Repositório (usuário/nome-do-repo)</label>
        <input type="text" id="vh_github_repo" name="vh_github_repo"
          value="<?php echo esc_attr( $repo ); ?>"
          placeholder="ex: vsvwz4whbv-maker/vivahost-tema">
        <span class="vh-hint">Informe apenas <strong>usuário/repositório</strong>.</span>
      </div>

      <div class="vh-field" style="margin-top:16px">
        <label for="vh_github_token">GitHub Personal Access Token (para repositórios privados)</label>
        <input type="password" id="vh_github_token" name="vh_github_token"
          value="<?php echo esc_attr( get_option( 'vh_github_token', '' ) ); ?>"
          placeholder="ghp_... ou github_pat_...">
        <span class="vh-hint">Permite ao WordPress verificar e baixar releases privadas do GitHub com segurança.</span>
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
</div>
