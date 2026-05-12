<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventImage;
use App\Models\Place;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class EventService
{
    public function getAll(): Collection
    {
        return Event::with(['place', 'images' => function($query) {
            $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
        }])->orderBy('start_date', 'asc')->get();
    }

    public function getById(int $id): Event
    {
        return Event::with(['place', 'images' => function($query) {
            $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
        }])->findOrFail($id);
    }

    public function create(array $data): Event
    {
        // Validar que el lugar existe
        if (!Place::find($data['place_id'])) {
            throw new \Exception('El lugar especificado no existe');
        }

        // Validar fechas
        $this->validateEventDates($data);

        $event = Event::create($data);

        // Cargar relaciones
        return $this->getById($event->id);
    }

    public function update(int $id, array $data): Event
    {
        $event = Event::findOrFail($id);

        // Validar fechas si se están actualizando
        if (isset($data['start_date']) || isset($data['end_date'])) {
            $eventData = array_merge($event->toArray(), $data);
            $this->validateEventDates($eventData);
        }

        $event->update($data);

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $event = Event::findOrFail($id);
        return $event->delete();
    }

    public function getUpcoming(): Collection
    {
        return Event::upcoming()
            ->with(['place', 'images' => function($query) {
                $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
            }])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getByPlace(int $placeId): Collection
    {
        return Event::byPlace($placeId)
            ->with(['place', 'images' => function($query) {
                $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
            }])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getByType(string $type): Collection
    {
        return Event::byType($type)
            ->with(['place', 'images' => function($query) {
                $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
            }])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getRecurring(): Collection
    {
        return Event::recurring()
            ->with(['place', 'images' => function($query) {
                $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
            }])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getNearby(float $latitude, float $longitude, float $radius = 50): Collection
    {
        return Event::nearby($latitude, $longitude, $radius)
            ->with(['place', 'images' => function($query) {
                $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
            }])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function searchByName(string $name): Collection
    {
        return Event::where('name', 'LIKE', "%{$name}%")
            ->orWhere('description', 'LIKE', "%{$name}%")
            ->with(['place', 'images' => function($query) {
                $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
            }])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getByDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Event::whereBetween('start_date', [$startDate, $endDate])
            ->orWhere(function($query) use ($startDate, $endDate) {
                $query->where('end_date', '>=', $startDate)
                      ->where('start_date', '<=', $endDate);
            })
            ->with(['place', 'images' => function($query) {
                $query->orderBy('is_main', 'desc')->orderBy('uploaded_at', 'asc');
            }])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    public function getToday(): Collection
    {
        $today = Carbon::today();
        return $this->getByDateRange($today, $today->copy()->endOfDay());
    }

    public function getThisWeek(): Collection
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        return $this->getByDateRange($startOfWeek, $endOfWeek);
    }

    public function getThisMonth(): Collection
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        return $this->getByDateRange($startOfMonth, $endOfMonth);
    }

    // Métodos para manejo de imágenes
    public function addImage(int $eventId, array $imageData): EventImage
    {
        $event = Event::findOrFail($eventId);
        
        // Si es la primera imagen o se marca como principal, establecer como principal
        if ($imageData['is_main'] ?? false || $event->images()->count() === 0) {
            $imageData['is_main'] = 1;
            // Quitar el flag de principal de otras imágenes
            $event->images()->update(['is_main' => 0]);
        }

        return $event->images()->create($imageData);
    }

    public function updateImage(int $imageId, array $imageData): EventImage
    {
        $image = EventImage::findOrFail($imageId);
        
        // Si se está estableciendo como imagen principal
        if ($imageData['is_main'] ?? false) {
            $image->setAsMain();
        }

        $image->update($imageData);
        return $image;
    }

    public function deleteImage(int $imageId): bool
    {
        $image = EventImage::findOrFail($imageId);
        return $image->delete();
    }

    public function setMainImage(int $eventId, int $imageId): bool
    {
        $event = Event::findOrFail($eventId);
        $image = $event->images()->findOrFail($imageId);
        
        return $image->setAsMain();
    }

    // Métodos de validación
    private function validateEventDates(array $data): void
    {
        $startDate = Carbon::parse($data['start_date']);
        
        if ($startDate < Carbon::now()) {
            throw new \Exception('La fecha de inicio no puede ser en el pasado');
        }

        if (isset($data['end_date']) && $data['end_date']) {
            $endDate = Carbon::parse($data['end_date']);
            
            if ($endDate < $startDate) {
                throw new \Exception('La fecha de fin no puede ser anterior a la fecha de inicio');
            }
        }
    }

    // Métodos de estadísticas
    public function getEventStats(): array
    {
        $totalEvents = Event::count();
        $upcomingEvents = Event::upcoming()->count();
        $recurringEvents = Event::recurring()->count();
        $eventsThisMonth = $this->getThisMonth()->count();

        return [
            'total_events' => $totalEvents,
            'upcoming_events' => $upcomingEvents,
            'recurring_events' => $recurringEvents,
            'events_this_month' => $eventsThisMonth
        ];
    }
}
