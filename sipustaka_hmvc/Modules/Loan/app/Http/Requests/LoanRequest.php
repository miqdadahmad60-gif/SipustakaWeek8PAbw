<?php

namespace Modules\Loan\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // admin maupun user (sudah dijaga middleware auth)
    }

    public function rules(): array
    {
        return [
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'judul_buku' => ['required', 'string', 'max:255'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'status' => ['required', Rule::in(['Dipinjam', 'Dikembalikan'])],
            'bukti_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'], // 2 MB
        ];
    }

    public function messages(): array
    {
        return [
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'judul_buku.required' => 'Judul buku wajib diisi.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_pinjam.date' => 'Tanggal pinjam tidak valid.',
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi.',
            'tanggal_kembali.date' => 'Tanggal kembali tidak valid.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus Dipinjam atau Dikembalikan.',
            'bukti_file.file' => 'Bukti harus berupa file.',
            'bukti_file.mimes' => 'Bukti harus berformat jpg, png, atau pdf.',
            'bukti_file.max' => 'Ukuran bukti maksimal 2 MB.',
        ];
    }
}
