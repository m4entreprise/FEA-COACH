<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssessmentRequest extends FormRequest
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
            'energy_level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'difficulty_level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'progress_notes' => ['nullable', 'string', 'max:2000'],
            'coach_comments' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:pending,completed'],
            'assessment_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'energy_level.min' => 'Le niveau d\'énergie doit être entre 1 et 10.',
            'energy_level.max' => 'Le niveau d\'énergie doit être entre 1 et 10.',
            'difficulty_level.min' => 'Le niveau de difficulté doit être entre 1 et 10.',
            'difficulty_level.max' => 'Le niveau de difficulté doit être entre 1 et 10.',
            'status.in' => 'Le statut sélectionné n\'est pas valide.',
            'assessment_date.required' => 'La date du bilan est obligatoire.',
            'assessment_date.before_or_equal' => 'La date du bilan ne peut pas être dans le futur.',
        ];
    }
}
