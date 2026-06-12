"use client";
import { useEffect } from "react";
import type { RefObject } from "react";

/**
 * Attaches mouse-drag-to-scroll behaviour to a scroll container.
 * - Works alongside native touch scroll (touch events are unaffected).
 * - Suppresses click events after a drag so links inside the container
 *   don't navigate when the user releases the mouse.
 */
export function useDragScroll<T extends HTMLElement>(ref: RefObject<T | null>) {
  useEffect(() => {
    const el = ref.current;
    if (!el) return;

    let isDown = false;
    let startX = 0;
    let startScrollLeft = 0;
    let moved = 0;

    const onMouseDown = (e: MouseEvent) => {
      isDown = true;
      moved = 0;
      startX = e.pageX;
      startScrollLeft = el.scrollLeft;
      el.style.cursor = "grabbing";
      el.style.userSelect = "none";
    };

    const onMouseMove = (e: MouseEvent) => {
      if (!isDown) return;
      const dx = e.pageX - startX;
      moved = Math.abs(dx);
      el.scrollLeft = startScrollLeft - dx;
    };

    const stop = () => {
      if (!isDown) return;
      isDown = false;
      el.style.cursor = "";
      el.style.userSelect = "";
    };

    // Capture-phase handler: fires before any React synthetic handlers or Link clicks.
    // Suppresses navigation when the mouse moved more than 5px.
    const onClickCapture = (e: MouseEvent) => {
      if (moved > 5) {
        e.preventDefault();
        e.stopPropagation();
        moved = 0;
      }
    };

    el.addEventListener("mousedown", onMouseDown);
    el.addEventListener("mousemove", onMouseMove);
    el.addEventListener("mouseleave", stop);
    window.addEventListener("mouseup", stop);
    el.addEventListener("click", onClickCapture, true);

    return () => {
      el.removeEventListener("mousedown", onMouseDown);
      el.removeEventListener("mousemove", onMouseMove);
      el.removeEventListener("mouseleave", stop);
      window.removeEventListener("mouseup", stop);
      el.removeEventListener("click", onClickCapture, true);
    };
  }, [ref]);
}
