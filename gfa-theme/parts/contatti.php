<?php
/**
 * Recapiti dal pannello Dati GFA.
 *
 * @package gfa
 */
?>
			<ul class="contact-lines">
				<li><?php if ( gfa_opt( 'telefono' ) ) : ?><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', gfa_opt( 'telefono' ) ) ); ?>"><?php echo esc_html( gfa_opt( 'telefono' ) ); ?></a><?php else : gfa_todo( 'telefono principale' ); endif; ?></li>
				<li><a href="<?php echo esc_url( 'mailto:' . gfa_opt( 'email' ) ); ?>"><?php echo esc_html( gfa_opt( 'email' ) ); ?></a></li>
				<?php if ( gfa_opt( 'indirizzo_rapallo' ) ) : ?><li>Rapallo: <?php echo esc_html( gfa_opt( 'indirizzo_rapallo' ) ); ?></li><?php endif; ?>
				<?php if ( gfa_opt( 'whatsapp' ) ) : ?>
					<li><a href="<?php echo esc_url( 'https://wa.me/' . preg_replace( '/\D/', '', gfa_opt( 'whatsapp' ) ) ); ?>">WhatsApp</a></li>
				<?php endif; ?>
			</ul>
