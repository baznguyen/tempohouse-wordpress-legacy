#!/usr/bin/env bash
# ──────────────────────────────────────────────────────────────────
# TEMPO House — Café Art Generation
# Model: FLUX.1-schnell via mflux (Apple Silicon / MLX)
# Style: Japanese botanical illustration, consistent across all 7
# Run: bash WordPress/scripts/generate-cafe-art.sh
# ──────────────────────────────────────────────────────────────────
set -euo pipefail

MFLUX="$HOME/.venvs/mflux/bin/mflux-generate"
OUT="/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/themes/tempohouse/assets/images/cafe"
LIVE="/Users/baileywang/Desktop/AppDev/TempoHouse-WP/themes/tempohouse/assets/images/cafe"

MODEL="schnell"
QUANTIZE="8"
STEPS="4"
SEED="42"
# Portrait: matcha frames are 3:4, coffee is 2:3
W_MATCHA=768
H_MATCHA=1024
W_COFFEE=768
H_COFFEE=1152

# Style prefix — locked across all 7 for visual consistency
# Melbourne boho oil painting: thick impasto, earthy bohemian palette, expressive Australian art feel
STYLE="Melbourne boho oil painting on canvas, thick expressive impasto brushstrokes, rich textured oil paint, bohemian earthy palette of terracotta, burnt sienna, dusty sage, mustard, warm cream and ochre, loose gestural Australian contemporary art style, gallery canvas artwork, no text, no typography"

echo "──────────────────────────────────────────"
echo " TEMPO House Café Art Generator"
echo " Model: FLUX.1-$MODEL  |  Quantize: ${QUANTIZE}-bit"
echo " NOTE: First run downloads ~12 GB model"
echo "──────────────────────────────────────────"
mkdir -p "$OUT" "$LIVE"

run_gen() {
  local name="$1"
  local prompt="$2"
  local w="$3"
  local h="$4"
  local out_path="$OUT/${name}.png"

  echo ""
  echo "▶ Generating: $name"
  "$MFLUX" \
    --model "$MODEL" \
    --quantize "$QUANTIZE" \
    --steps "$STEPS" \
    --seed "$SEED" \
    --width "$w" \
    --height "$h" \
    --prompt "$prompt" \
    --output "$out_path"

  cp "$out_path" "$LIVE/${name}.png"
  echo "✓ Saved: $name.png"
}

# ── 1. Coffee ──────────────────────────────────────────────────────
run_gen "cafe-coffee-latte" \
  "$STYLE — ceramic flat white coffee cup painted as hero centrepiece, intricate rosetta latte art on crema, wild dried grasses and coffee cherries woven as boho botanical border, dark terracotta and warm amber tones" \
  "$W_COFFEE" "$H_COFFEE"

# ── 2. Matcha Latte ────────────────────────────────────────────────
run_gen "cafe-matcha-latte" \
  "$STYLE — ceramic matcha latte cup as hero centrepiece, jade green matcha pour and white milk foam, dried pampas grass and tea leaves as boho botanical border, sage green and cream tones" \
  "$W_MATCHA" "$H_MATCHA"

# ── 3. Coconut Cloud ───────────────────────────────────────────────
run_gen "cafe-matcha-coconut-cloud" \
  "$STYLE — tall iced coconut matcha glass as hero centrepiece, white foam cloud on jade green matcha, tropical coconut fronds and dried palm leaves as boho botanical border, dusty sage and ivory tones" \
  "$W_MATCHA" "$H_MATCHA"

# ── 4. Jasmine Cloud ───────────────────────────────────────────────
run_gen "cafe-matcha-jasmine-cloud" \
  "$STYLE — tall iced jasmine matcha glass as hero centrepiece, white jasmine blossoms and petals scattered as boho botanical border, pale sage and warm cream tones, dreamy and ethereal" \
  "$W_MATCHA" "$H_MATCHA"

# ── 5. Matcha Yuzu ─────────────────────────────────────────────────
run_gen "cafe-matcha-yuzu" \
  "$STYLE — tall iced matcha yuzu glass as hero centrepiece, sliced yuzu citrus and citrus blossoms as boho botanical border, golden yellow and dusty jade green tones, vibrant and bold" \
  "$W_MATCHA" "$H_MATCHA"

# ── 6. Matcha Strawberry ───────────────────────────────────────────
run_gen "cafe-matcha-strawberry" \
  "$STYLE — tall iced matcha strawberry glass with crimson swirls as hero centrepiece, fresh strawberries and wildflower blossoms as boho botanical border, warm crimson red and dusty sage tones" \
  "$W_MATCHA" "$H_MATCHA"

# ── 7. Matcha Mango ────────────────────────────────────────────────
run_gen "cafe-matcha-mango" \
  "$STYLE — tall iced matcha mango glass with golden layers as hero centrepiece, tropical mango slices and dried tropical leaves as boho botanical border, amber golden and emerald green tones" \
  "$W_MATCHA" "$H_MATCHA"

echo ""
echo "══════════════════════════════════════════"
echo " All 7 images generated and synced."
echo " Assets: $OUT"
echo "══════════════════════════════════════════"
