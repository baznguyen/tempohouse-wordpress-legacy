#!/usr/bin/env python3
"""
TEMPO House - Events Page Art Generator
Model: Stable Diffusion XL (stabilityai/stable-diffusion-xl-base-1.0)
Style: Melbourne boho oil painting — event venue / gallery setting
Device: Apple Silicon MPS

9 images: 8 gallery wall event-type frames + 1 catering editorial frame.
Dimensions match exact CSS aspect-ratios defined in events-pages.css.
"""

import torch
import shutil
from diffusers import StableDiffusionXLPipeline
from pathlib import Path

OUT = Path("/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/themes/tempohouse/assets/images/events")
LIVE = Path("/Users/baileywang/Desktop/AppDev/TempoHouse-WP/themes/tempohouse/assets/images/events")

OUT.mkdir(parents=True, exist_ok=True)
LIVE.mkdir(parents=True, exist_ok=True)

STYLE = "Melbourne boho oil painting, thick impasto brushstrokes, terracotta sage ochre amber palette, gallery canvas artwork"

NEGATIVE = (
    "text, words, letters, watermark, blurry, low quality, "
    "cartoon, anime, 3d render, deformed, ugly, bad anatomy"
)

IMAGES = [
    # ── Gallery wall — 8 event type frames ──────────────────────────
    {
        "name": "event-product-launch",
        "width": 768, "height": 1024,   # 3:4 portrait
        "prompt": (
            "chic product launch event in gallery space, "
            "guests with wine glasses, branded installation on gallery wall, "
            "elegant District 3 Saigon venue interior, warm evening light, " + STYLE
        ),
        "seed": 101,
    },
    {
        "name": "event-brand-activation",
        "width": 768, "height": 1152,   # 2:3 portrait narrow
        "prompt": (
            "creative brand activation in art gallery, "
            "immersive installation, guests exploring exhibition, "
            "modern HCMC gallery venue, ambient warm lighting, " + STYLE
        ),
        "seed": 102,
    },
    {
        "name": "event-intimate",
        "width": 768, "height": 1280,   # 3:5 portrait tall
        "prompt": (
            "intimate private gathering in warm interior, "
            "small group around candlelit table, wine and conversation, "
            "cosy boho venue atmosphere District 3, amber candlelight, " + STYLE
        ),
        "seed": 103,
    },
    {
        "name": "event-art-exhibition",
        "width": 768, "height": 1024,   # 3:4 portrait
        "prompt": (
            "art gallery opening night, artworks hanging on white gallery walls, "
            "guests with champagne admiring paintings, track lighting overhead, "
            "gallery venue HCMC evening atmosphere, " + STYLE
        ),
        "seed": 104,
    },
    {
        "name": "event-corporate",
        "width": 1280, "height": 768,   # 5:3 landscape
        "prompt": (
            "elegant corporate dinner long table setup in gallery venue, "
            "white linen tablecloths candles flowers centrepieces, "
            "seated guests in sophisticated interior, warm ambient light, " + STYLE
        ),
        "seed": 105,
    },
    {
        "name": "event-birthday",
        "width": 1024, "height": 1024,  # 1:1 square
        "prompt": (
            "joyful birthday celebration in beautiful venue, "
            "flowers and balloons table arrangement, guests celebrating, "
            "warm festive interior atmosphere, golden amber tones, " + STYLE
        ),
        "seed": 106,
    },
    {
        "name": "event-weddings",
        "width": 1024, "height": 768,   # 4:3 landscape
        "prompt": (
            "intimate wedding ceremony in gallery venue, "
            "floral arch and white petals, couple exchanging vows, "
            "guests seated, soft romantic afternoon light District 3, " + STYLE
        ),
        "seed": 107,
    },
    {
        "name": "event-engagement",
        "width": 768, "height": 1024,   # 3:4 portrait
        "prompt": (
            "engagement party celebration in intimate venue, "
            "champagne glasses and floral arrangement, couple with friends, "
            "warm evening interior glow, terracotta and blush tones, " + STYLE
        ),
        "seed": 108,
    },
    # ── Catering editorial frame ─────────────────────────────────────
    {
        "name": "event-catering",
        "width": 768, "height": 1024,   # 3:4 portrait
        "prompt": (
            "beautiful catering spread, elegant canapes and small bites on board, "
            "cocktails and wine glasses, floral garnishes, "
            "styled event catering table in venue setting, " + STYLE
        ),
        "seed": 109,
    },
]


def main():
    device = "mps" if torch.backends.mps.is_available() else "cpu"
    print(f"\n{'─'*52}")
    print(f"  TEMPO House Events Art Generator")
    print(f"  Device: {device.upper()}  |  Model: SDXL  |  9 images")
    print(f"{'─'*52}\n")

    print("Loading SDXL pipeline...")
    pipe = StableDiffusionXLPipeline.from_pretrained(
        "stabilityai/stable-diffusion-xl-base-1.0",
        torch_dtype=torch.float16,
        use_safetensors=True,
        variant="fp16",
    )
    pipe = pipe.to(torch.float32)
    pipe = pipe.to(device)
    pipe.enable_attention_slicing()
    print("Pipeline ready.\n")

    for i, img in enumerate(IMAGES, 1):
        name = img["name"]
        print(f"[{i}/9] {name}  ({img['width']}x{img['height']})")
        result = pipe(
            prompt=img["prompt"],
            negative_prompt=NEGATIVE,
            width=img["width"],
            height=img["height"],
            num_inference_steps=35,
            guidance_scale=8.0,
            generator=torch.Generator(device=device).manual_seed(img["seed"]),
        )
        out_path = OUT / f"{name}.png"
        result.images[0].save(out_path)
        shutil.copy2(out_path, LIVE / f"{name}.png")
        size_kb = out_path.stat().st_size // 1024
        print(f"  Saved: {name}.png  ({size_kb} KB)\n")

    print("=" * 52)
    print("  All 9 events images generated and synced.")
    print(f"  {OUT}")
    print("=" * 52)


if __name__ == "__main__":
    main()
