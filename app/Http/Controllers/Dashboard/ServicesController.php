<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ServicesController extends Controller
{
    public function index(Request $request)
    {
        $coach = $request->user()->coach;

        if (!$coach) {
            Log::warning('ServicesController@index called without coach relation', [
                'user_id' => $request->user()->id ?? null,
            ]);
            return redirect()->route('dashboard');
        }

        $services = $coach->serviceTypes()
            ->orderBy('order')
            ->get();

        Log::info('ServicesController@index listing services', [
            'coach_id' => $coach->id,
            'service_count' => $services->count(),
        ]);

        return Inertia::render('Coach/ServicesBeta', [
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'price' => $this->normalizePriceInput($request->input('price')),
        ]);

        Log::info('ServicesController@store incoming payload', [
            'coach_id' => $request->user()->coach->id ?? null,
            'name' => $request->input('name'),
            'has_image' => $request->hasFile('image'),
            'price_raw' => $request->input('price'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'is_active' => 'boolean',
            'booking_enabled' => 'boolean',
            'max_advance_booking_days' => 'integer|min:1|max:365',
            'min_advance_booking_hours' => 'integer|min:0|max:168',
            'image' => 'nullable|image|max:5120',
        ]);

        $coach = $request->user()->coach;

        $maxOrder = $coach->serviceTypes()->max('order') ?? 0;

        $data = [
            ...$validated,
            'order' => $maxOrder + 1,
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('service-images', 'public');
            Log::info('ServicesController@store image stored', [
                'service_name' => $validated['name'],
                'path' => $data['image_path'],
            ]);
        }

        $service = $coach->serviceTypes()->create($data);

        Log::info('ServicesController@store service created', [
            'service_id' => $service->id,
            'image_path' => $service->image_path,
            'image_url' => $service->image_url,
        ]);

        return back()->with('success', 'Service créé avec succès');
    }

    public function update(Request $request, ServiceType $service)
    {
        $this->authorize('update', $service);

        $request->merge([
            'price' => $this->normalizePriceInput($request->input('price')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'is_active' => 'boolean',
            'booking_enabled' => 'boolean',
            'max_advance_booking_days' => 'integer|min:1|max:365',
            'min_advance_booking_hours' => 'integer|min:0|max:168',
            'image' => 'nullable|image|max:5120',
            'remove_image' => 'nullable|boolean',
        ]);

        $data = $validated;

        if ($request->hasFile('image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
                Log::info('ServicesController@update removed previous image', [
                    'service_id' => $service->id,
                    'old_path' => $service->image_path,
                ]);
            }
            $data['image_path'] = $request->file('image')->store('service-images', 'public');
            Log::info('ServicesController@update new image stored', [
                'service_id' => $service->id,
                'new_path' => $data['image_path'],
            ]);
        } elseif ($request->boolean('remove_image')) {
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
                Log::info('ServicesController@update image removed on request', [
                    'service_id' => $service->id,
                    'old_path' => $service->image_path,
                ]);
            }
            $data['image_path'] = null;
        }

        unset($data['image'], $data['remove_image']);

        $service->update($data);

        Log::info('ServicesController@update completed', [
            'service_id' => $service->id,
            'image_path' => $service->image_path,
            'image_url' => $service->image_url,
        ]);

        return back()->with('success', 'Service mis à jour avec succès');
    }

    public function destroy(ServiceType $service)
    {
        $this->authorize('delete', $service);

        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
            Log::info('ServicesController@destroy deleted image from disk', [
                'service_id' => $service->id,
                'path' => $service->image_path,
            ]);
        }

        $service->delete();

        Log::info('ServicesController@destroy service removed', [
            'service_id' => $service->id,
        ]);

        return back()->with('success', 'Service supprimé avec succès');
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|exists:service_types,id',
            'services.*.order' => 'required|integer|min:0',
        ]);

        $coach = $request->user()->coach;

        foreach ($validated['services'] as $serviceData) {
            $service = ServiceType::find($serviceData['id']);
            if ($service && $service->coach_id === $coach->id) {
                $service->update(['order' => $serviceData['order']]);
            }
        }

        Log::info('ServicesController@reorder updated services order', [
            'coach_id' => $coach->id,
            'services_payload' => $validated['services'],
        ]);

        return back()->with('success', 'Ordre des services mis à jour');
    }

    protected function normalizePriceInput($value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        if (is_string($value)) {
            $value = str_replace([' ', "\u{00A0}"], '', $value);
            $value = str_replace(',', '.', $value);
        }

        return $value;
    }
}
