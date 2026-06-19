<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Widget_Gallery_Walk extends \Elementor\Widget_Base {

    public function get_name()       { return 'tempohouse-gallery-walk'; }
    public function get_title()      { return 'Gallery Walk'; }
    public function get_icon()       { return 'eicon-gallery-grid'; }
    public function get_categories() { return ['tempohouse']; }
    public function get_keywords()   { return ['gallery', 'walk', 'carousel', 'artwork', 'tempo']; }

    public function get_style_depends()  { return ['tempohouse-gallery']; }
    public function get_script_depends() { return ['tempohouse-gallery-js', 'tempohouse-drag']; }

    protected function register_controls() {

        $this->start_controls_section( 'section_header', [
            'label' => 'Header',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'section_head', [
            'label'   => 'Section Head',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'The Walk',
        ] );

        $this->add_control( 'show_header', [
            'label'        => 'Show Header',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Yes',
            'label_off'    => 'No',
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_images', [
            'label' => 'Images',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'info_notice', [
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => '<p style="font-size:11px;color:#999;margin:0">Add images in the order you want them to appear in the horizontal scroll walk. Aspect ratio is preserved (portrait or landscape).</p>',
            'content_classes' => 'elementor-descriptor',
        ] );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control( 'image', [
            'label'   => 'Image',
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'default' => [ 'url' => \Elementor\Utils::get_placeholder_image_src() ],
        ] );

        $repeater->add_control( 'alt', [
            'label'   => 'Alt Text',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Gallery artwork',
        ] );

        $repeater->add_control( 'caption', [
            'label'   => 'Caption (optional)',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => '',
        ] );

        $this->add_control( 'images', [
            'label'       => 'Gallery Images',
            'type'        => \Elementor\Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [
                [ 'alt' => 'Gallery artwork 1' ],
                [ 'alt' => 'Gallery artwork 2' ],
                [ 'alt' => 'Gallery artwork 3' ],
            ],
            'title_field' => '{{{ alt }}}',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s      = $this->get_settings_for_display();
        $images = $s['images'] ?? [];
        ?>
        <section class="page-gallery__walk" aria-label="Gallery walk">

          <?php if ( $s['show_header'] === 'yes' ) : ?>
          <div class="page-inner__container">
            <p class="page-inner__section-head"><?php echo esc_html( $s['section_head'] ); ?></p>
          </div>
          <?php endif; ?>

          <div class="page-gallery__walk-viewport">
            <div class="page-gallery__walk-track">
              <?php foreach ( $images as $item ) :
                $url = $item['image']['url'] ?? '';
                if ( ! $url ) continue;

                // Detect orientation from attachment metadata if possible
                $img_id = $item['image']['id'] ?? 0;
                $orientation = 'landscape';
                if ( $img_id ) {
                    $meta = wp_get_attachment_metadata( $img_id );
                    if ( ! empty( $meta['height'] ) && ! empty( $meta['width'] ) && $meta['height'] > $meta['width'] ) {
                        $orientation = 'portrait';
                    }
                }
              ?>
              <figure class="page-gallery__walk-item" data-orientation="<?php echo esc_attr( $orientation ); ?>">
                <img
                  class="page-gallery__walk-img"
                  src="<?php echo esc_url( $url ); ?>"
                  alt="<?php echo esc_attr( $item['alt'] ?? '' ); ?>"
                  loading="lazy"
                >
                <?php if ( ! empty( $item['caption'] ) ) : ?>
                <figcaption class="page-gallery__walk-caption"><?php echo esc_html( $item['caption'] ); ?></figcaption>
                <?php endif; ?>
              </figure>
              <?php endforeach; ?>
            </div>
          </div>

        </section>
        <?php
    }
}
