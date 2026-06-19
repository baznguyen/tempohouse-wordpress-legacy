/**
 * TEMPO House — Elementor Adapter
 *
 * Reinitialises each custom widget's JS when Elementor drops it on the
 * editor canvas. The existing site JS modules init on DOMContentLoaded,
 * which fires once. When a widget is added/re-rendered in the editor,
 * Elementor fires frontend/element_ready/{widget-name} — we hook each
 * module here so the interactive behaviour works in the editor preview.
 */
( function () {
    'use strict';

    if ( typeof window.elementorFrontend === 'undefined' ) return;

    var hooks = window.elementorFrontend.hooks;

    // ── Helper: run a function only if it exists on window ───────────────
    function tryInit( fnName ) {
        if ( typeof window[ fnName ] === 'function' ) {
            window[ fnName ]();
        }
    }

    // ── Hero ─────────────────────────────────────────────────────────────
    hooks.addAction( 'frontend/element_ready/tempohouse-hero', function ( $scope ) {
        // hero.js scans for .hero — trigger its init if exported
        tryInit( 'initTempoHero' );
    } );

    // ── Events Carousel ───────────────────────────────────────────────────
    hooks.addAction( 'frontend/element_ready/tempohouse-events-carousel', function ( $scope ) {
        tryInit( 'initTempoEvents' );
    } );

    // ── Moods / Spaces Carousel ───────────────────────────────────────────
    hooks.addAction( 'frontend/element_ready/tempohouse-moods-carousel', function ( $scope ) {
        tryInit( 'initTempoMoods' );
    } );

    // ── Tempo Frame (downlight hover) ─────────────────────────────────────
    hooks.addAction( 'frontend/element_ready/tempohouse-tempo-frame', function ( $scope ) {
        tryInit( 'initTempoFrame' );
    } );

    // ── Cocktail Carousel ─────────────────────────────────────────────────
    hooks.addAction( 'frontend/element_ready/tempohouse-cocktail-carousel', function ( $scope ) {
        tryInit( 'initTempoBar' );
    } );

    // ── Gallery Walk ──────────────────────────────────────────────────────
    hooks.addAction( 'frontend/element_ready/tempohouse-gallery-walk', function ( $scope ) {
        tryInit( 'initTempoGallery' );
    } );

} )();
