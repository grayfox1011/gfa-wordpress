<?php
/**
 * Preferenze per le sole memorie facoltative del tema. Nessun tag di terze parti.
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
		?>
		<section class="gfa-cookie-banner" id="gfa-cookie-banner" aria-labelledby="gfa-cookie-title" hidden data-lenis-prevent>
			<div class="gfa-cookie-banner__copy">
				<h2 id="gfa-cookie-title">La tua scelta sui cookie</h2>
				<p>Usiamo una memoria necessaria per ricordare questa scelta. Solo con il tuo consenso memorizziamo le preferenze delle animazioni. Google Analytics, Google Ads e Meta Pixel non sono attivi.</p>
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
			<p id="gfa-cookie-description">Puoi usare il sito e chiedere un preventivo anche rifiutando le memorie facoltative.</p>
			<div class="gfa-cookie-category">
				<div class="gfa-cookie-category__title"><h3>Necessari</h3><span>Sempre attivi</span></div>
				<p>Ricordano le tue scelte sui cookie per sei mesi. Non vengono usati per pubblicità o statistiche.</p>
			</div>
			<div class="gfa-cookie-category">
				<label class="gfa-cookie-switch" for="gfa-cookie-functional"><span>Esperienza di navigazione</span><input id="gfa-cookie-functional" type="checkbox" aria-describedby="gfa-cookie-functional-description"></label>
				<p id="gfa-cookie-functional-description">Evita di ripetere l'animazione iniziale e gestisce i passaggi tra pagine durante la sessione. Non sono strumenti pubblicitari.</p>
			</div>
			<p class="gfa-cookie-inactive">Statistiche e marketing: Google Analytics, Google Ads e Meta Pixel non sono attivi. Questa scelta non autorizza la loro futura attivazione.</p>
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
