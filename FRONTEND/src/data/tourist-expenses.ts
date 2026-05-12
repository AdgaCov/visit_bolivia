export interface TouristExpenseCategory {
  accommodation: number // Alojamiento
  goods: {
    total: number // Compra de Bienes
    crafts: number // Artesanías
    clothing: number // Vestimenta
    others: number // Otros
  }
  services: {
    total: number // Gasto en Servicios
    food: number // Alimentos y Bebidas
    transport: number // Transporte Interno
    entertainment: number // Esparcimiento
    others: number // Otros
  }
  total: number // TOTAL
}

export interface TouristExpenseYear {
  year: number
  provisional?: boolean
  expenses: TouristExpenseCategory
}

export const touristExpenseYears: TouristExpenseYear[] = [
  {
    year: 2008,
    expenses: {
      total: 347.39,
      accommodation: 74.34,
      goods: {
        total: 75.73,
        crafts: 21.54,
        clothing: 30.92,
        others: 23.28
      },
      services: {
        total: 197.32,
        food: 86.85,
        transport: 43.08,
        entertainment: 37.52,
        others: 29.88
      }
    }
  },
  {
    year: 2009,
    expenses: {
      total: 396.34,
      accommodation: 84.87,
      goods: {
        total: 86.95,
        crafts: 24.57,
        clothing: 37.85,
        others: 24.54
      },
      services: {
        total: 224.52,
        food: 103.09,
        transport: 49.55,
        entertainment: 40.81,
        others: 31.09
      }
    }
  },
  {
    year: 2010,
    expenses: {
      total: 425.41,
      accommodation: 91.04,
      goods: {
        total: 92.74,
        crafts: 26.38,
        clothing: 37.86,
        others: 28.50
      },
      services: {
        total: 241.63,
        food: 106.35,
        transport: 52.75,
        entertainment: 45.94,
        others: 36.58
      }
    }
  },
  {
    year: 2011,
    expenses: {
      total: 502.54,
      accommodation: 107.59,
      goods: {
        total: 109.80,
        crafts: 30.46,
        clothing: 47.93,
        others: 31.42
      },
      services: {
        total: 285.14,
        food: 126.64,
        transport: 64.32,
        entertainment: 53.17,
        others: 41.02
      }
    }
  },
  {
    year: 2012,
    expenses: {
      total: 527.05,
      accommodation: 112.86,
      goods: {
        total: 115.47,
        crafts: 32.48,
        clothing: 47.91,
        others: 35.09
      },
      services: {
        total: 298.71,
        food: 131.74,
        transport: 65.95,
        entertainment: 56.90,
        others: 44.12
      }
    }
  },
  {
    year: 2013,
    expenses: {
      total: 556.95,
      accommodation: 119.19,
      goods: {
        total: 121.42,
        crafts: 35.53,
        clothing: 51.57,
        others: 34.32
      },
      services: {
        total: 316.35,
        food: 139.24,
        transport: 69.06,
        entertainment: 60.15,
        others: 47.90
      }
    }
  },
  {
    year: 2014,
    expenses: {
      total: 655.42,
      accommodation: 100.28,
      goods: {
        total: 140.26,
        crafts: 53.09,
        clothing: 62.92,
        others: 24.25
      },
      services: {
        total: 414.88,
        food: 155.34,
        transport: 109.46,
        entertainment: 81.27,
        others: 68.82
      }
    }
  },
  {
    year: 2015,
    expenses: {
      total: 692.50,
      accommodation: 105.95,
      goods: {
        total: 148.19,
        crafts: 56.13,
        clothing: 66.44,
        others: 25.62
      },
      services: {
        total: 438.35,
        food: 164.11,
        transport: 115.66,
        entertainment: 109.42,
        others: 49.17
      }
    }
  },
  {
    year: 2016,
    expenses: {
      total: 738.54,
      accommodation: 113.00,
      goods: {
        total: 158.05,
        crafts: 59.88,
        clothing: 70.85,
        others: 27.33
      },
      services: {
        total: 467.50,
        food: 175.04,
        transport: 123.39,
        entertainment: 116.69,
        others: 52.38
      }
    }
  },
  {
    year: 2017,
    expenses: {
      total: 802.57,
      accommodation: 122.79,
      goods: {
        total: 171.75,
        crafts: 65.01,
        clothing: 77.05,
        others: 29.69
      },
      services: {
        total: 508.02,
        food: 190.21,
        transport: 134.03,
        entertainment: 126.81,
        others: 56.98
      }
    }
  },
  {
    year: 2018,
    expenses: {
      total: 816.26,
      accommodation: 128.56,
      goods: {
        total: 173.04,
        crafts: 70.22,
        clothing: 83.25,
        others: 19.58
      },
      services: {
        total: 514.65,
        food: 205.97,
        transport: 149.80,
        entertainment: 115.84,
        others: 43.04
      }
    }
  },
  {
    year: 2019,
    expenses: {
      total: 837.29,
      accommodation: 135.63,
      goods: {
        total: 175.82,
        crafts: 76.19,
        clothing: 90.42,
        others: 9.21
      },
      services: {
        total: 525.84,
        food: 223.72,
        transport: 167.46,
        entertainment: 105.45,
        others: 29.21
      }
    }
  },
  {
    year: 2020,
    expenses: {
      total: 188.61,
      accommodation: 23.01,
      goods: {
        total: 54.60,
        crafts: 21.03,
        clothing: 20.56,
        others: 13.01
      },
      services: {
        total: 111.00,
        food: 42.92,
        transport: 37.72,
        entertainment: 16.21,
        others: 14.15
      }
    }
  },
  {
    year: 2021,
    expenses: {
      total: 189.74,
      accommodation: 23.72,
      goods: {
        total: 55.22,
        crafts: 21.44,
        clothing: 20.87,
        others: 12.90
      },
      services: {
        total: 110.81,
        food: 43.64,
        transport: 37.76,
        entertainment: 16.13,
        others: 13.28
      }
    }
  },
  {
    year: 2022,
    expenses: {
      total: 498.75,
      accommodation: 79.30,
      goods: {
        total: 105.57,
        crafts: 46.04,
        clothing: 53.86,
        others: 5.66
      },
      services: {
        total: 313.88,
        food: 138.85,
        transport: 94.76,
        entertainment: 62.81,
        others: 17.46
      }
    }
  },
  {
    year: 2023,
    provisional: true,
    expenses: {
      total: 687.87,
      accommodation: 116.94,
      goods: {
        total: 162.85,
        crafts: 61.91,
        clothing: 73.40,
        others: 27.54
      },
      services: {
        total: 408.08,
        food: 185.72,
        transport: 110.06,
        entertainment: 71.03,
        others: 41.27
      }
    }
  },
  {
    year: 2024,
    provisional: true,
    expenses: {
      total: 739.94,
      accommodation: 150.79,
      goods: {
        total: 159.09,
        crafts: 66.60,
        clothing: 59.94,
        others: 32.56
      },
      services: {
        total: 430.07,
        food: 196.09,
        transport: 113.37,
        entertainment: 81.75,
        others: 38.86
      }
    }
  }
]

export default touristExpenseYears

