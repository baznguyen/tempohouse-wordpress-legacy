<?php
function tempohouse_meta_tags() {
    $site_name    = get_bloginfo( 'name' );
    $default_desc = 'Specialty café, cocktail bar and rotating gallery at 218c Pasteur, District 3, Saigon. Private event venue for intimate hire. Ho Chi Minh City.';

    $description = $default_desc;
    if ( is_singular() ) {
        // Template-specific descriptions — keyword-rich, accurate to page content.
        // Yoast or excerpt override below; these serve as the fallback.
        if ( is_page_template( 'page-templates/page-cafe.php' ) ) {
            $description = 'Specialty coffee and matcha café at 218c Pasteur, District 3, Ho Chi Minh City. Vietnam Highlands beans, Ito En matcha from Uji, Kyoto. Flat white, Bạc Xỉu, Affogato and six matcha variations. Open daily 08:00–17:00.';
        } elseif ( is_page_template( 'page-templates/page-bar.php' ) ) {
            $description = 'Cocktail bar and wine at 218c Pasteur, District 3, Ho Chi Minh City. Classic and original cocktails — Lychee Martini, Panpan Spritz, Espresso Martini, Negroni, Manhattan. Natural wine, Champagne, house pours by the glass from 140k. Happy Hour daily 18:00–20:00. Opens nightly at 18:00.';
        } elseif ( is_page_template( 'page-templates/page-gallery.php' ) ) {
            $description = 'Free contemporary art gallery and exhibition space in Saigon. Rotating shows — Vietnamese and Southeast Asian artists. Art openings, private views, artist talks. Walk in daily, no ticket. 218c Pasteur, District 3, Ho Chi Minh City.';
        } elseif ( is_page_template( 'page-templates/page-venue.php' ) ) {
            $description = 'Private event venue at 218c Pasteur, District 3, Saigon. Gallery floor, café, bar and outdoor terrace — up to 150 guests. Available for private hire, product launches, art openings and intimate events.';
        }

        // Event posts — build a keyword-rich description from ACF fields if no excerpt.
        if ( is_singular( 'post' ) && has_tag( 'event' ) ) {
            $ev_category = get_field( 'event_category' );
            $ev_date_raw = get_field( 'event_date' );
            $ev_time     = get_field( 'event_time' );
            $ev_price    = get_field( 'event_price' );
            $ev_date_str = $ev_date_raw ? date_i18n( 'j F Y', strtotime( $ev_date_raw ) ) : '';
            $ev_meta     = implode( ', ', array_filter( [ $ev_category, $ev_date_str, $ev_time, $ev_price ] ) );
            if ( $ev_meta ) {
                $description = get_the_title() . ' at TEMPO House. ' . $ev_meta . '. 218c Pasteur, District 3, Ho Chi Minh City.';
            }
        }

        $yoast = get_post_meta( get_the_ID(), '_yoast_wpseo_metadesc', true );
        if ( $yoast ) {
            $description = $yoast;
        } elseif ( has_excerpt() ) {
            $description = wp_strip_all_tags( get_the_excerpt() );
        }
    }

    $og_title = is_singular() ? get_the_title() . ' — ' . $site_name : $site_name;
    $og_url   = is_singular() ? get_permalink() : home_url( '/' );
    $og_type  = is_singular() ? 'article' : 'website';

    $og_image = '';
    if ( is_singular() && has_post_thumbnail() ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        if ( $img ) {
            $og_image = $img[0];
        }
    }
    if ( ! $og_image ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            $img = wp_get_attachment_image_src( $logo_id, 'full' );
            if ( $img ) {
                $og_image = $img[0];
            }
        }
    }
    ?>
    <meta name="description" content="<?php echo esc_attr( $description ); ?>">
    <meta property="og:title" content="<?php echo esc_attr( $og_title ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
    <meta property="og:type" content="<?php echo esc_attr( $og_type ); ?>">
    <meta property="og:url" content="<?php echo esc_url( $og_url ); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>">
    <?php if ( $og_image ) : ?>
    <meta property="og:image" content="<?php echo esc_url( $og_image ); ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo esc_attr( $og_title ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
    <?php if ( $og_image ) : ?>
    <meta name="twitter:image" content="<?php echo esc_url( $og_image ); ?>">
    <?php endif; ?>
    <?php
}
add_action( 'wp_head', 'tempohouse_meta_tags', 1 );

function tempohouse_event_schema() {
    $is_cpt_event  = is_singular( 'event' );
    $is_post_event = is_singular( 'post' ) && has_tag( 'event' );

    if ( ! $is_cpt_event && ! $is_post_event ) {
        return;
    }

    $title   = get_the_title();
    $excerpt = wp_strip_all_tags( get_the_excerpt() );
    $url     = get_permalink();

    // Resolve image — prefer event_poster ACF field, fall back to featured image.
    $image_url = '';
    if ( $is_post_event ) {
        $poster_id = get_field( 'event_poster' );
        if ( $poster_id ) {
            $img = wp_get_attachment_image_src( $poster_id, 'full' );
            if ( $img ) {
                $image_url = $img[0];
            }
        }
    }
    if ( ! $image_url && has_post_thumbnail() ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        if ( $img ) {
            $image_url = $img[0];
        }
    }

    // Build ISO-8601 start/end dates for Posts (CPT uses a raw time string — kept as-is).
    $start_date_iso = '';
    $end_date_iso   = '';
    if ( $is_post_event ) {
        $date_raw = get_field( 'event_date' );     // Ymd from ACF date picker
        $end_raw  = get_field( 'event_end_date' ); // Ymd
        $time_str = get_field( 'event_time' );

        // Extract the opening time from strings like "20:00 – 23:00".
        $start_hm = '';
        if ( $time_str ) {
            preg_match( '/(\d{1,2}:\d{2})/', $time_str, $m );
            $start_hm = $m[1] ?? '';
        }

        if ( $date_raw ) {
            $start_date_iso = date( 'Y-m-d', strtotime( $date_raw ) ) . ( $start_hm ? 'T' . $start_hm . ':00' : '' );
        }
        if ( $end_raw ) {
            $end_date_iso = date( 'Y-m-d', strtotime( $end_raw ) );
        }
    } else {
        // Legacy CPT — event_time is a freetext string; use as startDate fallback.
        $start_date_iso = get_field( 'event_time' ) ?: '';
    }

    // Ticket / offer info (Posts only).
    $ticket_url = $is_post_event ? get_field( 'event_ticket_url' ) : '';
    $price      = $is_post_event ? get_field( 'event_price' ) : '';

    $schema = [
        '@context'             => 'https://schema.org',
        '@type'                => 'Event',
        'name'                 => $title,
        'description'          => $excerpt,
        'url'                  => $url,
        'eventStatus'          => 'https://schema.org/EventScheduled',
        'eventAttendanceMode'  => 'https://schema.org/OfflineEventAttendanceMode',
        'location'             => [
            '@type'   => 'Place',
            'name'    => 'TEMPO House',
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => '218c Pasteur Street',
                'addressLocality' => 'District 3',
                'addressRegion'   => 'Ho Chi Minh City',
                'addressCountry'  => 'VN',
            ],
        ],
        'organizer' => [
            '@type' => 'Organization',
            'name'  => 'TEMPO House',
            'url'   => home_url( '/' ),
        ],
    ];

    if ( $start_date_iso ) {
        $schema['startDate'] = $start_date_iso;
    }
    if ( $end_date_iso ) {
        $schema['endDate'] = $end_date_iso;
    }
    if ( $image_url ) {
        $schema['image'] = $image_url;
    }
    if ( $ticket_url || $price ) {
        $offer = [ '@type' => 'Offer' ];
        if ( $price ) {
            $offer['price']         = $price;
            $offer['priceCurrency'] = 'VND';
        }
        if ( $ticket_url ) {
            $offer['url'] = $ticket_url;
        }
        $schema['offers'] = $offer;
    }
    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
    </script>
    <?php
}
add_action( 'wp_head', 'tempohouse_event_schema', 5 );

function tempohouse_canonical() {
    if ( is_singular() ) {
        $url = get_permalink();
    } elseif ( is_home() || is_front_page() ) {
        $url = home_url( '/' );
    } else {
        return;
    }
    echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
}
add_action( 'wp_head', 'tempohouse_canonical' );

