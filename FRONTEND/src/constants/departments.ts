// Departamentos de Bolivia
export interface Department {
  id: number;
  name: string;
  shortName: string;
  slug: string;
  icon: string;
  color: string;
  description: string;
  capital: string;
  population: string;
  area: string;
  coordinates: [number, number]; // [lat, lng]
  highlights: string[];
  climate: string;
  altitude: string;
}

export const departments: Department[] = [
  {
    id: 1,
    name: 'La Paz',
    shortName: 'LP',
    slug: 'la-paz',
    icon: 'fas fa-mountain',
    color: '#1e40af',
    description: 'Departamento montañoso con el lago Titicaca, la ciudad de La Paz y el Salar de Uyuni',
    capital: 'La Paz',
    population: '2.7 millones',
    area: '133,985 km²',
    coordinates: [-16.5, -68.15],
    highlights: [
      'Lago Titicaca',
      'Ciudad de La Paz',
      'Salar de Uyuni',
      'Tiwanaku',
      'Copacabana',
      'Cordillera Real'
    ],
    climate: 'Frío y seco en el altiplano, templado en los valles',
    altitude: '1,800 - 6,542 msnm'
  },
  {
    id: 2,
    name: 'Cochabamba',
    shortName: 'CB',
    slug: 'cochabamba',
    icon: 'fas fa-seedling',
    color: '#059669',
    description: 'Valle fértil conocido como el granero de Bolivia, con clima templado y gastronomía única',
    capital: 'Cochabamba',
    population: '1.9 millones',
    area: '55,631 km²',
    coordinates: [-17.3895, -66.1568],
    highlights: [
      'Cristo de la Concordia',
      'Valle de Cochabamba',
      'Parque Nacional Tunari',
      'Gastronomía local',
      'Fiesta de la Virgen de Urkupiña',
      'Parque de la Familia'
    ],
    climate: 'Templado mediterráneo',
    altitude: '2,558 msnm'
  },
  {
    id: 3,
    name: 'Santa Cruz',
    shortName: 'SC',
    slug: 'santa-cruz',
    icon: 'fas fa-leaf',
    color: '#16A34A',
    description: 'Departamento tropical con selva amazónica, sabanas y la ciudad más grande de Bolivia',
    capital: 'Santa Cruz de la Sierra',
    population: '3.1 millones',
    area: '370,621 km²',
    coordinates: [-17.7863, -63.1812],
    highlights: [
      'Parque Nacional Amboró',
      'Samaipata',
      'San Javier',
      'Concepción',
      'Lomas de Arena',
      'Jardín Botánico'
    ],
    climate: 'Tropical húmedo',
    altitude: '400 - 1,500 msnm'
  },
  {
    id: 4,
    name: 'Oruro',
    shortName: 'OR',
    slug: 'oruro',
    icon: 'fas fa-gem',
    color: '#7C3AED',
    description: 'Tierra de mineros y el famoso Carnaval de Oruro, declarado Patrimonio de la Humanidad',
    capital: 'Oruro',
    population: '494,000',
    area: '53,588 km²',
    coordinates: [-17.9754, -67.1105],
    highlights: [
      'Carnaval de Oruro',
      'Mina San José',
      'Lago Poopó',
      'Sajama',
      'Termas de Capachos',
      'Museo Mineralógico'
    ],
    climate: 'Frío y seco',
    altitude: '3,700 - 4,000 msnm'
  },
  {
    id: 5,
    name: 'Potosí',
    shortName: 'PT',
    slug: 'potosi',
    icon: 'fas fa-mountain',
    color: '#DC2626',
    description: 'Ciudad histórica de la plata, con el Cerro Rico y arquitectura colonial excepcional',
    capital: 'Potosí',
    population: '823,000',
    area: '118,218 km²',
    coordinates: [-19.5833, -65.75],
    highlights: [
      'Cerro Rico',
      'Casa de la Moneda',
      'Iglesias coloniales',
      'Salar de Uyuni',
      'Laguna Colorada',
      'Termas de Ojo del Inca'
    ],
    climate: 'Frío y seco',
    altitude: '4,090 msnm'
  },
  {
    id: 6,
    name: 'Tarija',
    shortName: 'TJ',
    slug: 'tarija',
    icon: 'fas fa-wine-bottle',
    color: '#D97706',
    description: 'Tierra del vino y el singani, con clima mediterráneo y paisajes de viñedos',
    capital: 'Tarija',
    population: '482,000',
    area: '37,623 km²',
    coordinates: [-21.5355, -64.7296],
    highlights: [
      'Valle de Tarija',
      'Bodegas de vino',
      'San Lorenzo',
      'Reserva Biológica Cordillera de Sama',
      'Fiesta de San Roque',
      'Gastronomía local'
    ],
    climate: 'Mediterráneo templado',
    altitude: '1,854 msnm'
  },
  {
    id: 7,
    name: 'Beni',
    shortName: 'BN',
    slug: 'beni',
    icon: 'fas fa-water',
    color: '#0891B2',
    description: 'Departamento amazónico con ríos navegables, sabanas y biodiversidad excepcional',
    capital: 'Trinidad',
    population: '422,000',
    area: '213,564 km²',
    coordinates: [-14.8333, -64.9],
    highlights: [
      'Río Mamoré',
      'Llanos de Moxos',
      'Reserva de la Biosfera Estación Biológica del Beni',
      'Pampas del Yacuma',
      'Rurrenabaque',
      'Fauna silvestre'
    ],
    climate: 'Tropical húmedo',
    altitude: '150 - 500 msnm'
  },
  {
    id: 8,
    name: 'Pando',
    shortName: 'PD',
    slug: 'pando',
    icon: 'fas fa-tree',
    color: '#059669',
    description: 'Selva amazónica virgen con ríos, lagunas y la mayor biodiversidad de Bolivia',
    capital: 'Cobija',
    population: '110,000',
    area: '63,827 km²',
    coordinates: [-11.0267, -68.7692],
    highlights: [
      'Selva amazónica',
      'Río Madre de Dios',
      'Laguna Bay',
      'Reserva Nacional Amazónica Manuripi',
      'Biodiversidad',
      'Ecoturismo'
    ],
    climate: 'Tropical húmedo',
    altitude: '150 - 300 msnm'
  }
];

// Función para obtener departamento por ID
export function getDepartmentById(id: number): Department | undefined {
  return departments.find(dept => dept.id === id);
}

// Función para obtener departamento por slug
export function getDepartmentBySlug(slug: string): Department | undefined {
  return departments.find(dept => dept.slug === slug);
}

// Función para obtener departamento por nombre
export function getDepartmentByName(name: string): Department | undefined {
  return departments.find(dept => 
    dept.name.toLowerCase() === name.toLowerCase() ||
    dept.shortName.toLowerCase() === name.toLowerCase()
  );
}

// Función para obtener todos los departamentos
export function getAllDepartments(): Department[] {
  return departments;
}

// Función para obtener departamentos por región climática
export function getDepartmentsByClimate(climate: string): Department[] {
  return departments.filter(dept => 
    dept.climate.toLowerCase().includes(climate.toLowerCase())
  );
}

// Función para obtener departamentos por rango de altitud
export function getDepartmentsByAltitudeRange(min: number, max: number): Department[] {
  return departments.filter(dept => {
    const altRange = dept.altitude.match(/(\d+)/g);
    if (altRange && altRange.length >= 2) {
      const minAlt = parseInt(altRange[0]);
      const maxAlt = parseInt(altRange[1]);
      return minAlt >= min && maxAlt <= max;
    }
    return false;
  });
}

// Colores para los departamentos
export const departmentColors = {
  'la-paz': '#1e40af',
  'cochabamba': '#059669',
  'santa-cruz': '#16A34A',
  'oruro': '#7C3AED',
  'potosi': '#DC2626',
  'tarija': '#D97706',
  'beni': '#0891B2',
  'pando': '#059669'
};

// Iconos para los departamentos
export const departmentIcons = {
  'la-paz': 'fas fa-mountain',
  'cochabamba': 'fas fa-seedling',
  'santa-cruz': 'fas fa-leaf',
  'oruro': 'fas fa-gem',
  'potosi': 'fas fa-mountain',
  'tarija': 'fas fa-wine-bottle',
  'beni': 'fas fa-water',
  'pando': 'fas fa-tree'
};
