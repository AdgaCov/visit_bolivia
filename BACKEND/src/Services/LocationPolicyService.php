<?php

namespace App\Services;

use App\Models\LocationPolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LocationPolicyService
{
    public function getAll(): Collection
    {
        return LocationPolicy::with('location:id,name,type')->get();
    }

    public function getById(int $id): LocationPolicy
    {
        return LocationPolicy::with('location:id,name,type')->findOrFail($id);
    }

    public function getByLocation(int $locationId): ?LocationPolicy
    {
        return LocationPolicy::with('location:id,name,type')
            ->where('location_id', $locationId)
            ->first();
    }

    public function getByUserId(int $userId): Collection
    {
        return LocationPolicy::whereHas('location', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['location:id,name,type,user_id', 'location.user:id,name,email'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function create(array $data): LocationPolicy
    {
        return LocationPolicy::create($data);
    }

    public function update(int $id, array $data): LocationPolicy
    {
        $policy = LocationPolicy::findOrFail($id);
        $policy->update($data);
        return $policy->fresh(['location:id,name,type']);
    }

    public function updateOrCreateByLocation(int $locationId, array $data): LocationPolicy
    {
        $data['location_id'] = $locationId;
        return LocationPolicy::updateOrCreate(
            ['location_id' => $locationId],
            $data
        );
    }

    public function delete(int $id): bool
    {
        $policy = LocationPolicy::findOrFail($id);
        return $policy->delete();
    }

    public function deleteByLocation(int $locationId): bool
    {
        return LocationPolicy::where('location_id', $locationId)->delete();
    }
}

