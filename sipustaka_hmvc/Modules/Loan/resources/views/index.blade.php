@extends('layouts.app')
@section('title', 'Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Peminjaman Buku</h3>
    <button class="btn btn-primary" id="btn-add">+ Tambah Peminjaman</button>
</div>

<div id="flash" class="alert d-none"></div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Peminjam</th>
                    <th>Judul Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Status</th>
                    <th>Bukti</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody id="loan-body">
                <tr><td colspan="8" class="text-center text-muted py-4">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal tambah / ubah --}}
<div class="modal fade" id="loanModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="loan-form" enctype="multipart/form-data" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="loan-id">

                <div class="mb-3">
                    <label class="form-label">Nama Peminjam</label>
                    <input type="text" name="nama_peminjam" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Judul Buku</label>
                    <input type="text" name="judul_buku" class="form-control">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" name="tanggal_pinjam" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Kembali</label>
                        <input type="date" name="tanggal_kembali" class="form-control">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="Dipinjam">Dipinjam</option>
                        <option value="Dikembalikan">Dikembalikan</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bukti Peminjaman (jpg/png/pdf, maks 2 MB)</label>
                    <input type="file" name="bukti_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="invalid-feedback"></div>
                    <div class="form-text" id="current-file"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = @json(url('/loans'));
    const headers = {
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    const body = document.getElementById('loan-body');
    const form = document.getElementById('loan-form');
    const modalEl = document.getElementById('loanModal');
    const modal = new bootstrap.Modal(modalEl);
    const flash = document.getElementById('flash');
    const idInput = document.getElementById('loan-id');
    let rows = [];

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    function showFlash(msg, type = 'success') {
        flash.className = 'alert alert-' + type;
        flash.textContent = msg;
        setTimeout(() => flash.classList.add('d-none'), 4000);
    }

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    function showErrors(errors) {
        Object.entries(errors).forEach(([field, msgs]) => {
            const input = form.querySelector('[name="' + field + '"]');
            if (!input) return;
            input.classList.add('is-invalid');
            const fb = input.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.textContent = msgs[0];
        });
    }

    function render() {
        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada data peminjaman.</td></tr>';
            return;
        }
        body.innerHTML = rows.map((r, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>${esc(r.nama_peminjam)}</td>
                <td>${esc(r.judul_buku)}</td>
                <td>${esc(r.tanggal_pinjam)}</td>
                <td>${esc(r.tanggal_kembali)}</td>
                <td><span class="badge ${r.status === 'Dikembalikan' ? 'bg-success' : 'bg-warning text-dark'}">${esc(r.status)}</span></td>
                <td>${r.bukti_url ? `<a href="${esc(r.bukti_url)}" target="_blank" rel="noopener">Lihat</a>` : '-'}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-warning" data-edit="${r.id}">Ubah</button>
                    <button class="btn btn-sm btn-danger" data-del="${r.id}">Hapus</button>
                </td>
            </tr>`).join('');
    }

    async function load() {
        const res = await fetch(baseUrl + '/list', { headers });
        rows = (await res.json()).data;
        render();
    }

    function openForm(loan = null) {
        clearErrors();
        form.reset();
        idInput.value = loan ? loan.id : '';
        document.getElementById('modal-title').textContent = loan ? 'Ubah Peminjaman' : 'Tambah Peminjaman';
        const cur = document.getElementById('current-file');
        cur.innerHTML = '';
        if (loan) {
            ['nama_peminjam', 'judul_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status'].forEach(k => {
                form.querySelector('[name="' + k + '"]').value = loan[k] ?? '';
            });
            if (loan.bukti_url) {
                cur.innerHTML = 'File saat ini: <a href="' + esc(loan.bukti_url) + '" target="_blank" rel="noopener">lihat</a> (kosongkan jika tidak ingin mengganti)';
            }
        }
        modal.show();
    }

    document.getElementById('btn-add').addEventListener('click', () => openForm());

    body.addEventListener('click', async (e) => {
        const editId = e.target.dataset.edit;
        const delId = e.target.dataset.del;

        if (editId) {
            openForm(rows.find(r => String(r.id) === editId));
        }

        if (delId && confirm('Hapus data peminjaman ini?')) {
            const res = await fetch(baseUrl + '/' + delId, { method: 'DELETE', headers });
            const json = await res.json();
            if (res.ok) {
                showFlash(json.message);
                load(); // tanpa refresh halaman
            } else {
                showFlash(json.message || 'Gagal menghapus data.', 'danger');
            }
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        const id = idInput.value;
        const fd = new FormData(form);
        if (id) fd.append('_method', 'PUT'); // method spoofing agar file ikut terkirim

        const btn = document.getElementById('btn-save');
        btn.disabled = true;

        try {
            const res = await fetch(id ? baseUrl + '/' + id : baseUrl, {
                method: 'POST',
                headers,
                body: fd,
            });
            const json = await res.json();

            if (res.status === 422) {
                showErrors(json.errors); // error validasi 422 ditampilkan di bawah input
            } else if (res.ok) {
                modal.hide();
                showFlash(json.message);
                load();
            } else {
                showFlash(json.message || 'Terjadi kesalahan.', 'danger');
            }
        } finally {
            btn.disabled = false;
        }
    });

    load();
})();
</script>
@endpush
