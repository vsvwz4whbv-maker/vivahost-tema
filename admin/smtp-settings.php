<?php
/**
 * VivaHost — Admin SMTP Settings Page
 * Raggiungibile da: Impostazioni → VivaHost Email
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Accesso negato.' );

$saved  = isset( $_GET['saved'] ) && $_GET['saved'] === '1';
$result = get_transient( 'vh_smtp_test_result' );
if ( $result !== false ) delete_transient( 'vh_smtp_test_result' );
?>
<style>
.vh-smtp-wrap { max-width: 760px; padding: 24px 0 60px; }
.vh-smtp-wrap h1 { display: flex; align-items: center; gap: 10px; font-size: 1.4rem; margin-bottom: 6px; }
.vh-smtp-intro { color: #555; font-size: 14px; margin-bottom: 32px; line-height: 1.6; max-width: 600px; }
.vh-smtp-wrap .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px; margin-bottom: 24px; }
.vh-smtp-wrap .card h2 { font-size: 1rem; font-weight: 600; color: #1a1a2e; margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 14px; }
.vh-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.vh-field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
.vh-field label { font-size: 12px; font-weight: 600; color: #333; text-transform: uppercase; letter-spacing: .04em; }
.vh-field input, .vh-field select {
  font-size: 14px; padding: 9px 12px; border: 1.5px solid #e2e8f0;
  border-radius: 8px; background: #F9FAFB; color: #111;
  outline: none; transition: border-color .2s; width: 100%;
}
.vh-field input:focus, .vh-field select:focus { border-color: #FF5A5F; background: #fff; }
.vh-field input[type="password"] { letter-spacing: .1em; }
.vh-hint { font-size: 11px; color: #999; line-height: 1.5; margin-top: 2px; }
.vh-actions { display: flex; gap: 12px; align-items: center; margin-top: 8px; flex-wrap: wrap; }
.vh-btn { display: inline-flex; align-items: center; gap: 6px; font-family: inherit; font-size: 13.5px; font-weight: 600; padding: 9px 20px; border-radius: 999px; border: none; cursor: pointer; transition: filter .15s, transform .15s; }
.vh-btn-primary { background: #FF5A5F; color: #fff; }
.vh-btn-primary:hover { filter: brightness(.92); }
.vh-btn-outline { background: transparent; color: #333; border: 1.5px solid #ddd; }
.vh-btn-outline:hover { border-color: #aaa; }
.vh-notice { padding: 12px 18px; border-radius: 10px; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
.vh-notice--ok   { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
.vh-notice--fail { background: #FFF0F0; color: #CC0000; border: 1px solid #FCA5A5; }
.vh-notice--info { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
.vh-status-row { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #666; }
.vh-status-dot { width: 8px; height: 8px; border-radius: 50%; background: #ccc; }
.vh-status-dot.configured { background: #22C55E; }
.vh-section-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #999; margin: 24px 0 14px; border-bottom: 1px solid #f0f0f0; padding-bottom: 8px; }
.vh-suite-nav { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 22px; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; }
.vh-suite-link { display: inline-flex; align-items: center; gap: 7px; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; text-decoration: none; color: #475569; background: #fff; border: 1px solid #cbd5e1; transition: all .15s; }
.vh-suite-link:hover { color: #0f172a; border-color: #94a3b8; background: #f8fafc; }
.vh-suite-link.active { background: #0f172a; color: #fff; border-color: #0f172a; }
</style>

<div class="wrap vh-smtp-wrap">
  <div class="vh-suite-nav">
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-seo' ) ); ?>" class="vh-suite-link">🔍 SEO &amp; Opzioni Generali</a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-smtp' ) ); ?>" class="vh-suite-link active">✉️ Email &amp; SMTP</a>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-updates' ) ); ?>" class="vh-suite-link">🔄 Atualizações GitHub</a>
    <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="vh-suite-link">🎨 Personalizar Tema</a>
  </div>

  <h1>
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FF5A5F" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    VivaHost — Email &amp; SMTP
  </h1>
  <p class="vh-smtp-intro">
    Configure o servidor SMTP para que os formulários de contato do site enviem e-mails confiáveis. As credenciais são salvas com criptografia AES-256-CBC no banco de dados WordPress.
  </p>

  <?php if ( $saved ) : ?>
    <div class="vh-notice vh-notice--ok">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      Configurações salvas com sucesso.
    </div>
  <?php endif; ?>

  <?php if ( $result === 'ok' ) : ?>
    <div class="vh-notice vh-notice--ok">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      ✓ Email de teste enviado com sucesso! Verifique a caixa de entrada.
    </div>
  <?php elseif ( $result === 'fail' ) : ?>
    <div class="vh-notice vh-notice--fail">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      ✗ Falha ao enviar email de teste. Verifique as configurações SMTP e tente novamente.
    </div>
  <?php endif; ?>

  <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=vivahost-smtp' ) ); ?>">
    <?php wp_nonce_field( 'vh_smtp_save', 'vh_smtp_nonce' ); ?>

    <!-- DESTINATÁRIO -->
    <div class="card">
      <h2>Destinatário dos formulários</h2>
      <div class="vh-row">
        <div class="vh-field">
          <label for="vh_smtp_to_email">Email de destino</label>
          <input type="email" id="vh_smtp_to_email" name="vh_smtp_to_email"
            value="<?php echo esc_attr( get_option( 'vh_smtp_to_email', get_option( 'admin_email' ) ) ); ?>"
            placeholder="marcia@meuvivahost.com.br">
          <span class="vh-hint">Onde chegam as solicitações de avaliação do site.</span>
        </div>
        <div class="vh-field">
          <label for="vh_smtp_from_name">Nome do remetente</label>
          <input type="text" id="vh_smtp_from_name" name="vh_smtp_from_name"
            value="<?php echo esc_attr( get_option( 'vh_smtp_from_name', get_bloginfo( 'name' ) ) ); ?>"
            placeholder="VivaHost">
        </div>
      </div>
      <div class="vh-field" style="max-width:360px">
        <label for="vh_smtp_from_email">Email do remetente (From)</label>
        <input type="email" id="vh_smtp_from_email" name="vh_smtp_from_email"
          value="<?php echo esc_attr( get_option( 'vh_smtp_from_email', '' ) ); ?>"
          placeholder="noreply@meuvivahost.com.br">
        <span class="vh-hint">Use o mesmo domínio configurado no SMTP para evitar spam.</span>
      </div>
    </div>

    <!-- SMTP SERVER -->
    <div class="card">
      <h2>Servidor SMTP</h2>

      <?php
      $smtp_host = get_option( 'vh_smtp_host', '' );
      $is_configured = ! empty( $smtp_host );
      ?>
      <div class="vh-status-row" style="margin-bottom:20px">
        <span class="vh-status-dot <?php echo $is_configured ? 'configured' : ''; ?>"></span>
        <span><?php echo $is_configured ? "Configurado: <strong>{$smtp_host}</strong>" : 'Não configurado — usando PHP mail() padrão (pode cair no spam).'; ?></span>
      </div>

      <div class="vh-row">
        <div class="vh-field">
          <label for="vh_smtp_host">Host SMTP</label>
          <input type="text" id="vh_smtp_host" name="vh_smtp_host"
            value="<?php echo esc_attr( get_option( 'vh_smtp_host', '' ) ); ?>"
            placeholder="smtp.gmail.com">
        </div>
        <div class="vh-field">
          <label for="vh_smtp_port">Porta</label>
          <input type="number" id="vh_smtp_port" name="vh_smtp_port"
            value="<?php echo esc_attr( get_option( 'vh_smtp_port', '587' ) ); ?>"
            placeholder="587" min="1" max="65535">
        </div>
      </div>
      <div class="vh-row">
        <div class="vh-field">
          <label for="vh_smtp_user">Usuário SMTP</label>
          <input type="text" id="vh_smtp_user" name="vh_smtp_user"
            value="<?php echo esc_attr( get_option( 'vh_smtp_user', '' ) ); ?>"
            placeholder="seu@email.com" autocomplete="username">
        </div>
        <div class="vh-field">
          <label for="vh_smtp_pass">Senha SMTP</label>
          <input type="password" id="vh_smtp_pass" name="vh_smtp_pass"
            value="" placeholder="<?php echo get_option('vh_smtp_pass') ? '••••••••' : 'Senha ou App Password'; ?>"
            autocomplete="new-password">
          <span class="vh-hint">Deixe em branco para manter a senha atual.</span>
        </div>
      </div>
      <div class="vh-field" style="max-width:240px">
        <label for="vh_smtp_secure">Criptografia</label>
        <select id="vh_smtp_secure" name="vh_smtp_secure">
          <?php
          $secure = get_option( 'vh_smtp_secure', 'tls' );
          $opts = [ 'tls' => 'TLS (porta 587 — recomendado)', 'ssl' => 'SSL (porta 465)', 'none' => 'Nenhuma (porta 25)' ];
          foreach ( $opts as $val => $label ) :
          ?>
            <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $secure, $val ); ?>>
              <?php echo esc_html( $label ); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="vh-notice vh-notice--info" style="margin-top:16px">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="8"/><line x1="12" y1="12" x2="12" y2="16"/></svg>
        <span>
          <strong>Gmail:</strong> ative &ldquo;Autenticação de 2 fatores&rdquo; e crie uma <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">App Password</a> para usar aqui.
          | <strong>Outlook/Hotmail:</strong> host <code>smtp.office365.com</code>, porta 587, TLS.
          | <strong>Brevo (ex-Sendinblue):</strong> gratuito até 300 emails/dia.
        </span>
      </div>
    </div>

    <div class="vh-actions">
      <button type="submit" name="vh_smtp_save_only" class="vh-btn vh-btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Salvar configurações
      </button>
      <button type="submit" name="vh_smtp_test" value="1" class="vh-btn vh-btn-outline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        Salvar &amp; Enviar email de teste
      </button>
    </div>

  </form>

  <div style="margin-top:40px;padding-top:24px;border-top:1px solid #f0f0f0">
    <p style="font-size:12px;color:#aaa">
      VivaHost Child Theme v<?php echo defined('VH_VER') ? VH_VER : '3'; ?> &mdash;
      As configurações SMTP são armazenadas com criptografia no banco de dados WordPress.
      <a href="<?php echo admin_url('customize.php'); ?>" style="color:#FF5A5F;font-weight:600">Editar textos e cores no Personalizar →</a>
    </p>
  </div>
</div>
