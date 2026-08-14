<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Laravel 13's default skeleton ships this class EMPTY (no traits) — unlike
 * Laravel 10 and earlier. We add these back deliberately because every
 * Student/Admin controller in this app uses $this->authorize(...) against
 * our Policies, matching the spec's "Controller → deny feature" requirement.
 * Without this, every authorize() call throws "Call to undefined method."
 */
abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests;
}
