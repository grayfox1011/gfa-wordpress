<?php
/**
 * Preferenze separate per le memorie del tema, Google Ads e Meta Pixel.
 * I tag vengono caricati dal modulo pubblicitario solo dopo il consenso.
 *
 * @package gfa
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_head',
	function () {
		echo '<script id="gfa-consent-state">' . file_get_contents( get_template_directory() . '/assets/js/cookie-consent-state.js' ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	},
	0
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$uri = get_template_directory_uri();
		wp_enqueue_style( 'gfa-cookie-consent', $uri . '/assets/css/cookie-consent.css', array( 'gfa-main' ), GFA_VERSION );
		wp_enqueue_script( 'gfa-cookie-consent', $uri . '/assets/js/cookie-consent.js', array(), GFA_VERSION, true );
	}
);

add_action(
	'wp_footer',
	function () {
		$gfa_ads = function_exists( 'gfa_advertising_config' ) ? gfa_advertising_config() : array();
		$gfa_google = ! empty( $gfa_ads['google']['enabled'] );
		$gfa_meta = ! empty( $gfa_ads['meta']['enabled'] );
		$gfa_providers = array();
		if ( $gfa_google ) { $gfa_providers[] = 'Google Ads'; }
		if ( $gfa_meta ) { $gfa_providers[] = 'Meta Pixel'; }
		?>
		<section class="gfa-cookie-banner" id="gfa-cookie-banner" aria-labelledby="gfa-cookie-title" hidden data-lenis-prevent>
			<div class="gfa-cookie-banner__copy">
				<h2 id="gfa-cookie-title">La tua scelta sui cookie</h2>
				<p>Usiamo una memoria necessaria per ricordare la tua scelta. Puoi autorizzare separatamente le memorie delle animazioni e gli strumenti pubblicitari disponibili.</p>
				<?php if ( $gfa_providers ) : ?>
					<p>Solo con il tuo consenso possiamo usare <?php echo esc_html( implode( ' e ', $gfa_providers ) ); ?> per misurare visite e richieste di preventivo e supportare pubblicità personalizzata. Nessun tag pubblicitario viene caricato prima del consenso.</p>
				<?php else : ?>
					<p>Google Ads e Meta Pixel non sono attivi. Questa scelta riguarda solo le animazioni e non autorizza la futura attivazione dei tracciamenti.</p>
				<?php endif; ?>
				<p class="gfa-cookie-links"><a href="<?php echo esc_url( home_url( '/cookie-policy/' ) ); ?>">Cookie Policy</a><a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a></p>
			</div>
			<div class="gfa-cookie-actions">
				<button type="button" data-gfa-cookie-action="accept">Accetta tutto</button>
				<button type="button" data-gfa-cookie-action="reject">Rifiuta</button>
				<button type="button" data-gfa-cookie-action="preferences">Personalizza</button>
			</div>
		</section>
		<dialog class="gfa-cookie-dialog" id="gfa-cookie-dialog" aria-labelledby="gfa-cookie-preferences-title" aria-describedby="gfa-cookie-description" data-lenis-prevent>
			<div class="gfa-cookie-dialog__head">
				<h2 id="gfa-cookie-preferences-title">Preferenze cookie</h2>
				<button class="gfa-cookie-close" type="button" data-gfa-cookie-action="close" aria-label="Chiudi le preferenze senza salvare">&times;</button>
			</div>
			<p id="gfa-cookie-description">Puoi usare il sito e chiedere un preventivo anche rifiutando tutte le opzioni facoltative. Puoi modificare o revocare la scelta in ogni momento dal footer.</p>
			<div class="gfa-cookie-category">
				<div class="gfa-cookie-category__title"><h3>Necessari</h3><span>Sempre attivi</span></div>
				<p>Ricordano le tue scelte sui cookie per sei mesi. Non vengono usati per pubblicità o statistiche.</p>
			</div>
			<div class="gfa-cookie-category">
				<label class="gfa-cookie-switch" for="gfa-cookie-functional"><span>Esperienza di navigazione</span><input id="gfa-cookie-functional" type="checkbox" aria-describedby="gfa-cookie-functional-description"></label>
				<p id="gfa-cookie-functional-description">Evita di ripetere l'animazione iniziale e gestisce i passaggi tra pagine durante la sessione. Non sono strumenti pubblicitari.</p>
			</div>
			<div class="gfa-cookie-category<?php echo $gfa_google ? '' : ' gfa-cookie-category--inactive'; ?>">
				<label class="gfa-cookie-switch" for="gfa-cookie-google"><span>Google Ads</span><input id="gfa-cookie-google" type="checkbox" aria-describedby="gfa-cookie-google-description" <?php disabled( ! $gfa_google ); ?>></label>
				<?php if ( ! $gfa_google ) : ?><p class="gfa-cookie-provider-status">Non attivo</p><?php endif; ?>
				<p id="gfa-cookie-google-description"><?php echo $gfa_google ? 'Misura le visite e gli invii di richieste di preventivo e supporta la pubblicità personalizzata. Può comunicare a Google informazioni sulle pagine visitate e sul dispositivo. I dati compilati nel modulo non vengono inviati al tag.' : 'Questo servizio non viene caricato. Se verrà attivato, ti chiederemo una nuova scelta prima di utilizzarlo.'; ?></p>
			</div>
			<div class="gfa-cookie-category<?php echo $gfa_meta ? '' : ' gfa-cookie-category--inactive'; ?>">
				<label class="gfa-cookie-switch" for="gfa-cookie-meta"><span>Meta Pixel</span><input id="gfa-cookie-meta" type="checkbox" aria-describedby="gfa-cookie-meta-description" <?php disabled( ! $gfa_meta ); ?>></label>
				<?php if ( ! $gfa_meta ) : ?><p class="gfa-cookie-provider-status">Non attivo</p><?php endif; ?>
				<p id="gfa-cookie-meta-description"><?php echo $gfa_meta ? 'Misura le visite e gli invii di richieste di preventivo e può essere usato per pubblicità personalizzata su Facebook e Instagram. Può comunicare a Meta informazioni sulle pagine visitate e sul dispositivo. I dati compilati nel modulo non vengono inviati al pixel.' : 'Questo servizio non viene caricato. Se verrà attivato, ti chiederemo una nuova scelta prima di utilizzarlo.'; ?></p>
			</div>
			<p class="gfa-cookie-inactive">Accetta tutto abilita solo le opzioni disponibili. I servizi non attivi restano esclusi. Una modifica dei servizi pubblicitari richiederà una nuova scelta.</p>
			<p class="gfa-cookie-links"><a href="<?php echo esc_url( home_url( '/cookie-policy/' ) ); ?>">Cookie Policy</a><a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a></p>
			<div class="gfa-cookie-actions">
				<button type="button" data-gfa-cookie-action="accept">Accetta tutto</button>
				<button type="button" data-gfa-cookie-action="reject">Rifiuta</button>
				<button type="button" data-gfa-cookie-action="save">Salva preferenze</button>
			</div>
		</dialog>
		<p class="gfa-cookie-status" id="gfa-cookie-status" role="status" aria-live="polite" hidden></p>
		<?php
	},
	5
);
