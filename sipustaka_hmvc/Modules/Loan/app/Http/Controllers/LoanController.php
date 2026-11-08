<?php

namespace Modules\Loan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Loan\Http\Requests\LoanRequest;
use Modules\Loan\Models\Loan;

class LoanController extends Controller
{
    /** Halaman (shell) - data dimuat via AJAX dari list(). */
    public function index()
    {
        return view('loan::index');
    }

    /** JSON: seluruh data peminjaman (dipanggil via Fetch/XHR). */
    public function list()
    {
        return response()->json([
            'data' => Loan::orderByDesc('id')->get(),
        ]);
    }

    public function store(LoanRequest $request)
    {
        $data = $request->safe()->except('bukti_file');
        $data['user_id'] = $request->user()->id;

        if ($request->hasFile('bukti_file')) {
            $data['bukti_file'] = $request->file('bukti_file')->store('bukti', 'public');
        }

        $loan = Loan::create($data);

        return response()->json([
            'message' => 'Data peminjaman berhasil ditambahkan.',
            'data' => $loan,
        ], 201);
    }

    public function show(Loan $loan)
    {
        return response()->json(['data' => $loan]);
    }

    public function update(LoanRequest $request, Loan $loan)
    {
        $data = $request->safe()->except('bukti_file');

        if ($request->hasFile('bukti_file')) {
            // ganti file lama
            if ($loan->bukti_file) {
                Storage::disk('public')->delete($loan->bukti_file);
            }
            $data['bukti_file'] = $request->file('bukti_file')->store('bukti', 'public');
        }

        $loan->update($data);

        return response()->json([
            'message' => 'Data peminjaman berhasil diubah.',
            'data' => $loan->fresh(),
        ]);
    }

    public function destroy(Loan $loan)
    {
        // hapus file dari storage ketika data dihapus
        if ($loan->bukti_file) {
            Storage::disk('public')->delete($loan->bukti_file);
        }
        $loan->delete();

        return response()->json(['message' => 'Data peminjaman berhasil dihapus.']);
    }
}
