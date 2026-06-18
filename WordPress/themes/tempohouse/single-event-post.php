<?php
/**
 * Single event post template.
 *
 * Loaded automatically for any standard Post tagged 'event' via the
 * template_include filter in inc/cpt-events.php.
 *
 * Visual anatomy:
 *   1. Full-bleed poster hero with title, category, status badge
 *   2. Horizontal meta bar (date · time · recurrence · admission · location)
 *   3. Gutenberg content area (max 720px, editorial centered column)
 *   4. CTA block (tickets / reserve / host your own)
 *   5. Related upcoming events strip (3 cards, same card design)
 */
get_header();

// ── ACF fields ────────────────────────────────────────────────────────────
$category   = get_field( 'event_category' )  ?: '';
$date_raw   = get_field( 'event_date' );            // Ymd string from ACF date picker
$end_raw    = get_field( 'event_end_date' );
$time       = get_field( 'event_time' )       ?: '';
$recurrence = get_field( 'event_recurrence' ) ?: '';
$interior   = get_field( 'event_interior' )   ?: 'dark';
$poster_id  = get_field( 'event_poster' );
$price      = get_field( 'event_price' )      ?: '';
$ticket_url = get_field( 'event_ticket_url' ) ?: '';
$media_type = get_field( 'event_media_type' ) ?: 'none';
$media_id   = get_field( 'event_media_id' )   ?: 0;

$is_active   = has_tag( 'active' );
$is_upcoming = ! $is_active && $date_raw && $date_raw >= date( 'Ymd' );

// Format dates for display.
$date_display = $date_raw ? date_i18n( 'l j F Y', strtotime( $date_raw ) ) : '';
$end_display  = $end_raw  ? date_i18n( 'j F Y', strtotime( $end_raw ) )  : '';

// Resolve poster image: ACF event_poster → featured image fallback.
$poster_url = '';
$poster_alt = get_the_title();
if ( $poster_id ) {
    $img = wp_get_attachment_image_src( $poster_id, 'event-poster' );
    if ( $img ) {
        $poster_url = $img[0];
        $poster_alt = get_post_meta( $poster_id, '_wp_attachment_image_alt', true ) ?: $poster_alt;
    }
}
if ( ! $poster_url && has_post_thumbnail() ) {
    $poster_url = get_the_post_thumbnail_url( get_the_ID(), 'event-poster' );
    $poster_alt = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) ?: $poster_alt;
}

// Hero meta line: "Sat 12 July 2025 · 20:00 – 23:00"
$hero_meta = implode( ' · ', array_filter( [ $date_display, $time ] ) );
?>

<main class="event-single" id="main" role="main" data-interior="<?php echo esc_attr( $interior ); ?>">

<!-- ── 1. Hero ────────────────────────────────────────────────────────── -->
<div class="event-single__hero">

    <?php if ( $poster_url ) : ?>
        <img
            class="event-single__poster"
            src="<?php echo esc_url( $poster_url ); ?>"
            alt="<?php echo esc_attr( $poster_alt ); ?>"
            loading="eager"
            fetchpriority="high"
            decoding="async">
    <?php else : ?>
        <div class="event-single__poster-bg" data-interior="<?php echo esc_attr( $interior ); ?>"></div>
    <?php endif; ?>

    <div class="event-single__hero-overlay" aria-hidden="true"></div>

    <div class="event-single__hero-content">
        <div class="container">

            <?php if ( $is_active ) : ?>
                <span class="event-single__badge event-single__badge--active">Happening Now</span>
            <?php elseif ( $is_upcoming ) : ?>
                <span class="event-single__badge event-single__badge--upcoming">Coming Up</span>
            <?php endif; ?>

            <?php if ( $category ) : ?>
                <p class="event-single__category"><?php echo esc_html( $category ); ?></p>
            <?php endif; ?>

            <h1 class="event-single__title"><?php the_title(); ?></h1>

            <?php if ( $hero_meta ) : ?>
                <p class="event-single__hero-meta"><?php echo esc_html( $hero_meta ); ?></p>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ── 2. Meta bar ────────────────────────────────────────────────────── -->
<?php if ( $date_display || $time || $recurrence || $price ) : ?>
<div class="event-single__meta-bar" role="complementary" aria-label="Event details">
    <div class="container">

        <?php if ( $date_display ) : ?>
        <div class="event-single__meta-item">
            <p class="event-single__meta-label">Date</p>
            <p class="event-single__meta-value">
                <?php echo esc_html( $date_display );
                if ( $end_display ) {
                    echo ' &ndash; ' . esc_html( $end_display );
                } ?>
            </p>
        </div>
        <?php endif; ?>

        <?php if ( $time ) : ?>
        <div class="event-single__meta-item">
            <p class="event-single__meta-label">Time</p>
            <p class="event-single__meta-value"><?php echo esc_html( $time ); ?></p>
        </div>
        <?php endif; ?>

        <?php if ( $recurrence ) : ?>
        <div class="event-single__meta-item">
            <p class="event-single__meta-label">Frequency</p>
            <p class="event-single__meta-value"><?php
                $labels = [
                    'one-time' => 'One Night Only',
                    'weekly'   => 'Weekly',
                    'monthly'  => 'Monthly',
                    'ongoing'  => 'Ongoing',
                ];
                echo esc_html( $labels[ $recurrence ] ?? ucfirst( $recurrence ) );
            ?></p>
        </div>
        <?php endif; ?>

        <?php if ( $price ) : ?>
        <div class="event-single__meta-item">
            <p class="event-single__meta-label">Admission</p>
            <p class="event-single__meta-value"><?php echo esc_html( $price ); ?></p>
        </div>
        <?php endif; ?>

        <div class="event-single__meta-item">
            <p class="event-single__meta-label">Location</p>
            <p class="event-single__meta-value">218c Pasteur, Quận 3<br><span class="event-single__meta-note">TEMPO House, Ho Chi Minh City</span></p>
        </div>

    </div>
</div>
<?php endif; ?>

<!-- ── 3. Body content ───────────────────────────────────────────────── -->
<div class="event-single__body">
    <div class="event-single__content-wrap">

        <div class="event-single__content">
            <?php the_content(); ?>
        </div>

        <!-- CTA block -->
        <div class="event-single__cta-block">
            <?php if ( $ticket_url ) : ?>
                <a href="<?php echo esc_url( $ticket_url ); ?>"
                   class="event-single__cta event-single__cta--primary"
                   target="_blank" rel="noopener noreferrer">
                    Get Tickets
                </a>
            <?php endif; ?>
            <a href="<?php echo esc_url( home_url( '/reservations' ) ); ?>"
               class="event-single__cta <?php echo $ticket_url ? 'event-single__cta--secondary' : 'event-single__cta--primary'; ?>">
                Reserve a Table
            </a>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>"
               class="event-single__cta event-single__cta--ghost">
                Host Your Own Event
            </a>
        </div>

    </div>
</div>

<!-- ── 4. Related events strip ──────────────────────────────────────── -->
<?php
$related_args = [
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post__not_in'   => [ get_the_ID() ],
    'tax_query'      => [[
        'taxonomy' => 'post_tag',
        'field'    => 'slug',
        'terms'    => 'event',
    ]],
    'meta_query' => [
        'relation' => 'OR',
        'dated'    => [
            'key'     => 'event_date',
            'value'   => date( 'Ymd' ),
            'compare' => '>=',
            'type'    => 'CHAR',
        ],
        'undated'  => [
            'key'     => 'event_date',
            'compare' => 'NOT EXISTS',
        ],
    ],
    'orderby' => [
        'dated' => 'ASC',
        'date'  => 'DESC',
    ],
];
$related_query = new WP_Query( $related_args );

if ( $related_query->have_posts() ) :
?>
<section class="event-single__more" aria-label="More events at TEMPO House">

    <div class="event-single__more-header">
        <p class="event-single__more-eyebrow">More at TEMPO House</p>
        <a href="<?php echo esc_url( home_url( '/whats-on' ) ); ?>" class="event-single__more-link">
            See all events &rarr;
        </a>
    </div>

    <div class="event-single__more-grid">
        <?php while ( $related_query->have_posts() ) : $related_query->the_post();
            $ev_category   = get_field( 'event_category' )  ?: '';
            $ev_date_raw   = get_field( 'event_date' );
            $ev_recurrence = get_field( 'event_recurrence' ) ?: '';
            $ev_time       = get_field( 'event_time' )       ?: '';
            $ev_interior   = get_field( 'event_interior' )   ?: 'dark';
            $ev_media_type = get_field( 'event_media_type' ) ?: 'none';
            $ev_media_id   = get_field( 'event_media_id' )   ?: 0;

            if ( $ev_date_raw ) {
                $ev_month_label = date_i18n( 'M Y', strtotime( $ev_date_raw ) );
            } elseif ( $ev_recurrence ) {
                $ev_month_label = ucfirst( $ev_recurrence );
            } else {
                $ev_month_label = '';
            }
        ?>
        <article class="event-card" data-interior="<?php echo esc_attr( $ev_interior ); ?>">
            <a href="<?php the_permalink(); ?>" class="event-card__link"
               aria-label="<?php echo esc_attr( get_the_title() . ( $ev_time ? ' — ' . $ev_time : '' ) ); ?>"></a>
            <div class="event-card__frame-art">
                <div class="event-card__mat">
                    <div class="event-card__artwork">

                        <?php if ( $ev_media_type !== 'none' && $ev_media_id ) : ?>
                            <div class="event-card__media-layer">
                                <?php if ( $ev_media_type === 'video' ) : ?>
                                    <video class="event-card__media" muted loop playsinline preload="none"
                                        src="<?php echo esc_url( wp_get_attachment_url( $ev_media_id ) ); ?>"></video>
                                <?php else : ?>
                                    <?php echo wp_get_attachment_image( $ev_media_id, 'event-card', false, [ 'class' => 'event-card__media', 'loading' => 'lazy', 'alt' => '' ] ); ?>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <span class="event-card__category-ghost"><?php echo esc_html( $ev_category ); ?></span>
                        <?php endif; ?>

                        <div class="event-card__title-bar">
                            <p class="event-card__title"><?php the_title(); ?></p>
                        </div>

                        <div class="event-card__date-reveal">
                            <span class="event-card__month"><?php echo esc_html( $ev_month_label ); ?></span>
                            <span class="event-card__time"><?php echo esc_html( $ev_time ); ?></span>
                        </div>

                    </div>
                </div>
            </div>
        </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>

</section>
<?php endif; ?>

</main>

<?php get_footer(); ?>
