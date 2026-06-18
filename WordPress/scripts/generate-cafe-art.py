#!/usr/bin/env python3
"""
TEMPO House - Cafe Art Generator
Model: Stable Diffusion XL (stabilityai/stable-diffusion-xl-base-1.0)
Style: Melbourne boho oil painting, consistent across all 7 artframes
Device: Apple Silicon MPS

Fixes applied:
  - pipe.vae.to(torch.float32) prevents NaN/blank images on MPS with float16
  - Prompts kept under 77 CLIP tokens (drink detail first, style appended)
"""

import torch
import shutil
from diffusers import StableDiffusionXLPipeline
from pathlib import Path

OUT = Path("/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/themes/tempohouse/assets/images/cafe")
LIVE = Path("/Users/baileywang/Desktop/AppDev/TempoHouse-WP/themes/tempohouse/assets/images/cafe")

OUT.mkdir(parents=True, exist_ok=True)
LIVE.mkdir(parents=True, exist_ok=True)

# Style suffix - short, appended to each prompt to stay under 77 CLIP tokens
STYLE = "Melbourne boho oil painting, thick impasto brushstrokes, terracotta sage ochre palette, gallery canvas artwork"

NEGATIVE = (
    "text, words, letters, watermark, blurry, low quality, "
    "cartoon, anime, 3d render, deformed, ugly, bad anatomy"
)

# Each prompt: drink-specific detail first (most important), style appended
IMAGES = [
    {
        "name": "cafe-coffee-latte",
        "width": 768, "height": 1152,
        "prompt": (
            "ceramic flat white coffee cup, rosetta latte art on espresso crema, "
            "coffee cherries and roasted beans as botanical border, "
            "dark terracotta warm amber tones, " + STYLE
        ),
    },
    {
        "name": "cafe-matcha-latte",
        "width": 768, "height": 1024,
        "prompt": (
            "ceramic matcha latte cup, vibrant jade green matcha white milk foam, "
            "pampas grass and tea leaves as botanical border, "
            "sage green cream tones, " + STYLE
        ),
    },
    {
        "name": "cafe-matcha-coconut-cloud",
        "width": 768, "height": 1024,
        "prompt": (
            "tall iced coconut matcha glass, white foam cloud on jade green matcha, "
            "coconut fronds and palm leaves as botanical border, "
            "dusty sage ivory tones, " + STYLE
        ),
    },
    {
        "name": "cafe-matcha-jasmine-cloud",
        "width": 768, "height": 1024,
        "prompt": (
            "tall iced jasmine matcha glass, white jasmine blossoms floating, "
            "jasmine vines and wildflowers as botanical border, "
            "pale sage warm cream dreamy mood, " + STYLE
        ),
    },
    {
        "name": "cafe-matcha-yuzu",
        "width": 768, "height": 1024,
        "prompt": (
            "tall iced matcha yuzu glass, sliced yuzu citrus and blossoms, "
            "golden yuzu branches as botanical border, "
            "golden yellow jade green bold, " + STYLE
        ),
    },
    {
        "name": "cafe-matcha-strawberry",
        "width": 768, "height": 1024,
        "prompt": (
            "tall iced matcha strawberry glass with crimson swirls, "
            "fresh strawberries and wildflowers as botanical border, "
            "crimson red dusty sage tones, " + STYLE
        ),
    },
    {
        "name": "cafe-matcha-mango",
        "width": 768, "height": 1024,
        "prompt": (
            "tall iced matcha mango glass with golden layers, "
            "mango slices and tropical leaves as botanical border, "
            "amber golden emerald green tones, " + STYLE
        ),
    },
]


def main():
    device = "mps" if torch.backends.mps.is_available() else "cpu"
    print(f"\n{'─'*52}")
    print(f"  TEMPO House Cafe Art Generator")
    print(f"  Device: {device.upper()}  |  Model: SDXL")
    print(f"{'─'*52}\n")

    print("Loading SDXL pipeline...")
    pipe = StableDiffusionXLPipeline.from_pretrained(
        "stabilityai/stable-diffusion-xl-base-1.0",
        torch_dtype=torch.float16,
        use_safetensors=True,
        variant="fp16",
    )
    # MPS fp16 produces NaN throughout UNet + VAE on Apple Silicon.
    # Cast the entire pipeline to float32 after loading — uses ~13 GB RAM
    # but is stable. M1 Max handles this fine.
    pipe = pipe.to(torch.float32)
    pipe = pipe.to(device)
    pipe.enable_attention_slicing()
    print("Pipeline ready.\n")

    for img in IMAGES:
        name = img["name"]
        print(f"Generating: {name}")
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
    print("  All 7 images generated and synced.")
    print(f"  {OUT}")
    print("=" * 52)


if __name__ == "__main__":
    main()
