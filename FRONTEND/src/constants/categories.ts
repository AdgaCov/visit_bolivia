// Categorías principales para lugares turísticos
export interface Category {
  id: number;
  name: string;
  icon: string;
  color: string;
  description: string;
  image:string,
  subcategories: Subcategory[];
}

export interface Subcategory {
  id: number;
  categoryId: number;
  name: string;
  icon: string;
  description: string;
}

// Categorías principales
export const categories: Category[] = [
  {
    id: 1,
    name: 'Cultura',
    icon: 'fas fa-theater-masks',
    color: '#8B5CF6',
    description: 'Sitios históricos, museos, tradiciones y expresiones culturales',
    image:'https://unifranz.edu.bo/wp-content/uploads/2023/02/IMG_20220507_141504-ph.-Sole-Patty-1.jpg',
    subcategories: [
      {
        id: 1,
        categoryId: 1,
        name: 'Museos',
        icon: 'fas fa-landmark',
        description: 'Museos arqueológicos, históricos y culturales'
      },
      {
        id: 2,
        categoryId: 1,
        name: 'Iglesias',
        icon: 'fas fa-church',
        description: 'Templos coloniales y arquitectura religiosa'
      },
      {
        id: 3,
        categoryId: 1,
        name: 'Festividades',
        icon: 'fas fa-calendar-alt',
        description: 'Carnavales, fiestas patronales y celebraciones'
      },
      {
        id: 4,
        categoryId: 1,
        name: 'Artesanías',
        icon: 'fas fa-hands',
        description: 'Talleres y centros de artesanía tradicional'
      }
    ]
  },
  {
    id: 2,
    name: 'Historia',
    icon: 'fas fa-landmark',
    color: '#DC2626',
    description: 'Sitios arqueológicos, ruinas y lugares de importancia histórica',
    image:'https://alba-bo-bolivision.cdn.mediatiquepress.com/wp-content/uploads/2025/08/Los-10-hitos-economicos-que-marcaron-la-historia-de-Bolivia.jpg',
    subcategories: [
      {
        id: 5,
        categoryId: 2,
        name: 'Ruinas',
        icon: 'fas fa-mountain',
        description: 'Sitios arqueológicos y ruinas precolombinas'
      },
      {
        id: 6,
        categoryId: 2,
        name: 'Misiones',
        icon: 'fas fa-cross',
        description: 'Misiones jesuíticas y asentamientos coloniales'
      },
      {
        id: 7,
        categoryId: 2,
        name: 'Minería',
        icon: 'fas fa-gem',
        description: 'Sitios históricos de minería y explotación'
      }
    ]
  },
  {
    id: 3,
    name: 'Aventura',
    icon: 'fas fa-hiking',
    color: '#059669',
    description: 'Actividades de aventura y deportes extremos',
    image:'https://vision360-s3.cdn.net.ar/s3i233/2024/10/vision360/images/01/15/54/1155453_acd030a108b451a46eb0b6dd2d16e14390ed62ae831b0fede37d3fb3be91c531/md.webp',
    subcategories: [
      {
        id: 8,
        categoryId: 3,
        name: 'Trekking',
        icon: 'fas fa-mountain',
        description: 'Senderos y rutas de montaña'
      },
      {
        id: 9,
        categoryId: 3,
        name: 'Rafting',
        icon: 'fas fa-water',
        description: 'Deportes acuáticos y rafting'
      },
      {
        id: 10,
        categoryId: 3,
        name: 'Escalada',
        icon: 'fas fa-mountain',
        description: 'Escalada en roca y montaña'
      },
      {
        id: 11,
        categoryId: 3,
        name: 'Ciclismo',
        icon: 'fas fa-bicycle',
        description: 'Rutas de ciclismo de montaña'
      }
    ]
  },
  {
    id: 4,
    name: 'Naturaleza',
    icon: 'fas fa-tree',
    color: '#16A34A',
    description: 'Parques nacionales, reservas y ecosistemas naturales',
    image:'https://riquezasdebolivia.com/wp-content/uploads/2020/10/Captura-de-Pantalla-2020-10-29-a-las-19.20.34.jpg',
    subcategories: [
      {
        id: 12,
        categoryId: 4,
        name: 'Parques Nacionales',
        icon: 'fas fa-tree',
        description: 'Áreas protegidas y parques nacionales'
      },
      {
        id: 13,
        categoryId: 4,
        name: 'Lagunas',
        icon: 'fas fa-water',
        description: 'Lagos, lagunas y cuerpos de agua'
      },
      {
        id: 14,
        categoryId: 4,
        name: 'Cataratas',
        icon: 'fas fa-water',
        description: 'Cascadas y saltos de agua'
      },
      {
        id: 15,
        categoryId: 4,
        name: 'Fauna Silvestre',
        icon: 'fas fa-paw',
        description: 'Observación de animales y vida silvestre'
      }
    ]
  },
  {
    id: 5,
    name: 'Gastronomía',
    icon: 'fas fa-utensils',
    color: '#D97706',
    description: 'Restaurantes, platos típicos y experiencias culinarias',
    image:'https://www.hotellavillachiquitana.com/wp-content/uploads/2022/09/restaurantes-en-santa-cruz-1.jpg',
    subcategories: [
      {
        id: 16,
        categoryId: 5,
        name: 'Restaurantes',
        icon: 'fas fa-utensils',
        description: 'Restaurantes tradicionales y gourmet'
      },
      {
        id: 17,
        categoryId: 5,
        name: 'Mercados',
        icon: 'fas fa-shopping-basket',
        description: 'Mercados tradicionales y gastronómicos'
      },
      {
        id: 18,
        categoryId: 5,
        name: 'Vinos',
        icon: 'fas fa-wine-bottle',
        description: 'Bodegas y degustaciones de vino'
      }
    ]
  },
  {
    id: 6,
    name: 'Relax',
    icon: 'fas fa-spa',
    color: '#0891B2',
    description: 'Spas, termas y lugares de relajación',
    image:'https://www.hotellavillachiquitana.com/wp-content/uploads/2021/07/blog_chiquitano_aguas_clientes.jpg',
    subcategories: [
      {
        id: 19,
        categoryId: 6,
        name: 'Termas',
        icon: 'fas fa-hot-tub',
        description: 'Aguas termales y spas naturales'
      },
      {
        id: 20,
        categoryId: 6,
        name: 'Retiros',
        icon: 'fas fa-om',
        description: 'Centros de retiro y meditación'
      }
    ]
  }
];

// Función para obtener categoría por ID
export function getCategoryById(id: number): Category | undefined {
  return categories.find(cat => cat.id === id);
}

// Función para obtener subcategoría por ID
export function getSubcategoryById(id: number): Subcategory | undefined {
  for (const category of categories) {
    const subcategory = category.subcategories.find(sub => sub.id === id);
    if (subcategory) return subcategory;
  }
  return undefined;
}

// Función para obtener todas las subcategorías de una categoría
export function getSubcategoriesByCategoryId(categoryId: number): Subcategory[] {
  const category = getCategoryById(categoryId);
  return category ? category.subcategories : [];
}

// Función para obtener categoría por nombre
export function getCategoryByName(name: string): Category | undefined {
  return categories.find(cat => cat.name.toLowerCase() === name.toLowerCase());
}

// Función para obtener subcategoría por nombre
export function getSubcategoryByName(name: string): Subcategory | undefined {
  for (const category of categories) {
    const subcategory = category.subcategories.find(sub => 
      sub.name.toLowerCase() === name.toLowerCase()
    );
    if (subcategory) return subcategory;
  }
  return undefined;
}

// Colores para las categorías
export const categoryColors = {
  cultura: '#8B5CF6',
  historia: '#DC2626',
  aventura: '#059669',
  naturaleza: '#16A34A',
  gastronomia: '#D97706',
  relax: '#0891B2'
};

// Iconos para las categorías
export const categoryIcons = {
  cultura: 'fas fa-theater-masks',
  historia: 'fas fa-landmark',
  aventura: 'fas fa-hiking',
  naturaleza: 'fas fa-tree',
  gastronomia: 'fas fa-utensils',
  relax: 'fas fa-spa'
};

