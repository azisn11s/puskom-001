<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Team;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function counterCompany()
    {
        return response()->json([
            'success'=> true,
            'message'=> 'Counter company has been loaded!',
            'data'=> []
        ]);
    }

    public function counterEmployee()
    {
        return response()->json([
            'success'=> true,
            'message'=> 'Counter employee has been loaded!',
            'data'=> []
        ]);
    }

    public function counterDoctor()
    {
        return response()->json([
            'success'=> true,
            'message'=> 'Counter doctor has been loaded!',
            'data'=> []
        ]);
    }

    public function counterTeam()
    {        
        return response()->json([
            'success'=> true,
            'message'=> 'Counter team has been loaded!',
            'data'=> []
        ]);
        
    }
}
