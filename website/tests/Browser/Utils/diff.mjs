import { PNG } from "pngjs"
import fs from "fs"
import match from "pixelmatch"

const request = JSON.parse(process.argv[2]);

try {
    var img1 = fs.createReadStream(request.image_1).pipe(new PNG()).on('parsed', doneReading);
    var img2 = fs.createReadStream(request.image_2).pipe(new PNG()).on('parsed', doneReading);
} catch (e) {
    console.log(JSON.stringify({
        'error_type': 'unknown',
        'error': e.message
    }));
}

function doneReading() {
    if (!img1.data || !img2.data) return;

    if (img1.width !== img2.width || img1.height !== img2.height) {
        console.log(JSON.stringify({
            "error_type": "image_dimensions_mismatch",
            "error": `Image dimensions do not match: ${img1.width}x${img1.height} vs ${img2.width}x${img2.height}`
        }))
        return;
    }

    let diff = new PNG({width: img1.width, height: img1.height});

    let diffs = match(img1.data, img2.data, diff.data, diff.width, diff.height, {
        threshold: request.threshold,
        includeAA: false
    });

    if (request.output) {
        diff.pack().pipe(fs.createWriteStream(request.output));
    }

    let output = {
        'pixels': diffs,
        'error_percentage': (Math.round(100 * 100 * diffs / (diff.width * diff.height)) / 100)
    };

    console.log(JSON.stringify(output));
}
