<?php

namespace App\Http\Controllers;

use App\Models\DevicePlan;
use Illuminate\Routing\Controller;

class PlanController extends Controller
{
    public function all()
    {
        return DevicePlan::query()->with('region')->get()->all();
    }
}
