<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DatabaseService
{
    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
    }
}
