@extends('layouts.app')

@section('content')
    <main>
        @include('components.popupEditEmployee')
        @include('components.popupDelete')
        @include('components.popupPreviewEmployeePhoto')

        <section class="title-button d-flex flex-row justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold">Data Pegawai</h1>
                @if (isset($tahun))
                    <p class="text-sm text-gray-600 mt-1">Nilai Tahun: {{ $tahun }}</p>
                @endif
            </div>
            <section class="btn-group d-flex flex-row gap-2">
                <a href="{{ url('/employees/new') }}" class="btn btn-primary">
                    Tambah Data
                    <i class="material-symbols-rounded">add</i>
                </a>
                <a href="{{ route('penilaian.index') }}" class="btn btn-primary">
                    Penilaian Tahunan
                    <i class="material-symbols-rounded">grading</i>
                </a>
            </section>
        </section>

        <!-- 🔸 FILTER TAHUN -->
        <form method="GET" class="card mb-4 flex gap-3 flex-wrap" style="padding:1rem;">
            <div>
                <label class="form-control" style="gap:0.25rem;">
                    <span style="font-size:0.8125rem;font-weight:600;color:var(--text-secondary);">Tahun Penilaian</span>
                    <select name="tahun" onchange="this.form.submit()">
                        @foreach ($tahunOptions as $opt)
                            <option value="{{ $opt }}" @selected($opt == $tahun)>{{ $opt }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </form>

        <section id="summary" class="flex w-full text-sm items-center mb-4">
            <div id="jumlah-by-divisi" class="w-full">
                @foreach ($divisions as $division)
                    <p>{{ $division->nama }}: {{ $division->employees_count }}</p>
                @endforeach
            </div>
            <p id="jumlah-data" class="flex justify-end text-sm">
                Jumlah Data: {{ count($employees) }}
            </p>
        </section>

        @csrf
        <section class="container-table table-scroll-wrapper">
            <table id="employees-table" class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th rowspan="2" class="px-3 py-2 text-left">No</th>
                        <th rowspan="2" class="px-3 py-2 text-left">Foto</th>
                        <th class="px-3 py-2 text-left">Nama</th>
                        <th class="px-3 py-2 text-left">Nilai</th>
                        <th class="px-3 py-2 text-left">NIK</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Divisi</th>
                        <th class="px-3 py-2 text-left">Tim</th>
                        <th class="px-3 py-2 text-left">Password</th>
                        <th rowspan="2" class="px-3 py-2 text-left sticky-col-right">Aksi</th>
                    </tr>
                    <tr>
                        <th><input class="filter w-full px-2 py-1 border rounded" data-column="2" type="text"
                                placeholder="Cari Nama" /></th>
                        <th><input class="filter w-full px-2 py-1 border rounded" data-column="3" type="text"
                                placeholder="Cari Nilai" /></th>
                        <th><input class="filter w-full px-2 py-1 border rounded" data-column="4" type="text"
                                placeholder="Cari NIK" /></th>
                        <th>
                            <select class="filter w-full px-2 py-1 border rounded" data-column="5" data-exact="true">
                                <option value="">Semua</option>
                                <option value="Direct">Direct</option>
                                <option value="Non Direct">Non Direct</option>
                            </select>
                        </th>
                        <th>
                            <select class="filter w-full px-2 py-1 border rounded" data-column="6" data-exact="true">
                                <option value="">Semua Divisi</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->nama }}">{{ $division->nama }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>
                            <input class="filter w-full px-2 py-1 border rounded" data-column="7" type="text"
                                placeholder="Cari Tim" />
                        </th>
                        <th>
                            <input class="filter w-full px-2 py-1 border rounded" data-column="8" type="text"
                                placeholder="Cari Password" />
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $index => $employee)
                        <tr data-id="{{ $employee->id }}"
                            data-nama="{{ $employee->nama ?? '-' }}"
                            data-nik="{{ $employee->nik ?? '-' }}"
                            data-divisi="{{ $employee->division?->nama ?? '-' }}"
                            data-team="{{ $employee->team ?? '-' }}"
                            data-status="{{ $employee->status ?? '-' }}"
                            data-photo="{{ $employee->photo_url }}"
                            class="border-t hover:bg-gray-50">
                            <td class="px-3 py-2 number">{{ $index + 1 }}</td>
                            <td class="px-3 py-2 preview-photo-btn cursor-pointer" title="Klik untuk preview foto {{ $employee->nama }}">
                                @if ($employee->photo_url)
                                    <img src="{{ $employee->photo_url }}" alt="{{ $employee->nama }}" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:1px solid #e5e7eb; transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                @else
                                    <div style="width:40px; height:40px; border-radius:50%; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#9ca3af; border:1px solid #e5e7eb; transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                        <i class="material-symbols-rounded" style="font-size:20px;">person</i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-2 font-medium">{{ $employee->nama ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->nilaiTahunan->first()?->nilai ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->nik ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->status ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->division?->nama ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->team ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $employee->password ?? '-' }}</td>
                            <td class="px-3 py-2 sticky-col-right">
                                <div class="btn-group flex gap-1">
                                    <button type="button" class="btn btn-icon preview-photo-btn" title="Preview Foto">
                                        <i class="material-symbols-rounded text-purple-600">visibility</i>
                                    </button>
                                    <button type="button" class="btn btn-icon edit-row" title="Edit">
                                        <i class="material-symbols-rounded text-blue-600">edit_square</i>
                                    </button>
                                    <button type="button" class="btn btn-icon"
                                        onclick="showDeletePopup(this.closest('tr'))" title="Hapus">
                                        <i class="material-symbols-rounded delete-row btn-danger">delete</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($employees->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center py-6 text-gray-500">Tidak ada data karyawan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const table = document.getElementById('employees-table');
            const filters = table.querySelectorAll('.filter');

            // Jalankan filter saat halaman dimuat
            applyFilters();

            // Pasang event listener
            filters.forEach(filter => {
                filter.addEventListener('input', applyFilters);
                filter.addEventListener('change', applyFilters);
            });

            function applyFilters() {
                const rows = table.querySelectorAll('tbody tr:not(#no-data-row)');
                let visibleCount = 0;

                rows.forEach(row => {
                    let show = true;
                    filters.forEach(filter => {
                        const colIndex = parseInt(filter.dataset.column);
                        const filterValue = filter.value.toLowerCase().trim();
                        const isExact = filter.dataset.exact === "true";
                        const cell = row.cells[colIndex];

                        if (filterValue && cell) {
                            const cellText = cell.textContent.toLowerCase().trim();

                            if (isExact) {
                                // Perbandingan eksak untuk select
                                if (cellText !== filterValue) {
                                    show = false;
                                }
                            } else {
                                // Perbandingan parsial untuk input teks
                                if (!cellText.includes(filterValue)) {
                                    show = false;
                                }
                            }
                        }
                    });

                    row.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                // Update jumlah data
                document.getElementById('jumlah-data').textContent = `Jumlah Data: ${visibleCount}`;

                // Tampilkan pesan "tidak ada data" jika perlu
                const noDataRow = document.querySelector('#employees-table tbody tr:last-child[id="no-data-row"]');
                if (noDataRow) {
                    noDataRow.classList.toggle('hidden', visibleCount > 0);
                }
            }

            // ==== DELETE ====
            window.showDeletePopup = function(row) {
                const popup = document.getElementById('popupDelete');
                if (popup) {
                    popup.dataset.targetId = row.dataset.id;
                    popup.classList.replace('hidden', 'flex');
                }
            };

            function closeModal(modalId) {
                document.getElementById(modalId).classList.replace('flex', 'hidden');
            }
            window.closeModal = closeModal;

            function hideDeletePopup() {
                const popup = document.getElementById('popupDelete');
                if (popup) popup.classList.replace('flex', 'hidden');
            }

            document.getElementById('cancelDelete')?.addEventListener('click', hideDeletePopup);
            document.getElementById('confirmDelete')?.addEventListener('click', function() {
                const popup = document.getElementById('popupDelete');
                const id = popup?.dataset.targetId;
                if (!id || !csrfToken) return;

                fetch(`/iseki_rifa/public/employees/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            document.querySelector(`tr[data-id="${id}"]`)?.remove();
                            hideDeletePopup();
                            applyFilters();
                            updateRowNumbers();
                        } else {
                            alert('Gagal menghapus data');
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan saat menghapus'));
            });

            // ==== EDIT ====
            document.querySelectorAll('.edit-row').forEach(button => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    const cells = row.querySelectorAll('td');

                    const id = row.dataset.id;
                    const photoUrl = row.dataset.photo;
                    const nama = cells[2].textContent.trim();
                    const nik = cells[4].textContent.trim();
                    const status = cells[5].textContent.trim();
                    const divisi = cells[6].textContent.trim();
                    const team = cells[7].textContent.trim() === '-' ? '' : cells[7].textContent.trim();
                    const password = cells[8].textContent.trim();

                    document.getElementById('edit-employee-id').value = id;
                    document.getElementById('edit-employee-nama').value = nama;
                    document.getElementById('edit-employee-nik').value = nik;
                    document.getElementById('edit-employee-status').value = status;
                    document.getElementById('edit-employee-divisi').value = divisi;
                    document.getElementById('edit-employee-team').value = team;
                    document.getElementById('edit-employee-password').value = password;

                    const photoInput = document.getElementById('edit-employee-photo');
                    if (photoInput) photoInput.value = '';

                    const previewImg = document.getElementById('edit-photo-preview');
                    const placeholder = document.getElementById('edit-photo-placeholder');
                    if (photoUrl) {
                        previewImg.src = photoUrl;
                        previewImg.style.display = 'block';
                        previewImg.classList.remove('hidden');
                        if (placeholder) placeholder.style.display = 'none';
                    } else {
                        previewImg.src = '';
                        previewImg.style.display = 'none';
                        previewImg.classList.add('hidden');
                        if (placeholder) placeholder.style.display = 'flex';
                    }

                    document.getElementById('editEmployeeModal').classList.replace('hidden',
                        'flex');
                });
            });

            // Update nomor urut di kolom No setelah hapus baris
            function updateRowNumbers() {
                const numbers = document.querySelectorAll('#employees-table tbody tr .number');
                numbers.forEach((cell, index) => {
                    cell.textContent = index + 1;
                });
            }

            document.getElementById('editEmployeeForm')?.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('edit-employee-id').value;
                const form = document.getElementById('editEmployeeForm');
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                fetch(`/iseki_rifa/public/employees/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    })
                    .then(response => {
                        if (response.ok) {
                            location.reload();
                        } else {
                            alert('Gagal menyimpan perubahan');
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan saat menyimpan'));
            });

            // ==== PREVIEW FOTO MODAL ====
            let currentPhotoIndex = -1;
            let visiblePhotoRows = [];

            function getVisibleEmployeeRows() {
                return Array.from(document.querySelectorAll('#employees-table tbody tr')).filter(row => {
                    return row.style.display !== 'none' && row.dataset && row.dataset.id;
                });
            }

            function updatePhotoPreviewContent(index) {
                visiblePhotoRows = getVisibleEmployeeRows();
                if (visiblePhotoRows.length === 0 || index < 0 || index >= visiblePhotoRows.length) return;

                currentPhotoIndex = index;
                const row = visiblePhotoRows[currentPhotoIndex];

                const nama = row.dataset.nama || '-';
                const nik = row.dataset.nik || '-';
                const divisi = row.dataset.divisi || '-';
                const team = row.dataset.team || '-';
                const status = row.dataset.status || '-';
                const photoUrl = row.dataset.photo;

                document.getElementById('preview-employee-nama').textContent = nama;
                document.getElementById('preview-employee-nik').textContent = `NIK: ${nik}`;
                document.getElementById('preview-employee-divisi').textContent = `Divisi: ${divisi}`;
                document.getElementById('preview-employee-team').textContent = team;
                document.getElementById('preview-employee-status').textContent = status;
                document.getElementById('preview-counter').textContent = `Pegawai ${currentPhotoIndex + 1} dari ${visiblePhotoRows.length}`;

                const imgEl = document.getElementById('preview-employee-img');
                const placeholderEl = document.getElementById('preview-employee-placeholder');

                if (photoUrl && photoUrl.trim() !== '') {
                    imgEl.src = photoUrl;
                    imgEl.style.display = 'block';
                    placeholderEl.style.display = 'none';
                } else {
                    imgEl.src = '';
                    imgEl.style.display = 'none';
                    placeholderEl.style.display = 'flex';
                }

                // Update status tombol Navigasi
                const isFirst = currentPhotoIndex === 0;
                const isLast = currentPhotoIndex === visiblePhotoRows.length - 1;

                ['btn-prev-photo', 'btn-prev-photo-footer'].forEach(id => {
                    const btn = document.getElementById(id);
                    if (btn) {
                        btn.disabled = isFirst;
                        btn.style.opacity = isFirst ? '0.35' : '1';
                        btn.style.cursor = isFirst ? 'not-allowed' : 'pointer';
                    }
                });

                ['btn-next-photo', 'btn-next-photo-footer'].forEach(id => {
                    const btn = document.getElementById(id);
                    if (btn) {
                        btn.disabled = isLast;
                        btn.style.opacity = isLast ? '0.35' : '1';
                        btn.style.cursor = isLast ? 'not-allowed' : 'pointer';
                    }
                });
            }

            window.openPhotoPreviewModal = function(row) {
                visiblePhotoRows = getVisibleEmployeeRows();
                const index = visiblePhotoRows.indexOf(row);
                if (index !== -1) {
                    updatePhotoPreviewContent(index);
                    const modal = document.getElementById('previewEmployeePhotoModal');
                    if (modal) modal.classList.replace('hidden', 'flex');
                }
            };

            window.closePhotoPreviewModal = function() {
                const modal = document.getElementById('previewEmployeePhotoModal');
                if (modal) modal.classList.replace('flex', 'hidden');
            };

            window.navigatePhotoPreview = function(direction) {
                const nextIndex = currentPhotoIndex + direction;
                if (nextIndex >= 0 && nextIndex < visiblePhotoRows.length) {
                    updatePhotoPreviewContent(nextIndex);
                }
            };

            // Event listener klik pada foto atau tombol preview
            document.querySelectorAll('.preview-photo-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const row = btn.closest('tr');
                    if (row) openPhotoPreviewModal(row);
                });
            });

            // Shortcut Keyboard (Panah Kiri, Panah Kanan, Escape)
            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('previewEmployeePhotoModal');
                if (!modal || modal.classList.contains('hidden')) return;

                if (e.key === 'ArrowLeft') {
                    navigatePhotoPreview(-1);
                } else if (e.key === 'ArrowRight') {
                    navigatePhotoPreview(1);
                } else if (e.key === 'Escape') {
                    closePhotoPreviewModal();
                }
            });
        });
    </script>
@endsection
