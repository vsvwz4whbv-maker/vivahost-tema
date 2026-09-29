<?php
/**
 * Template Part: Footer del sito VivaHost
 * Incluso da front-page.php e single.php via get_template_part().
 *
 * @package VivahostChild
 */
defined( 'ABSPATH' ) || exit;

$logo_id  = get_theme_mod( 'custom_logo' );
$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : VH_URL . '/assets/images/vivahost-logo.png';
$email    = vh_mod( 'footer_email', 'marcia@meuvivahost.com.br' );
$home_url = esc_url( home_url( '/' ) );
?>
<footer class="vivahost-footer bg-neutral text-neutral-content pt-16 pb-12 border-t border-white/10" aria-label="Rodapé">
  <div class="vivahost-footer-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="footer-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 sm:gap-12 pb-12 border-b border-white/10">

      <div class="footer-col-brand">
        <a href="<?php echo $home_url; ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
          <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="brand-logo-footer h-11 w-auto object-contain mb-4" width="160" height="68" loading="lazy">
        </a>
        <p class="footer-tagline text-xs sm:text-sm text-neutral-content/70 leading-relaxed mb-6" data-vh="footer_tagline">
          <?php echo esc_html( vh_mod( 'footer_tagline', 'VivaHost — Superhost Airbnb em Salvador, Bahia. Gestão profissional de aluguel por temporada com 9 anos de experiência.' ) ); ?>
        </p>
        <div class="footer-social flex items-center gap-3">
          <?php if ( $ig = vh_mod( 'footer_instagram', 'https://www.instagram.com/vivahostbahia/' ) ) : ?>
            <a href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="Instagram" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
              <svg viewBox="0 0 24 24" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
          <?php endif; ?>
          <a href="<?php echo esc_url( vh_wa_url( 'Olá! Gostaria de mais informações sobre a VivaHost.' ) ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
            <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.103 1.523 5.824L.057 23.5a.5.5 0 0 0 .61.61l5.734-1.46A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.5-5.24-1.376l-.375-.213-3.882.99.998-3.795-.232-.387A9.946 9.946 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
          </a>
          <?php if ( $ab = vh_mod( 'footer_airbnb', 'https://www.airbnb.com.br/users/show/148412228' ) ) : ?>
            <a href="<?php echo esc_url( $ab ); ?>" target="_blank" rel="noopener" aria-label="Airbnb" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
              <svg viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.8 14.4c-.2.6-.7 1.1-1.3 1.4-.3.1-.7.2-1 .2-.8 0-1.5-.4-2.1-.9l-.1-.1-.1.1c-.6.5-1.3.9-2.1.9-.4 0-.7-.1-1-.2-.6-.3-1.1-.8-1.3-1.4-.1-.4-.1-.8 0-1.2.2-.6.5-1.2.9-1.8l2.4-3.4c.2-.3.5-.4.8-.4s.6.1.8.4l2.4 3.4c.4.6.7 1.2.9 1.8.2.4.1.8 0 1.2z"/></svg>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <p class="footer-col-heading font-bold text-sm text-white uppercase tracking-wider mb-4">Navegação</p>
        <ul class="footer-links space-y-2.5 text-xs sm:text-sm text-neutral-content/80">
          <li><a href="<?php echo $home_url; ?>#como-funciona" class="hover:text-white transition-colors">Como funciona</a></li>
          <li><a href="<?php echo $home_url; ?>#servicos" class="hover:text-white transition-colors">Serviços</a></li>
          <li><a href="<?php echo $home_url; ?>#imoveis" class="hover:text-white transition-colors">Imóveis</a></li>
          <li><a href="<?php echo $home_url; ?>#sobre-nos" class="hover:text-white transition-colors">Sobre a <?php echo esc_html( vh_mod( 'host_name', 'Marcia' ) ); ?></a></li>
          <li><a href="<?php echo $home_url; ?>#blog" class="hover:text-white transition-colors">Blog</a></li>
          <li><a href="<?php echo $home_url; ?>#contato" class="hover:text-white transition-colors">Fale conosco</a></li>
        </ul>
      </div>

      <div>
        <p class="footer-col-heading font-bold text-sm text-white uppercase tracking-wider mb-4">Serviços</p>
        <ul class="footer-links space-y-2.5 text-xs sm:text-sm text-neutral-content/80">
          <?php
          $svc_titles = [ 'Fotografia profissional', 'Precificação dinâmica', 'Check-in & Check-out', 'Limpeza & Amenidades', 'Suporte 24 horas', 'Relatórios mensais' ];
          for ( $i = 1; $i <= 6; $i++ ) :
          ?>
            <li><a href="<?php echo $home_url; ?>#servicos" class="hover:text-white transition-colors"><?php echo esc_html( vh_mod( "service_{$i}_title", '' ) ?: $svc_titles[ $i - 1 ] ); ?></a></li>
          <?php endfor; ?>
        </ul>
      </div>

      <div>
        <p class="footer-col-heading font-bold text-sm text-white uppercase tracking-wider mb-4">Contato</p>
        <a href="<?php echo esc_url( vh_wa_url( 'Olá! Gostaria de mais informações sobre a VivaHost.' ) ); ?>" target="_blank" rel="noopener" class="footer-wa-btn btn btn-sm bg-success hover:bg-success/90 text-white rounded-full px-5 flex items-center gap-2 mb-4 w-fit shadow-sm">
          <svg viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.554 4.103 1.523 5.824L.057 23.5a.5.5 0 0 0 .61.61l5.734-1.46A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.694-.5-5.24-1.376l-.375-.213-3.882.99.998-3.795-.232-.387A9.946 9.946 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
          Falar pelo WhatsApp
        </a>

        <?php if ( $email ) : ?>
          <div class="footer-contact-item flex items-center gap-2 text-xs text-neutral-content/70 mb-2">
            <svg viewBox="0 0 24 24" class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <a href="mailto:<?php echo esc_attr( $email ); ?>" class="hover:text-white transition-colors"><?php echo esc_html( $email ); ?></a>
          </div>
        <?php endif; ?>

        <?php if ( $city = vh_mod( 'footer_city', 'Salvador, Bahia e região metropolitana' ) ) : ?>
          <div class="footer-contact-item flex items-center gap-2 text-xs text-neutral-content/70 mb-2">
            <svg viewBox="0 0 24 24" class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <span data-vh="footer_city"><?php echo esc_html( $city ); ?></span>
          </div>
        <?php endif; ?>

        <?php if ( $hours = vh_mod( 'footer_hours', '7 dias por semana, das 8h às 20h' ) ) : ?>
          <div class="footer-contact-item flex items-center gap-2 text-xs text-neutral-content/70">
            <svg viewBox="0 0 24 24" class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span data-vh="footer_hours"><?php echo esc_html( $hours ); ?></span>
          </div>
        <?php endif; ?>
      </div>

    </div>

    <div class="footer-bottom pt-6 flex flex-col items-stretch gap-4 text-xs text-neutral-content/60">
      <div class="footer-company-info pb-4 border-b border-white/[0.06] text-center md:text-left flex flex-wrap items-center justify-center md:justify-start gap-x-2 gap-y-1">
        <span class="footer-razao font-semibold text-neutral-content/80" data-vh="footer_razao"><?php echo esc_html( vh_mod( 'footer_razao', 'Viva Host LTDA' ) ); ?></span>
        <span class="footer-sep">·</span>
        <span class="footer-cnpj" data-vh="footer_cnpj">CNPJ: <?php echo esc_html( vh_mod( 'footer_cnpj', '27.447.686/0001-10' ) ); ?></span>
        <span class="footer-sep">·</span>
        <span class="footer-address">
          <?php echo esc_html( vh_mod( 'footer_address', 'Avenida Tancredo Neves, 002539' ) ); ?>
          <?php if ( $comp = vh_mod( 'footer_complement', 'Edif CEO Salvador Shopping Torre Londres Sala 2609' ) ) : ?>— <?php echo esc_html( $comp ); ?><?php endif; ?>
          <?php if ( $bairro = vh_mod( 'footer_bairro', 'Caminho das Árvores' ) ) : ?>— <?php echo esc_html( $bairro ); ?><?php endif; ?>
          <?php if ( $cep = vh_mod( 'footer_cep', '41820-021' ) ) : ?>— CEP <?php echo esc_html( $cep ); ?><?php endif; ?>
        </span>
      </div>
      <div class="footer-bottom-row flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
        <p class="footer-copy m-0" data-vh="footer_copyright">
          <?php echo esc_html( vh_mod( 'footer_copyright', '© 2026 VivaHost. Todos os direitos reservados.' ) ); ?>
        </p>
        <nav class="footer-bottom-links flex items-center gap-6">
          <a href="<?php echo esc_url( home_url( '/privacidade/' ) ); ?>" class="hover:text-white transition-colors">Privacidade</a>
          <a href="<?php echo esc_url( home_url( '/termos/' ) ); ?>" class="hover:text-white transition-colors">Termos</a>
        </nav>
      </div>
    </div>
  </div>
</footer>
