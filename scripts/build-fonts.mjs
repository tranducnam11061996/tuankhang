import { existsSync, mkdirSync, statSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

const outputDirectory = resolve('assets/dist/fonts');
mkdirSync(outputDirectory, { recursive: true });

const fonts = [
  {
    family: 'Manrope',
    query: 'family=Manrope:wght@400..800',
    filename: 'manrope',
  },
  {
    family: 'Source Serif 4',
    query: 'family=Source+Serif+4:opsz,wght@8..60,600..700',
    filename: 'source-serif-4',
  },
];

const userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/126.0.0.0 Safari/537.36';

for (const font of fonts) {
  const stylesheetUrl = `https://fonts.googleapis.com/css2?${font.query}&display=swap`;
  const stylesheetResponse = await fetch(stylesheetUrl, { headers: { 'user-agent': userAgent } });
  if (!stylesheetResponse.ok) throw new Error(`Không tải được CSS cho ${font.family}: ${stylesheetResponse.status}`);
  const stylesheet = await stylesheetResponse.text();

  for (const subset of ['vietnamese', 'latin']) {
    const expression = new RegExp(`/\\*\\s*${subset}\\s*\\*/\\s*@font-face\\s*\\{([\\s\\S]*?)\\}`, 'i');
    const block = stylesheet.match(expression)?.[1] ?? '';
    const sourceUrl = block.match(/src:\s*url\(([^)]+)\)/i)?.[1]?.replace(/["']/g, '');
    if (!sourceUrl) throw new Error(`Không tìm thấy subset ${subset} cho ${font.family}.`);

    const destination = resolve(outputDirectory, `${font.filename}-${subset}.woff2`);
    if (existsSync(destination) && statSync(destination).size > 0) continue;
    const fontResponse = await fetch(sourceUrl);
    if (!fontResponse.ok) throw new Error(`Không tải được ${font.family} ${subset}: ${fontResponse.status}`);
    writeFileSync(destination, Buffer.from(await fontResponse.arrayBuffer()));
    console.log(`Đã tải ${font.family} ${subset}.`);
  }
}
