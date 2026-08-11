<?php

namespace App\Http\Controllers\Backend\Staff;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('backend::staff.dashboard');
    }
}
