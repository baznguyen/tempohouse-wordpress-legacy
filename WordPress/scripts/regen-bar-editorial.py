#!/usr/bin/env python3
"""
Regenerate only the 3 bar editorial frames with ambient bar-setting prompts.
"""

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
    "bright daylight, flat lighting, white background"
)

IMAGES = [
    {
        "name": "bar-programme",
        "width": 768, "height": 1024,
        "prompt": (
            "intimate cocktail bar interior at night, bartender silhouette behind bar, "
            "glowing amber bottles on shelves, cocktails on marble bar top, "
            "candlelit warm glow, moody atmospheric bar scene, " + STYLE
        ),
        "seed": 77,
    },
    {
        "name": "bar-wine",
        "width": 768, "height": 1024,
        "prompt": (
            "wine bar corner at night, wine glasses and bottles on wooden table, "
            "soft ambient bar lighting, romantic candlelit bar interior, "
            "warm ochre golden tones, intimate evening bar setting, " + STYLE
        ),
        "seed": 88,
    },
    {
        "name": "bar-atmosphere",
        "width": 768, "height": 1024,
        "prompt": (
            "open-air terrace bar at night District 3 Saigon, "
            "string lights overhead, cocktails on outdoor tables, "
            "tropical plants and lantern glow, beautiful ambient evening terrace, " + STYLE
        ),
        "seed": 99,
    },
]


def main():
    device = "mps" if torch.backends.mps.is_available() else "cpu"
    print(f"\n{'─'*52}")
    print(f"  TEMPO Bar — Editorial Frames Regen")
    print(f"  Device: {device.upper()}  |  3 images")
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
        print(f"[{i}/3] Generating: {name}  (seed={img['seed']})")
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
    print("  3 editorial frames regenerated and synced.")
    print("=" * 52)


if __name__ == "__main__":
    main()
