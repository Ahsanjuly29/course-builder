<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Course
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'feature_video' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:512000', // KB ~ 500MB

            // Modules
            'modules' => 'nullable|array',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.description' => 'nullable|string',


            // contents nested validation
            'modules.*.contents' => 'nullable|array',
            'modules.*.contents.*.content_type' => 'required|in:text,image,video,link,file',
            'modules.*.contents.*.title' => 'nullable|string|max:255',
            'modules.*.contents.*.body' => 'nullable|string',
            'modules.*.contents.*.external_link' => 'nullable|url',

            'modules.*.contents.*.media' => 'nullable|file|max:51200', // 50MB per content media by default
        ];
    }

    public function messages()
    {
        return [
            'modules.*.title.required' => 'Module title is required.',
            'modules.*.contents.*.content_type.required' => 'Content type is required.',
        ];
    }
}
