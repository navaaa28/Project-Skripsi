@extends('layouts.admin-dashboard')

@section('title', 'Manajemen Tahun Ajaran')

@section('content')
<style>
    .admin-index-page { display: flex; flex-direction: column; gap: 14px; }
    .admin-index-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .admin-index-title { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0; }
    .admin-index-subtitle { margin: 2px 0 0; font-size: 13px; color: #64748b; }

    .admin-btn-primary {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        background: #2563eb; color: #fff; border: none; border-radius: 10px;
        font-size: 13px; font-weight: 600; padding: 10px 14px; text-decoration: none; white-space: nowrap;
    }
    .admin-btn-primary:hover { background: #1d4ed8; }

    .admin-index-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
        padding: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .admin-table-shell { width: 100%; overflow-x: auto; border: 1px solid #e5e7eb; border-radius: 10px; }
    .admin-table { width: 100%; min-width: 600px; border-collapse: collapse; font-size: 13px; background: #fff; }
    .admin-table th {
        text-align: left; padding: 12px 10px; font-size: 11px; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: .02em; border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .admin-table td { padding: 12px 10px; border-bottom: 1px solid #edf2f7; color: #1f2937; vertical-align: middle; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-muted { color: #64748b; }

    .ta-chip {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700;
    }
    .ta-active {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        background: #dcfce7; color: #166534; font-size: 11px; font-weight: 700;
    }
    .ta-inactive {
        display: inline-block; padding: 4px 9px; border-radius: 999px;
        background: #f1f5f9; color: #94a3b8; font-size: 11px; font-weight: 700;
    }

    .admin-actions { display: flex; align-items: center; gap: 10px; }
    .admin-action-link { font-size: 12px; font-weight: 600; text-decoration: none; }
    .admin-action-link.edit { color: #2563eb; }
    .admin-action-link.delete { color: #ef4444; }
    .admin-action-link.activate { color: #16a34a; }
    .admin-action-btn {
        font-size: 12px; font-weight: 600; border: none; background: none; cursor: pointer;
    }
    .admin-action-btn.activate { color: #16a34a; }
    .admin-action-btn.delete { color: #ef4444; }
    .admin-action-btn.toggle-sem { color: #9333ea; }
    .admin-action-btn.deactivate { color: #f59e0b; }

    .admin-empty { text-align: center; padding: 20px 10px; color: #64748b; font-size: 13px; }

    .pager {
        margin-top: 12px; display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
    }
    .pager-info { font-size: 12px; color: #64748b; }
    .pager-links { display: flex; align-items: center; gap: 6px; }
    .pager-btn {
        border: 1px solid #d1d5db; background: #fff; color: #374151;
        border-radius: 8px; padding: 6px 10px; font-size: 12px; text-decoration: none; line-height: 1;
    }
    .pager-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .pager-btn.disabled { opacity: .5; pointer-events: none; }

    .confirm-modal-backdrop {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(15, 23, 42, 0.45); align-items: center; justify-content: center;
        padding: 20px;
    }
    .confirm-modal-backdrop.show { display: flex; }
    .confirm-modal {
        width: 360px; max-width: 100%; background: #fff; border-radius: 12px;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15); padding: 24px 28px; text-align: center;
    }
    .confirm-modal-title {
        font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 6px;
    }
    .confirm-modal-text {
        font-size: 13px; color: #6b7280; margin-bottom: 20px; line-height: 1.5;
    }
    .confirm-modal-actions {
        display: flex; justify-content: center; gap: 10px;
    }
    .confirm-modal-btn {
        padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
    }
    .confirm-modal-btn.cancel {
        border: 1px solid #e5e7eb; background: #fff; color: #374151;
    }
    .confirm-modal-btn.confirm {
        border: none; background: #2563eb; color: #fff;
    }
    .confirm-modal-btn.confirm.danger { background: #ef4444; }
    .confirm-modal-btn.confirm.success { background: #16a34a; }
    .confirm-modal-btn.confirm.secondary { background: #7c3aed; }
</style>

<div class="admin-index-page">
    <div class="admin-index-toolbar">
        <div>
            <h1 class="admin-index-title">Manajemen Tahun Ajaran</h1>
            <p class="admin-index-subtitle">Kelola tahun ajaran dan tentukan tahun ajaran aktif.</p>
        </div>
        <a href="{{ route('admin.tahun-ajaran.create') }}" class="admin-btn-primary">+ Tambah Tahun Ajaran</a>
    </div>

    <div class="admin-index-card">
        <div class="admin-table-shell">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">No</th>
                        <th>Tahun Ajaran</th>
                        <th style="width: 140px;">Status</th>
                        <th style="width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tahunAjarans as $ta)
                        <tr>
                            <td class="admin-muted">{{ $loop->iteration + ($tahunAjarans->currentPage() - 1) * $tahunAjarans->perPage() }}</td>
                            <td><span class="ta-chip">{{ $ta->nama_tahun_ajaran }}</span></td>
                            <td>
                                @if($ta->is_active)
                                    <span class="ta-active">✓ Aktif (Semester {{ $ta->semester_aktif == 1 ? 'Ganjil' : 'Genap' }})</span>
                                @else
                                    <span class="ta-inactive">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="admin-actions">
                                    @unless($ta->is_active)
                                        <form method="POST" action="{{ route('admin.tahun-ajaran.activate', $ta) }}" style="display:inline;">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="admin-action-btn activate js-confirm-action"
                                                data-confirm-title="Aktifkan Tahun Ajaran"
                                                data-confirm-text="Aktifkan tahun ajaran {{ $ta->nama_tahun_ajaran }}?"
                                                data-confirm-button="Ya, Aktifkan"
                                                data-confirm-variant="success"
                                            >Aktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.tahun-ajaran.deactivate', $ta) }}" style="display:inline;">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="admin-action-btn deactivate js-confirm-action"
                                                data-confirm-title="Nonaktifkan Tahun Ajaran"
                                                data-confirm-text="Nonaktifkan tahun ajaran {{ $ta->nama_tahun_ajaran }}? Setelah dinonaktifkan, tidak ada tahun ajaran aktif sampai Anda mengaktifkan yang lain."
                                                data-confirm-button="Ya, Nonaktifkan"
                                                data-confirm-variant="danger"
                                            >Nonaktifkan</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.tahun-ajaran.toggle-semester', $ta) }}" style="display:inline;">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="admin-action-btn toggle-sem js-confirm-action"
                                                data-confirm-title="Ganti Semester Aktif"
                                                data-confirm-text="Ganti semester aktif menjadi {{ $ta->semester_aktif == 1 ? 'Genap' : 'Ganjil' }}?"
                                                data-confirm-button="Ya, Ganti"
                                                data-confirm-variant="secondary"
                                            >Ganti Semester</button>
                                        </form>
                                    @endunless
                                    <a class="admin-action-link edit" href="{{ route('admin.tahun-ajaran.edit', $ta) }}">Edit</a>
                                    @unless($ta->is_active)
                                        <form method="POST" action="{{ route('admin.tahun-ajaran.destroy', $ta) }}" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button
                                                type="submit"
                                                class="admin-action-btn delete js-confirm-action"
                                                data-confirm-title="Hapus Tahun Ajaran"
                                                data-confirm-text="Hapus tahun ajaran {{ $ta->nama_tahun_ajaran }}?"
                                                data-confirm-button="Ya, Hapus"
                                                data-confirm-variant="danger"
                                            >Hapus</button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="admin-empty">Belum ada data tahun ajaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tahunAjarans->hasPages())
            <div class="pager">
                <div class="pager-info">Menampilkan {{ $tahunAjarans->firstItem() }} - {{ $tahunAjarans->lastItem() }} dari {{ $tahunAjarans->total() }} data</div>
                <div class="pager-links">
                    <a class="pager-btn {{ $tahunAjarans->onFirstPage() ? 'disabled' : '' }}" href="{{ $tahunAjarans->previousPageUrl() ?? '#' }}">Prev</a>
                    @foreach ($tahunAjarans->getUrlRange(1, $tahunAjarans->lastPage()) as $page => $url)
                        <a class="pager-btn {{ $page === $tahunAjarans->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="pager-btn {{ $tahunAjarans->hasMorePages() ? '' : 'disabled' }}" href="{{ $tahunAjarans->nextPageUrl() ?? '#' }}">Next</a>
                </div>
            </div>
        @endif
    </div>
</div>

<div id="actionConfirmModal" class="confirm-modal-backdrop" aria-hidden="true">
    <div class="confirm-modal" role="dialog" aria-modal="true" aria-labelledby="actionConfirmTitle">
        <div id="actionConfirmTitle" class="confirm-modal-title">Konfirmasi</div>
        <div id="actionConfirmText" class="confirm-modal-text">Apakah Anda yakin ingin melanjutkan?</div>
        <div class="confirm-modal-actions">
            <button type="button" id="actionConfirmCancel" class="confirm-modal-btn cancel">Batal</button>
            <button type="button" id="actionConfirmSubmit" class="confirm-modal-btn confirm">Ya</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('actionConfirmModal');
        const title = document.getElementById('actionConfirmTitle');
        const text = document.getElementById('actionConfirmText');
        const cancelButton = document.getElementById('actionConfirmCancel');
        const submitButton = document.getElementById('actionConfirmSubmit');
        let activeForm = null;

        function closeModal() {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            activeForm = null;
            submitButton.className = 'confirm-modal-btn confirm';
        }

        document.querySelectorAll('.js-confirm-action').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                activeForm = button.closest('form');
                title.textContent = button.dataset.confirmTitle || 'Konfirmasi';
                text.textContent = button.dataset.confirmText || 'Apakah Anda yakin ingin melanjutkan?';
                submitButton.textContent = button.dataset.confirmButton || 'Ya';
                submitButton.className = 'confirm-modal-btn confirm';

                if (button.dataset.confirmVariant) {
                    submitButton.classList.add(button.dataset.confirmVariant);
                }

                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
            });
        });

        cancelButton.addEventListener('click', closeModal);

        submitButton.addEventListener('click', function () {
            if (activeForm) {
                activeForm.submit();
            }
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('show')) {
                closeModal();
            }
        });
    });
</script>
@endsection
