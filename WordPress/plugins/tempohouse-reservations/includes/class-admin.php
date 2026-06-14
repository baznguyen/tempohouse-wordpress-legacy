<?php
defined( 'ABSPATH' ) || exit;

class THR_Admin {

    public function init(): void {
        add_action( 'admin_menu',            [ $this, 'add_menus' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_ajax_thr_update_status', [ $this, 'ajax_update_status' ] );
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

        add_submenu_page( 'thr-dashboard', 'Dashboard',     'Dashboard',     'thr_view_reservations',   'thr-dashboard',     [ $this, 'page_dashboard' ] );
        add_submenu_page( 'thr-dashboard', 'Reservations',  $res_label,      'thr_view_reservations',   'thr-reservations',  [ $this, 'page_list' ] );
        add_submenu_page( 'thr-dashboard', 'Floor Plans',   'Floor Plans',   'thr_manage_floor_plans',  'thr-floor-plans',   [ $this, 'page_floor_plans' ] );
        add_submenu_page( 'thr-dashboard', 'Blocks',        'Blocks',        'thr_manage_settings',     'thr-blocks',        [ $this, 'page_blocks' ] );
        add_submenu_page( 'thr-dashboard', 'Tags',          'Tags',          'thr_manage_tags',         'thr-tags',          [ $this, 'page_tags' ] );
        add_submenu_page( 'thr-dashboard', 'Waitlist',      $wl_label,       'thr_view_reservations',   'thr-waitlist',      [ $this, 'page_waitlist' ] );
        add_submenu_page( 'thr-dashboard', 'Shift Report',  'Shift Report',  'thr_view_reports',        'thr-shift-report',  [ $this, 'page_shift_report' ] );
        add_submenu_page( 'thr-dashboard', 'Settings',      'Settings',      'thr_manage_settings',     'thr-settings',      [ $this, 'page_settings' ] );

        // Hidden: single reservation view
        add_submenu_page( null, 'Reservation', 'Reservation', 'thr_view_reservations', 'thr-reservation', [ $this, 'page_single' ] );
    }

    public function enqueue_assets( string $hook ): void {
        $our_pages = [ 'thr-dashboard', 'thr-reservations', 'thr-floor-plans', 'thr-blocks', 'thr-tags', 'thr-waitlist', 'thr-settings', 'thr-shift-report', 'thr-reservation' ];
        $page      = $_GET['page'] ?? '';
        if ( ! in_array( $page, $our_pages, true ) ) return;

        wp_enqueue_style( 'thr-admin', THR_PLUGIN_URL . 'assets/css/admin.css', [], THR_VERSION );
        wp_enqueue_script( 'thr-admin', THR_PLUGIN_URL . 'assets/js/admin.js', [ 'jquery' ], THR_VERSION, true );
        wp_localize_script( 'thr-admin', 'thrAdmin', [
            'apiUrl'   => rest_url( THR_REST_NS . '/' ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
            'adminUrl' => admin_url( 'admin.php' ),
        ] );

        // Floor plan builder — only load Konva + builder on the floor-plans page
        if ( $page === 'thr-floor-plans' ) {
            wp_enqueue_style( 'thr-fp-builder', THR_PLUGIN_URL . 'assets/css/floor-plan-builder.css', [], THR_VERSION );
            wp_enqueue_script( 'konva', 'https://unpkg.com/konva@9.3.14/konva.min.js', [], '9.3.14', true );
            wp_enqueue_script( 'thr-fp-builder', THR_PLUGIN_URL . 'assets/js/floor-plan-builder.js', [ 'konva' ], THR_VERSION, true );

            // Normalize TYPES for JS (rename 'cap' → 'capacity' for consistency)
            $furniture_types = [];
            foreach ( THR_API_Furniture::TYPES as $slug => $type ) {
                $furniture_types[ $slug ] = [
                    'label'    => $type['label'],
                    'capacity' => $type['cap'],
                    'shape'    => $type['shape'],
                ];
            }

            wp_localize_script( 'thr-fp-builder', 'thrFloorPlan', [
                'apiUrl'         => rest_url( THR_REST_NS . '/' ),
                'nonce'          => wp_create_nonce( 'wp_rest' ),
                'furnitureTypes' => $furniture_types,
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
                <?php foreach ( $rows as $r ): ?>
                <tr class="<?= $r->is_vip ? 'thr-row-vip' : '' ?>">
                    <td><a href="<?= admin_url( "admin.php?page=thr-reservation&id={$r->id}" ) ?>" class="thr-ref"><?= esc_html( $r->reference_code ) ?></a></td>
                    <td>
                        <?= esc_html( substr( $r->dt_local, 0, 10 ) ) ?><br>
                        <span class="thr-muted"><?= esc_html( substr( $r->dt_local, 11, 5 ) ) ?></span>
                    </td>
                    <td>
                        <?= esc_html( $r->diner_name ) ?>
                        <?php if ( $r->is_vip ): ?><span class="thr-badge thr-badge--vip">VIP</span><?php endif; ?>
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
                            <li><?= $r->reminder_4h_sent_at ? '✓' : '○' ?> 4h reminder</li>
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
        $type_groups = [
            'Tables'   => [ 'table-round-2', 'table-round-4', 'table-round-6', 'table-round-8', 'table-rect-2', 'table-rect-4', 'table-rect-6', 'table-rect-8' ],
            'Booths'   => [ 'booth-2', 'booth-4', 'booth-6' ],
            'Bar'      => [ 'bar-stool', 'bar-counter', 'high-top-2', 'high-top-4' ],
            'Lounge'   => [ 'lounge-sofa', 'lounge-chair', 'banquette' ],
            'Zones'    => [ 'area-vip', 'stage', 'dj-booth', 'outdoor-table' ],
        ];
        ?>
        <div class="wrap thr-wrap" style="margin-right:0;">
            <div id="thr-fp-app">
                <!-- Toolbar -->
                <div id="thr-fp-toolbar">
                    <div class="thr-fp-toolbar-group">
                        <select id="thr-fp-floor-select" class="thr-fp-floor-select"></select>
                        <button class="thr-fp-btn" id="thr-fp-btn-add-floor">+ Floor</button>
                        <button class="thr-fp-btn" id="thr-fp-btn-upload-bg">Upload BG</button>
                    </div>
                    <div class="thr-fp-toolbar-sep"></div>
                    <div class="thr-fp-toolbar-group">
                        <button class="thr-fp-btn" id="thr-fp-btn-zoom-out">−</button>
                        <span class="thr-fp-zoom-label" id="thr-fp-zoom-label">100%</span>
                        <button class="thr-fp-btn" id="thr-fp-btn-zoom-in">+</button>
                        <button class="thr-fp-btn" id="thr-fp-btn-zoom-fit">Fit</button>
                    </div>
                    <div class="thr-fp-toolbar-sep"></div>
                    <div class="thr-fp-toolbar-group">
                        <button class="thr-fp-btn thr-fp-btn--danger" id="thr-fp-btn-delete">Delete</button>
                    </div>
                    <div class="thr-fp-toolbar-sep"></div>
                    <div class="thr-fp-toolbar-group">
                        <button class="thr-fp-btn thr-fp-btn--primary" id="thr-fp-btn-save">Save</button>
                        <span id="thr-fp-save-status"></span>
                    </div>
                    <div class="thr-fp-mode-toggle">
                        <button class="thr-fp-btn thr-fp-btn--active" id="thr-fp-btn-edit">Edit</button>
                        <button class="thr-fp-btn" id="thr-fp-btn-live">Live view</button>
                    </div>
                </div>

                <!-- Palette -->
                <div id="thr-fp-palette">
                    <?php foreach ( $type_groups as $group_label => $type_keys ):
                        $all_types = THR_API_Furniture::TYPES;
                    ?>
                    <div class="thr-fp-palette-heading"><?= esc_html( $group_label ) ?></div>
                    <?php foreach ( $type_keys as $key ):
                        $t   = $all_types[ $key ] ?? null;
                        if ( ! $t ) continue;
                        $icon_cls = ( $t['shape'] ?? '' ) === 'circle' ? 'thr-fp-palette-icon--circle' : '';
                        $cap = isset( $t['capacity'] ) ? $t['capacity'][0] . '–' . $t['capacity'][1] : '';
                    ?>
                    <div class="thr-fp-palette-item" data-place-type="<?= esc_attr( $key ) ?>">
                        <div class="thr-fp-palette-icon <?= $icon_cls ?>"><?= esc_html( $cap ) ?></div>
                        <?= esc_html( $t['label'] ) ?>
                    </div>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Canvas -->
                <div id="thr-fp-canvas-wrap">
                    <div id="thr-konva-container"></div>
                    <div class="thr-fp-legend">
                        <div class="thr-fp-legend-item"><div class="thr-fp-legend-dot" style="background:#2d6a4f;"></div> Available</div>
                        <div class="thr-fp-legend-item"><div class="thr-fp-legend-dot" style="background:#ddaa62;"></div> Reserved</div>
                        <div class="thr-fp-legend-item"><div class="thr-fp-legend-dot" style="background:#c0392b;"></div> Seated</div>
                        <div class="thr-fp-legend-item"><div class="thr-fp-legend-dot" style="background:#555;"></div> Blocked</div>
                    </div>
                </div>

                <!-- Properties -->
                <div id="thr-fp-props">
                    <p class="thr-fp-props-empty">Select a piece of furniture<br>to edit its properties.</p>
                </div>
            </div>
        </div>
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
                    <tr><th>Time</th><th>Name</th><th>Party</th><th>Occasion</th><th>Area</th><th>Tags</th><th>Notes</th><th>Phone</th></tr>
                </thead>
                <tbody>
                <?php foreach ( $rows as $r ): ?>
                <tr class="<?= $r->is_vip ? 'thr-row-vip' : '' ?>">
                    <td><strong><?= esc_html( substr( $r->dt_local, 11, 5 ) ) ?></strong></td>
                    <td>
                        <?= esc_html( $r->diner_name ) ?>
                        <?php if ( $r->is_vip ): ?><span class="thr-badge thr-badge--vip">VIP</span><?php endif; ?>
                    </td>
                    <td><?= (int) $r->party_size ?></td>
                    <td><?= esc_html( ucfirst( $r->occasion ) ) ?></td>
                    <td><?= esc_html( $r->area_label ?: '—' ) ?></td>
                    <td><?= esc_html( $r->tag_names ?: '' ) ?></td>
                    <td><?= esc_html( $r->notes_diner ?: '' ) ?></td>
                    <td><?= esc_html( $r->diner_phone ?: '' ) ?></td>
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
                'reminder_24h', 'reminder_4h', 'feedback_delay_min',
                'shift_report_email', 'shift_report_time',
            ];
            $data = [];
            foreach ( $save_keys as $key ) {
                if ( isset( $_POST[ $key ] ) ) $data[ $key ] = sanitize_text_field( $_POST[ $key ] );
            }
            // Checkboxes (unchecked = absent from POST)
            $data['reminder_24h']        = isset( $_POST['reminder_24h'] );
            $data['reminder_4h']         = isset( $_POST['reminder_4h'] );
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
                    <label class="thr-label"><input type="checkbox" name="reminder_4h"  value="1" <?= checked( $s['reminder_4h'], true, false ) ?>> Send 4h reminder email</label>
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
