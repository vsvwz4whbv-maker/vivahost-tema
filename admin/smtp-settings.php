<?php
/**
 * VivaHost — Admin SMTP Settings Page
 * Raggiungibile da: VivaHost → Email & SMTP
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Accesso negato.' );

$saved  = isset( $_GET['saved'] ) && $_GET['saved'] === '1';
$result = get_transient( 'vh_smtp_test_result' );
if ( $result !== false ) delete_transient( 'vh_smtp_test_result' );

$default_from_name = function_exists( 'vh_get_brand_name' ) ? vh_get_brand_name() : 'VivaHost';
$saved_from_name   = get_option( 'vh_smtp_from_name', $default_from_name );
if ( in_array( $saved_from_name, [ 'My WordPress Website', 'My Blog' ], true ) ) {
	$saved_from_name = 'VivaHost';
}
?>
<style>
.vh-smtp-wrap { max-width: 980px; padding: 20px 0 60px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
.vh-admin-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; background: linear-gradient(135deg, #2b1328 0%, #681739 60%, #941c42 100%); padding: 26px 30px; border-radius: 16px; color: #fff; box-shadow: 0 10px 25px -5px rgba(148, 28, 66, 0.25); }
.vh-admin-header h1 { color: #fff !important; font-size: 1.55rem; font-weight: 800; margin: 0 0 6px; padding: 0; display: flex; align-items: center; gap: 10px; line-height: 1.2; }
.vh-admin-header p { color: rgba(255,255,255,0.85); margin: 0; font-size: 13.5px; max-width: 640px; line-height: 1.5; }
.vh-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
.vh-card h2 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 14px; }
.vh-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.vh-field { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.vh-field label { font-size: 12px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: .04em; }
.vh-field input, .vh-field select {
  font-size: 14px; padding: 10px 14px; border: 1.5px solid #cbd5e1;
  border-radius: 8px; background: #f8fafc; color: #0f172a;
  outline: none; transition: border-color .2s; width: 100%;
}
.vh-field input:focus, .vh-field select:focus { border-color: #FF5A5F; background: #fff; box-shadow: 0 0 0 3px rgba(255,90,95,0.12); }
.vh-field input[type="password"] { letter-spacing: .1em; }
.vh-hint { font-size: 12px; color: #64748b; line-height: 1.5; margin-top: 2px; }
.vh-actions { display: flex; gap: 12px; align-items: center; margin-top: 8px; flex-wrap: wrap; }
.vh-btn { display: inline-flex; align-items: center; gap: 8px; font-family: inherit; font-size: 14px; font-weight: 700; padding: 11px 24px; border-radius: 999px; border: none; cursor: pointer; transition: all .15s; }
.vh-btn-primary { background: #FF5A5F; color: #fff; box-shadow: 0 4px 12px rgba(255, 90, 95, 0.3); }
.vh-btn-primary:hover { filter: brightness(.93); transform: translateY(-1px); }
.vh-btn-outline { background: #fff; color: #334155; border: 1.5px solid #cbd5e1; }
.vh-btn-outline:hover { border-color: #94a3b8; background: #f8fafc; }
.vh-notice { padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
.vh-notice--ok   { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
.vh-notice--fail { background: #fff0f0; color: #cc0000; border: 1px solid #fca5a5; }
.vh-notice--info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.vh-status-row { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: #475569; }
.vh-status-dot { width: 10px; height: 10px; border-radius: 50%; background: #cbd5e1; }
.vh-status-dot.configured { background: #22c55e; }
@media (max-width: 782px) { .vh-row { grid-template-columns: 1fr; } }
</style>

<div class="wrap vh-smtp-wrap">
  <div class="vh-admin-header">
    <div>
      <h1>
        <span>✉️ VivaHost — Servidor de Email &amp; SMTP</span>
      </h1>
      <p>Configure o servidor SMTP para que o formulário de avaliação envie e-mails autenticados. A senha é armazenada com criptografia <code>AES-256-CBC</code> no banco de dados.</p>
    </div>
    <div>
      <span style="background:rgba(255,255,255,0.18);padding:6px 14px;border-radius:999px;font-size:12px;font-weight:700;backdrop-filter:blur(6px)">
        AES-256 Ativo
      </span>
    </div>
  </div>

  <?php if ( $saved ) : ?>
    <div class="vh-notice vh-notice--ok">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      Configurações SMTP salvas com sucesso.
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
    <div class="vh-card">
      <h2>Destinatário e Remetente dos Formulários</h2>
      <div class="vh-row">
        <div class="vh-field">
          <label for="vh_smtp_to_email">Email de destino (Recebimento de Leads)</label>
          <input type="email" id="vh_smtp_to_email" name="vh_smtp_to_email"
            value="<?php echo esc_attr( get_option( 'vh_smtp_to_email', get_option( 'admin_email' ) ) ); ?>"
            placeholder="marcia@meuvivahost.com.br">
          <span class="vh-hint">Onde chegam as solicitações de avaliação do site.</span>
        </div>
        <div class="vh-field">
          <label for="vh_smtp_from_name">Nome do remetente</label>
          <input type="text" id="vh_smtp_from_name" name="vh_smtp_from_name"
            value="<?php echo esc_attr( $saved_from_name ); ?>"
            placeholder="VivaHost">
        </div>
      </div>
      <div class="vh-field" style="max-width:460px;margin-bottom:0">
        <label for="vh_smtp_from_email">Email do remetente (From)</label>
        <input type="email" id="vh_smtp_from_email" name="vh_smtp_from_email"
          value="<?php echo esc_attr( get_option( 'vh_smtp_from_email', '' ) ); ?>"
          placeholder="noreply@meuvivahost.com.br">
        <span class="vh-hint">Use o mesmo domínio configurado no SMTP para evitar bloqueios SPF/DKIM.</span>
      </div>
    </div>

    <!-- SMTP SERVER -->
    <div class="vh-card">
      <h2>Credenciais do Servidor SMTP</h2>

      <?php
      $smtp_host     = get_option( 'vh_smtp_host', '' );
      $is_configured = ! empty( $smtp_host );
      ?>
      <div class="vh-status-row" style="margin-bottom:20px">
        <span class="vh-status-dot <?php echo $is_configured ? 'configured' : ''; ?>"></span>
        <span><?php echo $is_configured ? 'Configurado: <strong>' . esc_html( $smtp_host ) . '</strong>' : 'Não configurado — usando PHP mail() padrão.'; ?></span>
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
          <label for="vh_smtp_pass">Senha SMTP (Criptografada)</label>
          <input type="password" id="vh_smtp_pass" name="vh_smtp_pass"
            value="" placeholder="<?php echo get_option( 'vh_smtp_pass' ) ? '••••••••' : 'Senha ou App Password'; ?>"
            autocomplete="new-password">
          <span class="vh-hint">Deixe em branco para manter a senha atual.</span>
        </div>
      </div>
      <div class="vh-field" style="max-width:320px">
        <label for="vh_smtp_secure">Criptografia de Conexão</label>
        <select id="vh_smtp_secure" name="vh_smtp_secure">
          <?php
          $secure = get_option( 'vh_smtp_secure', 'tls' );
          $opts   = [ 'tls' => 'TLS (porta 587 — recomendado)', 'ssl' => 'SSL (porta 465)', 'none' => 'Nenhuma (porta 25)' ];
          foreach ( $opts as $val_opt => $label ) :
          ?>
            <option value="<?php echo esc_attr( $val_opt ); ?>" <?php selected( $secure, $val_opt ); ?>>
              <?php echo esc_html( $label ); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="vh-notice vh-notice--info" style="margin-top:16px;margin-bottom:0">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="8"/><line x1="12" y1="12" x2="12" y2="16"/></svg>
        <span>
          <strong>Gmail:</strong> ative &ldquo;Autenticação de 2 fatores&rdquo; e gere uma <a href="https://myaccount.google.com/apppasswords" target="_blank" rel="noopener">App Password</a>.
          | <strong>Outlook/Office365:</strong> host <code>smtp.office365.com</code>, porta 587, TLS.
        </span>
      </div>
    </div>

    <div class="vh-actions" style="background:#fff;border:1px solid #e2e8f0;padding:18px 28px;border-radius:14px;justify-content:space-between">
      <span style="font-size:13px;color:#64748b">As credenciais SMTP permanecem salvas mesmo após atualizar o tema via ZIP ou GitHub.</span>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        <button type="submit" name="vh_smtp_test" value="1" class="vh-btn vh-btn-outline">
          Enviar Email de Teste
        </button>
        <button type="submit" name="vh_smtp_save_only" class="vh-btn vh-btn-primary">
          Salvar Configurações SMTP
        </button>
      </div>
    </div>

  </form>
</div>
