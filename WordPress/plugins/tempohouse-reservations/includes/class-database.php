<?php
defined( 'ABSPATH' ) || exit;

class THR_Database {

    // Call via register_activation_hook and on version bump
    public static function install(): void {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();
        $p       = $wpdb->prefix;

        // ── Reservations ──────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_reservations (
            id                  BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            reference_code      VARCHAR(12)  NOT NULL,
            status              VARCHAR(20)  NOT NULL DEFAULT 'pending',
            reservation_dt      DATETIME     NOT NULL COMMENT 'UTC',
            duration_min        SMALLINT UNSIGNED NOT NULL DEFAULT 120,
            party_size          TINYINT UNSIGNED  NOT NULL,
            floor_plan_id       BIGINT(20) UNSIGNED DEFAULT NULL,
            furniture_ids       TEXT         DEFAULT NULL COMMENT 'JSON array',
            area_label          VARCHAR(100) DEFAULT NULL,
            diner_name          VARCHAR(200) NOT NULL,
            diner_email         VARCHAR(200) NOT NULL,
            diner_phone         VARCHAR(50)  DEFAULT NULL,
            diner_zalo          VARCHAR(50)  DEFAULT NULL,
            diner_lang          VARCHAR(5)   NOT NULL DEFAULT 'en',
            occasion            VARCHAR(50)  NOT NULL DEFAULT 'dinner',
            notes_diner         TEXT         DEFAULT NULL,
            notes_internal      TEXT         DEFAULT NULL,
            is_vip              TINYINT(1)   NOT NULL DEFAULT 0,
            deposit_amount      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            deposit_paid        TINYINT(1)   NOT NULL DEFAULT 0,
            confirmation_sent_at DATETIME    DEFAULT NULL,
            reminder_24h_sent_at DATETIME   DEFAULT NULL,
            reminder_4h_sent_at  DATETIME   DEFAULT NULL,
            feedback_sent_at    DATETIME     DEFAULT NULL,
            seated_at           DATETIME     DEFAULT NULL,
            created_by          BIGINT(20) UNSIGNED DEFAULT NULL,
            created_at          DATETIME     NOT NULL,
            updated_at          DATETIME     NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY   reference_code (reference_code),
            KEY          status (status),
            KEY          reservation_dt (reservation_dt),
            KEY          diner_email (diner_email(100))
        ) $charset;" );

        // ── Floor plans ───────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_floor_plans (
            id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name        VARCHAR(200) NOT NULL,
            floor_number TINYINT NOT NULL DEFAULT 1,
            background_url TEXT DEFAULT NULL,
            width_px    SMALLINT UNSIGNED NOT NULL DEFAULT 1200,
            height_px   SMALLINT UNSIGNED NOT NULL DEFAULT 800,
            is_active   TINYINT(1) NOT NULL DEFAULT 1,
            sort_order  TINYINT NOT NULL DEFAULT 0,
            created_at  DATETIME NOT NULL,
            updated_at  DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY is_active (is_active)
        ) $charset;" );

        // ── Furniture ─────────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_furniture (
            id            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            floor_plan_id BIGINT(20) UNSIGNED NOT NULL,
            type          VARCHAR(50)  NOT NULL,
            label         VARCHAR(100) NOT NULL,
            pos_x         DECIMAL(8,2) NOT NULL DEFAULT 0,
            pos_y         DECIMAL(8,2) NOT NULL DEFAULT 0,
            width         DECIMAL(8,2) NOT NULL DEFAULT 80,
            height        DECIMAL(8,2) NOT NULL DEFAULT 80,
            rotation_deg  SMALLINT NOT NULL DEFAULT 0,
            capacity_min  TINYINT UNSIGNED NOT NULL DEFAULT 1,
            capacity_max  TINYINT UNSIGNED NOT NULL DEFAULT 4,
            shape         VARCHAR(20) NOT NULL DEFAULT 'rect',
            is_combinable TINYINT(1) NOT NULL DEFAULT 1,
            is_available  TINYINT(1) NOT NULL DEFAULT 1,
            meta          TEXT DEFAULT NULL COMMENT 'JSON',
            created_at    DATETIME NOT NULL,
            updated_at    DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY floor_plan_id (floor_plan_id),
            KEY is_available (is_available)
        ) $charset;" );

        // ── Tags ──────────────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_tags (
            id         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name       VARCHAR(100) NOT NULL,
            slug       VARCHAR(100) NOT NULL,
            color      VARCHAR(7)   NOT NULL DEFAULT '#666666',
            is_system  TINYINT(1)   NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY slug (slug)
        ) $charset;" );

        // ── Reservation ↔ Tag pivot ───────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_reservation_tags (
            reservation_id BIGINT(20) UNSIGNED NOT NULL,
            tag_id         BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY (reservation_id, tag_id),
            KEY tag_id (tag_id)
        ) $charset;" );

        // ── Availability blocks ───────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_availability_blocks (
            id           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            scope        VARCHAR(20) NOT NULL DEFAULT 'furniture',
            scope_id     BIGINT(20) UNSIGNED DEFAULT NULL,
            blocked_from DATETIME NOT NULL,
            blocked_to   DATETIME NOT NULL,
            reason       VARCHAR(200) DEFAULT NULL,
            created_by   BIGINT(20) UNSIGNED DEFAULT NULL,
            created_at   DATETIME NOT NULL,
            PRIMARY KEY (id),
            KEY scope_id (scope_id),
            KEY blocked_from (blocked_from)
        ) $charset;" );

        // ── Event enquiries ───────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_event_enquiries (
            id                 BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            reference_code     VARCHAR(12) NOT NULL,
            status             VARCHAR(20) NOT NULL DEFAULT 'new',
            event_type         VARCHAR(50) NOT NULL DEFAULT 'corporate',
            preferred_date     DATE DEFAULT NULL,
            guest_count        SMALLINT UNSIGNED NOT NULL DEFAULT 50,
            budget_range       VARCHAR(50) DEFAULT NULL,
            catering_needed    TINYINT(1) NOT NULL DEFAULT 0,
            contact_name       VARCHAR(200) NOT NULL,
            contact_email      VARCHAR(200) NOT NULL,
            contact_phone      VARCHAR(50) DEFAULT NULL,
            contact_zalo       VARCHAR(50) DEFAULT NULL,
            company_name       VARCHAR(200) DEFAULT NULL,
            notes              TEXT DEFAULT NULL,
            auto_reply_sent_at DATETIME DEFAULT NULL,
            created_at         DATETIME NOT NULL,
            updated_at         DATETIME NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY reference_code (reference_code),
            KEY status (status),
            KEY preferred_date (preferred_date),
            KEY contact_email (contact_email(100))
        ) $charset;" );

        // ── Waitlist ──────────────────────────────────────────────────────────
        dbDelta( "CREATE TABLE {$p}thr_waitlist (
            id                   BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            reference_code       VARCHAR(12)  NOT NULL,
            status               VARCHAR(20)  NOT NULL DEFAULT 'waiting',
            requested_date       DATE         NOT NULL COMMENT 'Venue local YYYY-MM-DD',
            requested_time       VARCHAR(5)   DEFAULT NULL COMMENT 'HH:MM preference, NULL = any',
            party_size           TINYINT UNSIGNED  NOT NULL,
            diner_name           VARCHAR(200) NOT NULL,
            diner_email          VARCHAR(200) NOT NULL,
            diner_phone          VARCHAR(50)  DEFAULT NULL,
            diner_lang           VARCHAR(5)   NOT NULL DEFAULT 'en',
            occasion             VARCHAR(50)  NOT NULL DEFAULT 'dinner',
            notes_diner          TEXT         DEFAULT NULL,
            joined_email_sent_at DATETIME     DEFAULT NULL,
            notification_sent_at DATETIME     DEFAULT NULL,
            created_at           DATETIME     NOT NULL,
            updated_at           DATETIME     NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY   reference_code (reference_code),
            KEY          status (status),
            KEY          requested_date (requested_date),
            KEY          diner_email (diner_email(100))
        ) $charset;" );

        // v1.2 migration — add diner_zalo column to existing installs
        $col = $wpdb->get_var( "SHOW COLUMNS FROM {$p}thr_reservations LIKE 'diner_zalo'" );
        if ( ! $col ) {
            $wpdb->query( "ALTER TABLE {$p}thr_reservations ADD COLUMN diner_zalo VARCHAR(50) DEFAULT NULL AFTER diner_phone" );
        }

        // v1.3 migration — floor plan background positioning fields
        $bg_migrations = [
            'bg_scale'    => "FLOAT NOT NULL DEFAULT 1.0",
            'bg_opacity'  => "FLOAT NOT NULL DEFAULT 0.5",
            'bg_offset_x' => "FLOAT NOT NULL DEFAULT 0.0",
            'bg_offset_y' => "FLOAT NOT NULL DEFAULT 0.0",
        ];
        foreach ( $bg_migrations as $col => $definition ) {
            if ( ! $wpdb->get_var( "SHOW COLUMNS FROM {$p}thr_floor_plans LIKE '$col'" ) ) {
                $wpdb->query( "ALTER TABLE {$p}thr_floor_plans ADD COLUMN $col $definition" );
            }
        }

        // v1.4 migration — bg_scale_y for non-uniform background scaling
        if ( ! $wpdb->get_var( "SHOW COLUMNS FROM {$p}thr_floor_plans LIKE 'bg_scale_y'" ) ) {
            $wpdb->query( "ALTER TABLE {$p}thr_floor_plans ADD COLUMN bg_scale_y FLOAT NOT NULL DEFAULT 0.0" );
        }

        // Seed system tags
        self::seed_tags();

        update_option( 'thr_db_version', THR_DB_VERSION );
    }

    private static function seed_tags(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'thr_tags';

        // Skip if already seeded
        if ( $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE is_system = 1" ) > 0 ) return;

        $now  = current_time( 'mysql', true );
        $tags = [
            [ 'VIP',         'vip',         '#B8860B', 1 ],
            [ 'Birthday',    'birthday',     '#C0392B', 1 ],
            [ 'Anniversary', 'anniversary',  '#8E44AD', 1 ],
            [ 'Corporate',   'corporate',    '#2C3E50', 1 ],
            [ 'Event',       'event',        '#1ABC9C', 1 ],
            [ 'Walk-in',     'walk-in',      '#7F8C8D', 1 ],
            [ 'Group',       'group',        '#E67E22', 1 ],
        ];

        foreach ( $tags as [ $name, $slug, $color, $system ] ) {
            $wpdb->insert( $table, compact( 'name', 'slug', 'color' ) + [
                'is_system'  => $system,
                'created_at' => $now,
            ] );
        }
    }

    // ── Helpers used by other classes ─────────────────────────────────────────

    public static function tables(): array {
        global $wpdb;
        $p = $wpdb->prefix;
        return [
            'reservations'       => "{$p}thr_reservations",
            'floor_plans'        => "{$p}thr_floor_plans",
            'furniture'          => "{$p}thr_furniture",
            'tags'               => "{$p}thr_tags",
            'reservation_tags'   => "{$p}thr_reservation_tags",
            'availability_blocks'=> "{$p}thr_availability_blocks",
            'waitlist'           => "{$p}thr_waitlist",
            'event_enquiries'    => "{$p}thr_event_enquiries",
        ];
    }

    public static function t( string $key ): string {
        return self::tables()[ $key ] ?? '';
    }
}
