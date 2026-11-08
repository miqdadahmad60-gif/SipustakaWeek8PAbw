<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Book\Models\Book;
use Modules\Loan\Models\Loan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin & user
        User::updateOrCreate(['email' => 'admin@sipustaka.test'], [
            'name' => 'Administrator',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
        ]);

        User::updateOrCreate(['email' => 'user@sipustaka.test'], [
            'name' => 'Anggota Perpustakaan',
            'password' => Hash::make('user12345'),
            'role' => 'user',
        ]);

        // Data buku contoh (>= 5)
        $books = [
            ['Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 8],
            ['Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 5],
            ['Pemrograman Web dengan Laravel', 'Budi Raharjo', 'Informatika', 2022, 10],
            ['Filosofi Teras', 'Henry Manampiring', 'Kompas', 2018, 6],
            ['Dasar-Dasar Basis Data', 'Abdul Kadir', 'Andi Offset', 2019, 7],
            ['Atomic Habits', 'James Clear', 'Gramedia', 2019, 4],
        ];
        foreach ($books as [$judul, $penulis, $penerbit, $tahun, $stok]) {
            Book::updateOrCreate(['judul' => $judul], [
                'penulis' => $penulis,
                'penerbit' => $penerbit,
                'tahun_terbit' => $tahun,
                'stok' => $stok,
            ]);
        }

        // Data peminjaman contoh (>= 5)
        if (Loan::count() === 0) {
            $user = User::where('email', 'user@sipustaka.test')->first();
            $loans = [
                ['Andi Saputra', 'Laskar Pelangi', '2026-10-01', '2026-10-08', 'Dikembalikan'],
                ['Siti Rahma', 'Bumi Manusia', '2026-10-05', '2026-10-12', 'Dikembalikan'],
                ['Budi Santoso', 'Pemrograman Web dengan Laravel', '2026-10-20', '2026-10-27', 'Dipinjam'],
                ['Dewi Lestari', 'Filosofi Teras', '2026-11-01', '2026-11-08', 'Dipinjam'],
                ['Rizky Pratama', 'Atomic Habits', '2026-11-03', '2026-11-10', 'Dipinjam'],
            ];
            foreach ($loans as [$nama, $judul, $pinjam, $kembali, $status]) {
                Loan::create([
                    'user_id' => $user?->id,
                    'nama_peminjam' => $nama,
                    'judul_buku' => $judul,
                    'tanggal_pinjam' => $pinjam,
                    'tanggal_kembali' => $kembali,
                    'status' => $status,
                ]);
            }
        }
    }
}
