<?php

namespace Modules\Loan\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Loan extends Model
{
    protected $table = 'loans';

    protected $fillable = [
        'user_id',
        'nama_peminjam',
        'judul_buku',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'bukti_file',
    ];

    protected $appends = ['bukti_url'];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date:Y-m-d',
            'tanggal_kembali' => 'date:Y-m-d',
        ];
    }

    /** URL publik file bukti (storage/app/public/bukti/...) */
    public function getBuktiUrlAttribute(): ?string
    {
        return $this->bukti_file ? Storage::disk('public')->url($this->bukti_file) : null;
    }
}
