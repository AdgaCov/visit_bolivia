import type { FavoriteCard } from './favorites.service'

const STORAGE_KEY = 'bt_temp_favorites'

function isBrowser(): boolean {
  return typeof window !== 'undefined' && typeof window.localStorage !== 'undefined'
}

function normalizeCard(card: FavoriteCard): FavoriteCard {
  const baseId = card.locationId ?? card.id ?? card.link ?? Math.random().toString(36).slice(2)
  const tempId = String(baseId).startsWith('temp-') ? String(baseId) : `temp-${baseId}`
  return {
    ...card,
    id: tempId,
    isTemporary: true
  }
}

function readStorage(): FavoriteCard[] {
  if (!isBrowser()) return []
  const raw = window.localStorage.getItem(STORAGE_KEY)
  if (!raw) return []
  try {
    const parsed = JSON.parse(raw) as FavoriteCard[]
    return parsed.map(normalizeCard)
  } catch (error) {
    console.warn('⚠️ No se pudieron parsear los favoritos temporales, reiniciando almacenamiento.', error)
    window.localStorage.removeItem(STORAGE_KEY)
    return []
  }
}

function writeStorage(favorites: FavoriteCard[]): void {
  if (!isBrowser()) return
  window.localStorage.setItem(STORAGE_KEY, JSON.stringify(favorites))
}

function getAll(): FavoriteCard[] {
  return readStorage()
}

function add(card: FavoriteCard): FavoriteCard[] {
  const current = readStorage()
  const exists = current.some(favorite => favorite.locationId === card.locationId || favorite.link === card.link)
  if (exists) {
    return current
  }
  const updated = [...current, normalizeCard(card)]
  writeStorage(updated)
  return updated
}

function remove(id: FavoriteCard['id']): FavoriteCard[] {
  const current = readStorage()
  const updated = current.filter(favorite => favorite.id !== id)
  writeStorage(updated)
  return updated
}

function findByLocationId(locationId: FavoriteCard['locationId']): FavoriteCard | undefined {
  const current = readStorage()
  return current.find(favorite => String(favorite.locationId) === String(locationId))
}

function clear(): void {
  if (!isBrowser()) return
  window.localStorage.removeItem(STORAGE_KEY)
}

export default {
  getAll,
  add,
  remove,
  findByLocationId,
  clear
}


