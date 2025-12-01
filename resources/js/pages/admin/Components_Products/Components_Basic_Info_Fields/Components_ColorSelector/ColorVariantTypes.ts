// Components_ColorSelector/types.ts

export interface ColorVariant {
  id?: number
  name: string
  code: string
  stock: number
  is_default: boolean
  image: File | string | null
  imagePreview?: string
  gallery: (File | string)[]
}

export interface AvailableColor {
  name: string
  hex: string
}

export const AVAILABLE_COLORS: readonly AvailableColor[] = [
  // Básicos
  { name: 'Rojo',            hex: '#EF4444' },
  { name: 'Naranja',         hex: '#F97316' },
  { name: 'Amarillo',        hex: '#EAB308' },
  { name: 'Verde',           hex: '#22C55E' },
  { name: 'Azul',            hex: '#3B82F6' },
  { name: 'Índigo',          hex: '#6366F1' },
  { name: 'Morado',          hex: '#A855F7' },
  { name: 'Rosa',            hex: '#EC4899' },
  { name: 'Negro',           hex: '#000000' },
  { name: 'Blanco',          hex: '#FFFFFF' },
  { name: 'Gris',            hex: '#6B7280' },
  { name: 'Café',            hex: '#92400E' },

  // Tonos pastel
  { name: 'Rosa pastel',     hex: '#F9A8D4' },
  { name: 'Lila pastel',     hex: '#E9D5FF' },
  { name: 'Azul pastel',     hex: '#BFDBFE' },
  { name: 'Verde pastel',    hex: '#BBF7D0' },
  { name: 'Amarillo pastel', hex: '#FEF9C3' },
  { name: 'Durazno pastel',  hex: '#FED7AA' },

  // Tonos intensos
  { name: 'Rojo vino',       hex: '#7F1D1D' },
  { name: 'Naranja quemado', hex: '#C2410C' },
  { name: 'Mostaza',         hex: '#D97706' },
  { name: 'Verde esmeralda', hex: '#059669' },
  { name: 'Azul marino',     hex: '#1E3A8A' },
  { name: 'Morado profundo', hex: '#6D28D9' },

  // Tonos tierra / piel
  { name: 'Beige claro',     hex: '#F5E9DA' },
  { name: 'Piel clara',      hex: '#F9D7B9' },
  { name: 'Piel media',      hex: '#E0A676' },
  { name: 'Piel oscura',     hex: '#8B5A2B' },
  { name: 'Arena',           hex: '#D6B98C' },

  // Otros útiles
  { name: 'Azul cielo',      hex: '#38BDF8' },
  { name: 'Verde menta',     hex: '#4ADE80' },
  { name: 'Lavanda',         hex: '#C4B5FD' },
  { name: 'Gris claro',      hex: '#D1D5DB' },
  { name: 'Gris oscuro',     hex: '#374151' }
] as const
