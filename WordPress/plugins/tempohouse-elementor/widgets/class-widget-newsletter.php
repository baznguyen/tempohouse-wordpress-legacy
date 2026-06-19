<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Widget_Newsletter extends \Elementor\Widget_Base {

    public function get_name()       { return 'tempohouse-newsletter'; }
    public function get_title()      { return 'Newsletter Signup'; }
    public function get_icon()       { return 'eicon-form-horizontal'; }
    public function get_categories() { return ['tempohouse']; }
    public function get_keywords()   { return ['newsletter', 'email', 'klaviyo', 'signup', 'tempo']; }

    public function get_style_depends() { return ['tempohouse-newsletter']; }

    protected function register_controls() {

        $this->start_controls_section( 'section_content', [
            'label' => 'Content',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'eyebrow', [
            'label'   => 'Eyebrow',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'First to know',
        ] );

        $this->add_control( 'title', [
            'label'   => 'Title',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'The TEMPO letter.',
        ] );

        $this->add_control( 'body', [
            'label'   => 'Body',
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'rows'    => 3,
            'default' => 'Upcoming programming, private hire dates, and the occasional table that opens up. First access for those on the list.',
        ] );

        $this->add_control( 'button_label', [
            'label'   => 'Button Label',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Join the List',
        ] );

        $this->add_control( 'placeholder', [
            'label'   => 'Email Placeholder',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'your@email.com',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_klaviyo', [
            'label' => 'Klaviyo',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'klaviyo_company_id', [
            'label'       => 'Company ID',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'VCR2Ei',
            'description' => 'Found in Klaviyo → Settings → API Keys.',
        ] );

        $this->add_control( 'custom_source', [
            'label'   => 'Custom Source Label',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Website',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $s            = $this->get_settings_for_display();
        $company_id   = esc_js( $s['klaviyo_company_id'] ?? 'VCR2Ei' );
        $custom_source = esc_js( $s['custom_source'] ?? 'Website' );
        $unique_id    = 'nl-' . $this->get_id();
        ?>
        <section class="newsletter" id="newsletter" aria-label="Stay connected">
          <div class="container container--narrow">
            <p class="newsletter__eyebrow"><?php echo esc_html( $s['eyebrow'] ); ?></p>
            <h2 class="newsletter__title"><?php echo esc_html( $s['title'] ); ?></h2>
            <p class="newsletter__body"><?php echo esc_html( $s['body'] ); ?></p>
            <div class="newsletter__form-wrap">
              <form class="newsletter__form" id="<?php echo esc_attr( $unique_id ); ?>" novalidate>
                <input
                  type="email"
                  id="<?php echo esc_attr( $unique_id ); ?>-email"
                  name="email"
                  class="newsletter__input"
                  placeholder="<?php echo esc_attr( $s['placeholder'] ); ?>"
                  required
                  autocomplete="email"
                >
                <button type="submit" class="newsletter__btn"><?php echo esc_html( $s['button_label'] ); ?></button>
                <p class="newsletter__error" id="<?php echo esc_attr( $unique_id ); ?>-error" hidden>Something went wrong &mdash; please try again.</p>
                <p class="newsletter__success" id="<?php echo esc_attr( $unique_id ); ?>-success" hidden>&#10022;&ensp;You&rsquo;re on the list.</p>
              </form>
            </div>
          </div>
        </section>

        <script>
        (function () {
          var formId   = '<?php echo esc_js( $unique_id ); ?>';
          var form     = document.getElementById(formId);
          if (!form) return;
          var emailEl  = document.getElementById(formId + '-email');
          var btn      = form.querySelector('.newsletter__btn');
          var errEl    = document.getElementById(formId + '-error');
          var okEl     = document.getElementById(formId + '-success');

          form.addEventListener('submit', function (e) {
            e.preventDefault();
            var email = emailEl.value.trim();
            if (!email) return;
            btn.disabled = true;
            btn.textContent = '···';
            errEl.hidden = true;
            okEl.hidden  = true;

            fetch('https://a.klaviyo.com/client/subscriptions/?company_id=<?php echo $company_id; ?>', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json', 'revision': '2024-02-15' },
              body: JSON.stringify({
                data: {
                  type: 'subscription',
                  attributes: {
                    custom_source: '<?php echo $custom_source; ?>',
                    profile: { data: { type: 'profile', attributes: { email: email } } }
                  }
                }
              })
            })
            .then(function (res) {
              if (res.status === 202 || res.ok) {
                form.style.display = 'none';
                okEl.hidden = false;
              } else { throw new Error('fail'); }
            })
            .catch(function () {
              btn.disabled = false;
              btn.textContent = '<?php echo esc_js( $s['button_label'] ); ?>';
              errEl.hidden = false;
            });
          });
        })();
        </script>
        <?php
    }
}
