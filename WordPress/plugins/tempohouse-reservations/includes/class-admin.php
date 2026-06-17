<?php
defined( 'ABSPATH' ) || exit;

class THR_Admin {

    public function init(): void {
        add_action( 'admin_menu',            [ $this, 'add_menus' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_head',            [ $this, 'floor_plan_head_styles' ] );
        add_action( 'wp_ajax_thr_update_status', [ $this, 'ajax_update_status' ] );
    }

    public function floor_plan_head_styles(): void {
        if ( ( $_GET['page'] ?? '' ) !== 'thr-floor-plans' ) return;
        ?>
        <style id="thr-fp-reset">
            #wpcontent { padding-left: 0 !important; }
            #wpbody-content { padding-bottom: 0 !important; }
            #wpbody { padding-top: 0 !important; float: none !important; }
            .thr-fp-wrap { margin: 0 !important; padding: 0 !important; }
            #wpbody-content > .notice,
            #wpbody-content > .updated,
            #wpbody-content > .error { display: none !important; }
        </style>
        <?php
    }

    public function add_menus(): void {
        global $wpdb;
        $res_table = THR_Database::t( 'reservations' );
        $wl_table  = THR_Database::t( 'waitlist' );

        $pending_res = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $res_table WHERE status='pending'" );
        $pending_wl  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $wl_table WHERE status='waiting'" );
        $total_badge = $pending_res + $pending_wl;

        $menu_title = 'Reservations';
        if ( $total_badge > 0 ) {
            $menu_title .= ' <span class="update-plugins count-' . $total_badge . '"><span class="update-count">' . $total_badge . '</span></span>';
        }

        add_menu_page(
            'Reservations', $menu_title,
            'thr_view_reservations',
            'thr-dashboard',
            [ $this, 'page_dashboard' ],
            'dashicons-calendar-alt',
            30
        );

        $res_label = 'Reservations';
        if ( $pending_res > 0 ) {
            $res_label .= ' <span class="update-plugins count-' . $pending_res . '"><span class="update-count">' . $pending_res . '</span></span>';
        }
        $wl_label = 'Waitlist';
        if ( $pending_wl > 0 ) {
            $wl_label .= ' <span class="update-plugins count-' . $pending_wl . '"><span class="update-count">' . $pending_wl . '</span></span>';
        }

        add_submenu_page( 'thr-dashboard', 'Dashboard',        'Dashboard',        'thr_view_reservations',   'thr-dashboard',        [ $this, 'page_dashboard' ] );
        add_submenu_page( 'thr-dashboard', 'Reservations',    $res_label,         'thr_view_reservations',   'thr-reservations',     [ $this, 'page_list' ] );
        add_submenu_page( 'thr-dashboard', 'FOH Run-sheet',   'FOH Run-sheet',    'thr_view_reservations',   'thr-run-sheet',        [ $this, 'page_run_sheet' ] );
        add_submenu_page( 'thr-dashboard', 'Floor Plans',     'Floor Plans',      'thr_manage_floor_plans',  'thr-floor-plans',      [ $this, 'page_floor_plans' ] );
        add_submenu_page( 'thr-dashboard', 'Blocks',          'Blocks',           'thr_manage_settings',     'thr-blocks',           [ $this, 'page_blocks' ] );
        add_submenu_page( 'thr-dashboard', 'Tags',            'Tags',             'thr_manage_tags',         'thr-tags',             [ $this, 'page_tags' ] );
        add_submenu_page( 'thr-dashboard', 'Waitlist',        $wl_label,          'thr_view_reservations',   'thr-waitlist',         [ $this, 'page_waitlist' ] );
        add_submenu_page( 'thr-dashboard', 'Event Enquiries', 'Event Enquiries',  'thr_view_reservations',   'thr-event-enquiries',  [ $this, 'page_event_enquiries' ] );
        add_submenu_page( 'thr-dashboard', 'Shift Report',    'Shift Report',     'thr_view_reports',        'thr-shift-report',     [ $this, 'page_shift_report' ] );
        add_submenu_page( 'thr-dashboard', 'Settings',        'Settings',         'thr_manage_settings',     'thr-settings',         [ $this, 'page_settings' ] );

        // Hidden: single reservation view
        add_submenu_page( null, 'Reservation', 'Reservation', 'thr_view_reservations', 'thr-reservation', [ $this, 'page_single' ] );
    }

    public function enqueue_assets( string $hook ): void {
        $our_pages = [ 'thr-dashboard', 'thr-reservations', 'thr-run-sheet', 'thr-floor-plans', 'thr-blocks', 'thr-tags', 'thr-waitlist', 'thr-event-enquiries', 'thr-settings', 'thr-shift-report', 'thr-reservation' ];
        $page      = $_GET['page'] ?? '';
        if ( ! in_array( $page, $our_pages, true ) ) return;

        wp_enqueue_style( 'thr-admin', THR_PLUGIN_URL . 'assets/css/admin.css', [], THR_VERSION );
        $modal_v = (string) filemtime( THR_PLUGIN_DIR . 'assets/js/thr-modal.js' );
        wp_enqueue_script( 'thr-modal', THR_PLUGIN_URL . 'assets/js/thr-modal.js', [], $modal_v, true );
        wp_enqueue_script( 'thr-admin', THR_PLUGIN_URL . 'assets/js/admin.js', [ 'jquery', 'thr-modal' ], THR_VERSION, true );
        wp_localize_script( 'thr-admin', 'thrAdmin', [
            'apiUrl'   => rest_url( THR_REST_NS . '/' ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
            'adminUrl' => admin_url( 'admin.php' ),
        ] );

        if ( $page === 'thr-floor-plans' ) {
            $css_v = (string) filemtime( THR_PLUGIN_DIR . 'assets/css/floor-plan-builder.css' );
            $js_v  = (string) filemtime( THR_PLUGIN_DIR . 'assets/js/floor-plan-builder.js' );
            wp_enqueue_style(  'thr-fp', THR_PLUGIN_URL . 'assets/css/floor-plan-builder.css', [], $css_v );
            wp_enqueue_script( 'konva',  'https://unpkg.com/konva@9.3.14/konva.min.js', [], '9.3.14', true );
            wp_enqueue_script( 'thr-fp', THR_PLUGIN_URL . 'assets/js/floor-plan-builder.js', [ 'konva', 'thr-modal' ], $js_v, true );

            $types = [];
            foreach ( THR_API_Furniture::TYPES as $slug => $def ) {
                $types[ $slug ] = [ 'label' => $def['label'], 'capacity' => $def['cap'], 'shape' => $def['shape'] ];
            }
            wp_localize_script( 'thr-fp', 'thrFP', [
                'apiUrl' => rest_url( THR_REST_NS . '/' ),
                'nonce'  => wp_create_nonce( 'wp_rest' ),
                'types'  => $types,
                'today'  => gmdate( 'Y-m-d', time() + 7 * 3600 ),
                'newResUrl' => admin_url( 'admin.php?page=thr-reservations&action=add' ),
            ] );
        }
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────
    public function page_dashboard(): void {
        global $wpdb;
        $table    = THR_Database::t( 'reservations' );
        $today    = gmdate( 'Y-m-d', time() + 7 * 3600 );

        $counts = $wpdb->get_row( $wpdb->prepare(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status='pending'   THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status='confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status='seated'    THEN 1 ELSE 0 END) as seated,
                SUM(party_size) as covers
             FROM $table
             WHERE DATE(DATE_ADD(reservation_dt, INTERVAL 7 HOUR)) = %s
               AND status NOT IN ('cancelled','no_show')", $today
        ) );

        $upcoming = $wpdb->get_results( $wpdb->prepare(
            "SELECT r.*, DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR) AS dt_local,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tag_names
             FROM $table r
             LEFT JOIN " . THR_Database::t( 'reservation_tags' ) . " rt ON rt.reservation_id = r.id
             LEFT JOIN " . THR_Database::t( 'tags' ) . " t ON t.id = rt.tag_id
             WHERE DATE(DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR)) = %s
               AND r.status IN ('pending','confirmed','seated')
             GROUP BY r.id
             ORDER BY r.reservation_dt ASC", $today
        ) );
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">Today — <?= esc_html( gmdate( 'l, F j', time() + 7 * 3600 ) ) ?></h1>

            <div class="thr-stats-grid">
                <?php foreach ( [
                    [ 'Reservations', $counts->total ?? 0, '' ],
                    [ 'Covers', $counts->covers ?? 0, '' ],
                    [ 'Pending', $counts->pending ?? 0, 'thr-stat--warning' ],
                    [ 'Confirmed', $counts->confirmed ?? 0, 'thr-stat--ok' ],
                    [ 'Seated', $counts->seated ?? 0, 'thr-stat--live' ],
                ] as [ $label, $value, $cls ] ): ?>
                <div class="thr-stat <?= $cls ?>">
                    <span class="thr-stat__value"><?= (int) $value ?></span>
                    <span class="thr-stat__label"><?= esc_html( $label ) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <h2 class="thr-section-title">Today's reservations</h2>
            <?php if ( $upcoming ): ?>
            <table class="thr-table wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Guest</th>
                        <th>Party</th>
                        <th>Occasion</th>
                        <th>Status</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $upcoming as $r ): ?>
                    <tr class="<?= $r->is_vip ? 'thr-row-vip' : '' ?>">
                        <td><strong><?= esc_html( substr( $r->dt_local, 11, 5 ) ) ?></strong></td>
                        <td>
                            <?= esc_html( $r->diner_name ) ?>
                            <?php if ( $r->is_vip ): ?><span class="thr-badge thr-badge--vip">VIP</span><?php endif; ?>
                        </td>
                        <td><?= (int) $r->party_size ?></td>
                        <td><?= esc_html( ucfirst( $r->occasion ) ) ?></td>
                        <td><?php $this->status_badge( $r->status ); ?></td>
                        <td><?= esc_html( $r->tag_names ?: '—' ) ?></td>
                        <td><?php $this->row_actions( $r->id, $r->status, $r->reference_code ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="thr-empty">No reservations for today yet.</p>
            <?php endif; ?>

            <p style="margin-top:24px;">
                <a href="<?= admin_url( 'admin.php?page=thr-reservations' ) ?>" class="button button-primary">View all reservations</a>
                <a href="<?= admin_url( 'admin.php?page=thr-shift-report' ) ?>" class="button" style="margin-left:8px;">Print shift report</a>
            </p>
        </div>
        <?php
    }

    // ── Reservations list ─────────────────────────────────────────────────────
    public function page_list(): void {
        global $wpdb;
        $table = THR_Database::t( 'reservations' );

        $status_filter = sanitize_text_field( $_GET['status'] ?? '' );
        $date_filter   = sanitize_text_field( $_GET['date'] ?? '' );
        $search        = sanitize_text_field( $_GET['s'] ?? '' );
        $page          = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
        $per_page      = 25;
        $offset        = ( $page - 1 ) * $per_page;

        $where  = [ '1=1' ];
        $values = [];

        if ( $status_filter ) { $where[] = 'r.status = %s'; $values[] = $status_filter; }
        if ( $date_filter )   { $where[] = "DATE(DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR)) = %s"; $values[] = $date_filter; }
        if ( $search )        { $where[] = '(r.diner_name LIKE %s OR r.diner_email LIKE %s OR r.reference_code = %s)'; $values[] = "%$search%"; $values[] = "%$search%"; $values[] = strtoupper( $search ); }

        $where_sql = implode( ' AND ', $where );
        $count_sql = "SELECT COUNT(*) FROM $table r WHERE $where_sql";
        $rows_sql  = "SELECT r.*, DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR) AS dt_local,
                             GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tag_names
                      FROM $table r
                      LEFT JOIN " . THR_Database::t( 'reservation_tags' ) . " rt ON rt.reservation_id = r.id
                      LEFT JOIN " . THR_Database::t( 'tags' ) . " t ON t.id = rt.tag_id
                      WHERE $where_sql GROUP BY r.id
                      ORDER BY r.reservation_dt DESC LIMIT %d OFFSET %d";

        if ( $values ) {
            $total = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $values ) );
            $rows  = $wpdb->get_results( $wpdb->prepare( $rows_sql, array_merge( $values, [ $per_page, $offset ] ) ) );
        } else {
            $total = (int) $wpdb->get_var( $count_sql );
            $rows  = $wpdb->get_results( $wpdb->prepare( $rows_sql, $per_page, $offset ) );
        }

        $total_pages = (int) ceil( $total / $per_page );
        $base_url    = admin_url( 'admin.php?page=thr-reservations' );
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">
                Reservations
                <?php if ( current_user_can( 'thr_create_reservations' ) ): ?>
                <a href="<?= admin_url( 'admin.php?page=thr-reservation&action=new' ) ?>" class="page-title-action">Add New</a>
                <?php endif; ?>
            </h1>

            <!-- Filters -->
            <form method="get" class="thr-filters">
                <input type="hidden" name="page" value="thr-reservations">
                <input type="text" name="s" value="<?= esc_attr( $search ) ?>" placeholder="Search name, email, ref…" class="regular-text">
                <input type="date" name="date" value="<?= esc_attr( $date_filter ) ?>">
                <select name="status">
                    <option value="">All statuses</option>
                    <?php foreach ( [ 'pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show' ] as $s ): ?>
                    <option value="<?= $s ?>" <?= selected( $status_filter, $s, false ) ?>><?= ucfirst( str_replace( '_', ' ', $s ) ) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button">Filter</button>
                <?php if ( $search || $date_filter || $status_filter ): ?><a href="<?= $base_url ?>" class="button">Clear</a><?php endif; ?>
            </form>

            <p class="thr-result-count"><?= number_format( $total ) ?> reservation<?= $total !== 1 ? 's' : '' ?></p>

            <?php if ( $rows ): ?>
            <table class="thr-table wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Date/Time</th>
                        <th>Guest</th>
                        <th>Party</th>
                        <th>Occasion</th>
                        <th>Status</th>
                        <th>Tags</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $rows as $r ):
                    $is_returning = (bool) $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*) FROM $table WHERE diner_email = %s AND id != %d AND status IN ('confirmed','completed','seated')",
                        $r->diner_email, $r->id
                    ) );
                ?>
                <tr class="<?= $r->is_vip ? 'thr-row-vip' : '' ?>">
                    <td><a href="<?= admin_url( "admin.php?page=thr-reservation&id={$r->id}" ) ?>" class="thr-ref"><?= esc_html( $r->reference_code ) ?></a></td>
                    <td>
                        <?= esc_html( substr( $r->dt_local, 0, 10 ) ) ?><br>
                        <span class="thr-muted"><?= esc_html( substr( $r->dt_local, 11, 5 ) ) ?></span>
                    </td>
                    <td>
                        <?= esc_html( $r->diner_name ) ?>
                        <?php if ( $r->is_vip ): ?><span class="thr-badge thr-badge--vip">VIP</span><?php endif; ?>
                        <?php if ( $is_returning ): ?><span class="thr-badge thr-badge--confirmed" style="font-size:10px;margin-left:4px;">Returning</span><?php endif; ?>
                        <br><span class="thr-muted"><?= esc_html( $r->diner_email ) ?></span>
                    </td>
                    <td><?= (int) $r->party_size ?></td>
                    <td><?= esc_html( ucfirst( $r->occasion ) ) ?></td>
                    <td><?php $this->status_badge( $r->status ); ?></td>
                    <td><?= esc_html( $r->tag_names ?: '—' ) ?></td>
                    <td><?php $this->row_actions( $r->id, $r->status, $r->reference_code ); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ( $total_pages > 1 ): ?>
            <div class="thr-pagination">
                <?php for ( $i = 1; $i <= $total_pages; $i++ ): ?>
                <a href="<?= add_query_arg( 'paged', $i, $base_url . ( $status_filter ? "&status=$status_filter" : '' ) . ( $date_filter ? "&date=$date_filter" : '' ) . ( $search ? "&s=$search" : '' ) ) ?>"
                   class="button <?= $i === $page ? 'button-primary' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <p class="thr-empty">No reservations found.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    // ── Single reservation view / create ─────────────────────────────────────
    public function page_single(): void {
        global $wpdb;
        $id     = (int) ( $_GET['id'] ?? 0 );
        $action = sanitize_text_field( $_GET['action'] ?? '' );
        $table  = THR_Database::t( 'reservations' );

        // ── Create new reservation ────────────────────────────────────────────
        if ( $action === 'new' && current_user_can( 'thr_create_reservations' ) ) {
            $slots    = THR_Settings::time_slots();
            $today    = gmdate( 'Y-m-d', time() + 7 * 3600 );
            $occasion = THR_Settings::occasion_types(); // slug => label
            ?>
            <div class="wrap thr-wrap">
                <h1 class="thr-page-title">
                    New Reservation
                    <a href="<?= admin_url( 'admin.php?page=thr-reservations' ) ?>" class="page-title-action">← Back</a>
                </h1>
                <div class="thr-card" style="max-width:680px;">
                    <form id="thr-create-form">
                        <?php wp_nonce_field( 'wp_rest', '_wpnonce' ); ?>

                        <div class="thr-field-row">
                            <p class="thr-field">
                                <label class="thr-label">Date *</label>
                                <input type="date" id="thr-new-date" name="date" class="regular-text" value="<?= esc_attr( $today ) ?>" required>
                            </p>
                            <p class="thr-field">
                                <label class="thr-label">Time *</label>
                                <select id="thr-new-time" name="time" class="regular-text" required>
                                    <option value="">— select —</option>
                                    <?php foreach ( $slots as $t ): ?>
                                    <option value="<?= esc_attr( $t ) ?>"><?= esc_html( $t ) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                        </div>

                        <div class="thr-field-row">
                            <p class="thr-field">
                                <label class="thr-label">Guest name *</label>
                                <input type="text" name="diner_name" class="regular-text" required>
                            </p>
                            <p class="thr-field">
                                <label class="thr-label">Email *</label>
                                <input type="email" name="diner_email" class="regular-text" required>
                            </p>
                        </div>

                        <div class="thr-field-row">
                            <p class="thr-field">
                                <label class="thr-label">Phone</label>
                                <input type="tel" name="diner_phone" class="regular-text">
                            </p>
                            <p class="thr-field">
                                <label class="thr-label">Party size *</label>
                                <input type="number" name="party_size" class="small-text" value="2" min="1" max="<?= (int) THR_Settings::get( 'party_size_max', 20 ) ?>" required>
                            </p>
                        </div>

                        <div class="thr-field-row">
                            <p class="thr-field">
                                <label class="thr-label">Occasion</label>
                                <select name="occasion" class="regular-text">
                                    <?php foreach ( $occasion as $slug => $label ): ?>
                                    <option value="<?= esc_attr( $slug ) ?>"><?= esc_html( $label ) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </p>
                            <p class="thr-field">
                                <label class="thr-label">Language</label>
                                <select name="diner_lang" class="regular-text">
                                    <option value="en">English</option>
                                    <option value="vi">Vietnamese</option>
                                </select>
                            </p>
                        </div>

                        <p class="thr-field">
                            <label class="thr-label">
                                <input type="checkbox" name="is_vip" value="1"> VIP guest
                            </label>
                        </p>

                        <p class="thr-field">
                            <label class="thr-label">Guest notes</label>
                            <textarea name="notes_diner" rows="2" class="large-text"></textarea>
                        </p>

                        <p class="thr-field">
                            <label class="thr-label">Internal notes</label>
                            <textarea name="notes_internal" rows="2" class="large-text"></textarea>
                        </p>

                        <p id="thr-create-error" style="color:#c00;display:none;"></p>
                        <p><button type="submit" class="button button-primary">Create Reservation</button></p>
                    </form>
                </div>
            </div>
            <script>
            document.getElementById('thr-create-form').addEventListener('submit', function(e) {
                e.preventDefault();
                var form     = this;
                var btn      = form.querySelector('[type=submit]');
                var errEl    = document.getElementById('thr-create-error');
                var date     = form.date.value;
                var time     = form.time.value;
                var nonce    = form._wpnonce.value;
                if (!date || !time) { errEl.textContent = 'Date and time are required.'; errEl.style.display='block'; return; }

                // Build UTC datetime from GMT+7 input
                var localDt   = date + 'T' + time + ':00+07:00';
                var utcDt     = new Date(localDt).toISOString().slice(0,19).replace('T',' ');

                btn.disabled  = true;
                btn.textContent = 'Creating…';
                errEl.style.display = 'none';

                fetch(thrAdmin.apiUrl + 'reservations', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                    body: JSON.stringify({
                        diner_name:      form.diner_name.value.trim(),
                        diner_email:     form.diner_email.value.trim(),
                        diner_phone:     form.diner_phone.value.trim(),
                        diner_lang:      form.diner_lang.value,
                        reservation_dt:  utcDt,
                        party_size:      parseInt(form.party_size.value, 10),
                        occasion:        form.occasion.value,
                        notes_diner:     form.notes_diner.value.trim(),
                        notes_internal:  form.notes_internal.value.trim(),
                        is_vip:          form.is_vip.checked ? 1 : 0,
                    })
                })
                .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
                .then(function(res) {
                    if (!res.ok) throw new Error(res.data.message || 'Create failed.');
                    window.location.href = thrAdmin.adminUrl + '?page=thr-reservation&id=' + res.data.id + '&created=1';
                })
                .catch(function(err) {
                    errEl.textContent = err.message; errEl.style.display = 'block';
                    btn.disabled = false; btn.textContent = 'Create Reservation';
                });
            });
            </script>
            <?php
            return;
        }

        if ( $id ) {
            $r = $wpdb->get_row( $wpdb->prepare(
                "SELECT r.*, DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR) AS dt_local
                 FROM $table r WHERE r.id = %d", $id
            ) );
        }

        if ( isset( $r ) && $r ): ?>
        <div class="wrap thr-wrap">
            <?php if ( ! empty( $_GET['created'] ) ): ?>
            <div class="notice notice-success is-dismissible"><p>Reservation created and confirmation email sent.</p></div>
            <?php endif; ?>
            <?php if ( ! empty( $_GET['updated'] ) ): ?>
            <div class="notice notice-success is-dismissible"><p>Notes saved.</p></div>
            <?php endif; ?>
            <h1 class="thr-page-title">
                <?= esc_html( $r->diner_name ) ?>
                <span class="thr-ref-inline"><?= esc_html( $r->reference_code ) ?></span>
                <?php if ( $r->is_vip ): ?><span class="thr-badge thr-badge--vip">VIP</span><?php endif; ?>
                <a href="<?= admin_url( 'admin.php?page=thr-reservations' ) ?>" class="page-title-action">← Back</a>
            </h1>

            <div class="thr-detail-grid">
                <div class="thr-detail-main">
                    <div class="thr-card">
                        <h3>Reservation Details
                            <?php if ( current_user_can( 'thr_edit_reservations' ) ): ?>
                            <button type="button" id="thr-edit-details-btn" class="button button-small" style="float:right;">Edit</button>
                            <?php endif; ?>
                        </h3>

                        <!-- View mode -->
                        <table class="thr-detail-table" id="thr-details-view">
                            <tr><th>Status</th><td><?php $this->status_badge( $r->status ); ?></td></tr>
                            <tr><th>Date</th><td><?= esc_html( gmdate( 'l, F j, Y', strtotime( $r->dt_local ) ) ) ?></td></tr>
                            <tr><th>Time</th><td><?= esc_html( gmdate( 'g:ia', strtotime( $r->dt_local ) ) ) ?></td></tr>
                            <tr><th>Party size</th><td><?= (int) $r->party_size ?> guest<?= $r->party_size > 1 ? 's' : '' ?></td></tr>
                            <tr><th>Occasion</th><td><?= esc_html( ucfirst( $r->occasion ) ) ?></td></tr>
                            <tr><th>Area</th><td><?= esc_html( $r->area_label ?: '—' ) ?></td></tr>
                            <tr><th>Seated at</th><td><?= $r->seated_at ? esc_html( $r->seated_at ) : '—' ?></td></tr>
                        </table>

                        <!-- Edit mode -->
                        <?php if ( current_user_can( 'thr_edit_reservations' ) ):
                            $dt_local_val = substr( $r->dt_local, 0, 10 );
                            $tm_local_val = substr( $r->dt_local, 11, 5 );
                            $slots        = THR_Settings::time_slots();
                            $occasions    = THR_Settings::occasion_types();
                        ?>
                        <div id="thr-details-edit" style="display:none;">
                            <div class="thr-field-row" style="margin-bottom:12px;">
                                <p class="thr-field" style="margin:0;">
                                    <label class="thr-label">Date</label>
                                    <input type="date" id="thr-edit-date" class="regular-text" value="<?= esc_attr( $dt_local_val ) ?>">
                                </p>
                                <p class="thr-field" style="margin:0;">
                                    <label class="thr-label">Time</label>
                                    <select id="thr-edit-time" class="regular-text">
                                        <?php foreach ( $slots as $t ): ?>
                                        <option value="<?= esc_attr( $t ) ?>" <?= selected( $tm_local_val, $t, false ) ?>><?= esc_html( $t ) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </p>
                            </div>
                            <div class="thr-field-row" style="margin-bottom:12px;">
                                <p class="thr-field" style="margin:0;">
                                    <label class="thr-label">Party size</label>
                                    <input type="number" id="thr-edit-party" class="small-text" value="<?= (int) $r->party_size ?>" min="1" max="<?= (int) THR_Settings::get( 'party_size_max', 20 ) ?>">
                                </p>
                                <p class="thr-field" style="margin:0;">
                                    <label class="thr-label">Occasion</label>
                                    <select id="thr-edit-occasion" class="regular-text">
                                        <?php foreach ( $occasions as $slug => $label ): ?>
                                        <option value="<?= esc_attr( $slug ) ?>" <?= selected( $r->occasion, $slug, false ) ?>><?= esc_html( $label ) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </p>
                            </div>
                            <p id="thr-edit-error" style="color:#c00;display:none;font-size:13px;"></p>
                            <button type="button" id="thr-edit-save-btn" class="button button-primary">Save changes</button>
                            <button type="button" id="thr-edit-cancel-btn" class="button" style="margin-left:6px;">Cancel</button>
                        </div>
                        <script>
                        (function() {
                            var editBtn    = document.getElementById('thr-edit-details-btn');
                            var cancelBtn  = document.getElementById('thr-edit-cancel-btn');
                            var saveBtn    = document.getElementById('thr-edit-save-btn');
                            var viewEl     = document.getElementById('thr-details-view');
                            var editEl     = document.getElementById('thr-details-edit');
                            var errEl      = document.getElementById('thr-edit-error');

                            editBtn.addEventListener('click', function() {
                                viewEl.style.display = 'none'; editEl.style.display = '';
                                editBtn.textContent = 'Editing…'; editBtn.disabled = true;
                            });
                            cancelBtn.addEventListener('click', function() {
                                viewEl.style.display = ''; editEl.style.display = 'none';
                                editBtn.textContent = 'Edit'; editBtn.disabled = false;
                            });
                            saveBtn.addEventListener('click', function() {
                                errEl.style.display = 'none';
                                var date   = document.getElementById('thr-edit-date').value;
                                var time   = document.getElementById('thr-edit-time').value;
                                var party  = parseInt(document.getElementById('thr-edit-party').value, 10);
                                var occ    = document.getElementById('thr-edit-occasion').value;
                                if (!date || !time) { errEl.textContent = 'Date and time are required.'; errEl.style.display = 'block'; return; }

                                var localDt = date + 'T' + time + ':00+07:00';
                                var utcDt   = new Date(localDt).toISOString().slice(0,19).replace('T',' ');

                                saveBtn.disabled = true; saveBtn.textContent = 'Saving…';
                                fetch(thrAdmin.apiUrl + 'reservations/<?= (int) $r->id ?>', {
                                    method: 'PATCH',
                                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': thrAdmin.nonce },
                                    body: JSON.stringify({ reservation_dt: utcDt, party_size: party, occasion: occ })
                                })
                                .then(function(r) { return r.json().then(function(d) { return {ok: r.ok, d: d}; }); })
                                .then(function(res) {
                                    if (!res.ok) throw new Error(res.d.message || 'Update failed.');
                                    window.location.href = window.location.pathname + '?page=thr-reservation&id=<?= (int) $r->id ?>&updated=1';
                                })
                                .catch(function(err) {
                                    errEl.textContent = err.message; errEl.style.display = 'block';
                                    saveBtn.disabled = false; saveBtn.textContent = 'Save changes';
                                });
                            });
                        })();
                        </script>
                        <?php endif; ?>
                    </div>

                    <div class="thr-card">
                        <h3>Guest Information</h3>
                        <table class="thr-detail-table">
                            <tr><th>Name</th><td><?= esc_html( $r->diner_name ) ?></td></tr>
                            <tr><th>Email</th><td><a href="mailto:<?= esc_attr( $r->diner_email ) ?>"><?= esc_html( $r->diner_email ) ?></a></td></tr>
                            <tr><th>Phone</th><td><?= $r->diner_phone ? esc_html( $r->diner_phone ) : '—' ?></td></tr>
                            <tr><th>Zalo</th><td><?= isset( $r->diner_zalo ) && $r->diner_zalo ? esc_html( $r->diner_zalo ) : '—' ?></td></tr>
                            <tr><th>Language</th><td><?= esc_html( strtoupper( $r->diner_lang ) ) ?></td></tr>
                        </table>
                    </div>

                    <?php if ( $r->notes_diner || current_user_can( 'thr_edit_reservations' ) ): ?>
                    <div class="thr-card">
                        <h3>Notes</h3>
                        <?php if ( $r->notes_diner ): ?>
                        <p class="thr-notes-diner"><strong>Guest:</strong> <?= esc_html( $r->notes_diner ) ?></p>
                        <?php endif; ?>
                        <?php if ( current_user_can( 'thr_edit_reservations' ) ): ?>
                        <form method="post" action="<?= admin_url( 'admin-post.php' ) ?>">
                            <?php wp_nonce_field( 'thr_update_notes_' . $r->id, 'thr_nonce' ); ?>
                            <input type="hidden" name="action" value="thr_update_notes">
                            <input type="hidden" name="reservation_id" value="<?= $r->id ?>">
                            <label class="thr-label">Internal notes</label>
                            <textarea name="notes_internal" rows="4" class="large-text"><?= esc_textarea( $r->notes_internal ) ?></textarea>
                            <p><button type="submit" class="button button-secondary">Save notes</button></p>
                        </form>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="thr-detail-sidebar">
                    <!-- Status actions -->
                    <?php if ( current_user_can( 'thr_edit_reservations' ) ): ?>
                    <div class="thr-card">
                        <h3>Update Status</h3>
                        <div class="thr-status-actions">
                            <?php
                            $actions = [];
                            if ( $r->status === 'pending' )   $actions = [ 'confirmed' => 'Confirm', 'cancelled' => 'Cancel' ];
                            if ( $r->status === 'confirmed' ) $actions = [ 'seated' => 'Seat Now', 'no_show' => 'No-show', 'cancelled' => 'Cancel' ];
                            if ( $r->status === 'seated' )    $actions = [ 'completed' => 'Complete' ];
                            foreach ( $actions as $new_status => $label ):
                                if ( $new_status === 'cancelled' && ! current_user_can( 'thr_cancel_reservations' ) ) continue;
                            ?>
                            <button class="button thr-status-btn" data-id="<?= $r->id ?>" data-status="<?= $new_status ?>">
                                <?= esc_html( $label ) ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- VIP toggle -->
                    <?php if ( current_user_can( 'thr_edit_reservations' ) ): ?>
                    <div class="thr-card">
                        <label class="thr-toggle-label">
                            <input type="checkbox" class="thr-vip-toggle" data-id="<?= $r->id ?>" <?= checked( $r->is_vip, 1, false ) ?>>
                            Mark as VIP
                        </label>
                    </div>
                    <?php endif; ?>

                    <!-- Emails sent -->
                    <div class="thr-card">
                        <h3>Emails</h3>
                        <ul class="thr-email-status">
                            <li><?= $r->confirmation_sent_at ? '✓' : '○' ?> Confirmation</li>
                            <li><?= $r->reminder_24h_sent_at ? '✓' : '○' ?> 24h reminder</li>
                            <li><?= $r->reminder_4h_sent_at ? '✓' : '○' ?> 2h reminder</li>
                            <li><?= $r->feedback_sent_at ? '✓' : '○' ?> Feedback</li>
                        </ul>
                        <?php if ( current_user_can( 'thr_edit_reservations' ) ): ?>
                        <button class="button thr-resend-btn" data-id="<?= $r->id ?>" data-type="confirmation" style="margin-top:8px;">
                            Resend confirmation
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Guest history -->
                    <?php
                    $history = $wpdb->get_results( $wpdb->prepare(
                        "SELECT id, reference_code, status, reservation_dt, party_size, occasion,
                                DATE_ADD(reservation_dt, INTERVAL 7 HOUR) AS dt_local
                         FROM $table
                         WHERE diner_email = %s AND id != %d
                         ORDER BY reservation_dt DESC LIMIT 10",
                        $r->diner_email, $r->id
                    ) );
                    if ( $history ): ?>
                    <div class="thr-card">
                        <h3>Guest History</h3>
                        <p class="thr-muted" style="font-size:12px;margin-bottom:10px;"><?= count( $history ) ?> other visit<?= count( $history ) !== 1 ? 's' : '' ?> by <?= esc_html( $r->diner_email ) ?></p>
                        <ul style="list-style:none;margin:0;padding:0;">
                        <?php foreach ( $history as $h ): ?>
                        <li style="border-bottom:1px solid rgba(0,0,0,0.06);padding:6px 0;">
                            <a href="<?= admin_url( "admin.php?page=thr-reservation&id={$h->id}" ) ?>" class="thr-ref" style="font-size:12px;"><?= esc_html( $h->reference_code ) ?></a>
                            <span class="thr-muted" style="font-size:12px;margin-left:6px;"><?= esc_html( substr( $h->dt_local, 0, 10 ) ) ?></span>
                            <?php $this->status_badge( $h->status ); ?>
                            <span class="thr-muted" style="font-size:12px;"><?= (int) $h->party_size ?> pax</span>
                        </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="wrap"><p>Reservation not found. <a href="<?= admin_url( 'admin.php?page=thr-reservations' ) ?>">Back to list</a></p></div>
        <?php endif;
    }

    // ── Floor Plans ───────────────────────────────────────────────────────────
    public function page_floor_plans(): void {
        $slots = THR_Settings::time_slots();
        ?>
        <div class="wrap thr-fp-wrap">
        <div id="fp-app" data-mode="live">

          <!-- ═══ HEADER ══════════════════════════════════════════════════════ -->
          <header class="fp-header">
            <div class="fp-h-left">
              <span class="fp-h-title fp-live-only">Restaurant map</span>
              <span class="fp-h-title fp-builder-only">Restaurant map <span class="fp-h-mode-pill">Editing</span></span>
            </div>
            <div class="fp-h-right">
              <!-- Live mode actions -->
              <button class="fp-btn fp-btn-outline fp-live-only" id="fp-btn-edit">
                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><path d="M9 2l2 2L4 11H2V9L9 2z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Edit Floorplan
              </button>
              <a class="fp-btn fp-btn-primary fp-live-only" id="fp-btn-new-res" href="#">+ Reservation</a>
              <!-- Builder mode actions -->
              <button class="fp-btn fp-btn-ghost fp-builder-only" id="fp-btn-undo" disabled title="Undo (Ctrl+Z)">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M3 6.5H9.5a3.5 3.5 0 0 1 0 7H6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M3 6.5L5.5 4M3 6.5L5.5 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
              <button class="fp-btn fp-btn-ghost fp-builder-only" id="fp-btn-redo" disabled title="Redo (Ctrl+Y)">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M12 6.5H5.5a3.5 3.5 0 0 0 0 7H9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><path d="M12 6.5L9.5 4M12 6.5L9.5 9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
              <span class="fp-h-sep fp-builder-only"></span>
              <button class="fp-btn fp-btn-outline fp-builder-only" id="fp-btn-exit-builder">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M7 1L2 6l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Live view
              </button>
              <button class="fp-btn fp-btn-primary fp-builder-only" id="fp-btn-publish" disabled>Publish updates</button>
            </div>
          </header>

          <!-- ═══ FLOOR NAVIGATOR ═══════════════════════════════════════════ -->
          <nav class="fp-floor-nav" id="fp-floor-nav">
            <div class="fp-floor-tabs" id="fp-floor-tabs">
              <!-- populated by JS -->
            </div>
            <div class="fp-floor-nav-end">
              <!-- Floor background button (builder only) -->
              <div class="fp-bg-nav-wrap fp-builder-only" id="fp-bg-nav-wrap">
                <input type="file" id="fp-bg-file" accept="image/jpeg,image/png,image/webp" style="display:none" aria-hidden="true">
                <button class="fp-bg-nav-btn" id="fp-bg-nav-btn" type="button" title="Set floor background image">
                  <svg width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true"><rect x="1" y="2.5" width="11" height="8.5" rx="1.5" stroke="currentColor" stroke-width="1.2"/><circle cx="4.5" cy="5.5" r="1" fill="currentColor"/><path d="M1 9l3-3 2.5 2.5L9 6l3 3" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  Background
                  <span class="fp-bg-nav-dot" id="fp-bg-nav-dot" hidden></span>
                </button>
                <div class="fp-bg-popover" id="fp-bg-popover" hidden>
                  <div class="fp-bg-thumb-wrap">
                    <img class="fp-bg-thumb" id="fp-bg-thumb" src="" alt="" hidden>
                    <span class="fp-bg-no-thumb" id="fp-bg-no-thumb">No background image</span>
                  </div>
                  <button class="fp-btn fp-btn-outline fp-bg-upload-btn" id="fp-bg-upload" type="button">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M6 1v7M2 4l4-4 4 4M1 10h10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Upload image</span>
                  </button>
                  <button class="fp-btn fp-btn-outline fp-bg-move-btn" id="fp-bg-move-btn" type="button" hidden>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M6 1v10M1 6h10M6 1L4 3M6 1l2 2M6 11l-2-2M6 11l2-2M1 6l2-2M1 6l2 2M11 6l-2-2M11 6l-2 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Move / resize
                  </button>
                  <button class="fp-btn fp-btn-outline fp-bg-crop-btn" id="fp-bg-crop-btn" type="button" hidden>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 1v7a1 1 0 0 0 1 1h7M1 3h7a1 1 0 0 1 1 1v7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Crop
                  </button>
                  <div class="fp-bg-sliders" id="fp-bg-sliders" hidden>
                    <div class="fp-bg-ratio-row">
                      <span>Resize</span>
                      <button class="fp-bg-ratio-btn" id="fp-bg-ratio-btn" type="button" data-locked="true" title="Toggle aspect ratio lock">
                        <svg class="fp-bg-ratio-icon--locked" width="11" height="13" viewBox="0 0 11 13" fill="none" aria-hidden="true"><rect x="1" y="5.5" width="9" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M3 5.5V4a2.5 2.5 0 0 1 5 0v1.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        <svg class="fp-bg-ratio-icon--free" width="11" height="13" viewBox="0 0 11 13" fill="none" aria-hidden="true"><rect x="1" y="5.5" width="9" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M3 5.5V4a2.5 2.5 0 0 1 5 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" opacity="0.35"/></svg>
                        <span class="fp-bg-ratio-label">Lock ratio</span>
                      </button>
                    </div>
                    <div class="fp-bg-slider-row">
                      <label for="fp-bg-opacity">Opacity</label>
                      <input type="range" id="fp-bg-opacity" min="0" max="1" step="0.05" value="0.5">
                    </div>
                    <div class="fp-bg-slider-row">
                      <label for="fp-bg-scale">Scale</label>
                      <input type="range" id="fp-bg-scale" min="0.1" max="3" step="0.05" value="1">
                    </div>
                    <button class="fp-btn fp-btn-ghost fp-bg-remove-btn" id="fp-bg-remove" type="button">Remove background</button>
                  </div>
                </div>
              </div>
              <button class="fp-add-floor-btn fp-builder-only" id="fp-add-floor" title="Add floor">
                <svg width="11" height="11" viewBox="0 0 11 11" fill="none"><path d="M5.5 1v9M1 5.5h9" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                Floor
              </button>
            </div>
          </nav>

          <!-- ═══ TOOLBAR (builder only) ══════════════════════════════════════ -->
          <div class="fp-toolbar fp-builder-only" id="fp-toolbar"></div>

          <!-- ═══ LIVE SUBBAR ════════════════════════════════════════════════ -->
          <div class="fp-subbar fp-live-only" id="fp-subbar">
            <button class="fp-chip" id="fp-chip-date">
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><rect x="1" y="2" width="11" height="10" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M1 5h11M4 1v2M9 1v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
              <span id="fp-date-lbl">Today</span>
            </button>
            <button class="fp-chip" id="fp-chip-time">
              <svg width="13" height="13" viewBox="0 0 13 13" fill="none"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/><path d="M6.5 3.5V6.5l2 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
              <span id="fp-time-lbl">All day</span>
            </button>
            <span class="fp-subbar-sep"></span>
            <div class="fp-view-tabs">
              <button class="fp-view-tab fp-view-tab--active" data-view="map">2D floor plan</button>
              <button class="fp-view-tab fp-view-tab--disabled" data-view="calendar" disabled title="Calendar view coming soon">Calendar</button>
            </div>
          </div>

          <!-- ═══ BODY ═══════════════════════════════════════════════════════ -->
          <div class="fp-body" id="fp-body">

            <!-- Canvas -->
            <div class="fp-canvas-wrap" id="fp-canvas-wrap">
              <div class="fp-canvas-toolbar fp-builder-only" id="fp-canvas-toolbar">
                <div class="fp-tool-group">
                  <button class="fp-tool" id="fp-zoom-in" title="Zoom in">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.3"/><path d="M10 10l3 3M4 6h4M6 4v4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                  </button>
                  <button class="fp-tool" id="fp-zoom-out" title="Zoom out">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="6" cy="6" r="4.5" stroke="currentColor" stroke-width="1.3"/><path d="M10 10l3 3M4 6h4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                  </button>
                  <button class="fp-tool" id="fp-zoom-fit" title="Fit all">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1 5V2a1 1 0 0 1 1-1h3M1 9v3a1 1 0 0 0 1 1h3M13 5V2a1 1 0 0 0-1-1h-3M13 9v3a1 1 0 0 1-1 1h-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                  </button>
                </div>
                <span class="fp-tool-sep"></span>
                <div class="fp-tool-group">
                  <button class="fp-tool fp-tool--delete" id="fp-delete-sel" title="Delete selected" disabled>
                    <svg width="13" height="14" viewBox="0 0 13 14" fill="none"><path d="M1 3.5h11M4 3.5V2.5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1M5 6.5v4M8 6.5v4M2 3.5l.7 8a1 1 0 0 0 1 .9h5.6a1 1 0 0 0 1-.9l.7-8" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                  </button>
                </div>
                <div class="fp-zoom-display" id="fp-zoom-pct">100%</div>
              </div>
              <div id="fp-konva"></div>
              <div class="fp-place-hint" id="fp-place-hint" hidden>
                Click canvas to place &nbsp;·&nbsp; <kbd>Esc</kbd> to cancel
              </div>
              <div class="fp-bg-edit-bar" id="fp-bg-edit-bar" hidden>
                Drag to move &nbsp;·&nbsp; Drag handles to resize &nbsp;·&nbsp; <kbd>Esc</kbd> to finish
                <button class="fp-bg-edit-done" id="fp-bg-edit-done" type="button">Done</button>
              </div>
              <div class="fp-bg-crop-bar" id="fp-bg-crop-bar" hidden>
                Drag handles to crop &nbsp;·&nbsp; <kbd>Esc</kbd> to cancel
                <button class="fp-bg-edit-done" id="fp-bg-crop-reset" type="button">Reset</button>
                <button class="fp-bg-edit-done fp-bg-edit-done--apply" id="fp-bg-crop-apply" type="button">Apply crop</button>
              </div>
              <!-- Floating properties panel (desktop: near item; mobile: bottom sheet) -->
              <div class="fp-float-panel" id="fp-float-panel" hidden>
                <div class="fp-fp-handle"></div>
                <div class="fp-fp-hd">
                  <span class="fp-fp-title" id="fp-fp-title">Table</span>
                  <button class="fp-fp-close" id="fp-fp-close" type="button" aria-label="Close">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                  </button>
                </div>
                <div class="fp-fp-body" id="fp-fp-body"></div>
                <div class="fp-fp-foot fp-builder-only" id="fp-fp-foot">
                  <button class="fp-btn fp-btn-danger-ghost" id="fp-fp-delete" type="button" disabled>
                    <svg width="12" height="13" viewBox="0 0 12 13" fill="none"><path d="M.5 3h11M3.5 3V2a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1M4.5 6v4M7.5 6v4M1.5 3l.7 7.5a1 1 0 0 0 1 .9h5.6a1 1 0 0 0 1-.9l.7-7.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    Remove
                  </button>
                </div>
              </div>
            </div>


          </div><!-- .fp-body -->

          <!-- ═══ LIVE LEGEND ════════════════════════════════════════════════ -->
          <footer class="fp-legend fp-live-only">
            <span class="fp-legend-item"><i class="fp-dot" style="background:#3B82F6"></i>Booked</span>
            <span class="fp-legend-item"><i class="fp-dot" style="background:#F97316"></i>Occupied</span>
            <span class="fp-legend-item"><i class="fp-dot" style="background:#22C55E"></i>Free</span>
            <span class="fp-legend-item"><i class="fp-dot" style="background:#9CA3AF"></i>Blocked</span>
          </footer>

          <!-- ═══ FLOATING PANELS ═══════════════════════════════════════════ -->
          <div class="fp-datepicker" id="fp-date-panel" hidden>
            <input type="date" id="fp-date-input" class="fp-date-input">
          </div>
          <div class="fp-timepicker" id="fp-time-panel" hidden>
            <div class="fp-time-opt fp-time-opt--active" data-time="">All day</div>
            <?php foreach ( $slots as $slot ):
              $h = (int) substr( $slot, 0, 2 );
              $m = substr( $slot, 3, 2 );
              $label = ( $h % 12 ?: 12 ) . ':' . $m . ( $h >= 12 ? ' PM' : ' AM' );
            ?>
            <div class="fp-time-opt" data-time="<?= esc_attr( $slot ) ?>"><?= esc_html( $label ) ?></div>
            <?php endforeach; ?>
          </div>

          <!-- Toast notification -->
          <div class="fp-toast" id="fp-toast" role="status" aria-live="polite"></div>

        </div><!-- #fp-app -->
        </div><!-- .wrap -->
        <?php
    }

    // ── Availability Blocks ───────────────────────────────────────────────────
    public function page_blocks(): void {
        global $wpdb;
        $blocks_table = THR_Database::t( 'availability_blocks' );
        $floors_table = THR_Database::t( 'floor_plans' );

        // Handle delete
        if ( isset( $_POST['thr_block_delete'] ) && wp_verify_nonce( $_POST['thr_nonce'] ?? '', 'thr_block_delete' ) ) {
            $del_id = (int) ( $_POST['block_id'] ?? 0 );
            if ( $del_id ) $wpdb->delete( $blocks_table, [ 'id' => $del_id ] );
            echo '<div class="notice notice-success"><p>Block removed.</p></div>';
        }

        // Handle create — form sends venue local time (GMT+7), convert to UTC for storage
        if ( isset( $_POST['thr_block_create'] ) && wp_verify_nonce( $_POST['thr_nonce'] ?? '', 'thr_block_create' ) ) {
            $scope    = sanitize_text_field( $_POST['scope'] ?? 'venue' );
            $scope_id = (int) ( $_POST['scope_id'] ?? 0 );
            $from_raw = sanitize_text_field( str_replace( 'T', ' ', $_POST['blocked_from'] ?? '' ) );
            $to_raw   = sanitize_text_field( str_replace( 'T', ' ', $_POST['blocked_to']   ?? '' ) );
            $reason   = sanitize_text_field( $_POST['reason'] ?? '' );

            // Convert venue local (GMT+7) → UTC
            $from_utc = $from_raw ? gmdate( 'Y-m-d H:i:s', strtotime( $from_raw . ' +0700' ) ) : '';
            $to_utc   = $to_raw   ? gmdate( 'Y-m-d H:i:s', strtotime( $to_raw   . ' +0700' ) ) : '';

            if ( $from_utc && $to_utc && strtotime( $from_utc ) < strtotime( $to_utc ) ) {
                $wpdb->insert( $blocks_table, [
                    'scope'        => $scope,
                    'scope_id'     => in_array( $scope, [ 'floor', 'furniture' ] ) && $scope_id ? $scope_id : null,
                    'blocked_from' => $from_utc,
                    'blocked_to'   => $to_utc,
                    'reason'       => $reason,
                    'created_by'   => get_current_user_id(),
                    'created_at'   => THR_API::now_utc(),
                ] );
                echo '<div class="notice notice-success"><p>Block added.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Invalid date range.</p></div>';
            }
        }

        $blocks = $wpdb->get_results(
            "SELECT b.*, u.display_name AS created_by_name
             FROM $blocks_table b
             LEFT JOIN {$wpdb->users} u ON u.ID = b.created_by
             ORDER BY b.blocked_from DESC LIMIT 100"
        );

        $floors = $wpdb->get_results( "SELECT id, name, floor_number FROM $floors_table ORDER BY floor_number ASC" );
        $today  = gmdate( 'Y-m-d', time() + 7 * 3600 );
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">Availability Blocks</h1>
            <p class="description">Block the entire venue, a floor, or specific furniture from accepting reservations during a date/time window (e.g. private events, maintenance, closures).</p>

            <!-- Add block form -->
            <div class="thr-card" style="max-width:660px;margin:20px 0;">
                <h3>Add Block</h3>
                <form method="post">
                    <?php wp_nonce_field( 'thr_block_create', 'thr_nonce' ); ?>
                    <div class="thr-field-row">
                        <p class="thr-field">
                            <label class="thr-label">Scope</label>
                            <select name="scope" id="thr-block-scope" class="regular-text" onchange="thrBlockScopeChange(this.value)">
                                <option value="venue">Whole venue</option>
                                <option value="floor">Floor</option>
                            </select>
                        </p>
                        <p class="thr-field" id="thr-block-scope-id-row" style="display:none;">
                            <label class="thr-label">Floor</label>
                            <select name="scope_id" class="regular-text">
                                <option value="">— select —</option>
                                <?php foreach ( $floors as $f ): ?>
                                <option value="<?= (int) $f->id ?>"><?= esc_html( $f->name ?: "Floor {$f->floor_number}" ) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </p>
                    </div>
                    <div class="thr-field-row">
                        <p class="thr-field">
                            <label class="thr-label">Block from (venue time GMT+7)</label>
                            <input type="datetime-local" name="blocked_from" class="regular-text" value="<?= esc_attr( $today . 'T18:00' ) ?>" required>
                        </p>
                        <p class="thr-field">
                            <label class="thr-label">Block until (venue time GMT+7)</label>
                            <input type="datetime-local" name="blocked_to" class="regular-text" value="<?= esc_attr( $today . 'T23:30' ) ?>" required>
                        </p>
                    </div>
                    <p class="thr-field">
                        <label class="thr-label">Reason (optional)</label>
                        <input type="text" name="reason" class="large-text" placeholder="Private event, maintenance…">
                    </p>
                    <p><button type="submit" name="thr_block_create" class="button button-primary">Add Block</button></p>
                </form>
            </div>

            <!-- Active / upcoming blocks -->
            <h2 class="thr-section-title">Current &amp; upcoming blocks</h2>
            <?php
            $upcoming = array_filter( $blocks, fn($b) => strtotime( $b->blocked_to . ' UTC' ) > time() );
            if ( $upcoming ): ?>
            <table class="thr-table wp-list-table widefat striped">
                <thead>
                    <tr><th>Scope</th><th>From</th><th>To</th><th>Reason</th><th>By</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ( $upcoming as $b ):
                    $scope_label = match( $b->scope ) {
                        'venue'     => 'Whole venue',
                        'floor'     => 'Floor ' . $b->scope_id,
                        'furniture' => 'Furniture #' . $b->scope_id,
                        default     => $b->scope,
                    };
                    $is_active = strtotime( $b->blocked_from . ' UTC' ) <= time() && strtotime( $b->blocked_to . ' UTC' ) > time();
                ?>
                <tr <?= $is_active ? 'style="background:rgba(192,57,43,0.06);"' : '' ?>>
                    <td>
                        <?= esc_html( $scope_label ) ?>
                        <?php if ( $is_active ): ?><span class="thr-badge thr-badge--seated" style="margin-left:6px;">Active</span><?php endif; ?>
                    </td>
                    <td><?= esc_html( gmdate( 'D j M Y, H:i', strtotime( $b->blocked_from . ' UTC' ) + 7 * 3600 ) ) ?></td>
                    <td><?= esc_html( gmdate( 'D j M Y, H:i', strtotime( $b->blocked_to . ' UTC' ) + 7 * 3600 ) ) ?></td>
                    <td><?= esc_html( $b->reason ?: '—' ) ?></td>
                    <td><?= esc_html( $b->created_by_name ?: '—' ) ?></td>
                    <td>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Remove this block?')">
                            <?php wp_nonce_field( 'thr_block_delete', 'thr_nonce' ); ?>
                            <input type="hidden" name="block_id" value="<?= (int) $b->id ?>">
                            <button type="submit" name="thr_block_delete" class="button button-small thr-btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="thr-empty">No upcoming blocks — all time slots are open.</p>
            <?php endif; ?>

            <!-- Past blocks -->
            <?php
            $past = array_filter( $blocks, fn($b) => strtotime( $b->blocked_to . ' UTC' ) <= time() );
            if ( $past ): ?>
            <h2 class="thr-section-title" style="margin-top:32px;">Past blocks</h2>
            <table class="thr-table wp-list-table widefat striped">
                <thead><tr><th>Scope</th><th>From</th><th>To</th><th>Reason</th></tr></thead>
                <tbody>
                <?php foreach ( $past as $b ):
                    $scope_label = match( $b->scope ) { 'venue' => 'Whole venue', 'floor' => 'Floor ' . $b->scope_id, 'furniture' => 'Furniture #' . $b->scope_id, default => $b->scope };
                ?>
                <tr style="opacity:0.55;">
                    <td><?= esc_html( $scope_label ) ?></td>
                    <td><?= esc_html( gmdate( 'D j M Y, H:i', strtotime( $b->blocked_from . ' UTC' ) + 7 * 3600 ) ) ?></td>
                    <td><?= esc_html( gmdate( 'D j M Y, H:i', strtotime( $b->blocked_to . ' UTC' ) + 7 * 3600 ) ) ?></td>
                    <td><?= esc_html( $b->reason ?: '—' ) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <script>
        function thrBlockScopeChange(val) {
            document.getElementById('thr-block-scope-id-row').style.display = (val === 'floor') ? '' : 'none';
        }
        </script>
        <?php
    }

    // ── Tags management ───────────────────────────────────────────────────────
    public function page_tags(): void {
        global $wpdb;
        $table = THR_Database::t( 'tags' );

        if ( isset( $_POST['thr_tag_delete'] ) && wp_verify_nonce( $_POST['thr_nonce'] ?? '', 'thr_tag_delete' ) ) {
            $del_id = (int) ( $_POST['tag_id'] ?? 0 );
            $tag    = $del_id ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $del_id ) ) : null;
            if ( $tag && ! $tag->is_system ) {
                $wpdb->delete( THR_Database::t( 'reservation_tags' ), [ 'tag_id' => $del_id ] );
                $wpdb->delete( $table, [ 'id' => $del_id ] );
                echo '<div class="notice notice-success"><p>Tag deleted.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>System tags cannot be deleted.</p></div>';
            }
        }

        if ( isset( $_POST['thr_tag_create'] ) && wp_verify_nonce( $_POST['thr_nonce'] ?? '', 'thr_tag_create' ) ) {
            $name  = sanitize_text_field( $_POST['tag_name'] ?? '' );
            $color = sanitize_hex_color( $_POST['tag_color'] ?? '' ) ?: '#888888';
            if ( $name ) {
                $slug = sanitize_title( $name );
                if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE slug = %s", $slug ) ) ) {
                    $wpdb->insert( $table, [ 'name' => $name, 'slug' => $slug, 'color' => $color, 'is_system' => 0, 'created_at' => THR_API::now_utc() ] );
                    echo '<div class="notice notice-success"><p>Tag created.</p></div>';
                } else {
                    echo '<div class="notice notice-error"><p>A tag with this name already exists.</p></div>';
                }
            }
        }

        $tags = $wpdb->get_results( "SELECT *, (SELECT COUNT(*) FROM " . THR_Database::t( 'reservation_tags' ) . " rt WHERE rt.tag_id = id) AS usage_count FROM $table ORDER BY is_system DESC, name ASC" );
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">Tags</h1>

            <?php if ( current_user_can( 'thr_manage_tags' ) ): ?>
            <div class="thr-card" style="max-width:480px;margin-bottom:24px;">
                <h3>Add Tag</h3>
                <form method="post">
                    <?php wp_nonce_field( 'thr_tag_create', 'thr_nonce' ); ?>
                    <div class="thr-field-row">
                        <p class="thr-field">
                            <label class="thr-label">Tag name</label>
                            <input type="text" name="tag_name" class="regular-text" placeholder="e.g. Returning Guest" required>
                        </p>
                        <p class="thr-field">
                            <label class="thr-label">Colour</label>
                            <input type="color" name="tag_color" value="#ddaa62" style="height:34px;width:60px;padding:2px;border-radius:3px;">
                        </p>
                    </div>
                    <p><button type="submit" name="thr_tag_create" class="button button-primary">Add Tag</button></p>
                </form>
            </div>
            <?php endif; ?>

            <table class="thr-table wp-list-table widefat striped">
                <thead>
                    <tr><th>Tag</th><th>Slug</th><th>Colour</th><th>Uses</th><th>Type</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ( $tags as $tag ): ?>
                <tr>
                    <td>
                        <span class="thr-badge" style="background:<?= esc_attr( $tag->color ) ?>;color:#fff;font-weight:600;">
                            <?= esc_html( $tag->name ) ?>
                        </span>
                    </td>
                    <td><code><?= esc_html( $tag->slug ) ?></code></td>
                    <td>
                        <span style="display:inline-block;width:20px;height:20px;background:<?= esc_attr( $tag->color ) ?>;border-radius:3px;vertical-align:middle;"></span>
                        <?= esc_html( $tag->color ) ?>
                    </td>
                    <td><?= (int) $tag->usage_count ?></td>
                    <td><?= $tag->is_system ? '<span class="thr-badge thr-badge--confirmed">System</span>' : 'Custom' ?></td>
                    <td>
                        <?php if ( ! $tag->is_system && current_user_can( 'thr_manage_tags' ) ): ?>
                        <form method="post" style="display:inline;" onsubmit="return confirm('Delete tag <?= esc_js( $tag->name ) ?>?')">
                            <?php wp_nonce_field( 'thr_tag_delete', 'thr_nonce' ); ?>
                            <input type="hidden" name="tag_id" value="<?= (int) $tag->id ?>">
                            <button type="submit" name="thr_tag_delete" class="button button-small thr-btn-danger">Delete</button>
                        </form>
                        <?php else: ?>
                        <span class="thr-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    // ── Waitlist ──────────────────────────────────────────────────────────────
    public function page_waitlist(): void {
        global $wpdb;
        $table = THR_Database::t( 'waitlist' );

        $status_filter = sanitize_text_field( $_GET['status'] ?? 'waiting' );
        $date_filter   = sanitize_text_field( $_GET['date'] ?? '' );

        $where  = [ '1=1' ];
        $values = [];
        if ( $status_filter ) { $where[] = 'status = %s'; $values[] = $status_filter; }
        if ( $date_filter )   { $where[] = 'requested_date = %s'; $values[] = $date_filter; }

        $sql  = "SELECT * FROM $table WHERE " . implode( ' AND ', $where ) . " ORDER BY requested_date ASC, created_at ASC LIMIT 200";
        $rows = $values ? $wpdb->get_results( $wpdb->prepare( $sql, $values ) ) : $wpdb->get_results( $sql );

        $waiting_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status='waiting'" );
        $base_url      = admin_url( 'admin.php?page=thr-waitlist' );

        $statuses = [ 'waiting', 'notified', 'converted', 'expired' ];
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">
                Waitlist
                <?php if ( $waiting_count ): ?>
                <span class="thr-badge thr-badge--pending" style="margin-left:8px;"><?= $waiting_count ?> waiting</span>
                <?php endif; ?>
            </h1>
            <p class="description">Guests who joined when no slots were available. Use <strong>Notify</strong> to alert them a table has opened, or <strong>Convert</strong> to create a confirmed reservation.</p>

            <form method="get" class="thr-filters">
                <input type="hidden" name="page" value="thr-waitlist">
                <input type="date" name="date" value="<?= esc_attr( $date_filter ) ?>">
                <select name="status">
                    <option value="">All statuses</option>
                    <?php foreach ( $statuses as $s ): ?>
                    <option value="<?= $s ?>" <?= selected( $status_filter, $s, false ) ?>><?= ucfirst( $s ) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button">Filter</button>
                <?php if ( $date_filter || $status_filter !== 'waiting' ): ?>
                <a href="<?= $base_url ?>" class="button">Clear</a>
                <?php endif; ?>
            </form>

            <?php if ( $rows ): ?>
            <table class="thr-table wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Guest</th>
                        <th>Date</th>
                        <th>Time pref</th>
                        <th>Party</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $rows as $wl ): ?>
                <tr>
                    <td><span class="thr-ref"><?= esc_html( $wl->reference_code ) ?></span></td>
                    <td>
                        <?= esc_html( $wl->diner_name ) ?><br>
                        <span class="thr-muted"><?= esc_html( $wl->diner_email ) ?></span>
                    </td>
                    <td><?= esc_html( date( 'D j M Y', strtotime( $wl->requested_date ) ) ) ?></td>
                    <td><?= $wl->requested_time ? esc_html( $wl->requested_time ) : '<span class="thr-muted">Any</span>' ?></td>
                    <td><?= (int) $wl->party_size ?></td>
                    <td><?php $this->waitlist_status_badge( $wl->status ); ?></td>
                    <td><span class="thr-muted"><?= esc_html( substr( $wl->created_at, 0, 10 ) ) ?></span></td>
                    <td class="thr-waitlist-actions" data-id="<?= (int) $wl->id ?>" data-ref="<?= esc_attr( $wl->reference_code ) ?>" data-date="<?= esc_attr( $wl->requested_date ) ?>" data-time="<?= esc_attr( $wl->requested_time ?? '' ) ?>">
                        <?php if ( $wl->status === 'waiting' && current_user_can( 'thr_edit_reservations' ) ): ?>
                        <button class="button button-small thr-wl-notify" data-id="<?= (int) $wl->id ?>">Notify</button>
                        <?php endif; ?>
                        <?php if ( in_array( $wl->status, [ 'waiting', 'notified' ], true ) && current_user_can( 'thr_create_reservations' ) ): ?>
                        <button class="button button-small thr-wl-convert" data-id="<?= (int) $wl->id ?>" data-date="<?= esc_attr( $wl->requested_date ) ?>" data-time="<?= esc_attr( $wl->requested_time ?? '' ) ?>">Convert</button>
                        <?php endif; ?>
                        <?php if ( current_user_can( 'thr_edit_reservations' ) ): ?>
                        <button class="button button-small thr-btn-danger thr-wl-delete" data-id="<?= (int) $wl->id ?>">Delete</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="thr-empty">No waitlist entries found.</p>
            <?php endif; ?>
        </div>
        <script>
        (function() {
            var nonce = thrAdmin.nonce;
            var api   = thrAdmin.apiUrl;

            document.querySelectorAll('.thr-wl-notify').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = btn.dataset.id;
                    if (!confirm('Send notification email to this guest?')) return;
                    btn.disabled = true; btn.textContent = 'Sending…';
                    fetch(api + 'waitlist/' + id + '/notify', {
                        method: 'POST',
                        headers: { 'X-WP-Nonce': nonce, 'Content-Type': 'application/json' }
                    })
                    .then(function(r) { return r.json(); })
                    .then(function() { location.reload(); })
                    .catch(function() { btn.disabled = false; btn.textContent = 'Notify'; alert('Error sending notification.'); });
                });
            });

            document.querySelectorAll('.thr-wl-convert').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id   = btn.dataset.id;
                    var date = btn.dataset.date;
                    var time = btn.dataset.time || prompt('Enter reservation time (HH:MM) for ' + date + ':');
                    if (!time) return;
                    if (!confirm('Convert to confirmed reservation at ' + date + ' ' + time + '?')) return;
                    btn.disabled = true; btn.textContent = 'Converting…';
                    fetch(api + 'waitlist/' + id + '/convert', {
                        method: 'POST',
                        headers: { 'X-WP-Nonce': nonce, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ time: time })
                    })
                    .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
                    .then(function(res) {
                        if (!res.ok) throw new Error(res.data.message || 'Convert failed.');
                        alert('Reservation ' + res.data.reference_code + ' created and confirmation email sent.');
                        location.reload();
                    })
                    .catch(function(err) { btn.disabled = false; btn.textContent = 'Convert'; alert(err.message); });
                });
            });

            document.querySelectorAll('.thr-wl-delete').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var id = btn.dataset.id;
                    if (!confirm('Delete this waitlist entry? This cannot be undone.')) return;
                    btn.disabled = true;
                    fetch(api + 'waitlist/' + id, {
                        method: 'DELETE',
                        headers: { 'X-WP-Nonce': nonce }
                    })
                    .then(function() { btn.closest('tr').remove(); })
                    .catch(function() { btn.disabled = false; alert('Error deleting entry.'); });
                });
            });
        })();
        </script>
        <?php
    }

    // ── FOH Mobile Run-sheet ──────────────────────────────────────────────────
    public function page_run_sheet(): void {
        $today     = gmdate( 'Y-m-d', time() + 7 * 3600 );
        $today_fmt = gmdate( 'l, F j, Y', time() + 7 * 3600 );
        $api_url   = rest_url( THR_REST_NS . '/' );
        $nonce     = wp_create_nonce( 'wp_rest' );
        ?>
        <style>
        #wpcontent { padding-left: 0 !important; }
        #wpbody { padding-top: 0 !important; }
        .thr-rs-wrap { max-width: 100%; padding: 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #0d0d0d; min-height: 100vh; color: #F7F3EE; }
        .thr-rs-header { display: flex; align-items: center; justify-content: space-between; padding: 12px 0 16px; border-bottom: 1px solid rgba(221,170,98,0.2); margin-bottom: 16px; }
        .thr-rs-title { font-size: 20px; font-weight: 700; color: #DDAA62; margin: 0; }
        .thr-rs-date { font-size: 13px; color: rgba(247,243,238,0.5); margin-top: 2px; }
        .thr-rs-summary { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .thr-rs-stat { background: rgba(221,170,98,0.08); border: 1px solid rgba(221,170,98,0.15); border-radius: 6px; padding: 10px 16px; text-align: center; min-width: 80px; }
        .thr-rs-stat__val { font-size: 22px; font-weight: 700; color: #DDAA62; display: block; }
        .thr-rs-stat__lbl { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(247,243,238,0.4); margin-top: 2px; display: block; }
        .thr-rs-table { width: 100%; border-collapse: collapse; }
        .thr-rs-table th { padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(247,243,238,0.4); border-bottom: 1px solid rgba(247,243,238,0.1); text-align: left; }
        .thr-rs-table td { padding: 12px 10px; border-bottom: 1px solid rgba(247,243,238,0.06); vertical-align: top; }
        .thr-rs-time { font-size: 16px; font-weight: 700; color: #F7F3EE; white-space: nowrap; }
        .thr-rs-name { font-size: 15px; font-weight: 600; }
        .thr-rs-meta { font-size: 12px; color: rgba(247,243,238,0.5); margin-top: 2px; }
        .thr-rs-tags { font-size: 11px; color: rgba(221,170,98,0.7); }
        .thr-rs-notes { font-size: 12px; color: rgba(247,243,238,0.55); font-style: italic; }
        .thr-rs-vip { color: #B8860B; font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-left: 4px; }
        .thr-rs-actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .thr-rs-btn { padding: 8px 12px; border-radius: 4px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; min-height: 44px; min-width: 60px; }
        .thr-rs-btn--seat { background: #2563EB; color: #fff; }
        .thr-rs-btn--complete { background: #16A34A; color: #fff; }
        .thr-rs-btn--noshow { background: rgba(247,243,238,0.08); color: rgba(247,243,238,0.6); border: 1px solid rgba(247,243,238,0.15); }
        .thr-rs-btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .thr-rs-status { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .thr-rs-status--confirmed { background: rgba(221,170,98,0.15); color: #DDAA62; }
        .thr-rs-status--seated { background: rgba(231,76,60,0.15); color: #e74c3c; }
        .thr-rs-status--pending { background: rgba(247,243,238,0.1); color: rgba(247,243,238,0.5); }
        .thr-rs-status--completed { background: rgba(22,163,74,0.15); color: #16A34A; }
        .thr-rs-status--no_show { background: rgba(107,114,128,0.15); color: #6B7280; }
        .thr-rs-refresh { font-size: 12px; color: rgba(247,243,238,0.3); }
        .thr-rs-empty { color: rgba(247,243,238,0.4); padding: 32px; text-align: center; }
        .thr-rs-err { color: #e74c3c; padding: 16px; font-size: 14px; }
        @media print { .thr-rs-actions { display: none; } #wpbody { padding-top: 0 !important; } }
        </style>

        <div class="thr-rs-wrap">
            <div class="thr-rs-header">
                <div>
                    <div class="thr-rs-title">FOH Run-sheet</div>
                    <div class="thr-rs-date" id="thr-rs-date"><?= esc_html( $today_fmt ) ?></div>
                </div>
                <div>
                    <button onclick="window.print()" style="background:rgba(247,243,238,0.08);border:1px solid rgba(247,243,238,0.15);color:#F7F3EE;padding:8px 14px;border-radius:4px;cursor:pointer;font-size:13px;">Print</button>
                    <span class="thr-rs-refresh" id="thr-rs-refresh" style="display:block;margin-top:4px;text-align:right;"></span>
                </div>
            </div>

            <div class="thr-rs-summary" id="thr-rs-summary">
                <div class="thr-rs-stat"><span class="thr-rs-stat__val" id="rs-total">—</span><span class="thr-rs-stat__lbl">Reservations</span></div>
                <div class="thr-rs-stat"><span class="thr-rs-stat__val" id="rs-covers">—</span><span class="thr-rs-stat__lbl">Covers</span></div>
                <div class="thr-rs-stat"><span class="thr-rs-stat__val" id="rs-vip">—</span><span class="thr-rs-stat__lbl">VIP</span></div>
            </div>

            <div id="thr-rs-body">
                <p class="thr-rs-empty">Loading…</p>
            </div>
        </div>

        <script>
        (function() {
            var API   = '<?= esc_js( $api_url ) ?>';
            var NONCE = '<?= esc_js( $nonce ) ?>';
            var TODAY = '<?= esc_js( $today ) ?>';
            var timer = null;

            function statusLabel(s) {
                var map = { confirmed:'Confirmed', seated:'Seated', pending:'Pending', completed:'Done', no_show:'No-show', late:'Late', cancelled:'Cancelled' };
                return map[s] || s;
            }
            function statusClass(s) {
                var map = { confirmed:'confirmed', seated:'seated', pending:'pending', completed:'completed', no_show:'no_show', late:'seated', cancelled:'no_show' };
                return 'thr-rs-status thr-rs-status--' + (map[s] || 'pending');
            }

            function updateStatus(id, newStatus, rowEl) {
                var btns = rowEl.querySelectorAll('.thr-rs-btn');
                btns.forEach(function(b) { b.disabled = true; });
                fetch(API + 'reservations/' + id + '/status', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) { loadData(); })
                .catch(function() {
                    btns.forEach(function(b) { b.disabled = false; });
                    alert('Status update failed. Please try again.');
                });
            }

            function renderRow(r) {
                var tr = document.createElement('tr');
                var actions = '';
                if (r.status === 'confirmed' || r.status === 'pending') {
                    actions += '<button class="thr-rs-btn thr-rs-btn--seat" data-action="seated">Seat</button>';
                }
                if (r.status === 'seated' || r.status === 'confirmed') {
                    actions += '<button class="thr-rs-btn thr-rs-btn--complete" data-action="completed">Done</button>';
                }
                if (r.status !== 'no_show' && r.status !== 'completed' && r.status !== 'cancelled') {
                    actions += '<button class="thr-rs-btn thr-rs-btn--noshow" data-action="no_show">No-show</button>';
                }
                var vipBadge = r.is_vip ? '<span class="thr-rs-vip">VIP</span>' : '';
                var tags     = r.tags ? '<div class="thr-rs-tags">' + escHtml(r.tags) + '</div>' : '';
                var notes    = r.notes_diner ? '<div class="thr-rs-notes">' + escHtml(r.notes_diner) + '</div>' : '';
                tr.innerHTML =
                    '<td><div class="thr-rs-time">' + escHtml(r.time_local) + '</div></td>' +
                    '<td>' +
                        '<div class="thr-rs-name">' + escHtml(r.diner_name) + vipBadge + '</div>' +
                        '<div class="thr-rs-meta">' + r.party_size + ' pax · ' + escHtml(ucfirst(r.occasion)) + (r.area_label ? ' · ' + escHtml(r.area_label) : '') + '</div>' +
                        tags + notes +
                    '</td>' +
                    '<td><span class="' + statusClass(r.status) + '">' + statusLabel(r.status) + '</span></td>' +
                    '<td><div class="thr-rs-actions">' + actions + '</div></td>';

                tr.querySelectorAll('.thr-rs-btn[data-action]').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        updateStatus(r.id, btn.dataset.action, tr);
                    });
                });
                return tr;
            }

            function escHtml(s) {
                if (!s) return '';
                return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }
            function ucfirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

            function loadData() {
                fetch(API + 'reports/shift?date=' + TODAY, {
                    headers: { 'X-WP-Nonce': NONCE }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    document.getElementById('rs-total').textContent  = data.summary.total_reservations;
                    document.getElementById('rs-covers').textContent = data.summary.total_covers;
                    document.getElementById('rs-vip').textContent    = data.summary.vip_count;

                    var body = document.getElementById('thr-rs-body');
                    if (!data.reservations || data.reservations.length === 0) {
                        body.innerHTML = '<p class="thr-rs-empty">No reservations for today.</p>';
                        return;
                    }
                    var table = document.createElement('table');
                    table.className = 'thr-rs-table';
                    table.innerHTML = '<thead><tr><th>Time</th><th>Guest</th><th>Status</th><th>Actions</th></tr></thead>';
                    var tbody = document.createElement('tbody');
                    data.reservations.forEach(function(r) { tbody.appendChild(renderRow(r)); });
                    table.appendChild(tbody);
                    body.innerHTML = '';
                    body.appendChild(table);

                    var now = new Date();
                    document.getElementById('thr-rs-refresh').textContent = 'Last updated ' + now.toLocaleTimeString();
                })
                .catch(function() {
                    document.getElementById('thr-rs-body').innerHTML = '<p class="thr-rs-err">Failed to load data. Will retry in 60s.</p>';
                });
            }

            loadData();
            timer = setInterval(loadData, 60000);
        })();
        </script>
        <?php
    }

    // ── Event Enquiries list ──────────────────────────────────────────────────
    public function page_event_enquiries(): void {
        global $wpdb;
        $table = THR_Database::t( 'event_enquiries' );

        // Handle status update
        if ( isset( $_POST['thr_eq_status'] ) && wp_verify_nonce( $_POST['thr_nonce'] ?? '', 'thr_eq_status' ) ) {
            $eq_id     = (int) ( $_POST['eq_id'] ?? 0 );
            $new_status = sanitize_text_field( $_POST['eq_status'] ?? '' );
            $allowed   = [ 'new', 'reviewing', 'quoted', 'confirmed', 'declined', 'closed' ];
            if ( $eq_id && in_array( $new_status, $allowed, true ) ) {
                $wpdb->update( $table, [ 'status' => $new_status, 'updated_at' => current_time( 'mysql', true ) ], [ 'id' => $eq_id ] );
                echo '<div class="notice notice-success"><p>Status updated.</p></div>';
            }
        }

        $status_filter = sanitize_text_field( $_GET['status'] ?? '' );
        $where  = $status_filter ? $wpdb->prepare( 'WHERE status = %s', $status_filter ) : '';
        $rows   = $wpdb->get_results( "SELECT * FROM $table $where ORDER BY created_at DESC LIMIT 200" );
        $counts = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE status='new'" );
        $statuses = [ 'new', 'reviewing', 'quoted', 'confirmed', 'declined', 'closed' ];
        $base_url = admin_url( 'admin.php?page=thr-event-enquiries' );
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">
                Event Enquiries
                <?php if ( $counts ): ?><span class="thr-badge thr-badge--pending" style="margin-left:8px;"><?= (int) $counts ?> new</span><?php endif; ?>
            </h1>
            <p class="description">Private event and venue hire enquiries submitted via the website.</p>

            <form method="get" class="thr-filters">
                <input type="hidden" name="page" value="thr-event-enquiries">
                <select name="status">
                    <option value="">All statuses</option>
                    <?php foreach ( $statuses as $s ): ?>
                    <option value="<?= $s ?>" <?= selected( $status_filter, $s, false ) ?>><?= ucfirst( $s ) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button">Filter</button>
                <?php if ( $status_filter ): ?><a href="<?= $base_url ?>" class="button">Clear</a><?php endif; ?>
            </form>

            <?php if ( $rows ): ?>
            <table class="thr-table wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Contact</th>
                        <th>Event type</th>
                        <th>Preferred date</th>
                        <th>Guests</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th>Update status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $rows as $eq ): ?>
                <tr>
                    <td><span class="thr-ref"><?= esc_html( $eq->reference_code ) ?></span></td>
                    <td>
                        <?= esc_html( $eq->contact_name ) ?><br>
                        <a href="mailto:<?= esc_attr( $eq->contact_email ) ?>" class="thr-muted"><?= esc_html( $eq->contact_email ) ?></a><br>
                        <?php if ( $eq->contact_phone ): ?><span class="thr-muted"><?= esc_html( $eq->contact_phone ) ?></span><?php endif; ?>
                        <?php if ( $eq->company_name ): ?><br><em class="thr-muted"><?= esc_html( $eq->company_name ) ?></em><?php endif; ?>
                    </td>
                    <td><?= esc_html( ucfirst( str_replace( '_', ' ', $eq->event_type ) ) ) ?></td>
                    <td><?= $eq->preferred_date ? esc_html( date( 'D j M Y', strtotime( $eq->preferred_date ) ) ) : '<span class="thr-muted">—</span>' ?></td>
                    <td><?= (int) $eq->guest_count ?></td>
                    <td><?= esc_html( $eq->budget_range ?: '—' ) ?></td>
                    <td>
                        <?php
                        $badge_map = [ 'new' => 'thr-badge--pending', 'reviewing' => 'thr-badge--confirmed', 'quoted' => 'thr-badge--seated', 'confirmed' => 'thr-badge--done', 'declined' => 'thr-badge--cancelled', 'closed' => 'thr-badge--noshow' ];
                        $badge_cls = $badge_map[ $eq->status ] ?? '';
                        ?>
                        <span class="thr-badge <?= $badge_cls ?>"><?= esc_html( ucfirst( $eq->status ) ) ?></span>
                    </td>
                    <td><span class="thr-muted"><?= esc_html( substr( $eq->created_at, 0, 10 ) ) ?></span></td>
                    <td>
                        <?php if ( current_user_can( 'thr_edit_reservations' ) ): ?>
                        <form method="post" style="display:flex;gap:4px;align-items:center;">
                            <?php wp_nonce_field( 'thr_eq_status', 'thr_nonce' ); ?>
                            <input type="hidden" name="eq_id" value="<?= (int) $eq->id ?>">
                            <select name="eq_status" class="regular-text" style="max-width:120px;">
                                <?php foreach ( $statuses as $s ): ?>
                                <option value="<?= $s ?>" <?= selected( $eq->status, $s, false ) ?>><?= ucfirst( $s ) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="thr_eq_status" class="button button-small">Save</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ( $eq->notes ): ?>
                <tr>
                    <td></td>
                    <td colspan="8" style="padding-top:0;padding-bottom:12px;font-size:12px;color:rgba(0,0,0,0.5);font-style:italic;"><?= esc_html( $eq->notes ) ?></td>
                </tr>
                <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="thr-empty">No event enquiries found.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    // ── Shift Report ──────────────────────────────────────────────────────────
    public function page_shift_report(): void {
        global $wpdb;
        $table  = THR_Database::t( 'reservations' );
        $date   = sanitize_text_field( $_GET['date'] ?? gmdate( 'Y-m-d', time() + 7 * 3600 ) );
        $shift  = sanitize_text_field( $_GET['shift'] ?? 'all' );

        $where = $wpdb->prepare(
            "DATE(DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR)) = %s AND r.status NOT IN ('cancelled','no_show')", $date
        );

        $rows = $wpdb->get_results(
            "SELECT r.*, DATE_ADD(r.reservation_dt, INTERVAL 7 HOUR) AS dt_local,
                    GROUP_CONCAT(t.name ORDER BY t.name SEPARATOR ', ') AS tag_names
             FROM $table r
             LEFT JOIN " . THR_Database::t( 'reservation_tags' ) . " rt ON rt.reservation_id = r.id
             LEFT JOIN " . THR_Database::t( 'tags' ) . " t ON t.id = rt.tag_id
             WHERE $where GROUP BY r.id ORDER BY r.reservation_dt ASC"
        );

        $covers = array_sum( array_column( $rows, 'party_size' ) );
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">
                Shift Report — <?= esc_html( gmdate( 'l, F j, Y', strtotime( $date ) ) ) ?>
                <button onclick="window.print()" class="button" style="margin-left:12px;">Print</button>
            </h1>

            <form method="get" class="thr-filters">
                <input type="hidden" name="page" value="thr-shift-report">
                <input type="date" name="date" value="<?= esc_attr( $date ) ?>">
                <select name="shift">
                    <option value="all" <?= selected( $shift, 'all', false ) ?>>All shifts</option>
                    <option value="lunch" <?= selected( $shift, 'lunch', false ) ?>>Lunch</option>
                    <option value="dinner" <?= selected( $shift, 'dinner', false ) ?>>Dinner</option>
                    <option value="late" <?= selected( $shift, 'late', false ) ?>>Late</option>
                </select>
                <button type="submit" class="button">Update</button>
            </form>

            <div class="thr-stats-grid" style="margin:16px 0;">
                <div class="thr-stat"><span class="thr-stat__value"><?= count( $rows ) ?></span><span class="thr-stat__label">Reservations</span></div>
                <div class="thr-stat"><span class="thr-stat__value"><?= $covers ?></span><span class="thr-stat__label">Covers</span></div>
                <div class="thr-stat thr-stat--warning"><span class="thr-stat__value"><?= count( array_filter( $rows, fn($r) => $r->is_vip ) ) ?></span><span class="thr-stat__label">VIP</span></div>
            </div>

            <?php if ( $rows ): ?>
            <table class="thr-table thr-shift-table wp-list-table widefat">
                <thead>
                    <tr><th>Time</th><th>Name</th><th>Party</th><th>Occasion</th><th>Area</th><th>Tags</th><th>Notes</th><th>Phone</th><th>Zalo</th></tr>
                </thead>
                <tbody>
                <?php foreach ( $rows as $r ):
                    $is_returning = (bool) $wpdb->get_var( $wpdb->prepare(
                        "SELECT COUNT(*) FROM $table WHERE diner_email = %s AND id != %d AND status IN ('confirmed','completed','seated')",
                        $r->diner_email, $r->id
                    ) );
                ?>
                <tr class="<?= $r->is_vip ? 'thr-row-vip' : '' ?>">
                    <td><strong><?= esc_html( substr( $r->dt_local, 11, 5 ) ) ?></strong></td>
                    <td>
                        <?= esc_html( $r->diner_name ) ?>
                        <?php if ( $r->is_vip ): ?><span class="thr-badge thr-badge--vip">VIP</span><?php endif; ?>
                        <?php if ( $is_returning ): ?><span class="thr-badge thr-badge--confirmed" style="font-size:10px;margin-left:4px;">Returning</span><?php endif; ?>
                    </td>
                    <td><?= (int) $r->party_size ?></td>
                    <td><?= esc_html( ucfirst( $r->occasion ) ) ?></td>
                    <td><?= esc_html( $r->area_label ?: '—' ) ?></td>
                    <td><?= esc_html( $r->tag_names ?: '' ) ?></td>
                    <td><?= esc_html( $r->notes_diner ?: '' ) ?></td>
                    <td><?= esc_html( $r->diner_phone ?: '' ) ?></td>
                    <td><?= esc_html( $r->diner_zalo ?? '' ) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="thr-empty">No reservations for this shift.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    // ── Settings page ─────────────────────────────────────────────────────────
    public function page_settings(): void {
        if ( isset( $_POST['thr_settings_nonce'] ) && wp_verify_nonce( $_POST['thr_settings_nonce'], 'thr_settings_save' ) ) {
            $save_keys = [
                'venue_name', 'venue_address', 'venue_phone', 'venue_email',
                'email_from_name', 'email_from_address', 'email_reply_to', 'email_logo_url',
                'feedback_form_url', 'google_review_url',
                'booking_advance_min', 'booking_advance_max', 'party_size_min', 'party_size_max',
                'default_duration', 'slots_lunch', 'slots_dinner', 'slots_late', 'slots_enabled',
                'status_orange_min', 'status_red_min', 'cancel_policy_text',
                'reminder_24h', 'reminder_2h', 'feedback_delay_min',
                'shift_report_email', 'shift_report_time',
                'venue_capacity', 'vietqr_bank_id', 'vietqr_account_no', 'vietqr_account_name',
                'booking_default_lang',
            ];
            $data = [];
            foreach ( $save_keys as $key ) {
                if ( isset( $_POST[ $key ] ) ) $data[ $key ] = sanitize_text_field( $_POST[ $key ] );
            }
            // Checkboxes (unchecked = absent from POST)
            $data['reminder_24h']        = isset( $_POST['reminder_24h'] );
            $data['reminder_2h']         = isset( $_POST['reminder_2h'] );
            $data['auto_confirm_public'] = isset( $_POST['auto_confirm_public'] );
            $data['shift_report_enabled']= isset( $_POST['shift_report_enabled'] );
            if ( isset( $_POST['cancel_policy_text'] ) ) $data['cancel_policy_text'] = sanitize_textarea_field( $_POST['cancel_policy_text'] );
            THR_Settings::update( $data );
            // Reschedule shift report if those settings changed
            THR_Cron::reschedule_shift_report();
            echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
        }

        $s = THR_Settings::all();
        ?>
        <div class="wrap thr-wrap">
            <h1 class="thr-page-title">Settings</h1>
            <form method="post">
                <?php wp_nonce_field( 'thr_settings_save', 'thr_settings_nonce' ); ?>

                <div class="thr-settings-sections">

                <div class="thr-card">
                    <h3>Venue</h3>
                    <?php $this->field( 'Venue name',    'venue_name',    $s['venue_name'] ); ?>
                    <?php $this->field( 'Address',       'venue_address', $s['venue_address'] ); ?>
                    <?php $this->field( 'Phone',         'venue_phone',   $s['venue_phone'] ); ?>
                    <?php $this->field( 'Contact email', 'venue_email',   $s['venue_email'], 'email' ); ?>
                </div>

                <div class="thr-card">
                    <h3>Email Notifications</h3>
                    <?php $this->field( 'From name',         'email_from_name',    $s['email_from_name'] ); ?>
                    <?php $this->field( 'From email',        'email_from_address', $s['email_from_address'], 'email' ); ?>
                    <?php $this->field( 'Reply-to email',    'email_reply_to',     $s['email_reply_to'], 'email' ); ?>
                    <?php $this->field( 'Logo URL (email)',  'email_logo_url',     $s['email_logo_url'], 'url' ); ?>
                    <label class="thr-label"><input type="checkbox" name="reminder_24h" value="1" <?= checked( $s['reminder_24h'], true, false ) ?>> Send 24h reminder email</label><br>
                    <label class="thr-label"><input type="checkbox" name="reminder_2h"  value="1" <?= checked( $s['reminder_2h'] ?? true, true, false ) ?>> Send 2h reminder email</label>
                </div>

                <div class="thr-card">
                    <h3>Feedback</h3>
                    <?php $this->field( 'Google Forms URL', 'feedback_form_url', $s['feedback_form_url'], 'url', 'Append ?ref={reference_code} auto-prefill. Leave blank to disable.' ); ?>
                    <?php $this->field( 'Google Review URL', 'google_review_url', $s['google_review_url'], 'url' ); ?>
                    <?php $this->field( 'Send feedback after (min)', 'feedback_delay_min', $s['feedback_delay_min'], 'number' ); ?>
                </div>

                <div class="thr-card">
                    <h3>Booking Rules</h3>
                    <?php $this->field( 'Min advance notice (min)', 'booking_advance_min', $s['booking_advance_min'], 'number' ); ?>
                    <?php $this->field( 'Max advance booking (days)', 'booking_advance_max', $s['booking_advance_max'], 'number' ); ?>
                    <?php $this->field( 'Min party size', 'party_size_min', $s['party_size_min'], 'number' ); ?>
                    <?php $this->field( 'Max party size', 'party_size_max', $s['party_size_max'], 'number' ); ?>
                    <?php $this->field( 'Default reservation duration (min)', 'default_duration', $s['default_duration'], 'number' ); ?>
                </div>

                <div class="thr-card">
                    <h3>Time Slots</h3>
                    <?php $this->field( 'Lunch slots (HH:MM, comma-separated)', 'slots_lunch', $s['slots_lunch'] ); ?>
                    <?php $this->field( 'Dinner slots', 'slots_dinner', $s['slots_dinner'] ); ?>
                    <?php $this->field( 'Late night slots', 'slots_late', $s['slots_late'] ); ?>
                    <?php $this->field( 'Enabled groups (comma-separated: lunch,dinner,late)', 'slots_enabled', $s['slots_enabled'] ); ?>
                </div>

                <div class="thr-card">
                    <h3>Floor Status Thresholds</h3>
                    <?php $this->field( 'Orange after (min)', 'status_orange_min', $s['status_orange_min'], 'number' ); ?>
                    <?php $this->field( 'Red after (min)',    'status_red_min',    $s['status_red_min'], 'number' ); ?>
                </div>

                <div class="thr-card">
                    <h3>Policies</h3>
                    <label class="thr-label">Cancellation policy</label>
                    <textarea name="cancel_policy_text" rows="3" class="large-text"><?= esc_textarea( $s['cancel_policy_text'] ) ?></textarea>
                </div>

                <div class="thr-card">
                    <h3>Public Booking</h3>
                    <label class="thr-label">
                        <input type="checkbox" name="auto_confirm_public" value="1" <?= checked( $s['auto_confirm_public'] ?? true, true, false ) ?>>
                        Auto-confirm public bookings (uncheck for manual review)
                    </label>
                </div>

                <div class="thr-card">
                    <h3>Daily Shift Report</h3>
                    <p class="description" style="margin-bottom:12px;">Send an automated shift report email each evening with the next day's reservation list.</p>
                    <label class="thr-label">
                        <input type="checkbox" name="shift_report_enabled" value="1" <?= checked( $s['shift_report_enabled'] ?? false, true, false ) ?>>
                        Enable daily shift report email
                    </label>
                    <?php $this->field( 'Recipient email', 'shift_report_email', $s['shift_report_email'] ?? '', 'email', 'e.g. manager@tempohouse.com.vn' ); ?>
                    <?php $this->field( 'Send time (venue local HH:MM)', 'shift_report_time', $s['shift_report_time'] ?? '22:00', 'text', 'GMT+7 time, e.g. 22:00 to send at 10 PM' ); ?>
                </div>

                </div>

                <p class="submit"><button type="submit" class="button button-primary">Save Settings</button></p>
            </form>
        </div>
        <?php
    }

    // ── AJAX status update (used by admin JS) ─────────────────────────────────
    public function ajax_update_status(): void {
        check_ajax_referer( 'wp_rest', 'nonce' );
        if ( ! current_user_can( 'thr_edit_reservations' ) ) wp_send_json_error( 'Forbidden', 403 );

        global $wpdb;
        $id     = (int) ( $_POST['id'] ?? 0 );
        $status = sanitize_text_field( $_POST['status'] ?? '' );
        $table  = THR_Database::t( 'reservations' );
        $valid  = [ 'pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show' ];

        if ( ! $id || ! in_array( $status, $valid, true ) ) wp_send_json_error( 'Invalid params' );
        if ( $status === 'cancelled' && ! current_user_can( 'thr_cancel_reservations' ) ) wp_send_json_error( 'No permission to cancel', 403 );

        $reservation = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
        if ( ! $reservation ) wp_send_json_error( 'Not found', 404 );

        $old_status = $reservation->status;
        $update     = [ 'status' => $status, 'updated_at' => current_time( 'mysql', true ) ];
        if ( $status === 'seated' ) $update['seated_at'] = current_time( 'mysql', true );
        $wpdb->update( $table, $update, [ 'id' => $id ] );

        $fresh = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
        match ( $status ) {
            'confirmed' => THR_Email::send_confirmation( $fresh ),
            'cancelled' => THR_Email::send_cancellation( $fresh ),
            default     => null,
        };

        // Let cron know so waitlist can be notified on cancellation
        do_action( 'thr_reservation_status_changed', $fresh, $old_status, $status );

        wp_send_json_success( [ 'status' => $status ] );
    }

    // ── Shared UI helpers ─────────────────────────────────────────────────────

    private function waitlist_status_badge( string $status ): void {
        $map = [
            'waiting'   => [ 'Waiting',   'thr-badge--pending' ],
            'notified'  => [ 'Notified',  'thr-badge--confirmed' ],
            'converted' => [ 'Converted', 'thr-badge--done' ],
            'expired'   => [ 'Expired',   'thr-badge--cancelled' ],
        ];
        [ $label, $cls ] = $map[ $status ] ?? [ ucfirst( $status ), '' ];
        echo "<span class=\"thr-badge $cls\">" . esc_html( $label ) . "</span>";
    }

    private function status_badge( string $status ): void {
        $map = [
            'pending'   => [ 'Pending',   'thr-badge--pending' ],
            'confirmed' => [ 'Confirmed', 'thr-badge--confirmed' ],
            'seated'    => [ 'Seated',    'thr-badge--seated' ],
            'completed' => [ 'Done',      'thr-badge--done' ],
            'cancelled' => [ 'Cancelled', 'thr-badge--cancelled' ],
            'no_show'   => [ 'No-show',   'thr-badge--noshow' ],
        ];
        [ $label, $cls ] = $map[ $status ] ?? [ ucfirst( $status ), '' ];
        echo "<span class=\"thr-badge $cls\">" . esc_html( $label ) . "</span>";
    }

    private function row_actions( int $id, string $status, string $ref ): void {
        echo "<a href=\"" . admin_url( "admin.php?page=thr-reservation&id=$id" ) . "\" class=\"button button-small\">View</a> ";
        if ( $status === 'pending' && current_user_can( 'thr_edit_reservations' ) ):
            echo "<button class=\"button button-small thr-status-btn\" data-id=\"$id\" data-status=\"confirmed\">Confirm</button> ";
        endif;
        if ( $status === 'confirmed' && current_user_can( 'thr_edit_reservations' ) ):
            echo "<button class=\"button button-small thr-status-btn\" data-id=\"$id\" data-status=\"seated\">Seat</button> ";
        endif;
    }

    private function field( string $label, string $name, $value, string $type = 'text', string $desc = '' ): void {
        echo "<p class=\"thr-field\">";
        echo "<label class=\"thr-label\">" . esc_html( $label ) . "</label>";
        echo "<input type=\"$type\" name=\"$name\" value=\"" . esc_attr( $value ) . "\" class=\"regular-text\">";
        if ( $desc ) echo "<span class=\"description\"> $desc</span>";
        echo "</p>";
    }
}
