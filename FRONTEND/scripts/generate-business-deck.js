/* eslint-disable */
const fs = require('fs')
const path = require('path')
const PptxGenJS = require('pptxgenjs')
const sharp = require('sharp')

// Utilidades de assets
const projectRoot = process.cwd()
const tryPaths = (
  candidates
) => candidates.map((p) => path.join(projectRoot, p)).find((p) => fs.existsSync(p))

// Preferir icono SVG del usuario y convertir a PNG temporal si existe
const svgIconPath = tryPaths([
  'public/icons/tucan_n.svg',
  'public/icons/icono_b.svg'
])
const logoPngOut = path.join(projectRoot, 'public', 'icons', 'logo_tmp.png')
let logoPath = tryPaths([
  'src/assets/logo.png',
  'public/images/5.png',
  'public/images/10.png'
])
const coverPath = tryPaths([
  'public/images/viaja.jpg',
  'public/images/canaval.jpg',
  'public/images/41.png'
])
const hotelPics = [1, 2, 3].map((i) => tryPaths([`public/images/mas_destinos/${i}.png`])).filter(Boolean)
const foodPics = [4, 5, 6].map((i) => tryPaths([`public/images/mas_destinos/${i}.png`])).filter(Boolean)
const hospitalityPics = [7, 8, 9].map((i) => tryPaths([`public/images/mas_destinos/${i}.png`])).filter(Boolean)

// Paleta básica basada en UI
const COLORS = {
  primary: '234C8A',
  secondary: 'F4F6FA',
  dark: '203040',
  text: '333333',
  muted: '6B7280'
}

function addLogo(slide) {
  if (logoPath) {
    slide.addImage({ path: logoPath, x: 9.0, y: 0.3, w: 1.0, h: 1.0 })
  }
}

function addTitleSlide(ppt, title, subtitle) {
  const slide = ppt.addSlide()
  // Banda superior
  slide.addShape(ppt.ShapeType.rect, { x: 0, y: 0, w: 10, h: 0.9, fill: { color: COLORS.primary } })
  slide.addText(title, { x: 0.5, y: 1.0, w: 9, h: 1.0, fontSize: 36, bold: true, color: COLORS.dark })
  if (subtitle) {
    slide.addText(subtitle, { x: 0.5, y: 2.0, w: 9, h: 0.6, fontSize: 18, color: COLORS.muted })
  }
  // Imagen de portada opcional
  if (coverPath) {
    slide.addImage({ path: coverPath, x: 0.5, y: 3.0, w: 9.5, h: 3.5, rounding: 8 })
  }
  addLogo(slide)
  return slide
}

function addBulletsSlide(ppt, title, bullets) {
  const slide = ppt.addSlide()
  // Encabezado con banda ligera
  slide.addShape(ppt.ShapeType.rect, { x: 0, y: 0, w: 10, h: 0.4, fill: { color: COLORS.secondary } })
  slide.addText(title, { x: 0.5, y: 0.5, w: 9, h: 0.6, fontSize: 28, bold: true, color: COLORS.dark })
  const items = bullets.map((t) => ({ text: t, options: { bullet: true, fontSize: 18, color: COLORS.text } }))
  slide.addText(items, { x: 0.7, y: 1.2, w: 8.6, h: 5.0 })
  addLogo(slide)
  return slide
}

async function resizeIfNeeded(inputPath, outDir, maxWidth = 1280) {
  try {
    if (!inputPath) return null
    if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true })
    const outPath = path.join(outDir, path.basename(inputPath).replace(/\.(svg)$/i, '.png'))
    const meta = await sharp(inputPath).metadata()
    const width = meta.width || maxWidth
    const target = Math.min(width, maxWidth)
    await sharp(inputPath).resize({ width: target }).jpeg({ quality: 85 }).toFile(outPath)
    return outPath
  } catch (e) {
    return inputPath
  }
}

async function addSectionWithImageGrid(ppt, title, caption, imagePaths) {
  const slide = ppt.addSlide()
  slide.addShape(ppt.ShapeType.rect, { x: 0, y: 0, w: 10, h: 0.4, fill: { color: COLORS.secondary } })
  slide.addText(title, { x: 0.5, y: 0.5, w: 9, h: 0.6, fontSize: 28, bold: true, color: COLORS.dark })
  if (caption) {
    slide.addText(caption, { x: 0.5, y: 1.1, w: 9, h: 0.5, fontSize: 16, color: COLORS.muted })
  }
  const images = imagePaths.filter(Boolean)
  const cols = Math.min(images.length, 3)
  const w = cols > 0 ? (9.0 - (cols - 1) * 0.3) / cols : 2.8
  const h = 2.2
  const yStart = 1.8
  images.slice(0, 6).forEach((p, idx) => {
    const row = Math.floor(idx / cols)
    const col = idx % cols
    const x = 0.5 + col * (w + 0.3)
    const y = yStart + row * (h + 0.3)
    slide.addImage({ path: p, x, y, w, h, rounding: 8 })
  })
  addLogo(slide)
  return slide
}

function ensureDir(dir) {
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true })
}

async function generate() {
  // Convertir SVG a PNG si es necesario
  if (svgIconPath) {
    try {
      await sharp(svgIconPath).png({ quality: 90 }).toFile(logoPngOut)
      logoPath = logoPngOut
    } catch (e) {
      console.warn('No se pudo convertir el SVG a PNG, se usará fallback si existe.', e?.message)
    }
  }

  const ppt = new PptxGenJS()
  ppt.layout = 'LAYOUT_16x9'
  ppt.author = 'Conoce Bolivia'
  ppt.company = 'Conoce Bolivia'
  ppt.title = 'Perfiles de Negocio para Atraer Turistas'

  // 1 Portada
  addTitleSlide(ppt, 'Conoce Bolivia – Perfiles de Negocio', 'Más reservas. Más visibilidad. Más ventas.')

  // 2 Problema
  addBulletsSlide(ppt, 'El problema', [
    'Muchos negocios no tienen web propia o no la posicionan',
    'Turistas buscan info todo en uno: fotos, precios, contacto',
    'Oportunidad: centralizar demanda y facilitar la reserva'
  ])

  // 3 Solución
  addBulletsSlide(ppt, 'La solución', [
    'Perfiles profesionales autogestionados',
    'Visibilidad por categorías: alojamiento, gastronomía, experiencias',
    'CTAs directos: WhatsApp, Llamar, Reservar'
  ])

  // 4 Cómo se ve su perfil
  addBulletsSlide(ppt, 'Cómo se ve su perfil', [
    'Hero con fotos + logo y descripción corta',
    'Servicios/Amenities y políticas',
    'Mapa + Cómo llegar; opiniones y destacados'
  ])

  // 5 Para Hoteles
  const slideHotels = addBulletsSlide(ppt, 'Para Hoteles', [
    'Habitaciones y tarifas con "Ver más"',
    'Disponibilidad opcional y políticas de cancelación',
    'Botones Reservar/WhatsApp con tracking'
  ])
  if (hotelPics.length > 0) {
    const baseX = 0.6
    const y = 4.8
    const w = 3.0
    const h = 2.0
    hotelPics.slice(0, 3).forEach((p, idx) => {
      slideHotels.addImage({ path: p, x: baseX + idx * (w + 0.3), y, w, h, rounding: 6 })
    })
  }

  // 6 Para Restaurantes
  const slideFood = addBulletsSlide(ppt, 'Para Restaurantes', [
    'Menú destacado: foto, precio, descripción',
    'Etiquetas: vegano, sin gluten, terraza',
    'Reservar mesa por WhatsApp o teléfono'
  ])
  if (foodPics.length > 0) {
    const baseX = 0.6
    const y = 4.8
    const w = 3.0
    const h = 2.0
    foodPics.slice(0, 3).forEach((p, idx) => {
      slideFood.addImage({ path: p, x: baseX + idx * (w + 0.3), y, w, h, rounding: 6 })
    })
  }

  // Slides de galerías por categoría
  const tmpDir = path.join(projectRoot, 'public', 'images', 'tmp')
  const hotelGal = []
  for (const p of [coverPath, ...hotelPics, tryPaths(['public/images/10.png']), tryPaths(['public/images/12.png'])].filter(Boolean)) {
    hotelGal.push(await resizeIfNeeded(p, tmpDir))
  }
  await addSectionWithImageGrid(ppt, 'Galería: Hoteles y Hospedaje', 'Selección de alojamientos destacados', hotelGal.filter(Boolean))

  const foodGal = []
  for (const p of [tryPaths(['public/images/13.png']), ...foodPics, tryPaths(['public/images/5.png'])].filter(Boolean)) {
    foodGal.push(await resizeIfNeeded(p, tmpDir))
  }
  await addSectionWithImageGrid(ppt, 'Galería: Restaurantes y Gastronomía', 'Platos y espacios gastronómicos', foodGal.filter(Boolean))

  const hospGal = []
  for (const p of [...hospitalityPics, tryPaths(['public/images/41.png']), tryPaths(['public/images/huatajata.jpg'])].filter(Boolean)) {
    hospGal.push(await resizeIfNeeded(p, tmpDir))
  }
  await addSectionWithImageGrid(ppt, 'Galería: Lugares de hospedaje', 'Opciones variadas para distintos viajeros', hospGal.filter(Boolean))

  // 7 Panel del empresario
  addBulletsSlide(ppt, 'Panel del empresario', [
    'Subir fotos, editar textos, horarios y servicios',
    'Estadísticas: vistas, clics, conversión',
    'Plan y facturación desde el panel'
  ])

  // 8 Planes y Precios
  addBulletsSlide(ppt, 'Planes y Precios (ejemplo)', [
    'Básico: Perfil + 5 fotos + CTAs — $X/mes',
    'Pro: + Destacado, + Reseñas, +10 fotos — $Y/mes',
    'Premium: + Top búsqueda, + Campaña, +20 fotos — $Z/mes'
  ])

  // 9 Beneficios
  addBulletsSlide(ppt, 'Beneficios', [
    'Más visibilidad y reservas directas',
    'Sin necesidad de web propia',
    'Control del contenido y métricas'
  ])

  // 10 Casos de uso
  addBulletsSlide(ppt, 'Casos de uso', [
    'Hotel boutique: fotos/amenities',
    'Restaurante típico: menú/cultura',
    'Tour operador: experiencias/itinerarios'
  ])

  // 11 Estadísticas
  addBulletsSlide(ppt, 'Ejemplo de estadísticas', [
    'Tráfico mensual y clics a WhatsApp/Reserva',
    'Top fotos y páginas origen',
    'Tasa de conversión por canal'
  ])

  // 12 SEO y alcance
  addBulletsSlide(ppt, 'SEO y alcance', [
    'Rich results: Hotel/Restaurant/AggregateRating',
    'Índices por ciudad/categoría con destacados',
    'Campañas en redes y OpenGraph'
  ])

  // 13 Moderación y calidad
  addBulletsSlide(ppt, 'Moderación y calidad', [
    'Guía de imágenes y tamaños mínimos',
    'Chequeos de texto y banderas',
    'Revisión de contenido inapropiado'
  ])

  // 14 Roadmap
  addBulletsSlide(ppt, 'Roadmap', [
    'Fase 1: Perfiles y pagos',
    'Fase 2: Reseñas y destacados',
    'Fase 3: Reservas/Disponibilidad (opcional)'
  ])

  // 15 Modelo de ingresos
  addBulletsSlide(ppt, 'Modelo de ingresos', [
    'Suscripción mensual: Básico/Pro/Premium',
    'Add-ons y campañas',
    'Comisión por lead (opcional)'
  ])

  // 16 Inversión y retorno
  addBulletsSlide(ppt, 'Inversión y retorno', [
    'ROI estimado: X clics/mes con conversión Y%',
    'Escenarios: conservador, base, agresivo',
    'Punto de equilibrio y payback'
  ])

  // 17 Llamado a la acción
  addBulletsSlide(ppt, 'Llamado a la acción', [
    'Crea tu perfil hoy',
    'QR/URL y contacto comercial',
    'Soporte para onboarding'
  ])

  ensureDir(path.join(process.cwd(), 'public'))
  const outPath = path.join(process.cwd(), 'public', 'bolivia-negocios-deck.pptx')
  await ppt.writeFile({ fileName: outPath })
  console.log(`Deck generado: ${outPath}`)
}

generate().catch((err) => {
  console.error('Error generando deck:', err)
  process.exit(1)
})


