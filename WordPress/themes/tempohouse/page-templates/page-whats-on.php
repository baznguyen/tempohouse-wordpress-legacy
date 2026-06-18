<?php
/**
 * Template Name: What's On
 * Description: Full-page gallery wall of active and upcoming TEMPO House events.
 *
 * Events are standard WordPress Posts with the tag 'event'.
 * Active (happening now) events are also tagged 'active'.
 * Staff toggle the 'active' tag to move events between sections.
 */
get_header();

$today = date( 'Ymd' );

// ── Query: Happening Now — tagged 'event' AND 'active' ────────────────────
// No meta_key ordering — active events may have no fixed date (recurring/ongoing).
// Simple newest-published-first sort; staff control order via publish date.
$active_args = [
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'tax_query'      => [
        'relation' => 'AND',
        [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'event',
        ],
        [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'active',
        ],
    ],
    'orderby' => 'date',
    'order'   => 'DESC',
];
$active_query = new WP_Query( $active_args );

// ── Query: Coming Up — tagged 'event' NOT 'active', with future or no date ─
// Named meta_query clauses avoid the INNER JOIN that would exclude posts with no date set.
// Events with event_date >= today come first (ASC); undated recurring events follow.
$upcoming_args = [
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'tax_query'      => [
        'relation' => 'AND',
        [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'event',
        ],
        [
            'taxonomy' => 'post_tag',
            'field'    => 'slug',
            'terms'    => 'active',
            'operator' => 'NOT IN',
        ],
    ],
    'meta_query' => [
        'relation' => 'OR',
        'dated'    => [
            'key'     => 'event_date',
            'value'   => $today,
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
$upcoming_query = new WP_Query( $upcoming_args );

$has_active   = $active_query->have_posts();
$has_upcoming = $upcoming_query->have_posts();
?>

<main class="page-whats-on" id="main" role="main">

    <!-- ── 1. Page banner ───────────────────────────── -->
    <header class="page-inner__banner">
        <p class="page-inner__eyebrow">Programming</p>
        <h1 class="page-inner__title">What&rsquo;s happening at TEMPO House.</h1>
        <p class="page-inner__lead">Gallery openings, live music sessions, cocktail masterclasses, private tastings. The full programme&nbsp;&mdash; updated as it&nbsp;happens. Subscribe and you&rsquo;ll hear first.</p>
        <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>" class="page-inner__cta-primary">Subscribe to the List</a>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Host Your Own Event</a>
        </div>
    </header>

    <!-- ── 2. Gallery wall ──────────────────────────── -->
    <div class="page-whats-on__gallery">

        <?php if ( $has_active ) : ?>

        <!-- Happening Now -->
        <section class="page-whats-on__section" aria-label="Happening now">
            <p class="page-whats-on__section-label">Happening Now</p>
            <div class="events__viewport events__viewport--grid">
                <div class="events__track">
                    <?php while ( $active_query->have_posts() ) : $active_query->the_post();
                        $category   = get_field( 'event_category' )  ?: '';
                        $date_raw   = get_field( 'event_date' );
                        $recurrence = get_field( 'event_recurrence' ) ?: '';
                        $time       = get_field( 'event_time' )       ?: '';
                        $interior   = get_field( 'event_interior' )   ?: 'dark';
                        $media_type = get_field( 'event_media_type' ) ?: 'none';
                        $media_id   = get_field( 'event_media_id' )   ?: 0;

                        // Display label below card: month+year if dated, else recurrence text.
                        if ( $date_raw ) {
                            $month_label = date_i18n( 'M Y', strtotime( $date_raw ) );
                        } elseif ( $recurrence ) {
                            $month_label = ucfirst( $recurrence );
                        } else {
                            $month_label = 'Ongoing';
                        }
                    ?>
                    <article class="event-card" data-interior="<?php echo esc_attr( $interior ); ?>">
                        <a href="<?php the_permalink(); ?>" class="event-card__link"
                           aria-label="<?php echo esc_attr( get_the_title() . ( $time ? ' — ' . $time : '' ) ); ?>"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">

                                    <?php if ( $media_type !== 'none' && $media_id ) : ?>
                                        <div class="event-card__media-layer">
                                            <?php if ( $media_type === 'video' ) : ?>
                                                <video class="event-card__media" muted loop playsinline preload="none"
                                                    src="<?php echo esc_url( wp_get_attachment_url( $media_id ) ); ?>"></video>
                                            <?php else : ?>
                                                <?php echo wp_get_attachment_image( $media_id, 'event-card', false, [ 'class' => 'event-card__media', 'loading' => 'lazy', 'alt' => '' ] ); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php else : ?>
                                        <span class="event-card__category-ghost"><?php echo esc_html( $category ); ?></span>
                                    <?php endif; ?>

                                    <div class="event-card__title-bar">
                                        <p class="event-card__title"><?php the_title(); ?></p>
                                    </div>

                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month"><?php echo esc_html( $month_label ); ?></span>
                                        <span class="event-card__time"><?php echo esc_html( $time ); ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            <?php $active_count = $active_query->post_count; if ( $active_count > 1 ) : ?>
            <nav class="page-whats-on__carousel-nav" aria-label="Happening Now events navigation">
                <button class="events__nav-btn events__nav-prev" type="button" aria-label="Previous event">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="events__dots">
                    <?php for ( $d = 0; $d < $active_count; $d++ ) : ?>
                    <button class="events__dot<?php echo $d === 0 ? ' events__dot--active' : ''; ?>"
                            type="button" aria-label="Event <?php echo $d + 1; ?>"></button>
                    <?php endfor; ?>
                </div>
                <button class="events__nav-btn events__nav-next" type="button" aria-label="Next event">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </nav>
            <?php endif; ?>
        </section>

        <?php endif; ?>

        <?php if ( $has_upcoming ) : ?>

        <!-- Coming Up -->
        <section class="page-whats-on__section<?php echo $has_active ? ' page-whats-on__section--ruled' : ''; ?>" aria-label="Coming up">
            <p class="page-whats-on__section-label">Coming Up</p>
            <div class="events__viewport events__viewport--grid">
                <div class="events__track">
                    <?php while ( $upcoming_query->have_posts() ) : $upcoming_query->the_post();
                        $category   = get_field( 'event_category' )  ?: '';
                        $date_raw   = get_field( 'event_date' );
                        $recurrence = get_field( 'event_recurrence' ) ?: '';
                        $time       = get_field( 'event_time' )       ?: '';
                        $interior   = get_field( 'event_interior' )   ?: 'dark';
                        $media_type = get_field( 'event_media_type' ) ?: 'none';
                        $media_id   = get_field( 'event_media_id' )   ?: 0;

                        if ( $date_raw ) {
                            $month_label = date_i18n( 'M Y', strtotime( $date_raw ) );
                        } elseif ( $recurrence ) {
                            $month_label = ucfirst( $recurrence );
                        } else {
                            $month_label = '';
                        }
                    ?>
                    <article class="event-card" data-interior="<?php echo esc_attr( $interior ); ?>">
                        <a href="<?php the_permalink(); ?>" class="event-card__link"
                           aria-label="<?php echo esc_attr( get_the_title() . ( $time ? ' — ' . $time : '' ) ); ?>"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">

                                    <?php if ( $media_type !== 'none' && $media_id ) : ?>
                                        <div class="event-card__media-layer">
                                            <?php if ( $media_type === 'video' ) : ?>
                                                <video class="event-card__media" muted loop playsinline preload="none"
                                                    src="<?php echo esc_url( wp_get_attachment_url( $media_id ) ); ?>"></video>
                                            <?php else : ?>
                                                <?php echo wp_get_attachment_image( $media_id, 'event-card', false, [ 'class' => 'event-card__media', 'loading' => 'lazy', 'alt' => '' ] ); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php else : ?>
                                        <span class="event-card__category-ghost"><?php echo esc_html( $category ); ?></span>
                                    <?php endif; ?>

                                    <div class="event-card__title-bar">
                                        <p class="event-card__title"><?php the_title(); ?></p>
                                    </div>

                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month"><?php echo esc_html( $month_label ); ?></span>
                                        <span class="event-card__time"><?php echo esc_html( $time ); ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            <?php $upcoming_count = $upcoming_query->post_count; if ( $upcoming_count > 1 ) : ?>
            <nav class="page-whats-on__carousel-nav" aria-label="Coming Up events navigation">
                <button class="events__nav-btn events__nav-prev" type="button" aria-label="Previous event">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="events__dots">
                    <?php for ( $d = 0; $d < $upcoming_count; $d++ ) : ?>
                    <button class="events__dot<?php echo $d === 0 ? ' events__dot--active' : ''; ?>"
                            type="button" aria-label="Event <?php echo $d + 1; ?>"></button>
                    <?php endfor; ?>
                </div>
                <button class="events__nav-btn events__nav-next" type="button" aria-label="Next event">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </nav>
            <?php endif; ?>
        </section>

        <?php endif; ?>

        <?php if ( ! $has_active && ! $has_upcoming ) : ?>
        <!-- Empty state: programme coming soon. No clickthrough — frames are decorative only. -->
        <section class="page-whats-on__section page-whats-on__section--empty" aria-label="Programme coming soon">
            <p class="page-whats-on__coming-soon-label">Coming Soon</p>
            <div class="events__viewport events__viewport--grid">
                <div class="events__track">

                    <article class="event-card event-card--placeholder" data-interior="dark" aria-hidden="true">
                        <div class="event-card__frame-art"><div class="event-card__mat"><div class="event-card__artwork">
                            <span class="event-card__category-ghost event-card__category-ghost--muted">&nbsp;</span>
                        </div></div></div>
                    </article>

                    <article class="event-card event-card--placeholder" data-interior="sand" aria-hidden="true">
                        <div class="event-card__frame-art"><div class="event-card__mat"><div class="event-card__artwork">
                            <span class="event-card__category-ghost event-card__category-ghost--muted">&nbsp;</span>
                        </div></div></div>
                    </article>

                    <article class="event-card event-card--placeholder" data-interior="cream" aria-hidden="true">
                        <div class="event-card__frame-art"><div class="event-card__mat"><div class="event-card__artwork">
                            <span class="event-card__category-ghost event-card__category-ghost--muted">&nbsp;</span>
                        </div></div></div>
                    </article>

                </div>
            </div>
            <div class="page-whats-on__coming-soon">
                <p class="page-whats-on__coming-soon-body">Exciting events and programming are on their way. Subscribe to the TEMPO Letter and you&rsquo;ll hear about them first.</p>
                <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>" class="page-inner__cta-primary">Stay in the loop</a>
            </div>
        </section>
        <?php endif; ?>

    </div><!-- /.page-whats-on__gallery -->

    <!-- ── 3. Subscribe strip ────────────────────────── -->
    <section class="page-whats-on__subscribe-strip" aria-label="Newsletter subscription">
        <p class="page-whats-on__subscribe-body">The TEMPO Letter goes out when something is worth saying: new exhibitions, upcoming sessions, first access to private tastings. No filler.</p>
        <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>" class="page-inner__cta-primary">Subscribe</a>
    </section>

</main>

<?php get_footer(); ?>
