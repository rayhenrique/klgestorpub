<?php

namespace App\Http\Controllers;

use App\Models\CitySetting;
use Illuminate\Http\Request;

class CitySettingsController extends Controller
{
    public function edit()
    {
        $settings = CitySetting::first() ?? new CitySetting();
        return view('settings.city.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'city_name' => 'nullable|string|max:255',
            'city_hall_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'ibge_code' => 'nullable|string|size:7',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|size:8',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'mayor_name' => 'nullable|string|max:255',
        ]);

        CitySetting::updateOrCreate(['id' => 1], $validated);

        return redirect()->route('settings.city.edit')
            ->with('success', 'Configurações atualizadas com sucesso.');
    }
} 