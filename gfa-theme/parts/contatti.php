<?php
/**
 * Recapiti dal pannello Dati GFA.
 *
 * @package gfa
 */
?>
			<ul class="contact-lines">
				<li><?php gfa_value( 'telefono', 'telefono principale' ); ?></li>
				<li><?php echo esc_html( gfa_opt( 'email' ) ); ?></li>
				<?php if ( gfa_opt( 'whatsapp' ) ) : ?>
					<li><a href="<?php echo esc_url( 'https://wa.me/' . preg_replace( '/\D/', '', gfa_opt( 'whatsapp' ) ) ); ?>">WhatsApp</a></li>
				<?php endif; ?>
			</ul>
