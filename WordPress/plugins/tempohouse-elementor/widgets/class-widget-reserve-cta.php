<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Widget_Reserve_CTA extends \Elementor\Widget_Base {

    public function get_name()       { return 'tempohouse-reserve-cta'; }
    public function get_title()      { return 'Reserve CTA'; }
    public function get_icon()       { return 'eicon-call-to-action'; }
    public function get_categories() { return ['tempohouse']; }
    public function get_keywords()   { return ['reserve', 'cta', 'book', 'table', 'tempo']; }

    public function get_style_depends() {
        return ['tempohouse-reserve'];
    }

    protected function register_controls() {

        // ── Content ───────────────────────────────────────────────────────────
        $this->start_controls_section( 'section_content', [
            'label' => 'Content',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'eyebrow', [
            'label'   => 'Eyebrow',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'The Café & Bar',
        ] );

        $this->add_control( 'title', [
            'label'   => 'Title',
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'Come for the coffee.<br>Stay for the cocktails.',
            'description' => 'Use &lt;br&gt; for line breaks.',
        ] );

        $this->add_control( 'body', [
            'label'   => 'Body',
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'rows'    => 4,
            'default' => 'Specialty coffee and a rotating gallery through the day. Craft cocktails from 18:00 — in a French-colonial shophouse at 218c Pasteur, District 3, Ho Chi Minh City. Reservations recommended for evenings and weekends.',
        ] );

        $this->end_controls_section();

        // ── Primary Button ────────────────────────────────────────────────────
        $this->start_controls_section( 'section_primary', [
            'label' => 'Primary Button',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'primary_label', [
            'label'   => 'Label',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Book a table',
        ] );

        $this->add_control( 'primary_url', [
            'label'         => 'URL',
            'type'          => \Elementor\Controls_Manager::URL,
            'placeholder'   => '/reservations',
            'default'       => [ 'url' => '/reservations' ],
            'show_external' => false,
        ] );

        $this->end_controls_section();

        // ── Secondary Button ──────────────────────────────────────────────────
        $this->start_controls_section( 'section_secondary', [
            'label' => 'Secondary Button',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'secondary_label', [
            'label'   => 'Label',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Plan a private event →',
        ] );

        $this->add_control( 'secondary_url', [
            'label'         => 'URL',
            'type'          => \Elementor\Controls_Manager::URL,
            'placeholder'   => '/event-enquiry',
            'default'       => [ 'url' => '/event-enquiry' ],
            'show_external' => false,
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s             = $this->get_settings_for_display();
        $primary_url   = ! empty( $s['primary_url']['url'] )   ? $s['primary_url']['url']   : home_url( '/reservations' );
        $secondary_url = ! empty( $s['secondary_url']['url'] ) ? $s['secondary_url']['url'] : home_url( '/event-enquiry' );
        ?>
        <section class="reserve-cta" aria-label="Reservations">
          <div class="container container--narrow">
            <p class="reserve-cta__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
            <h2 class="reserve-cta__title"><?php echo wp_kses( $s['title'], [ 'br' => [], 'em' => [], 'strong' => [] ] ); ?></h2>
            <p class="reserve-cta__body"><?php echo esc_html( $s['body'] ); ?></p>
            <div class="reserve-cta__actions">
              <a href="<?php echo esc_url( $primary_url ); ?>" class="reserve-cta__primary">
                <?php echo esc_html( $s['primary_label'] ); ?>
              </a>
              <a href="<?php echo esc_url( $secondary_url ); ?>" class="reserve-cta__secondary">
                <?php echo esc_html( $s['secondary_label'] ); ?>
              </a>
            </div>
          </div>
        </section>
        <?php
    }
}
