package logopatch

import (
	"image"
	"image/color"
)

func isWhiteish(r, g, b, a float64) bool {
	if a <= 5 {
		return true
	}

	return (((0.2126*r + 0.7152*g + 0.0722*b) / 255) * 100) >= 90
}

func Patch(logo image.Image) (*image.NRGBA64, bool) {
	bounds := logo.Bounds()
	rgba := image.NewNRGBA64(bounds)
	filled := 0.0

	for y := bounds.Min.Y; y < bounds.Max.Y; y++ {
		for x := bounds.Min.X; x < bounds.Max.X; x++ {
			r32, g32, b32, a32 := logo.At(x, y).RGBA()
			rF, gF, bF, aF := float64(r32>>8), float64(g32>>8), float64(b32>>8), float64(a32>>8)
			r, g, b, a := uint8(rF), uint8(gF), uint8(bF), uint8(aF)

			if isWhiteish(rF, gF, bF, aF) || a == 0 {
				rgba.Set(x, y, color.RGBA{255, 255, 255, 0})
			} else {
				filled++
				rgba.Set(x, y, color.RGBA{r, g, b, a})
			}
		}
	}

	totalFillable := float64(bounds.Dy() * bounds.Dx())
	filledPercent := (filled / totalFillable) * 100

	return rgba, filledPercent >= 85
}
