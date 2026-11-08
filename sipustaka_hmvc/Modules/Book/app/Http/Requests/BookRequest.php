<?php

namespace Modules\Book\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses dibatasi oleh middleware role:admin pada route
    }

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', 'string', 'max:255'],
            'tahun_terbit' => ['required', 'integer', 'min:1000', 'max:'.date('Y')],
            'stok' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required' => 'Judul buku wajib diisi.',
            'penulis.required' => 'Penulis wajib diisi.',
            'penerbit.required' => 'Penerbit wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.min' => 'Tahun terbit tidak valid.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi tahun ini.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok tidak boleh negatif.',
        ];
    }
}
