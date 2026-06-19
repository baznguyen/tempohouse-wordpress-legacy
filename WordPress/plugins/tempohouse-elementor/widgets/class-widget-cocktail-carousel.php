<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Widget_Cocktail_Carousel extends \Elementor\Widget_Base {

    public function get_name()       { return 'tempohouse-cocktail-carousel'; }
    public function get_title()      { return 'Cocktail Carousel'; }
    public function get_icon()       { return 'eicon-gallery-justified'; }
    public function get_categories() { return ['tempohouse']; }
    public function get_keywords()   { return ['cocktail', 'bar', 'carousel', 'signatures', 'tempo']; }

    public function get_style_depends()  { return ['tempohouse-bar', 'tempohouse-tempo-frame']; }
    public function get_script_depends() { return ['tempohouse-bar-js', 'tempohouse-tempo-frame']; }

    protected function register_controls() {

        $this->start_controls_section( 'section_header', [
            'label' => 'Section Header',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'section_head', [
            'label'   => 'Section Head',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Six to Know',
        ] );

        $this->add_control( 'title', [
            'label'   => 'Title',
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'The signatures.<br>Where to start.',
        ] );

        $this->add_control( 'source_note', [
            'label'   => 'Source Note',
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'default' => 'Seventeen cocktails on the list. These are the six that earn their entry — pulled local where they can be, classical where they can\'t be anything else.',
        ] );

        $this->end_controls_section();

        // ── Cocktail Items (repeater) ─────────────────────────────────────────
        $this->start_controls_section( 'section_items', [
            'label' => 'Cocktails',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control( 'name', [
            'label'   => 'Cocktail Name',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Espresso Martini',
        ] );

        $repeater->add_control( 'notes', [
            'label'   => 'Ingredients / Notes',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Vodka · Coffee Liqueur · Vietnamese Espresso',
        ] );

        $repeater->add_control( 'image', [
            'label'   => 'Photo',
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
        ] );

        $repeater->add_control( 'alt', [
            'label'   => 'Alt Text',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => '',
        ] );

        $this->add_control( 'items', [
            'label'       => 'Cocktail Items',
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'name' => 'Espresso Martini', 'notes' => 'Vodka · Coffee Liqueur · Vietnamese Espresso' ],
                [ 'name' => 'Lychee Martini',   'notes' => 'Vodka · Lychee Liqueur · Mekong Delta' ],
                [ 'name' => 'Panpan Spritz',    'notes' => 'Saigon Pandan · Lime · Coconut Soda' ],
                [ 'name' => 'Negroni',          'notes' => 'Gin · Campari · Sweet Vermouth' ],
                [ 'name' => 'Manhattan',        'notes' => 'Rye · Vermouth Ngọt · Angostura' ],
                [ 'name' => 'Yuzu Spritz',      'notes' => 'Saigon Spirit One · Yuzu · Prosecco' ],
            ],
            'title_field' => '{{{ name }}}',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s     = $this->get_settings_for_display();
        $items = $s['items'] ?? [];
        ?>
        <section class="page-inner__section page-inner__section--alt page-bar__signatures" aria-labelledby="bar-sigs-title">
          <div class="page-inner__container">

            <div class="page-bar__signatures-head">
              <p class="page-inner__section-head"><?php echo esc_html( $s['section_head'] ); ?></p>
              <div class="page-bar__signatures-intro">
                <h2 class="page-inner__section-title" id="bar-sigs-title">
                  <?php echo wp_kses( $s['title'], [ 'br' => [], 'em' => [] ] ); ?>
                </h2>
                <p class="page-bar__signatures-source"><?php echo esc_html( $s['source_note'] ); ?></p>
              </div>
            </div>

            <div class="page-bar__signatures-grid">
              <?php foreach ( $items as $i => $item ) :
                $num       = str_pad( $i + 1, 2, '0', STR_PAD_LEFT );
                $image_url = $item['image']['url'] ?? '';
                $alt       = ! empty( $item['alt'] ) ? $item['alt'] : esc_attr( $item['name'] );
              ?>
              <div class="page-bar__sig-item">
                <div class="tempo-frame page-bar__sig-frame" data-interactive aria-label="<?php echo esc_attr( $item['name'] ); ?> at TEMPO House Bar">
                  <div class="tempo-frame__mat">
                    <div class="tempo-frame__artwork">
                      <?php if ( $image_url ) : ?>
                      <img class="tempo-frame__img" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy">
                      <?php else : ?>
                      <div class="tempo-frame__placeholder" aria-hidden="true"></div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="page-bar__sig-label">
                  <span class="page-bar__sig-num" aria-hidden="true"><?php echo esc_html( $num ); ?></span>
                  <div>
                    <h3 class="page-bar__sig-name"><?php echo esc_html( $item['name'] ); ?></h3>
                    <p class="page-bar__sig-notes"><?php echo esc_html( $item['notes'] ); ?></p>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>

          </div>
        </section>
        <?php
    }
}
