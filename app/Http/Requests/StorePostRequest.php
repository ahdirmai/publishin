<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'platforms'    => ['required', 'array', 'min:1'],
            'platforms.*'  => ['string', 'in:instagram,facebook,tiktok,twitter,youtube'],
            'caption'      => ['nullable', 'string', 'max:2200'],
            'content_type' => ['nullable', 'string', 'in:feed,reel,story,video,carousel,thread,foto'],
            'status'       => ['required', 'string', 'in:draft,scheduled'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];

        if ($this->input('status') === 'scheduled') {
            $rules['scheduled_at'][] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'platforms.required'    => 'Pilih minimal satu platform',
            'platforms.min'         => 'Pilih minimal satu platform',
            'caption.max'           => 'Caption maksimal 2200 karakter',
            'scheduled_at.after'    => 'Waktu posting harus di masa depan',
            'scheduled_at.required' => 'Waktu posting wajib diisi untuk konten terjadwal',
        ];
    }
}
