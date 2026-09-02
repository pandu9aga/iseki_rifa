@extends('layouts.app')

@section('title', 'Perizinan Baru')

@section('content')
    <main>
        @include('components.popupDelete')

        <h1 class="text-2xl font-bold">Perizinan Baru</h1>
        <form action="{{ route('reporting.nihil') }}" method="POST" class="inline-block">
            @csrf
            <button type="submit" class="btn btn-secondary" style="width: 150px;" title="Laporkan Nihil">
                <i class="material-symbols-rounded btn-primary align-middle">ad_off</i>
                Nihil
            </button>
        </form>
        <p class="text-sm flex w-full justify-end">Jumlah Data:&nbsp;<span
                id="jumlah-data">{{ old('nama') ? count(old('nama')) : 1 }}</span></p>

        <div class="text-sm" style="margin: 1px 0; margin-bottom: -15px;">
            Untuk Terlambat wajib mengisi jam masuk
        </div>
        <div class="text-sm" style="margin: 1px 0; margin-bottom: -15px;">
            Untuk Pulang Cepat wajib mengisi jam keluar
        </div>
        <div class="text-sm" style="margin: 1px 0; margin-bottom: -15px;">
            Untuk Izin Keluar wajib mengisi jam masuk & jam keluar
        </div>
        <div class="text-sm" style="margin: 1px 0;">
            Untuk Salah Fingerprint wajib mengisi jam masuk atau jam keluar
        </div>

        <form action="{{ route('reporting.store') }}" method="POST" id="izin-form" class="g-5">
            @csrf
            <section class="w-full container-table desktop-form-table">
                <table id="cuti-table" class="min-w-full table-auto border-collapse">
                    <thead class="table-head">
                        <tr>
                            <th class="table-cell w-4">No</th>
                            <th class="table-cell w-18">Nama</th>
                            <th class="table-cell w-18">Jenis Izin</th>
                            <th class="table-cell w-18">Keterangan</th>
                            <th class="table-cell w-10">Tanggal</th>
                            <th class="table-cell w-8">Jam Masuk</th>
                            <th class="table-cell w-8">Jam Keluar</th>
                            <th class="table-cell w-8">Status</th>
                            <th class="table-cell w-10">Divisi</th>
                            <th class="table-cell w-10">Tim</th>
                            <th class="table-cell w-4 sticky-col-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td class="number">1</td>
                            <td class="">
                                <select name="nama[]" class="form-control select2 nama w-full"
                                    data-placeholder="Pilih Pegawai" onchange="handleNamaEnter(event, this)"
                                    style="width: 100%">
                                    <option></option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="jenis_cuti[]" data-placeholder="Pilih jenis izin" class="select2 jenis_cuti"
                                    style="width: 100%">
                                    <option></option>
                                    <option value="Cuti">Cuti</option>
                                    <option value="Cuti Setengah Hari Pagi">Cuti Setengah Hari Pagi</option>
                                    <option value="Cuti Setengah Hari Siang">Cuti Setengah Hari Siang</option>
                                    <option value="Terlambat">Terlambat</option>
                                    <option value="Izin Keluar">Izin Keluar</option>
                                    <option value="Pulang Cepat">Pulang Cepat</option>
                                    <option value="Pulang Cepat Dengan Surat">Pulang Cepat Dengan Surat</option>
                                    <option value="Absen">Absen</option>
                                    <option value="Absen Setengah Hari Pagi">Absen Setengah Hari Pagi</option>
                                    <option value="Absen Setengah Hari Siang">Absen Setengah Hari Siang</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Cuti Khusus">Cuti Khusus</option>
                                    <option value="Serikat">Serikat</option>
                                    <option value="Salah Fingerprint">Salah Fingerprint</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="keterangan[]" />
                            </td>
                            <td>
                                <input type="date" name="tanggal[]" class="tanggal" value="{{ now()->format('Y-m-d') }}">
                            </td>
                            <td>
                                <input type="time" name="jam_masuk[]" class="jam_masuk">
                            </td>
                            <td>
                                <input type="time" name="jam_keluar[]" class="jam_keluar">
                            </td>
                            <td class="status"></td>
                            <td class="divisi"></td>
                            <td class="team"></td>
                            <td class="sticky-col-right">
                                <button type="button" class="btn btn-icon danger delete-row">
                                    <i class="material-symbols-rounded delete-row btn-danger">
                                        delete
                                    </i>
                                </button>
                            </td>
                        </tr>
                        <tr id="row-button" class="hover-none">
                            <td colspan="11">
                                <button type="button" id="add-row" class="btn btn-secondary">
                                    <i class="material-symbols-rounded btn-primary">
                                        add
                                    </i>
                                    Baris Baru
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- MOBILE CARD FORM -->
            <div class="mobile-form-cards" id="mobile-izin-form-cards"></div>
            <button type="button" id="mobile-izin-add-card" class="btn btn-secondary mobile-only" style="width:100%;margin-top:0.5rem;">
                <span class="material-symbols-rounded">add</span> Tambah Izin
            </button>

            <p id="error-text" class="text-red-500 hidden"></p>
            <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:0.75rem;">
                <button type="submit" id="submit-data" class="btn btn-primary" style="flex:1;min-width:120px;">Simpan</button>
            </div>
        </form>
    </main>

    <script>
        const tableBody = document.querySelector('#cuti-table tbody');
        const jumlahDataElement = document.getElementById('jumlah-data');

        // Fungsi untuk menghitung data
        function updateRowNumbers() {
            const rows = tableBody.querySelectorAll('tr:not(#row-button)');
            rows.forEach((row, index) => {
                const noCell = row.querySelector('td.number');
                if (noCell) noCell.textContent = index + 1;
            });
            updateJumlahData();
        }

        // Fungsi untuk update jumlah data
        function updateJumlahData() {
            const rows = tableBody.querySelectorAll('tr:not(#row-button)');
            jumlahDataElement.textContent = rows.length;
        }

        document.getElementById('izin-form').addEventListener('submit', function (e) {
            const errorText = document.getElementById('error-text');
            const namaInputs = document.querySelectorAll('select[name="nama[]"]');
            const jenisSelects = document.querySelectorAll('select[name="jenis_cuti[]"]');

            let isValid = true;

            // Cek apakah semua input nama terisi
            namaInputs.forEach((input, i) => {
                console.log(`NAMA ${i}:`, input.value); // 👈 debug
                if (!input.value || input.value === "") {
                    console.log(`-> NAMA BARIS ${i + 1} KOSONG`);
                    isValid = false;
                }
            });


            // Cek apakah semua jenis cuti terisi
            jenisSelects.forEach((select) => {
                if (!select.value.trim()) {
                    isValid = false;
                }
            });

            if (!isValid || namaInputs.length === 0 || jenisSelects.length === 0) {
                e.preventDefault();
                errorText.classList.remove('hidden');
                errorText.textContent = 'Data tidak boleh ada yang kosong. Pastikan kembali data setiap baris.';
            } else {
                errorText.classList.add('hidden');
                errorText.textContent = '';
            }
        });

        document.getElementById('izin-form').addEventListener('submit', function (e) {
            const rows = document.querySelectorAll('#cuti-table tbody tr:not(#row-button)');
            let isValid = true;
            let errorMessage = '';

            rows.forEach((row, index) => {
                const jenis = row.querySelector('select[name="jenis_cuti[]"]')?.value;
                const jamMasuk = row.querySelector('input[name="jam_masuk[]"]')?.value;
                const jamKeluar = row.querySelector('input[name="jam_keluar[]"]')?.value;

                // if (jenis === 'Terlambat' && !jamMasuk) {
                //     isValid = false;
                //     errorMessage = `Baris ${index + 1}: Jam masuk wajib diisi untuk Terlambat`;
                // }

                // if (jenis === 'Pulang Cepat' && !jamKeluar) {
                //     isValid = false;
                //     errorMessage = `Baris ${index + 1}: Jam keluar wajib diisi untuk Pulang Cepat`;
                // }

                // if (jenis === 'Izin Keluar' && (!jamMasuk || !jamKeluar)) {
                //     isValid = false;
                //     errorMessage = `Baris ${index + 1}: Jam masuk & keluar wajib diisi untuk Izin Keluar`;
                // }
            });

            if (!isValid) {
                e.preventDefault();
                const errorText = document.getElementById('error-text');
                errorText.textContent = errorMessage;
                errorText.classList.remove('hidden');
            }
        });

        // Fungsi untuk membuat baris baru
        function addRow() {
            const row = document.createElement('tr');
            row.innerHTML = `
                                        <td class="number"></td>
                                        <td>
                                            <select name="nama[]" class="form-control select2 nama" data-placeholder="Pilih Pegawai" onchange="handleNamaEnter(event, this)" style="width: 100%">
                                                <option></option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}">{{ $employee->nama }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="jenis_cuti[]" data-placeholder="Pilih jenis izin" class="select2 jenis_cuti" style="width: 100%">
                                                <option></option>
                                                <option value="Cuti">Cuti</option>
                                                <option value="Cuti Setengah Hari Pagi">Cuti Setengah Hari Pagi</option>
                                                <option value="Cuti Setengah Hari Siang">Cuti Setengah Hari Siang</option>
                                                <option value="Terlambat">Terlambat</option>
                                                <option value="Izin Keluar">Izin Keluar</option>
                                                <option value="Pulang Cepat">Pulang Cepat</option>
                                                <option value="Pulang Cepat Dengan Surat">Pulang Cepat Dengan Surat</option>
                                                <option value="Absen">Absen</option>
                                                <option value="Absen Setengah Hari Pagi">Absen Setengah Hari Pagi</option>
                                                <option value="Absen Setengah Hari Siang">Absen Setengah Hari Siang</option>
                                                <option value="Sakit">Sakit</option>
                                                <option value="Cuti Khusus">Cuti Khusus</option>
                                                <option value="Serikat">Serikat</option>
                                                <option value="Salah Fingerprint">Salah Fingerprint</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="keterangan[]"/>
                                        </td>
                                        <td>
                                            <input type="date" name="tanggal[]" value="{{ now()->format('Y-m-d') }}">
                                        </td>
                                        <td>
                                            <input type="time" name="jam_masuk[]" class="jam_masuk">
                                        </td>
                                        <td>
                                            <input type="time" name="jam_keluar[]" class="jam_keluar">
                                        </td>
                                        <td class="status"></td>
                                        <td class="divisi"></td>
                                        <td class="team"></td>
                                        <td class="sticky-col-right">
                                            <button type="button" class="btn btn-icon danger delete-row">
                                                <i class="material-symbols-rounded delete-row btn-danger">
                                                    delete
                                                </i>
                                            </button>
                                        </td>
                                    `;
            tableBody.insertBefore(row, document.getElementById('row-button'));
            window.updateRowNumbers();

            // Fokus ke row baru
            const inputNamaBaru = row.querySelector('input.nama');
            if (inputNamaBaru) {
                setTimeout(() => inputNamaBaru.focus(), 50);
            }

            $(row).find('.select2').select2();
        }

        document.getElementById('add-row').addEventListener('click', addRow);

        tableBody.addEventListener('keydown', function (e) {
            if (e.target.matches('input.nama') && e.key === 'Enter') {
                e.preventDefault();

                const currentRow = e.target.closest('tr');
                if (!currentRow) return;

                const nextRow = currentRow.nextElementSibling;
                if (nextRow && nextRow.id !== 'row-button') {
                    const nextInput = nextRow.querySelector('input.nama');
                    if (nextInput) {
                        nextInput.focus();
                        return;
                    }
                }

                addRow();
            }
        });

        tableBody.addEventListener('click', function (e) {
            if (e.target.closest('.delete-row')) {
                const row = e.target.closest('tr');
                if (row && row.id !== 'row-button') {
                    showDeletePopup(row);
                }
            }
        });

        function handleNamaEnter(event, input) {
            event.preventDefault();
            const employee_id = input.value;

            // API untuk dapat detail employee berdasarkan nama (tim, divisi)
            fetch("{{ route('employee.details') }}?employee_id=" + encodeURIComponent(employee_id))
                .then(res => res.json())
                .then(data => {
                    const row = input.closest('tr');
                    if (!row) return;

                    const statusTd = row.querySelector('td.status');
                    const divisiTd = row.querySelector('td.divisi');
                    const timTd = row.querySelector('td.team');

                    statusTd.textContent = data.status || '-';
                    divisiTd.textContent = data.divisi || '-';
                    timTd.textContent = data.team || '-';
                });
        }

        let rowToDelete = null;

        function showDeletePopup(row) {
            rowToDelete = row;
            document.getElementById('popupDelete').classList.replace('hidden', 'flex');
        }

        function hideDeletePopup() {
            rowToDelete = null;
            document.getElementById('popupDelete').classList.replace('flex', 'hidden');
        }

        document.getElementById('cancelDelete').addEventListener('click', hideDeletePopup);
        document.getElementById('confirmDelete').addEventListener('click', function () {
            if (rowToDelete) {
                rowToDelete.remove();
                updateRowNumbers();
            }
            hideDeletePopup();
        });

        $(document).on('change', '.jenis_cuti', function () {
            const row = $(this).closest('tr');
            const jenis = $(this).val();

            const jamMasuk = row.find('.jam_masuk');
            const jamKeluar = row.find('.jam_keluar');

            jamMasuk.prop('readonly', true).val('');
            jamKeluar.prop('readonly', true).val('');

            if (jenis === 'Terlambat') jamMasuk.prop('readonly', false);
            if (jenis === 'Pulang Cepat' || jenis === 'Pulang Cepat Dengan Surat') jamKeluar.prop('readonly', false);
            if (jenis === 'Izin Keluar' || jenis === 'Salah Fingerprint') {
                jamMasuk.prop('readonly', false);
                jamKeluar.prop('readonly', false);
            }
        });

        /* ==========================================================
           MOBILE CARD FORM — IZIN/ABSENSI
        ========================================================== */
        const izinMobileContainer = document.getElementById('mobile-izin-form-cards');
        const jenisIzinList = [
            '', 'Cuti', 'Cuti Setengah Hari Pagi', 'Cuti Setengah Hari Siang',
            'Terlambat', 'Izin Keluar', 'Pulang Cepat', 'Pulang Cepat Dengan Surat',
            'Absen', 'Absen Setengah Hari Pagi', 'Absen Setengah Hari Siang',
            'Sakit', 'Cuti Khusus', 'Serikat', 'Salah Fingerprint'
        ];
        const employeeListIzin = @json($employees->map(fn($e) => ['id' => $e->id, 'nama' => $e->nama]));
        let izinCardCount = 0;

        function buildIzinCard(index) {
            return `
            <div class="mobile-form-card" data-izin-card="${index}">
                <div class="card-number">${index + 1}</div>
                <div class="form-field">
                    <label>Nama Pegawai <span style="color:var(--danger)">*</span></label>
                    <select class="mic-nama" required>
                        <option value="">-- Pilih Pegawai --</option>
                        ${employeeListIzin.map(e => `<option value="${e.id}">${e.nama}</option>`).join('')}
                    </select>
                </div>
                <div class="form-field">
                    <label>Jenis Izin <span style="color:var(--danger)">*</span></label>
                    <select class="mic-jenis" required>
                        ${jenisIzinList.map(j => `<option value="${j}">${j || '-- Pilih Jenis Izin --'}</option>`).join('')}
                    </select>
                </div>
                <div class="form-field">
                    <label>Keterangan</label>
                    <input type="text" class="mic-keterangan" placeholder="Keterangan...">
                </div>
                <div class="form-field">
                    <label>Tanggal <span style="color:var(--danger)">*</span></label>
                    <input type="date" class="mic-tanggal" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="field-row">
                    <div class="form-field">
                        <label>Jam Masuk</label>
                        <input type="time" class="mic-jam-masuk">
                    </div>
                    <div class="form-field">
                        <label>Jam Keluar</label>
                        <input type="time" class="mic-jam-keluar">
                    </div>
                </div>
                ${index > 0 ? `<button type="button" class="btn btn-secondary mic-remove-btn" style="background:var(--danger-bg);color:var(--danger);border:1px solid var(--danger);">
                    <span class="material-symbols-rounded" style="font-size:1rem;">delete</span> Hapus
                </button>` : ''}
            </div>`;
        }

        function addIzinCard() {
            if (!izinMobileContainer) return;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = buildIzinCard(izinCardCount);
            const cardEl = wrapper.firstElementChild;
            izinMobileContainer.appendChild(cardEl);
            cardEl.querySelector('.mic-remove-btn')?.addEventListener('click', function() {
                cardEl.remove();
                izinMobileContainer.querySelectorAll('.mobile-form-card').forEach((c, i) => {
                    c.querySelector('.card-number').textContent = i + 1;
                });
            });
            izinCardCount++;
        }

        if (window.innerWidth <= 768) {
            addIzinCard();
        }

        document.getElementById('mobile-izin-add-card')?.addEventListener('click', addIzinCard);

        // On submit: sync mobile cards to hidden desktop table
        document.getElementById('izin-form')?.addEventListener('submit', function(e) {
            if (window.innerWidth > 768) return;

            const tbody = document.querySelector('#cuti-table tbody');
            tbody.querySelectorAll('tr:not(#row-button)').forEach(r => r.remove());

            document.querySelectorAll('#mobile-izin-form-cards .mobile-form-card').forEach((card, i) => {
                const namaId    = card.querySelector('.mic-nama')?.value    || '';
                const jenis     = card.querySelector('.mic-jenis')?.value   || '';
                const ket       = card.querySelector('.mic-keterangan')?.value || '';
                const tanggal   = card.querySelector('.mic-tanggal')?.value || '';
                const jamMasuk  = card.querySelector('.mic-jam-masuk')?.value || '';
                const jamKeluar = card.querySelector('.mic-jam-keluar')?.value || '';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="number">${i + 1}</td>
                    <td><input type="hidden" name="nama[]" value="${namaId}"></td>
                    <td><input type="hidden" name="jenis_cuti[]" value="${jenis}"></td>
                    <td><input type="hidden" name="keterangan[]" value="${ket}"></td>
                    <td><input type="hidden" name="tanggal[]" value="${tanggal}"></td>
                    <td><input type="hidden" name="jam_masuk[]" value="${jamMasuk}"></td>
                    <td><input type="hidden" name="jam_keluar[]" value="${jamKeluar}"></td>
                    <td class="status"></td>
                    <td class="divisi"></td>
                    <td class="team"></td>
                    <td></td>
                `;
                tbody.insertBefore(row, document.getElementById('row-button'));
            });
        });
    </script>
@endsection