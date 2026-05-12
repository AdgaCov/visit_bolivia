function patchOverlayUpdate(prototype: any) {
  if (!prototype || prototype._conoceBoliviaSafeUpdatePosition) return

  const originalUpdatePosition = prototype._updatePosition
  if (typeof originalUpdatePosition !== 'function') return

  prototype._updatePosition = function safeUpdatePosition(...args: unknown[]) {
    if (!this || !this._map) return

    try {
      return originalUpdatePosition.apply(this, args)
    } catch (error: any) {
      const message = String(error?.message || '')
      if (
        message.includes('latLngToLayerPoint') ||
        message.includes('Cannot read properties of null')
      ) {
        return
      }
      throw error
    }
  }

  prototype._conoceBoliviaSafeUpdatePosition = true
}

export function patchLeafletForVueLifecycle(leaflet: any) {
  if (!leaflet) return

  patchOverlayUpdate(leaflet.Marker?.prototype)
  patchOverlayUpdate(leaflet.Tooltip?.prototype)
  patchOverlayUpdate(leaflet.Popup?.prototype)
}
