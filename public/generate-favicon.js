const { createCanvas } = require('canvas');
const fs = require('fs');

// Fonction pour créer un favicon avec un P sur fond violet
function createFavicon(size) {
  const canvas = createCanvas(size, size);
  const ctx = canvas.getContext('2d');
  
  // Fond violet
  ctx.fillStyle = '#8B5CF6';
  ctx.beginPath();
  ctx.arc(size/2, size/2, size/2 - 1, 0, Math.PI * 2);
  ctx.fill();
  
  // Lettre P en blanc
  ctx.fillStyle = '#FFFFFF';
  ctx.font = `bold ${size * 0.6}px Arial`;
  ctx.textAlign = 'center';
  ctx.textBaseline = 'middle';
  ctx.fillText('P', size/2, size/2 + size*0.05);
  
  return canvas;
}

// Générer le favicon.ico (32x32)
const canvas32 = createFavicon(32);
const buffer32 = canvas32.toBuffer('image/png');
fs.writeFileSync('public/favicon-32x32.png', buffer32);
console.log('Favicon 32x32 généré');

// Générer l'icône Apple Touch (180x180)
const canvas180 = createFavicon(180);
const buffer180 = canvas180.toBuffer('image/png');
fs.writeFileSync('public/apple-touch-icon.png', buffer180);
console.log('Apple Touch Icon généré');

console.log('Tous les favicons ont été générés avec succès!');
