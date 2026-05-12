export interface TravelerModalityTotals {
  national: number
  foreign: number
  total: number
}

export interface TravelerYearSummary {
  year: number
  provisional?: boolean
  air: TravelerModalityTotals
  road: TravelerModalityTotals
  overall: TravelerModalityTotals
}

export const travelerYearSummaries: TravelerYearSummary[] = [
  {
    year: 2008,
    air: { national: 159382, foreign: 223420, total: 382802 },
    road: { national: 427093, foreign: 540508, total: 967601 },
    overall: { national: 586475, foreign: 763928, total: 1350403 }
  },
  {
    year: 2009,
    air: { national: 192423, foreign: 247085, total: 439508 },
    road: { national: 457246, foreign: 604029, total: 1061275 },
    overall: { national: 649669, foreign: 851114, total: 1500783 }
  },
  {
    year: 2010,
    air: { national: 232606, foreign: 269596, total: 502202 },
    road: { national: 490465, foreign: 723012, total: 1213477 },
    overall: { national: 723071, foreign: 992608, total: 1715679 }
  },
  {
    year: 2011,
    air: { national: 277050, foreign: 312850, total: 589900 },
    road: { national: 552575, foreign: 652796, total: 1205371 },
    overall: { national: 829625, foreign: 965646, total: 1795271 }
  },
  {
    year: 2012,
    air: { national: 259941, foreign: 319126, total: 579067 },
    road: { national: 623384, foreign: 795341, total: 1418725 },
    overall: { national: 883325, foreign: 1114467, total: 1997792 }
  },
  {
    year: 2013,
    air: { national: 252053, foreign: 361523, total: 613576 },
    road: { national: 748865, foreign: 728735, total: 1477600 },
    overall: { national: 1000918, foreign: 1090258, total: 2091176 }
  },
  {
    year: 2014,
    air: { national: 241569, foreign: 416899, total: 658468 },
    road: { national: 760950, foreign: 763551, total: 1524501 },
    overall: { national: 1002519, foreign: 1180450, total: 2182969 }
  },
  {
    year: 2015,
    air: { national: 297572, foreign: 410277, total: 707849 },
    road: { national: 816754, foreign: 721164, total: 1537918 },
    overall: { national: 1114326, foreign: 1131441, total: 2245767 }
  },
  {
    year: 2016,
    air: { national: 342950, foreign: 398807, total: 741757 },
    road: { national: 916949, foreign: 778648, total: 1695597 },
    overall: { national: 1259899, foreign: 1177455, total: 2437354 }
  },
  {
    year: 2017,
    air: { national: 367768, foreign: 418591, total: 786359 },
    road: { national: 905691, foreign: 979359, total: 1885050 },
    overall: { national: 1273459, foreign: 1397950, total: 2671409 }
  },
  {
    year: 2018,
    air: { national: 382018, foreign: 415941, total: 797959 },
    road: { national: 1026294, foreign: 1032435, total: 2058729 },
    overall: { national: 1408312, foreign: 1448376, total: 2856688 }
  },
  {
    year: 2019,
    air: { national: 407463, foreign: 390938, total: 798401 },
    road: { national: 1202478, foreign: 1084964, total: 2287442 },
    overall: { national: 1609941, foreign: 1475902, total: 3085843 }
  },
  {
    year: 2020,
    air: { national: 148426, foreign: 110546, total: 258972 },
    road: { national: 497630, foreign: 266434, total: 764064 },
    overall: { national: 646056, foreign: 376980, total: 1023036 }
  },
  {
    year: 2021,
    air: { national: 207954, foreign: 130334, total: 338288 },
    road: { national: 287092, foreign: 87024, total: 374116 },
    overall: { national: 495046, foreign: 217358, total: 712404 }
  },
  {
    year: 2022,
    air: { national: 339817, foreign: 269200, total: 609017 },
    road: { national: 767469, foreign: 626309, total: 1393778 },
    overall: { national: 1107286, foreign: 895509, total: 2002795 }
  },
  {
    year: 2023,
    provisional: true,
    air: { national: 385018, foreign: 344319, total: 729337 },
    road: { national: 1289405, foreign: 1132720, total: 2422125 },
    overall: { national: 1674423, foreign: 1477039, total: 3151462 }
  },
  {
    year: 2024,
    provisional: true,
    air: { national: 411620, foreign: 391850, total: 803470 },
    road: { national: 1468507, foreign: 1018122, total: 2486629 },
    overall: { national: 1880127, foreign: 1409972, total: 3290099 }
  },
  {
    year: 2025,
    provisional: true,
    air: { national: 210776, foreign: 224857, total: 435633 },
    road: { national: 984511, foreign: 694700, total: 1679211 },
    overall: { national: 1195287, foreign: 919557, total: 2114844 }
  }
]

export default travelerYearSummaries


