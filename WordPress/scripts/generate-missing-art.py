#!/usr/bin/env python3
"""
TEMPO House - Missing Art Generator + Full Web Optimisation Pass
Generates 11 missing images (saved as JPEG q82) and converts all
existing PNGs to JPEG for consistent web-optimised delivery.

New images:
  2 cafe (kitchen, space)
  7 gallery (artists frame, walk I-V, level1)
  2 venue (gallery interior, neighbourhood)

Existing images converted: 7 cafe + 9 bar + 9 events PNGs → JPEG
"""

import torch
import shutil
from pathlib import Path
from PIL import Image
from diffusers import StableDiffusionXLPipeline

THEME_GD = Path("/Users/baileywang/Library/CloudStorage/GoogleDrive-bailey@ragingmonk.co/My Drive/Websites/tempohouse.com.vn/WordPress/themes/tempohouse/assets/images")
THEME_LOCAL = Path("/Users/baileywang/Desktop/AppDev/TempoHouse-WP/themes/tempohouse/assets/images")

JPEG_QUALITY = 82  # Good balance of quality vs file size (~150-300 KB per image)

STYLE = "Melbourne boho oil painting, thick impasto brushstrokes, terracotta sage ochre amber palette, gallery canvas artwork"

NEGATIVE = (
    "text, words, letters, watermark, blurry, low quality, "
    "cartoon, anime, 3d render, deformed, ugly, bad anatomy"
)

NEW_IMAGES = [
    # ── /cafe missing frames ─────────────────────────────────────────
    {
        "subdir": "cafe",
        "name": "cafe-kitchen",
        "width": 768, "height": 1024,
        "prompt": (
            "small plates and canapes on cafe table, seasonal Vietnamese snacks, "
            "bruschetta and sharing plates styled on timber surface, "
            "warm daylight cafe interior, earthy food styling, " + STYLE
        ),
        "seed": 201,
    },
    {
        "subdir": "cafe",
        "name": "cafe-space",
        "width": 768, "height": 1024,
        "prompt": (
            "specialty cafe interior daytime, exposed brick walls high ceilings, "
            "timber furniture and plants, colonial shophouse cafe District 3 Saigon, "
            "morning light streaming through windows, warm terracotta tones, " + STYLE
        ),
        "seed": 202,
    },
    # ── /gallery missing frames ──────────────────────────────────────
    {
        "subdir": "gallery",
        "name": "gallery-artists",
        "width": 768, "height": 1024,
        "prompt": (
            "white gallery interior with artworks on walls, track lighting overhead, "
            "column-free gallery floor Level 1, neutral gallery space District 3, "
            "gallery opening atmosphere, warm ambient light, " + STYLE
        ),
        "seed": 203,
    },
    {
        "subdir": "gallery",
        "name": "gallery-walk-I",
        "width": 768, "height": 1024,
        "prompt": (
            "abstract oil painting canvas, gestural botanical landscape composition, "
            "expressive loose brushwork, terracotta burnt sienna and sage greens, "
            "gallery artwork abstract expressionist, " + STYLE
        ),
        "seed": 204,
    },
    {
        "subdir": "gallery",
        "name": "gallery-walk-II",
        "width": 1024, "height": 768,
        "prompt": (
            "abstract horizontal oil painting, sweeping gestural landscape marks, "
            "golden ochre and deep terracotta with cream horizon, "
            "wide format expressionist canvas gallery artwork, " + STYLE
        ),
        "seed": 205,
    },
    {
        "subdir": "gallery",
        "name": "gallery-walk-III",
        "width": 768, "height": 1152,
        "prompt": (
            "tall abstract oil painting, vertical gestural figure or tree composition, "
            "deep sage and amber with organic flowing marks, "
            "tall portrait canvas contemporary gallery artwork, " + STYLE
        ),
        "seed": 206,
    },
    {
        "subdir": "gallery",
        "name": "gallery-walk-IV",
        "width": 1024, "height": 768,
        "prompt": (
            "wide abstract oil painting landscape, loose Saigon streetscape impression, "
            "tropical trees and warm light in earthy palette, "
            "horizontal expressionist canvas gallery artwork, " + STYLE
        ),
        "seed": 207,
    },
    {
        "subdir": "gallery",
        "name": "gallery-walk-V",
        "width": 768, "height": 1024,
        "prompt": (
            "abstract portrait oil painting, moody tonal composition, "
            "deep terracotta and ink with amber highlights, "
            "gallery artwork contemporary portrait expressionist, " + STYLE
        ),
        "seed": 208,
    },
    {
        "subdir": "gallery",
        "name": "gallery-level1",
        "width": 768, "height": 1024,
        "prompt": (
            "gallery floor interior Level 1, artworks on neutral walls, "
            "adjustable track lighting, open plan gallery space HCMC, "
            "gallery showing colonial shophouse interior, warm gallery light, " + STYLE
        ),
        "seed": 209,
    },
    # ── /venue missing frames ────────────────────────────────────────
    {
        "subdir": "venue",
        "name": "venue-gallery",
        "width": 1152, "height": 768,
        "prompt": (
            "wide gallery interior Level 1 TEMPO House, artworks on walls, "
            "column-free open space, track lighting, restored colonial building, "
            "gallery venue District 3 Saigon landscape view, " + STYLE
        ),
        "seed": 210,
    },
    {
        "subdir": "venue",
        "name": "venue-neighbourhood",
        "width": 768, "height": 1024,
        "prompt": (
            "Pasteur Street District 3 Ho Chi Minh City, "
            "tree-lined colonial shophouse street, warm afternoon light, "
            "Vietnamese urban neighbourhood street scene, earthy tones, " + STYLE
        ),
        "seed": 211,
    },
]


def save_jpeg(image, path, quality=JPEG_QUALITY):
    """Save PIL image as JPEG with optimization."""
    rgb = image.convert("RGB")
    rgb.save(path, format="JPEG", quality=quality, optimize=True, progressive=True)


def convert_existing_pngs():
    """Convert all existing PNGs to optimized JPEGs in-place."""
    print("\n── Converting existing PNGs to JPEG ──────────────────")
    subdirs = ["cafe", "bar", "events"]
    total_saved_kb = 0

    for subdir in subdirs:
        png_dir = THEME_GD / subdir
        local_dir = THEME_LOCAL / subdir
        pngs = sorted(png_dir.glob("*.png"))
        if not pngs:
            continue
        print(f"\n  [{subdir}] {len(pngs)} PNGs")
        for png_path in pngs:
            jpg_path_gd = png_path.with_suffix(".jpg")
            jpg_path_local = local_dir / jpg_path_gd.name
            original_kb = png_path.stat().st_size // 1024
            img = Image.open(png_path)
            save_jpeg(img, jpg_path_gd)
            shutil.copy2(jpg_path_gd, jpg_path_local)
            new_kb = jpg_path_gd.stat().st_size // 1024
            saved = original_kb - new_kb
            total_saved_kb += saved
            print(f"  {png_path.name} → {jpg_path_gd.name}  {original_kb}KB → {new_kb}KB  (-{saved}KB)")
            # Remove old PNGs
            png_path.unlink()
            (local_dir / png_path.name).unlink(missing_ok=True)

    print(f"\n  Total saved: {total_saved_kb // 1024:.1f} MB across existing images")


def main():
    # Step 1: Convert existing PNGs first (no GPU needed)
    convert_existing_pngs()

    # Step 2: Generate 11 new images
    device = "mps" if torch.backends.mps.is_available() else "cpu"
    total = len(NEW_IMAGES)
    print(f"\n── Generating {total} new images ─────────────────────")
    print(f"   Device: {device.upper()}  |  Format: JPEG q{JPEG_QUALITY}\n")

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

    for i, img in enumerate(NEW_IMAGES, 1):
        subdir = img["subdir"]
        name = img["name"]
        out_dir = THEME_GD / subdir
        live_dir = THEME_LOCAL / subdir
        out_dir.mkdir(parents=True, exist_ok=True)
        live_dir.mkdir(parents=True, exist_ok=True)

        print(f"[{i}/{total}] {name}  ({img['width']}x{img['height']})")
        result = pipe(
            prompt=img["prompt"],
            negative_prompt=NEGATIVE,
            width=img["width"],
            height=img["height"],
            num_inference_steps=35,
            guidance_scale=8.0,
            generator=torch.Generator(device=device).manual_seed(img["seed"]),
        )
        out_path = out_dir / f"{name}.jpg"
        save_jpeg(result.images[0], out_path)
        shutil.copy2(out_path, live_dir / f"{name}.jpg")
        size_kb = out_path.stat().st_size // 1024
        print(f"  Saved: {name}.jpg  ({size_kb} KB)\n")

    print("=" * 52)
    print("  All done. New images generated, existing PNGs converted.")
    print("=" * 52)


if __name__ == "__main__":
    main()
