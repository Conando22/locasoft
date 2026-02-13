const fs = require('fs');
const path = require('path');
const sharp = require('sharp');

// Configuration: folders and widths
const IMAGES_DIR = path.join(__dirname, '..', 'assets', 'img', 'about');
const OUTPUT_DIR = IMAGES_DIR;
const widths = [400, 800, 1200];

async function processImage(file) {
  const ext = path.extname(file).toLowerCase();
  const base = path.basename(file, ext);
  const input = path.join(IMAGES_DIR, file);

  for (const w of widths) {
    const outName = `${base}-${w}.webp`;
    const outPath = path.join(OUTPUT_DIR, outName);
    try {
      await sharp(input)
        .resize({ width: w })
        .webp({ quality: 80 })
        .toFile(outPath);
      console.log('Generated', outPath);
    } catch (err) {
      console.error('Error processing', input, err.message);
    }
  }
}

(async () => {
  try {
    const files = fs.readdirSync(IMAGES_DIR).filter(f => /\.(jpe?g|png|webp)$/i.test(f));
    for (const f of files) {
      await processImage(f);
    }
    console.log('All done');
  } catch (err) {
    console.error(err);
    process.exit(1);
  }
})();
