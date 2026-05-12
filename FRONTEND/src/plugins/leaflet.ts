import 'leaflet/dist/leaflet.css'
import * as L from 'leaflet'
import { patchLeafletForVueLifecycle } from '@/utils/leafletSafety'

export default function installLeaflet() {
  patchLeafletForVueLifecycle(L)
  console.log('Leaflet plugin installed')
}
