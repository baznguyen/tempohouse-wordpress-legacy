<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Widget_Hero extends \Elementor\Widget_Base {

    public function get_name()       { return 'tempohouse-hero'; }
    public function get_title()      { return 'Hero'; }
    public function get_icon()       { return 'eicon-banner'; }
    public function get_categories() { return ['tempohouse']; }
    public function get_keywords()   { return ['hero', 'banner', 'homepage', 'tempo']; }

    public function get_style_depends()  { return ['tempohouse-hero']; }
    public function get_script_depends() { return ['tempohouse-hero-js']; }

    protected function register_controls() {

        $this->start_controls_section( 'section_text', [
            'label' => 'Text',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'eyebrow', [
            'label'   => 'Eyebrow',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Specialty Café · Cocktail Bar · Gallery · Private Event Venue',
        ] );

        $this->add_control( 'tagline', [
            'label'   => 'Tagline',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Saigon at its own pace.',
            'description' => 'Displayed in italic.',
        ] );

        $this->add_control( 'descriptor', [
            'label'   => 'Descriptor',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'District 3 · Ho Chi Minh City',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_ctas', [
            'label' => 'Buttons',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'primary_label', [
            'label'   => 'Primary Label',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Reserve a table',
        ] );

        $this->add_control( 'primary_url', [
            'label'         => 'Primary URL',
            'type'          => \Elementor\Controls_Manager::URL,
            'default'       => [ 'url' => '/reservations' ],
            'show_external' => false,
        ] );

        $this->add_control( 'secondary_label', [
            'label'   => 'Secondary Label',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Host an event',
        ] );

        $this->add_control( 'secondary_url', [
            'label'         => 'Secondary URL',
            'type'          => \Elementor\Controls_Manager::URL,
            'default'       => [ 'url' => '/event-enquiry' ],
            'show_external' => false,
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s             = $this->get_settings_for_display();
        $primary_url   = ! empty( $s['primary_url']['url'] )   ? $s['primary_url']['url']   : home_url( '/reservations' );
        $secondary_url = ! empty( $s['secondary_url']['url'] ) ? $s['secondary_url']['url'] : home_url( '/event-enquiry' );

        $letters_tempo = [ 'T', 'E', 'M', 'P', 'O' ];
        $letters_house = [ 'H', 'O', 'U', 'S', 'E' ];
        ?>
        <section class="hero" data-tempo-act="morning" aria-label="TEMPO House">
          <div class="hero__noise" aria-hidden="true"></div>

          <div class="hero__inner">
            <p class="hero__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>

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

            <p class="hero__tagline"><em><?php echo esc_html( $s['tagline'] ); ?></em></p>
            <p class="hero__descriptor"><?php echo esc_html( $s['descriptor'] ); ?></p>

            <div class="hero__ctas">
              <a href="<?php echo esc_url( $primary_url ); ?>" class="hero__cta-primary">
                <?php echo esc_html( $s['primary_label'] ); ?>
              </a>
              <a href="<?php echo esc_url( $secondary_url ); ?>" class="hero__cta-secondary">
                <?php echo esc_html( $s['secondary_label'] ); ?>
              </a>
            </div>
          </div>

          <div class="hero__scroll-hint" aria-hidden="true"><span></span></div>
        </section>
        <?php
    }
}
