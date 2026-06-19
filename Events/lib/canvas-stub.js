// Empty stub — replaces the native 'canvas' package for Konva's Node.js build path.
// Konva resolves to this when running in a Next.js SSR context.
// Actual canvas rendering happens client-side only (all react-konva code uses 'use client').
module.exports = {}
