import type { NextConfig } from 'next'

const nextConfig: NextConfig = {
  // Full server app (SiteGround Node.js / Passenger)
  serverExternalPackages: ['canvas'],
  turbopack: {
    // Alias the native canvas module to an empty stub
    // (konva loads it in its Node.js build path, but we use Client Components only)
    resolveAlias: {
      canvas: './lib/canvas-stub.js',
    },
  },
}

export default nextConfig
