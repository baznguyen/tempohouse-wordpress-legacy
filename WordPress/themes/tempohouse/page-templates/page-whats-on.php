<?php
/**
 * Template Name: What's On
 * Description: Full-page gallery wall of upcoming TEMPO House events
 */
get_header();

$events_args = [
    'post_type'      => 'event',
    'posts_per_page' => -1,
    'meta_query'     => [
        [
            'key'     => 'event_is_active',
            'value'   => '1',
            'compare' => '=',
        ],
    ],
    'orderby' => 'date',
    'order'   => 'ASC',
];
$events_query = new WP_Query( $events_args );
?>

<main class="page-whats-on" id="main" role="main">

    <!-- ── 1. Page banner ───────────────────────────── -->
    <header class="page-inner__banner">
        <p class="page-inner__eyebrow">Programming</p>
        <h1 class="page-inner__title">Events &amp; Programme</h1>
        <p class="page-inner__lead">Gallery openings, live music sessions, cocktail masterclasses, private tastings. The full HCMC events programme&nbsp;&mdash; updated as it&nbsp;happens. Subscribe and you&rsquo;ll hear first.</p>
        <div class="page-inner__cta-row">
            <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>" class="page-inner__cta-primary">Subscribe to the List</a>
            <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="page-inner__cta-secondary">Host Your Own Event</a>
        </div>
    </header>

    <!-- ── 2. Gallery wall ──────────────────────────── -->
    <section class="page-whats-on__gallery" aria-label="Upcoming events">

        <div class="events__viewport events__viewport--grid">
            <div class="events__track">

                <?php if ( $events_query->have_posts() ) : ?>

                    <?php while ( $events_query->have_posts() ) : $events_query->the_post(); ?>
                        <?php
                        $category  = get_field( 'event_category' )  ?: '';
                        $month     = get_field( 'event_month' )      ?: '';
                        $time      = get_field( 'event_time' )       ?: '';
                        $interior  = get_field( 'event_interior' )   ?: 'dark';
                        $href      = get_field( 'event_href' )       ?: get_permalink();
                        $media_type = get_field( 'event_media_type' );
                        $media_id   = get_field( 'event_media_id' );
                        ?>
                        <article class="event-card" data-interior="<?php echo esc_attr( $interior ); ?>">
                            <a href="<?php echo esc_url( $href ); ?>" class="event-card__link" aria-label="<?php echo esc_attr( get_the_title() ); ?>"></a>
                            <div class="event-card__frame-art">
                                <div class="event-card__mat">
                                    <div class="event-card__artwork">

                                        <?php if ( $media_id ) : ?>
                                            <div class="event-card__media-layer">
                                                <?php if ( $media_type === 'video' ) : ?>
                                                    <video class="event-card__media" autoplay muted loop playsinline>
                                                        <source src="<?php echo esc_url( wp_get_attachment_url( $media_id ) ); ?>" type="video/mp4">
                                                    </video>
                                                <?php else : ?>
                                                    <?php echo wp_get_attachment_image( $media_id, 'large', false, [ 'class' => 'event-card__media', 'loading' => 'lazy', 'alt' => '' ] ); ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <span class="event-card__category-ghost"><?php echo esc_html( $category ); ?></span>

                                        <div class="event-card__title-bar">
                                            <p class="event-card__title"><?php the_title(); ?></p>
                                        </div>

                                        <div class="event-card__date-reveal">
                                            <span class="event-card__month"><?php echo esc_html( $month ); ?></span>
                                            <span class="event-card__time"><?php echo esc_html( $time ); ?></span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>

                <?php else : ?>

                    <!-- Placeholder cards — shown when no active events are published -->

                    <article class="event-card" data-interior="dark">
                        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="event-card__link" aria-label="TEMPO Sessions"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">
                                    <span class="event-card__category-ghost">Live Music</span>
                                    <div class="event-card__title-bar">
                                        <p class="event-card__title">TEMPO Sessions</p>
                                    </div>
                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month">Monthly</span>
                                        <span class="event-card__time">20:00 &ndash; 23:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="event-card" data-interior="sand">
                        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="event-card__link" aria-label="Works on Paper"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">
                                    <span class="event-card__category-ghost">Exhibition</span>
                                    <div class="event-card__title-bar">
                                        <p class="event-card__title">Works on Paper</p>
                                    </div>
                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month">Rotating</span>
                                        <span class="event-card__time">By programme</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="event-card" data-interior="cream">
                        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="event-card__link" aria-label="Cocktail Masterclass"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">
                                    <span class="event-card__category-ghost">Workshop</span>
                                    <div class="event-card__title-bar">
                                        <p class="event-card__title">Cocktail Masterclass</p>
                                    </div>
                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month">Monthly</span>
                                        <span class="event-card__time">18:00 &ndash; 21:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="event-card" data-interior="sand">
                        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="event-card__link" aria-label="Opening Night"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">
                                    <span class="event-card__category-ghost">Gallery Launch</span>
                                    <div class="event-card__title-bar">
                                        <p class="event-card__title">Opening Night</p>
                                    </div>
                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month">By programme</span>
                                        <span class="event-card__time">&nbsp;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="event-card" data-interior="dark">
                        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="event-card__link" aria-label="Private Tasting"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">
                                    <span class="event-card__category-ghost">Dining</span>
                                    <div class="event-card__title-bar">
                                        <p class="event-card__title">Private Tasting</p>
                                    </div>
                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month">Weekly</span>
                                        <span class="event-card__time">19:00 &ndash; 22:00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="event-card" data-interior="cream">
                        <a href="<?php echo esc_url( home_url( '/event-enquiry' ) ); ?>" class="event-card__link" aria-label="The TEMPO Letter Launch"></a>
                        <div class="event-card__frame-art">
                            <div class="event-card__mat">
                                <div class="event-card__artwork">
                                    <span class="event-card__category-ghost">Brand Event</span>
                                    <div class="event-card__title-bar">
                                        <p class="event-card__title">The TEMPO Letter Launch</p>
                                    </div>
                                    <div class="event-card__date-reveal">
                                        <span class="event-card__month">Occasional</span>
                                        <span class="event-card__time">&nbsp;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                <?php endif; ?>

            </div>
        </div>

    </section>

    <!-- ── 3. Subscribe strip ────────────────────────── -->
    <section class="page-whats-on__subscribe-strip" aria-label="Newsletter subscription">
        <p class="page-whats-on__subscribe-body">The TEMPO Letter goes out when something is worth saying: new exhibitions, upcoming sessions, first access to private tastings. No filler.</p>
        <a href="<?php echo esc_url( home_url( '/#newsletter' ) ); ?>" class="page-inner__cta-primary">Subscribe</a>
    </section>

</main>

<?php get_footer(); ?>
