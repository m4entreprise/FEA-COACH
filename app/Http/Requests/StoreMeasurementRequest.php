<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeasurementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'weight' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'body_measurements' => ['nullable', 'array'],
            'body_measurements.waist' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'body_measurements.hips' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'body_measurements.chest' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'body_measurements.arms' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'body_measurements.thighs' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'body_fat_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5MB max
            'measurement_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'measurement_date.required' => 'La date de mesure est obligatoire.',
            'measurement_date.before_or_equal' => 'La date de mesure ne peut pas être dans le futur.',
            'weight.min' => 'Le poids doit être supérieur à 20 kg.',
            'weight.max' => 'Le poids doit être inférieur à 300 kg.',
            'photos.*.image' => 'Le fichier doit être une image.',
            'photos.*.max' => 'Chaque photo ne peut pas dépasser 5 Mo.',
        ];
    }
}
