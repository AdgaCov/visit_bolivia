<?php

namespace App\Services;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;

class PlanService
{
    public function getAll(?bool $activeOnly = null): Collection
    {
        $query = Plan::query();
        
        if ($activeOnly === true) {
            $query->where('is_active', true);
        }
        
        return $query->orderBy('price', 'asc')->get();
    }

    public function getById(int $id): Plan
    {
        return Plan::findOrFail($id);
    }

    public function create(array $data): Plan
    {
        return Plan::create($data);
    }

    public function update(int $id, array $data): Plan
    {
        $plan = Plan::findOrFail($id);
        $plan->update($data);
        return $plan;
    }

    public function delete(int $id): bool
    {
        $plan = Plan::findOrFail($id);
        return $plan->delete();
    }

    public function toggleActive(int $id): Plan
    {
        $plan = Plan::findOrFail($id);
        $plan->is_active = !$plan->is_active;
        $plan->save();
        return $plan;
    }

    public function getActive(): Collection
    {
        return $this->getAll(true);
    }
}


