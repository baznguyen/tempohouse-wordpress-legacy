<footer class="site-footer" role="contentinfo">

  <!-- Statement band -->
  <div class="site-footer__statement">
    <div class="site-footer__statement-inner">
      <p class="site-footer__statement-text">
        A place that treats the everyday<br>
        <em>with the same quiet reverence as art.</em>
      </p>
      <div class="site-footer__statement-meta">
        <span class="site-footer__statement-brand">TEMPO HOUSE</span>
        <span class="site-footer__divider" aria-hidden="true">&middot;</span>
        <span>EST. 2026</span>
        <span class="site-footer__divider" aria-hidden="true">&middot;</span>
        <span>H&#7891; CH&Iacute; MINH</span>
      </div>
    </div>
  </div>

  <!-- Navigation grid -->
  <div class="site-footer__nav-grid">

    <!-- Col 1: Discover -->
    <nav class="site-footer__col" aria-label="Discover">
      <p class="site-footer__col-head">Discover</p>
      <ul class="site-footer__col-links" role="list">
        <li><a href="<?php echo esc_url( home_url( '/cafe' ) ); ?>" class="site-footer__col-link">Specialty Caf&eacute;</a></li>
        <li><a href="<?php echo esc_url( home_url( '/bar' ) ); ?>" class="site-footer__col-link">Cocktail Bar</a></li>
        <li><a href="<?php echo esc_url( home_url( '/gallery' ) ); ?>" class="site-footer__col-link">Gallery</a></li>
        <li><a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="site-footer__col-link">What&rsquo;s On</a></li>
        <li><a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="site-footer__col-link">Private Events</a></li>
        <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="site-footer__col-link">Contact</a></li>
      </ul>
    </nav>

    <!-- Col 2: Visit -->
    <div class="site-footer__col">
      <p class="site-footer__col-head">Visit</p>
      <address class="site-footer__address">
        <p>218c Pasteur</p>
        <p>Xu&acirc;n Ho&agrave;, Qu&#7853;n 3</p>
        <p>H&#7891; Ch&iacute; Minh City</p>
        <p>Vietnam</p>
      </address>
      <div class="site-footer__hours">
        <div class="site-footer__hours-row">
          <span class="site-footer__hours-mode">Caf&eacute;</span>
          <span class="site-footer__hours-time">08:00 &ndash; 17:00</span>
        </div>
        <div class="site-footer__hours-row">
          <span class="site-footer__hours-mode">Bar</span>
          <span class="site-footer__hours-time">18:00 &ndash; 01:00</span>
        </div>
      </div>
      <div class="site-footer__connect">
        <p class="site-footer__col-head">Connect</p>
        <ul class="site-footer__col-links" role="list">
          <li>
            <a href="https://www.instagram.com/tempohouse.sgn" target="_blank" rel="noopener noreferrer" class="site-footer__col-link">@tempohouse.sgn</a>
          </li>
          <li>
            <a href="mailto:hello@tempohouse.com.vn" class="site-footer__col-link">hello@tempohouse.com.vn</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Col 3: Stay in the loop -->
    <div class="site-footer__col">
      <p class="site-footer__col-head">Stay in the loop</p>
      <p class="site-footer__newsletter-sub">Events, openings, and what&rsquo;s happening at TEMPO. The letter goes out when there&rsquo;s something worth saying.</p>
      <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>" class="site-footer__newsletter-btn">Subscribe to the TEMPO Letter &rarr;</a>
      <div class="site-footer__reserve-block">
        <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>" class="site-footer__reserve-btn">Reserve a Table</a>
      </div>
    </div>

  </div>

  <!-- Colophon strip -->
  <div class="site-footer__colophon">
    <div class="site-footer__colophon-inner">
      <p class="site-footer__copy">&copy; <?php echo gmdate( 'Y' ); ?> TEMPO House. All rights reserved.</p>
      <p class="site-footer__descriptor">Specialty Caf&eacute; &nbsp;&middot;&nbsp; Cocktail Bar &nbsp;&middot;&nbsp; Art Gallery &nbsp;&middot;&nbsp; Events</p>
      <div class="site-footer__legal">
        <a href="<?php echo esc_url( home_url( '/privacy-policy' ) ); ?>" class="site-footer__legal-link">Privacy Policy</a>
        <span class="site-footer__divider" aria-hidden="true">&middot;</span>
        <a href="https://ragingmonk.co" target="_blank" rel="noopener noreferrer" class="site-footer__legal-link site-footer__legal-link--credit">Designed by Raging Monk</a>
      </div>
    </div>
  </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
