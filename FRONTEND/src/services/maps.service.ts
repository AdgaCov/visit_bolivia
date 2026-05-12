import type { Map as LeafletMap, Marker, MarkerOptions, LatLngExpression, TileLayer } from 'leaflet'
import * as L from 'leaflet'
import { patchLeafletForVueLifecycle } from '@/utils/leafletSafety'

patchLeafletForVueLifecycle(L)

export function createMarker(map: LeafletMap | null, coords: LatLngExpression, options: MarkerOptions = {}): Marker | null {
  if (!map) return null
  return L.marker(coords, options).addTo(map)
}

export function addTileLayer(map: LeafletMap | null): TileLayer | null {
  if (!map) return null
  return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 18,
    minZoom: 3
  }).addTo(map)
}

export function drawRoute(map: LeafletMap | null, from: LatLngExpression, to: LatLngExpression) {
  return { map, from, to }
} 
