<?php
$letters_tempo = [ 'T', 'E', 'M', 'P', 'O' ];
$letters_house = [ 'H', 'O', 'U', 'S', 'E' ];
?>
<section class="hero" data-tempo-act="morning" aria-label="TEMPO House">
  <div class="hero__noise" aria-hidden="true"></div>

  <div class="hero__inner">
    <p class="hero__eyebrow">Specialty Caf&eacute; &middot; Cocktail Bar &middot; Gallery &middot; Private Event Venue</p>

    <div class="hero__bleed-wrap" aria-hidden="true">
      <span class="hero__bleed-line">
        <?php foreach ( $letters_tempo as $i => $letter ) : ?>
          <span class="hero__bleed-char" style="--i:<?php echo $i; ?>"><?php echo esc_html( $letter ); ?></span>
        <?php endforeach; ?>
      </span>
      <span class="hero__bleed-line">
        <?php foreach ( $letters_house as $i => $letter ) : ?>
          <span class="hero__bleed-char" style="--i:<?php echo $i; ?>"><?php echo esc_html( $letter ); ?></span>
        <?php endforeach; ?>
      </span>
    </div>

    <p class="hero__tagline"><em>Where the city slows down.</em></p>
    <p class="hero__descriptor">District 1 &middot; Ho Chi Minh City</p>

    <div class="hero__ctas">
      <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="hero__cta-primary">Reserve a table</a>
      <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="hero__cta-secondary">Host an event</a>
    </div>
  </div>

  <div class="hero__scroll-hint" aria-hidden="true"><span></span></div>
</section>
