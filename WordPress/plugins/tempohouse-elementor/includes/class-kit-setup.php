<?php
/**
 * Registers TEMPO House Global Colors, Typography, and a Global CSS
 * token block into Elementor's active Kit — the single source of truth
 * that drives all color pickers, font dropdowns, and variable resolution
 * inside the Elementor editor.
 *
 * Run via WP-CLI:  wp eval 'THR_Kit_Setup::apply();'
 * Or visit:        /wp-admin/admin.php?page=thr-elementor-setup&thr_apply_kit=1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class THR_Kit_Setup {

    // ── Brand colours (source: tokens.css) ────────────────────────────────
    // Maps to Elementor's 8 "system" colour slots + brand custom colours.
    private static $system_colors = [
        [ '_id' => 'primary',    'title' => 'Terracotta (Accent)',  'color' => '#7C3B3B' ],
        [ '_id' => 'secondary',  'title' => 'Ink (Text Dark)',      'color' => '#1A1816' ],
        [ '_id' => 'text',       'title' => 'Text Primary',         'color' => '#1A1816' ],
        [ '_id' => 'accent',     'title' => 'Amber',                'color' => '#DDAA62' ],
        [ '_id' => 'background', 'title' => 'Cream',                'color' => '#F7F3EE' ],
        [ '_id' => 'stroke',     'title' => 'Sand',                 'color' => '#C8B89A' ],
        [ '_id' => 'light',      'title' => 'Cream Dark',           'color' => '#EDE7DE' ],
        [ '_id' => 'dark',       'title' => 'Section Dark',         'color' => '#2D2924' ],
    ];

    private static $custom_colors = [
        [ '_id' => 'tempo-sage',         'title' => 'Sage',               'color' => '#6B7B5E' ],
        [ '_id' => 'tempo-bg-alt',       'title' => 'Background Alt',     'color' => '#F0EBE3' ],
        [ '_id' => 'tempo-accent-hover', 'title' => 'Terracotta Hover',   'color' => '#6A3232' ],
        [ '_id' => 'tempo-border',       'title' => 'Border Subtle',      'color' => 'rgba(26,24,22,0.10)' ],
        [ '_id' => 'tempo-text-muted',   'title' => 'Text Muted',         'color' => 'rgba(26,24,22,0.45)' ],
    ];

    // ── Typography presets ─────────────────────────────────────────────────
    // Maps to Elementor's 4 "system" typography slots.
    // Font sizes intentionally left as Elementor defaults — typography scale
    // is handled by tokens.css. The presets exist solely so font-family and
    // weight are available in every widget's typography control.
    private static $system_typography = [
        [
            '_id'                           => 'primary',
            'title'                         => 'Display — Bricolage Grotesque',
            'typography_typography'         => 'custom',
            'typography_font_family'        => 'Bricolage Grotesque',
            'typography_font_weight'        => '600',
            'typography_font_style'         => 'normal',
            'typography_text_transform'     => 'none',
            'typography_letter_spacing'     => [ 'unit' => 'em', 'size' => -0.025 ],
            'typography_line_height'        => [ 'unit' => 'em', 'size' => 1.1 ],
        ],
        [
            '_id'                           => 'secondary',
            'title'                         => 'Accent — Cormorant Garamond',
            'typography_typography'         => 'custom',
            'typography_font_family'        => 'Cormorant Garamond',
            'typography_font_weight'        => '400',
            'typography_font_style'         => 'italic',
            'typography_text_transform'     => 'none',
            'typography_letter_spacing'     => [ 'unit' => 'em', 'size' => 0 ],
            'typography_line_height'        => [ 'unit' => 'em', 'size' => 1.3 ],
        ],
        [
            '_id'                           => 'text',
            'title'                         => 'Body — Space Grotesk',
            'typography_typography'         => 'custom',
            'typography_font_family'        => 'Space Grotesk',
            'typography_font_weight'        => '400',
            'typography_font_style'         => 'normal',
            'typography_text_transform'     => 'none',
            'typography_letter_spacing'     => [ 'unit' => 'em', 'size' => 0 ],
            'typography_line_height'        => [ 'unit' => 'em', 'size' => 1.5 ],
        ],
        [
            '_id'                           => 'accent',
            'title'                         => 'Eyebrow — Bricolage Grotesque (caps)',
            'typography_typography'         => 'custom',
            'typography_font_family'        => 'Bricolage Grotesque',
            'typography_font_weight'        => '500',
            'typography_font_style'         => 'normal',
            'typography_text_transform'     => 'uppercase',
            'typography_letter_spacing'     => [ 'unit' => 'em', 'size' => 0.12 ],
            'typography_line_height'        => [ 'unit' => 'em', 'size' => 1.5 ],
        ],
    ];

    private static $custom_typography = [
        [
            '_id'                           => 'tempo-serif-body',
            'title'                         => 'Serif Body — Cormorant Garamond',
            'typography_typography'         => 'custom',
            'typography_font_family'        => 'Cormorant Garamond',
            'typography_font_weight'        => '300',
            'typography_font_style'         => 'normal',
            'typography_text_transform'     => 'none',
            'typography_letter_spacing'     => [ 'unit' => 'em', 'size' => 0 ],
            'typography_line_height'        => [ 'unit' => 'em', 'size' => 1.5 ],
        ],
    ];

    // ── CSS token block injected into Elementor's Global CSS ──────────────
    // This ensures --tempo-* and --color-* custom properties resolve inside
    // any Elementor-generated inline CSS (e.g. widget background colours
    // set as var(--color-accent) via the Custom CSS field on a widget).
    private static function get_global_css() {
        return '/* TEMPO House Design Tokens — auto-injected by tempohouse-elementor plugin */
:root {
  --tempo-cream:      #F7F3EE;
  --tempo-cream-dark: #EDE7DE;
  --tempo-ink:        #1A1816;
  --tempo-terracotta: #7C3B3B;
  --tempo-amber:      #DDAA62;
  --tempo-sage:       #6B7B5E;
  --tempo-sand:       #C8B89A;

  --color-bg:             var(--tempo-cream);
  --color-bg-alt:         #F0EBE3;
  --color-text-primary:   var(--tempo-ink);
  --color-text-secondary: rgba(26,24,22,0.72);
  --color-text-muted:     rgba(26,24,22,0.45);
  --color-text-inverse:   var(--tempo-cream);
  --color-accent:         var(--tempo-terracotta);
  --color-accent-hover:   #6A3232;
  --color-border:         rgba(26,24,22,0.10);
  --color-border-strong:  rgba(26,24,22,0.22);
  --color-border-accent:  rgba(123,59,59,0.4);
  --color-nav-bg:         rgba(247,243,238,0.92);
  --color-section-dark:   #2D2924;
  --tempo-frame-border:   var(--tempo-sand);

  --font-display: \'Bricolage Grotesque\', sans-serif;
  --font-accent:  \'Cormorant Garamond\', serif;
  --font-body:    \'Space Grotesk\', sans-serif;
}';
    }

    /**
     * Apply all settings to the active Elementor kit.
     * Safe to run multiple times — checks existing settings first.
     *
     * @return string  Status message.
     */
    public static function apply() {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return 'Elementor not loaded.';
        }

        $kit_id = \Elementor\Plugin::$instance->kits_manager->get_active_id();
        if ( ! $kit_id ) {
            return 'No active Elementor Kit found.';
        }

        $settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
        if ( ! is_array( $settings ) ) {
            $settings = [];
        }

        $settings['system_colors']     = self::$system_colors;
        $settings['custom_colors']     = self::$custom_colors;
        $settings['system_typography'] = self::$system_typography;
        $settings['custom_typography'] = self::$custom_typography;

        // Append token block to Global CSS — merge rather than overwrite
        $existing_css = $settings['custom_css'] ?? '';
        $token_marker = '/* TEMPO House Design Tokens';
        if ( strpos( $existing_css, $token_marker ) === false ) {
            $settings['custom_css'] = self::get_global_css() . "\n\n" . $existing_css;
        }

        update_post_meta( $kit_id, '_elementor_page_settings', $settings );

        // Clear Elementor's generated CSS cache so changes appear immediately
        \Elementor\Plugin::$instance->files_manager->clear_cache();

        return 'TEMPO House kit settings applied. Kit ID: ' . $kit_id;
    }

    /**
     * Admin page at WP Admin → Elementor → TEMPO Kit Setup.
     */
    public static function register_admin_page() {
        add_submenu_page(
            'elementor',
            'TEMPO Kit Setup',
            'TEMPO Kit Setup',
            'manage_options',
            'thr-elementor-setup',
            [ __CLASS__, 'render_admin_page' ]
        );
    }

    public static function render_admin_page() {
        $message = '';
        if ( isset( $_GET['thr_apply_kit'] ) && check_admin_referer( 'thr_apply_kit' ) ) {
            $message = self::apply();
        }
        ?>
        <div class="wrap">
          <h1>TEMPO House — Elementor Kit Setup</h1>
          <?php if ( $message ) : ?>
          <div class="notice notice-success"><p><?php echo esc_html( $message ); ?></p></div>
          <?php endif; ?>
          <p>Applies the TEMPO House brand colours, typography presets, and CSS token block to the active Elementor Kit. Safe to re-run — will update existing settings.</p>
          <p>After applying, go to <strong>Elementor → Site Settings → Global Colors</strong> and <strong>Global Typography</strong> to verify.</p>
          <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=thr-elementor-setup&thr_apply_kit=1' ), 'thr_apply_kit' ); ?>"
             class="button button-primary button-large">
            Apply TEMPO Kit Settings
          </a>

          <hr>
          <h2>What this applies</h2>
          <h3>System Colours</h3>
          <ul>
            <?php foreach ( self::$system_colors as $c ) : ?>
            <li>
              <span style="display:inline-block;width:16px;height:16px;background:<?php echo esc_attr($c['color']); ?>;border:1px solid #ccc;vertical-align:middle;margin-right:6px;border-radius:2px;"></span>
              <strong><?php echo esc_html($c['title']); ?></strong> — <?php echo esc_html($c['color']); ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <h3>Custom Colours</h3>
          <ul>
            <?php foreach ( self::$custom_colors as $c ) : ?>
            <li>
              <strong><?php echo esc_html($c['title']); ?></strong> — <?php echo esc_html($c['color']); ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <h3>Typography Presets</h3>
          <ul>
            <li><strong>Primary:</strong> Bricolage Grotesque 600 — Display headings</li>
            <li><strong>Secondary:</strong> Cormorant Garamond 400 Italic — Accent / pull quotes</li>
            <li><strong>Text:</strong> Space Grotesk 400 — Body copy</li>
            <li><strong>Accent:</strong> Bricolage Grotesque 500 Uppercase — Eyebrows / labels</li>
          </ul>
          <h3>Fonts (Google Fonts)</h3>
          <ul>
            <li>Bricolage Grotesque — 400, 500, 600</li>
            <li>Cormorant Garamond — 300, 400 (normal + italic)</li>
            <li>Space Grotesk — 300, 400, 500</li>
          </ul>
        </div>
        <?php
    }
}
