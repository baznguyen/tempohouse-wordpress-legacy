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
            $description = 'Contemporary art gallery in District 3, Ho Chi Minh City. Rotating exhibitions across painting, photography and installation — regional and Southeast Asian artists. Free entry, no appointment. Level 1 at TEMPO House.';
        } elseif ( is_page_template( 'page-templates/page-venue.php' ) ) {
            $description = 'Private event venue at 218c Pasteur, District 3, Saigon. Gallery floor, café, bar and outdoor terrace — up to 150 guests. Available for private hire, product launches, art openings and intimate events.';
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
    if ( ! is_singular( 'event' ) ) {
        return;
    }

    $title      = get_the_title();
    $excerpt    = wp_strip_all_tags( get_the_excerpt() );
    $url        = get_permalink();
    $event_time = get_field( 'event_time' );
    $image_url  = '';

    if ( has_post_thumbnail() ) {
        $img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        if ( $img ) {
            $image_url = $img[0];
        }
    }

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Event',
        'name'        => $title,
        'description' => $excerpt,
        'startDate'   => $event_time ?: '',
        'location'    => [
            '@type'   => 'Place',
            'name'    => 'TEMPO House',
            'address' => '218c Pasteur Street, District 3, Ho Chi Minh City, Vietnam',
        ],
        'url'         => $url,
    ];

    if ( $image_url ) {
        $schema['image'] = $image_url;
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

