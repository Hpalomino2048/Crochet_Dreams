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
  { name: 'Rojo', hex: '#EF4444' },
  { name: 'Naranja', hex: '#F97316' },
  { name: 'Amarillo', hex: '#EAB308' },
  { name: 'Verde', hex: '#22C55E' },
  { name: 'Azul', hex: '#3B82F6' },
  { name: 'Índigo', hex: '#6366F1' },
  { name: 'Morado', hex: '#A855F7' },
  { name: 'Rosa', hex: '#EC4899' },
  { name: 'Negro', hex: '#000000' },
  { name: 'Blanco', hex: '#FFFFFF' },
  { name: 'Gris', hex: '#6B7280' },
  { name: 'Café', hex: '#92400E' },
] as const