<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Widget_Events_Carousel extends \Elementor\Widget_Base {

    public function get_name()       { return 'tempohouse-events-carousel'; }
    public function get_title()      { return 'Events Carousel'; }
    public function get_icon()       { return 'eicon-posts-carousel'; }
    public function get_categories() { return ['tempohouse']; }
    public function get_keywords()   { return ['events', 'carousel', 'whats on', 'tempo']; }

    public function get_style_depends()  { return ['tempohouse-events']; }
    public function get_script_depends() { return ['tempohouse-drag', 'tempohouse-events-js']; }

    protected function register_controls() {

        $this->start_controls_section( 'section_header', [
            'label' => 'Header',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'eyebrow', [
            'label'   => 'Eyebrow',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Programming',
        ] );

        $this->add_control( 'title', [
            'label'   => 'Title',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'What\'s On',
        ] );

        $this->add_control( 'footer_note', [
            'label'   => 'Footer Note',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'The programme rotates. The list gets first notice.',
        ] );

        $this->add_control( 'footer_cta_label', [
            'label'   => 'Footer Link Label',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'See all events →',
        ] );

        $this->add_control( 'footer_cta_url', [
            'label'         => 'Footer Link URL',
            'type'          => \Elementor\Controls_Manager::URL,
            'default'       => [ 'url' => '/whats-on' ],
            'show_external' => false,
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_query', [
            'label' => 'Query',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'limit', [
            'label'   => 'Max Events',
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 10,
            'default' => 3,
        ] );

        $this->add_control( 'require_active_tag', [
            'label'        => 'Active Events Only',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => 'yes',
            'description'  => 'When on, only shows posts tagged both "event" and "active".',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s           = $this->get_settings_for_display();
        $limit       = max( 1, intval( $s['limit'] ?? 3 ) );
        $active_only = $s['require_active_tag'] === 'yes';
        $footer_url  = ! empty( $s['footer_cta_url']['url'] ) ? $s['footer_cta_url']['url'] : home_url( '/whats-on' );

        $tax_query = [ 'relation' => 'AND',
            [ 'taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => 'event' ],
        ];
        if ( $active_only ) {
            $tax_query[] = [ 'taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => 'active' ];
        }

        $events_query = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => $limit,
            'tax_query'      => $tax_query,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ] );

        $event_items = [];
        $recurrence_labels = [
            'one-time' => 'One Night Only',
            'weekly'   => 'Weekly',
            'monthly'  => 'Monthly',
            'ongoing'  => 'Ongoing',
        ];

        if ( $events_query->have_posts() ) {
            while ( $events_query->have_posts() ) {
                $events_query->the_post();
                $date_raw   = get_field( 'event_date' );
                $recurrence = get_field( 'event_recurrence' ) ?: '';

                if ( $date_raw ) {
                    $month_label = date_i18n( 'M Y', strtotime( $date_raw ) );
                } elseif ( $recurrence ) {
                    $month_label = $recurrence_labels[ $recurrence ] ?? ucfirst( $recurrence );
                } else {
                    $month_label = '';
                }

                $event_items[] = [
                    'title'      => get_the_title(),
                    'category'   => get_field( 'event_category' )  ?: 'Event',
                    'month'      => $month_label,
                    'time'       => get_field( 'event_time' )       ?: '',
                    'interior'   => get_field( 'event_interior' )   ?: 'dark',
                    'href'       => get_permalink(),
                    'media_type' => get_field( 'event_media_type' ) ?: 'none',
                    'media_id'   => get_field( 'event_media_id' )   ?: 0,
                ];
            }
            wp_reset_postdata();
        }

        $has_events  = ! empty( $event_items );
        $track_items = $has_events ? array_merge( $event_items, $event_items ) : [];
        $total       = count( $event_items );
        ?>
        <section class="events" aria-label="What's on">
          <div class="container">
            <header class="events__header">
              <p class="events__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
              <h2 class="events__title"><?php echo esc_html( $s['title'] ); ?></h2>
            </header>
          </div>

          <?php if ( $has_events ) : ?>

          <div class="events__viewport">
            <div class="events__track">
              <?php foreach ( $track_items as $ev ) : ?>
              <article class="event-card" data-interior="<?php echo esc_attr( $ev['interior'] ); ?>">
                <a href="<?php echo esc_url( $ev['href'] ); ?>" class="event-card__link"
                   aria-label="<?php echo esc_attr( $ev['title'] . ( $ev['time'] ? ' — ' . $ev['time'] : '' ) ); ?>"></a>

                <div class="event-card__frame-art">
                  <div class="event-card__mat">
                    <div class="event-card__artwork">

                      <?php if ( $ev['media_type'] !== 'none' && $ev['media_id'] ) : ?>
                      <div class="event-card__media-layer">
                        <?php if ( $ev['media_type'] === 'video' ) : ?>
                          <video class="event-card__media" muted loop playsinline preload="none"
                            src="<?php echo esc_url( wp_get_attachment_url( $ev['media_id'] ) ); ?>"></video>
                        <?php else : ?>
                          <?php echo wp_get_attachment_image( $ev['media_id'], 'event-card', false, [ 'class' => 'event-card__media', 'loading' => 'lazy', 'alt' => '' ] ); ?>
                        <?php endif; ?>
                      </div>
                      <?php else : ?>
                      <span class="event-card__category-ghost"><?php echo esc_html( $ev['category'] ); ?></span>
                      <?php endif; ?>

                      <div class="event-card__title-bar">
                        <p class="event-card__title"><?php echo esc_html( $ev['title'] ); ?></p>
                      </div>
                      <div class="event-card__date-reveal">
                        <span class="event-card__month"><?php echo esc_html( $ev['month'] ); ?></span>
                        <span class="event-card__time"><?php echo esc_html( $ev['time'] ); ?></span>
                      </div>

                    </div>
                  </div>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
          </div>

          <nav class="events__carousel-nav" aria-label="Events navigation">
            <button class="events__nav-btn events__nav-prev" aria-label="Previous event" disabled>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <div class="events__dots">
              <?php for ( $d = 0; $d < $total; $d++ ) : ?>
              <button class="events__dot<?php echo $d === 0 ? ' events__dot--active' : ''; ?>"
                      aria-label="Event <?php echo $d + 1; ?>"></button>
              <?php endfor; ?>
            </div>
            <button class="events__nav-btn events__nav-next" aria-label="Next event">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </nav>

          <?php else : ?>

          <?php $ph_texts = ['Coming Soon','In the Works','Watch This Space']; $ph_ints = ['dark','sand','dark']; ?>
          <div class="events__viewport">
            <div class="events__track">
              <?php for ( $r = 0; $r < 2; $r++ ) : for ( $p = 0; $p < 3; $p++ ) : ?>
              <article class="event-card event-card--placeholder" data-interior="<?php echo esc_attr( $ph_ints[$p] ); ?>" aria-hidden="true">
                <div class="event-card__frame-art">
                  <div class="event-card__mat">
                    <div class="event-card__artwork">
                      <span class="event-card__category-ghost"><?php echo esc_html( $ph_texts[$p] ); ?></span>
                    </div>
                  </div>
                </div>
              </article>
              <?php endfor; endfor; ?>
            </div>
          </div>

          <nav class="events__carousel-nav" aria-label="Events navigation">
            <button class="events__nav-btn events__nav-prev" aria-label="Previous event" disabled>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <div class="events__dots">
              <?php for ( $d = 0; $d < 3; $d++ ) : ?>
              <button class="events__dot<?php echo $d === 0 ? ' events__dot--active' : ''; ?>"
                      aria-label="Event <?php echo $d + 1; ?>"></button>
              <?php endfor; ?>
            </div>
            <button class="events__nav-btn events__nav-next" aria-label="Next event">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                <path d="M6 12l4-4-4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </nav>

          <div class="container">
            <p class="events__coming-soon-note">We&rsquo;re designing something worth showing up for.<br>First notice goes to the list.</p>
          </div>

          <?php endif; ?>

          <div class="container">
            <div class="events__footer">
              <p class="events__footer-note"><em><?php echo esc_html( $s['footer_note'] ); ?></em></p>
              <a href="<?php echo esc_url( $footer_url ); ?>" class="events__footer-cta">
                <?php echo esc_html( $s['footer_cta_label'] ); ?>
              </a>
            </div>
          </div>
        </section>
        <?php
    }
}
