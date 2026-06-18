#!/usr/bin/env python3
"""
TEMPO House - Bar Art Generator
Model: Stable Diffusion XL (stabilityai/stable-diffusion-xl-base-1.0)
Style: Melbourne boho oil painting — bar/night edition (amber, candlelit, moody)
Device: Apple Silicon MPS

9 images total:
  3 editorial frames (programme, wine, atmosphere)
  6 signature cocktail frames
All 768x1024 (3:4 portrait, matching tempo-frame aspect-ratio in bar.css)
"""

import torch
import shutil
from diffusers import StableDiffusionXLPipeline
from pathlib import Path

OUT = Path("/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/themes/tempohouse/assets/images/bar")
LIVE = Path("/Users/baileywang/Desktop/AppDev/TempoHouse-WP/themes/tempohouse/assets/images/bar")

OUT.mkdir(parents=True, exist_ok=True)
LIVE.mkdir(parents=True, exist_ok=True)

# Style suffix — same visual language as cafe but bar-shifted: amber candlelight,
# moody evening tones instead of morning sage/cream
STYLE = "Melbourne boho oil painting, thick impasto brushstrokes, amber candlelight palette, terracotta ochre burnt sienna, gallery canvas artwork"

NEGATIVE = (
    "text, words, letters, watermark, blurry, low quality, "
    "cartoon, anime, 3d render, deformed, ugly, bad anatomy"
)

IMAGES = [
    # ── Editorial frames ─────────────────────────────────────────────
    {
        "name": "bar-programme",
        "width": 768, "height": 1024,
        "prompt": (
            "intimate cocktail bar interior at night, bartender silhouette behind bar, "
            "glowing amber bottles on shelves, cocktails on marble bar top, "
            "candlelit warm glow, moody atmospheric bar scene, " + STYLE
        ),
    },
    {
        "name": "bar-wine",
        "width": 768, "height": 1024,
        "prompt": (
            "wine bar corner at night, wine glasses and bottles on wooden table, "
            "soft ambient bar lighting, romantic candlelit bar interior, "
            "warm ochre golden tones, intimate evening bar setting, " + STYLE
        ),
    },
    {
        "name": "bar-atmosphere",
        "width": 768, "height": 1024,
        "prompt": (
            "open-air terrace bar at night District 3 Saigon, "
            "string lights overhead, cocktails on outdoor tables, "
            "tropical plants and lantern glow, beautiful ambient evening terrace, " + STYLE
        ),
    },
    # ── Signature cocktails ──────────────────────────────────────────
    {
        "name": "bar-espresso-martini",
        "width": 768, "height": 1024,
        "prompt": (
            "dark espresso martini in coupe glass, frothy coffee crema top, "
            "Vietnamese coffee beans and phin filter as botanical border, "
            "deep brown black amber tones, " + STYLE
        ),
    },
    {
        "name": "bar-lychee-martini",
        "width": 768, "height": 1024,
        "prompt": (
            "pale pink lychee martini in coupe glass, lychee fruit garnish, "
            "lychee fruits and Mekong Delta leaves as botanical border, "
            "blush pink and dusty amber tones, " + STYLE
        ),
    },
    {
        "name": "bar-panpan-spritz",
        "width": 768, "height": 1024,
        "prompt": (
            "green pandan spritz in highball glass with bubbles and lime wheel, "
            "fresh pandan leaves and coconut as botanical border, "
            "deep sage green and lime tones, " + STYLE
        ),
    },
    {
        "name": "bar-negroni",
        "width": 768, "height": 1024,
        "prompt": (
            "deep amber negroni in rocks glass with large ice cube and orange peel, "
            "Campari botanicals and citrus peel as border, "
            "burnt orange crimson deep terracotta tones, " + STYLE
        ),
    },
    {
        "name": "bar-manhattan",
        "width": 768, "height": 1024,
        "prompt": (
            "rich mahogany Manhattan in coupe glass with cherry garnish, "
            "rye grain and dark cherry as botanical border, "
            "deep brown mahogany warm amber tones, " + STYLE
        ),
    },
    {
        "name": "bar-yuzu-spritz",
        "width": 768, "height": 1024,
        "prompt": (
            "pale golden yuzu spritz in wine glass with bubbles and rose petal, "
            "yuzu slices and rose botanicals as border, "
            "golden yellow soft rose tones, " + STYLE
        ),
    },
]


def main():
    device = "mps" if torch.backends.mps.is_available() else "cpu"
    print(f"\n{'─'*52}")
    print(f"  TEMPO House Bar Art Generator")
    print(f"  Device: {device.upper()}  |  Model: SDXL  |  9 images")
    print(f"{'─'*52}\n")

    print("Loading SDXL pipeline...")
    pipe = StableDiffusionXLPipeline.from_pretrained(
        "stabilityai/stable-diffusion-xl-base-1.0",
        torch_dtype=torch.float16,
        use_safetensors=True,
        variant="fp16",
    )
    # MPS fp16 causes NaN throughout UNet + VAE — cast entire pipeline to float32.
    pipe = pipe.to(torch.float32)
    pipe = pipe.to(device)
    pipe.enable_attention_slicing()
    print("Pipeline ready.\n")

    for i, img in enumerate(IMAGES, 1):
        name = img["name"]
        print(f"[{i}/9] Generating: {name}")
        result = pipe(
            prompt=img["prompt"],
            negative_prompt=NEGATIVE,
            width=img["width"],
            height=img["height"],
            num_inference_steps=30,
            guidance_scale=7.5,
            generator=torch.Generator(device=device).manual_seed(42),
        )
        out_path = OUT / f"{name}.png"
        result.images[0].save(out_path)
        shutil.copy2(out_path, LIVE / f"{name}.png")
        size_kb = out_path.stat().st_size // 1024
        print(f"  Saved: {name}.png  ({size_kb} KB)\n")

    print("=" * 52)
    print("  All 9 bar images generated and synced.")
    print(f"  {OUT}")
    print("=" * 52)


if __name__ == "__main__":
    main()
