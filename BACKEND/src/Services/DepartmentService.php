<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartmentService
{
    public function getAll(): Collection
    {
        // Listado básico sin dependencias de la tabla cities
        return Department::query()->get();
    }

    public function getById(int $id): Department
    {
        // Detalle básico sin dependencias de la tabla cities
        return Department::findOrFail($id);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(int $id, array $data): Department
    {
        $department = Department::findOrFail($id);
        $department->update($data);
        return $department;
    }

    public function delete(int $id): bool
    {
        $department = Department::findOrFail($id);
        return $department->delete();
    }

    public function getWithCities(): Collection
    {
        // La tabla cities ya no existe; devolver solo departamentos
        return Department::query()->get();
    }

    public function getWithPlaces(): Collection
    {
        // Sin cities: si se requiere, migrar a locations agrupadas por department
        return Department::query()->get();
    }
}
