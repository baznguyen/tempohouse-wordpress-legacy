<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Widget_Tempo_Frame extends \Elementor\Widget_Base {

    public function get_name()       { return 'tempohouse-tempo-frame'; }
    public function get_title()      { return 'Tempo Frame'; }
    public function get_icon()       { return 'eicon-image-rollover'; }
    public function get_categories() { return ['tempohouse']; }
    public function get_keywords()   { return ['frame', 'image', 'hover', 'downlight', 'tempo']; }

    public function get_style_depends()  { return ['tempohouse-tempo-frame']; }
    public function get_script_depends() { return ['tempohouse-tempo-frame']; }

    protected function register_controls() {

        $this->start_controls_section( 'section_image', [
            'label' => 'Image',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'image', [
            'label'   => 'Image',
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
        ] );

        $this->add_control( 'alt', [
            'label'       => 'Alt Text',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'description' => 'Describe the image for screen readers.',
        ] );

        $this->add_control( 'aria_label', [
            'label'   => 'Section Label (aria-label)',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Image',
        ] );

        $this->add_control( 'interactive', [
            'label'        => 'Interactive Hover Effect',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'On',
            'label_off'    => 'Off',
            'return_value' => 'yes',
            'default'      => 'yes',
            'description'  => 'Enables the downlight cursor-tracking hover effect.',
        ] );

        $this->add_control( 'extra_class', [
            'label'       => 'Extra CSS Class',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'description' => 'e.g. page-bar__programme-img — adds a modifier class alongside tempo-frame.',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_loading', [
            'label' => 'Loading',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'loading', [
            'label'   => 'Image Loading',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'lazy' => 'Lazy (default)', 'eager' => 'Eager (above fold)' ],
            'default' => 'lazy',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s           = $this->get_settings_for_display();
        $image_url   = ! empty( $s['image']['url'] ) ? $s['image']['url'] : '';
        $alt         = ! empty( $s['alt'] )           ? $s['alt']           : '';
        $aria_label  = ! empty( $s['aria_label'] )    ? $s['aria_label']    : 'Image';
        $interactive = $s['interactive'] === 'yes'    ? ' data-interactive' : '';
        $extra_class = ! empty( $s['extra_class'] )   ? ' ' . esc_attr( $s['extra_class'] ) : '';
        $loading     = $s['loading'] ?? 'lazy';
        ?>
        <div class="tempo-frame<?php echo $extra_class; ?>"<?php echo $interactive; ?> aria-label="<?php echo esc_attr( $aria_label ); ?>">
          <div class="tempo-frame__mat">
            <div class="tempo-frame__artwork">
              <?php if ( $image_url ) : ?>
              <img
                class="tempo-frame__img"
                src="<?php echo esc_url( $image_url ); ?>"
                alt="<?php echo esc_attr( $alt ); ?>"
                loading="<?php echo esc_attr( $loading ); ?>"
              >
              <?php else : ?>
              <div class="tempo-frame__placeholder" aria-hidden="true"></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php
    }
}
