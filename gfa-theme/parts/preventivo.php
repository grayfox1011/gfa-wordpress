<?php
/**
 * Modulo preventivo in tre passi.
 *
 * Gli ID dei campi sono unici per ogni modulo della pagina, così due moduli non si confondono;
 * i nomi dei campi inviati restano gli stessi. tools/build-prototipo.py cerca "data-quote-form ".
 *
 * @package gfa
 */
$gfa_state   = isset( $_GET['preventivo'] ) ? sanitize_key( wp_unslash( $_GET['preventivo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- solo l'esito da mostrare.
$gfa_uid     = wp_unique_id( 'gfa-q' );
$gfa_privacy = get_privacy_policy_url();
?>

		<form class="form" data-quote-form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-msg-check="<?php /* translators: %s: il campo da controllare, per esempio "l'email". */ echo esc_attr__( 'Controlla %s per continuare.', 'gfa' ); ?>" data-msg-sending="<?php echo esc_attr__( 'Invio in corso…', 'gfa' ); ?>">
			<input type="hidden" name="action" value="gfa_preventivo">
			<?php wp_nonce_field( 'gfa_quote', 'gfa_quote_nonce' ); ?>
			<div class="hp" aria-hidden="true"><label for="<?php echo esc_attr( $gfa_uid ); ?>-website">Sito web</label><input type="text" id="<?php echo esc_attr( $gfa_uid ); ?>-website" name="gfa_website" tabindex="-1" autocomplete="off"></div>

			<?php if ( 'inviato' === $gfa_state ) : ?>
				<p class="form__notice form__notice--ok" role="status"><strong>Richiesta inviata.</strong> Ti ricontattiamo al più presto all'email che hai indicato.</p>
				<p><a href="<?php echo esc_url( remove_query_arg( 'preventivo' ) ); ?>#preventivo"><?php esc_html_e( 'Invia un\'altra richiesta', 'gfa' ); ?></a></p>
		</form>
				<?php
				return;
			elseif ( 'errore' === $gfa_state ) : ?>
				<p class="form__notice form__notice--error" role="alert">Non siamo riusciti a inviare la richiesta. Controlla nome, email e consenso privacy, oppure chiamaci.</p>
			<?php endif; ?>

			<ol class="form__steps" aria-label="Passi del modulo"><li>1 · Servizio</li><li>2 · Zona e quantità</li><li>3 · Contatti</li></ol>

			<fieldset data-step="1">
				<legend>Cosa ti serve?</legend>
				<div class="choices">
					<label class="choice"><input type="radio" name="gfa_servizio" id="<?php echo esc_attr( $gfa_uid ); ?>-s1" value="Volantinaggio" required data-label="il servizio"><span>Volantinaggio<small>in cassetta, negozi, eventi</small></span></label>
					<label class="choice"><input type="radio" name="gfa_servizio" id="<?php echo esc_attr( $gfa_uid ); ?>-s2" value="Grafica, stampa e distribuzione"><span>Tutto incluso<small>grafica, stampa e distribuzione</small></span></label>
					<label class="choice"><input type="radio" name="gfa_servizio" id="<?php echo esc_attr( $gfa_uid ); ?>-s3" value="Promozione evento"><span>Promuovere un evento<small>locandine, volantini, QR</small></span></label>
					<label class="choice"><input type="radio" name="gfa_servizio" id="<?php echo esc_attr( $gfa_uid ); ?>-s4" value="Stampa e grafica"><span>Solo stampa o grafica<small>volantini, banner, gadget</small></span></label>
				</div>
			</fieldset>

			<fieldset data-step="2">
				<legend>Dove e quanto?</legend>
				<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-zona">Comune o zona</label><input id="<?php echo esc_attr( $gfa_uid ); ?>-zona" name="gfa_zona" type="text" required data-label="il comune o la zona" placeholder="Es. Rapallo e Santa Margherita"></div>
				<div class="field--row">
					<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-quantita">Quante copie?</label>
						<select id="<?php echo esc_attr( $gfa_uid ); ?>-quantita" name="gfa_quantita"><option value="Non lo so, consigliatemi">Non lo so, consigliatemi</option><option>Fino a 5.000</option><option>5.000–10.000</option><option>10.000–20.000</option><option>Oltre 20.000</option></select>
					</div>
					<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-periodo">Quando?</label><input id="<?php echo esc_attr( $gfa_uid ); ?>-periodo" name="gfa_periodo" type="text" placeholder="Es. prima settimana di novembre"></div>
				</div>
				<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-dettagli">Cosa promuovi? <span class="hint">(facoltativo)</span></label><textarea id="<?php echo esc_attr( $gfa_uid ); ?>-dettagli" name="gfa_dettagli" rows="3" placeholder="Apertura, offerta, evento…"></textarea></div>
			</fieldset>

			<fieldset data-step="3">
				<legend>Come ti ricontattiamo?</legend>
				<div class="field--row">
					<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-nome">Nome</label><input id="<?php echo esc_attr( $gfa_uid ); ?>-nome" name="gfa_nome" type="text" required autocomplete="name" data-label="il nome"></div>
					<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-azienda">Azienda o ente <span class="hint">(facoltativo)</span></label><input id="<?php echo esc_attr( $gfa_uid ); ?>-azienda" name="gfa_azienda" type="text" autocomplete="organization"></div>
				</div>
				<div class="field--row">
					<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-email">Email</label><input id="<?php echo esc_attr( $gfa_uid ); ?>-email" name="gfa_email" type="email" required autocomplete="email" data-label="l'email"></div>
					<div class="field"><label for="<?php echo esc_attr( $gfa_uid ); ?>-telefono">Telefono <span class="hint">(facoltativo)</span></label><input id="<?php echo esc_attr( $gfa_uid ); ?>-telefono" name="gfa_telefono" type="tel" autocomplete="tel"></div>
				</div>
				<?php // Senza un'informativa pubblicata il link porterebbe a una pagina inesistente: si vede il segnaposto. ?>
				<label class="check" for="<?php echo esc_attr( $gfa_uid ); ?>-privacy"><input type="checkbox" id="<?php echo esc_attr( $gfa_uid ); ?>-privacy" name="gfa_privacy" value="1" required data-label="il consenso privacy"><span>Ho letto l'<?php if ( $gfa_privacy ) : ?><a href="<?php echo esc_url( $gfa_privacy ); ?>">informativa privacy</a><?php else : ?><mark class="todo">informativa privacy da pubblicare</mark><?php endif; ?> e accetto di essere ricontattato per questa richiesta.</span></label>
			</fieldset>

			<div class="form__ok" hidden tabindex="-1" role="status"><strong>Richiesta pronta.</strong> <span>Questa è una dimostrazione: non è stato inviato nulla. Sul sito la richiesta arriva via email a GFA.</span></div>
			<p class="form__error" aria-live="polite" data-step-error></p>
			<div class="form__nav">
				<button class="btn btn--ghost" type="button" data-back hidden>← Indietro</button>
				<button class="btn btn--blue" type="button" data-next hidden>Avanti →</button>
				<button class="btn btn--primary" type="submit" data-send autocomplete="off">Invia la richiesta</button>
			</div>
		</form>
