#!/usr/bin/env python3
"""Regenerate 6 signature cocktail frames — cocktail in ambient bar setting."""

import torch
import shutil
from diffusers import StableDiffusionXLPipeline
from pathlib import Path

OUT = Path("/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/themes/tempohouse/assets/images/bar")
LIVE = Path("/Users/baileywang/Desktop/AppDev/TempoHouse-WP/themes/tempohouse/assets/images/bar")

STYLE = "Melbourne boho oil painting, thick impasto brushstrokes, amber candlelight palette, terracotta ochre burnt sienna, gallery canvas artwork"

NEGATIVE = (
    "text, words, letters, watermark, blurry, low quality, "
    "cartoon, anime, 3d render, deformed, ugly, bad anatomy, "
    "flat lighting, white background, isolated object"
)

IMAGES = [
    {
        "name": "bar-espresso-martini",
        "prompt": (
            "espresso martini coupe glass on dark bar counter, "
            "candlelit bar interior glowing bottles background, "
            "moody evening bar atmosphere, deep brown amber shadows, " + STYLE
        ),
        "seed": 11,
    },
    {
        "name": "bar-lychee-martini",
        "prompt": (
            "pink lychee martini coupe on wooden bar table, "
            "soft ambient bar lighting warm bokeh background, "
            "intimate bar setting evening glow, blush amber tones, " + STYLE
        ),
        "seed": 22,
    },
    {
        "name": "bar-panpan-spritz",
        "prompt": (
            "green pandan spritz highball glass on outdoor terrace table, "
            "string lights and tropical plants background, "
            "warm evening terrace bar atmosphere, sage and amber glow, " + STYLE
        ),
        "seed": 33,
    },
    {
        "name": "bar-negroni",
        "prompt": (
            "negroni rocks glass on dark marble bar top, "
            "glowing amber bar shelves and bottles in background, "
            "candlelit moody cocktail bar scene, burnt orange deep shadows, " + STYLE
        ),
        "seed": 44,
    },
    {
        "name": "bar-manhattan",
        "prompt": (
            "Manhattan coupe glass on polished bar counter at night, "
            "dark intimate bar interior candlelight background, "
            "classic bar atmosphere warm mahogany amber tones, " + STYLE
        ),
        "seed": 55,
    },
    {
        "name": "bar-yuzu-spritz",
        "prompt": (
            "yuzu spritz wine glass on bar table with golden bokeh, "
            "warm ambient bar interior evening light background, "
            "beautiful cocktail bar atmosphere golden soft tones, " + STYLE
        ),
        "seed": 66,
    },
]


def main():
    device = "mps" if torch.backends.mps.is_available() else "cpu"
    print(f"\n{'─'*52}")
    print(f"  TEMPO Bar — Cocktail Frames (Bar Setting)")
    print(f"  Device: {device.upper()}  |  6 images")
    print(f"{'─'*52}\n")

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
        print(f"[{i}/6] {name}  (seed={img['seed']})")
        result = pipe(
            prompt=img["prompt"],
            negative_prompt=NEGATIVE,
            width=768,
            height=1024,
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
    print("  6 cocktail frames regenerated and synced.")
    print("=" * 52)


if __name__ == "__main__":
    main()
