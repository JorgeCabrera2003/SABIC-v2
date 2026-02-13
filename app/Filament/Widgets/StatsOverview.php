<?php

// namespace App\Filament\Widgets;

// use App\Models\User;
// use App\Models\Attendance;
// use App\Models\Personal;
// use Filament\Widgets\StatsOverviewWidget as BaseWidget;
// use Filament\Widgets\StatsOverviewWidget\Stat;
// use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;


// class StatsOverview extends BaseWidget
// {
//     use HasWidgetShield;

//     protected function getStats(): array
//     {

//         $today = now()->toDateString();
//         $todayCount = Attendance::where('day', $today)->count();
//         $totalEmployees = Personal::where('status', 'active')->orWhere('status', 'authorized')->count();
        
//         return [
            
//         ];
//     }
// }