@extends('layouts.app')

@section('content')
@php
    $isEmployee = !Auth::check() && session('employee_login');
    $currentEmp = $employees->first();
@endphp
<div class="container">
    <form action="{{ $isEmployee ? route('employee.lemburs.store') : route('lemburs.store') }}" method="POST" id="lembur-create-form">
        @csrf

        <h3 class="mb-3">Tambah Jadwal Lembur</h3>

        <!-- DESKTOP: WRAPPER SCROLL HORIZONTAL -->
        <div class="table-responsive desktop-form-table" style="overflow-x:auto;">
            <table class="table table-bordered" id="lembur-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Divisi</th>
                        <th>Tanggal</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Durasi</th>
                        <th>Pekerjaan</th>
                        <th>Makan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="number">1</td>
                        <td>
                            @if($isEmployee)
                                <input type="text" class="form-control" value="{{ $currentEmp?->nama ?? session('employee_user')->name }}" readonly style="background-color:#e9ecef; cursor:not-allowed;">
                                <input type="hidden" name="employee_id[]" value="{{ $currentEmp?->id }}">
                            @else
                                <select name="employee_id[]" class="form-control select2 employee-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}"
                                        data-division="{{ $emp->division?->nama ?? '-' }}">
                                        {{ $emp->nama }}
                                    </option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                        <td><input type="text" name="division[]" class="form-control division" value="{{ $isEmployee ? ($currentEmp?->division?->nama ?? '-') : '' }}" readonly></td>
                        <td><input type="date" name="tanggal_lembur[]" class="form-control" required></td>
                        <td><input type="time" name="jam_mulai[]" class="form-control"></td>
                        <td><input type="time" name="jam_selesai[]" class="form-control"></td>
                        <td>
                            <input type="number" name="durasi_lembur[]" class="form-control" placeholder="Durasi (jam)"
                                min="0" step="0.01" required>
                        </td>
                        <td>
                            <select name="keterangan_lembur[]" class="form-control" required>
                                <option value="">-- Pilih Pekerjaan --</option>
                                <option value="Produksi">Produksi</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Kaizen">Kaizen</option>
                                <option value="5S">5S</option>
                                <option value="Pekerjaan Leader/PIC Lembur">Pekerjaan Leader/PIC Lembur</option>
                            </select>
                        </td>
                        <td>
                            <select name="makan_lembur[]" class="form-control">
                                <option value="tidak">tidak</option>
                                <option value="ya">ya</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-row">Hapus</button>
                        </td>
                    </tr>

                    <tr id="row-button">
                        <td colspan="10">
                            <button type="button" id="add-row" class="btn btn-secondary">
                                <i class="material-symbols-rounded btn-primary">add</i> Baris Baru
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- END WRAPPER -->

        <!-- MOBILE: CARD FORM (hidden on desktop) -->
        <div class="mobile-form-cards" id="mobile-form-cards">
            <!-- Cards injected by JS -->
        </div>
        <button type="button" id="mobile-add-card" class="btn btn-secondary mobile-only" style="width:100%;margin-top:0.5rem;">
            <span class="material-symbols-rounded">add</span> Tambah Jadwal
        </button>

        <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:0.75rem;">
            <button type="submit" class="btn btn-primary" style="flex:1;min-width:120px;">Simpan</button>
            <a href="{{ $isEmployee ? route('employee.lemburs.index') : route('lemburs.index') }}" class="btn btn-secondary" style="flex:1;min-width:120px;">Batal</a>
        </div>
    </form>
</div>
<script>
    const isEmployee = @json($isEmployee);
    const currentEmpId = @json($currentEmp?->id ?? '');
    const currentEmpName = @json($currentEmp?->nama ?? (session('employee_user')->name ?? ''));
    const currentEmpDivision = @json($currentEmp?->division?->nama ?? '-');

    $(document).ready(function() {
        function initDivisionAutoFill(container) {
            container.find('.employee-select').on('select2:select', function(e) {
                var data = e.params.data;
                var division = data.element ? $(data.element).data('division') || '' : '';
                $(this).closest('tr').find('.division').val(division);
            });
        }

        initDivisionAutoFill($(document));

        const tableBody = document.querySelector('#lembur-table tbody');

        function addRow() {
            const row = document.createElement('tr');
            
            let nameColHtml = '';
            let divColHtml = '';
            if (isEmployee) {
                nameColHtml = `
                    <input type="text" class="form-control" value="${currentEmpName}" readonly style="background-color:#e9ecef; cursor:not-allowed;">
                    <input type="hidden" name="employee_id[]" value="${currentEmpId}">
                `;
                divColHtml = `<input type="text" name="division[]" class="form-control division" value="${currentEmpDivision}" readonly>`;
            } else {
                nameColHtml = `
                    <select name="employee_id[]" class="form-control select2 employee-select" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" data-division="{{ $emp->division?->nama ?? '-' }}">
                                {{ $emp->nama }}
                            </option>
                        @endforeach
                    </select>
                `;
                divColHtml = `<input type="text" name="division[]" class="form-control division" readonly>`;
            }

            row.innerHTML = `
            <td class="number"></td>
            <td>${nameColHtml}</td>
            <td>${divColHtml}</td>
            <td><input type="date" name="tanggal_lembur[]" class="form-control" required></td>
            <td><input type="time" name="jam_mulai[]" class="form-control"></td>
            <td><input type="time" name="jam_selesai[]" class="form-control"></td>
            <td>
                <input type="number" name="durasi_lembur[]" 
                    class="form-control" 
                    placeholder="Durasi (jam)" 
                    min="0" step="0.01" required>
            </td>
            <td>
                <select name="keterangan_lembur[]" class="form-control" required>
                    <option value="">-- Pilih Pekerjaan --</option>
                    <option value="Produksi">Produksi</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Kaizen">Kaizen</option>
                    <option value="5S">5S</option>
                    <option value="Pekerjaan Leader/PIC Lembur">Pekerjaan Leader/PIC Lembur</option>
                </select>
            </td>
            <td>
                <select name="makan_lembur[]" class="form-control">
                    <option value="tidak">tidak</option>
                    <option value="ya">ya</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-row">Hapus</button>
            </td>
        `;
            tableBody.insertBefore(row, document.getElementById('row-button'));

            if (!isEmployee) {
                $(row).find('.select2').select2();
                initDivisionAutoFill($(row));
            }

            updateRowNumbers();
        }

        $('#add-row').click(addRow);

        $(document).on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
            updateRowNumbers();
            syncMobileFromDesktop();
        });

        function updateRowNumbers() {
            tableBody.querySelectorAll('tr:not(#row-button)').forEach((row, index) => {
                row.querySelector('td.number').textContent = index + 1;
            });
        }
    });

    /* ==========================================================
       MOBILE CARD FORM LOGIC
    ========================================================== */
    const employeesData = @json($employees->map(fn($e) => ['id' => $e->id, 'nama' => $e->nama, 'division' => $e->division?->nama ?? '-']));
    const mobileCards = document.getElementById('mobile-form-cards');
    const pekerjaan = [
        { value: '', label: '-- Pilih Pekerjaan --' },
        { value: 'Produksi', label: 'Produksi' },
        { value: 'Maintenance', label: 'Maintenance' },
        { value: 'Kaizen', label: 'Kaizen' },
        { value: '5S', label: '5S' },
        { value: 'Pekerjaan Leader/PIC Lembur', label: 'Pekerjaan Leader/PIC Lembur' },
    ];

    function buildPekerjaanOptions(selected = '') {
        return pekerjaan.map(p => `<option value="${p.value}" ${p.value === selected ? 'selected' : ''}>${p.label}</option>`).join('');
    }

    function buildMobileCard(index, data = {}) {
        const nameField = isEmployee
            ? `<div class="form-field">
                <label>Nama</label>
                <input type="text" value="${currentEmpName}" readonly>
               </div>`
            : `<div class="form-field">
                <label>Nama Karyawan</label>
                <select class="mc-employee" data-idx="${index}">
                    <option value="">-- Pilih Karyawan --</option>
                    ${employeesData.map(e => `<option value="${e.id}" data-division="${e.division}" ${data.empId == e.id ? 'selected' : ''}>${e.nama}</option>`).join('')}
                </select>
               </div>`;

        return `
        <div class="mobile-form-card" data-card-idx="${index}">
            <div class="card-number">${index + 1}</div>
            ${nameField}
            <div class="form-field">
                <label>Divisi</label>
                <input type="text" class="mc-division" value="${isEmployee ? currentEmpDivision : (data.division || '')}" readonly>
            </div>
            <div class="form-field">
                <label>Tanggal <span style="color:var(--danger)">*</span></label>
                <input type="date" class="mc-tanggal" value="${data.tanggal || ''}" required>
            </div>
            <div class="field-row">
                <div class="form-field">
                    <label>Jam Mulai</label>
                    <input type="time" class="mc-jam-mulai" value="${data.jamMulai || ''}">
                </div>
                <div class="form-field">
                    <label>Jam Selesai</label>
                    <input type="time" class="mc-jam-selesai" value="${data.jamSelesai || ''}">
                </div>
            </div>
            <div class="form-field">
                <label>Durasi (jam) <span style="color:var(--danger)">*</span></label>
                <input type="number" class="mc-durasi" value="${data.durasi || ''}" min="0" step="0.01" placeholder="mis: 3.5" required>
            </div>
            <div class="form-field">
                <label>Pekerjaan <span style="color:var(--danger)">*</span></label>
                <select class="mc-pekerjaan" required>
                    ${buildPekerjaanOptions(data.pekerjaan || '')}
                </select>
            </div>
            <div class="form-field">
                <label>Makan</label>
                <select class="mc-makan">
                    <option value="tidak" ${(data.makan || 'tidak') === 'tidak' ? 'selected' : ''}>Tidak</option>
                    <option value="ya" ${data.makan === 'ya' ? 'selected' : ''}>Ya</option>
                </select>
            </div>
            ${index > 0 ? `<button type="button" class="btn btn-secondary card-remove-btn" style="background:var(--danger-bg);color:var(--danger);border:1px solid var(--danger);">
                <span class="material-symbols-rounded" style="font-size:1rem;">delete</span> Hapus
            </button>` : ''}
        </div>`;
    }

    let mobileCardCount = 0;

    function addMobileCard(data = {}) {
        const card = document.createElement('div');
        card.innerHTML = buildMobileCard(mobileCardCount, data);
        const cardEl = card.firstElementChild;
        mobileCards.appendChild(cardEl);

        // Division autofill
        cardEl.querySelector('.mc-employee')?.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const division = selected?.dataset.division || '';
            cardEl.querySelector('.mc-division').value = division;
        });

        // Remove card
        cardEl.querySelector('.card-remove-btn')?.addEventListener('click', function() {
            cardEl.remove();
            // Renumber cards
            mobileCards.querySelectorAll('.mobile-form-card').forEach((c, i) => {
                c.querySelector('.card-number').textContent = i + 1;
            });
        });

        mobileCardCount++;
    }

    // Initialize first card on mobile
    if (window.innerWidth <= 768) {
        addMobileCard();
    }

    document.getElementById('mobile-add-card')?.addEventListener('click', () => addMobileCard());

    // On form submit: sync mobile cards to hidden desktop table form inputs
    document.getElementById('lembur-create-form')?.addEventListener('submit', function(e) {
        if (window.innerWidth > 768) return; // desktop handles itself

        // Remove existing desktop rows
        const tbody = document.querySelector('#lembur-table tbody');
        tbody.querySelectorAll('tr:not(#row-button)').forEach(r => r.remove());

        // Build hidden rows from mobile cards
        document.querySelectorAll('.mobile-form-card').forEach((card, i) => {
            const empSelect = card.querySelector('.mc-employee');
            const empId = isEmployee ? currentEmpId : (empSelect?.value || '');
            const division = card.querySelector('.mc-division')?.value || '';
            const tanggal = card.querySelector('.mc-tanggal')?.value || '';
            const jamMulai = card.querySelector('.mc-jam-mulai')?.value || '';
            const jamSelesai = card.querySelector('.mc-jam-selesai')?.value || '';
            const durasi = card.querySelector('.mc-durasi')?.value || '';
            const keterangan = card.querySelector('.mc-pekerjaan')?.value || '';
            const makan = card.querySelector('.mc-makan')?.value || 'tidak';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="number">${i + 1}</td>
                <td>
                    ${isEmployee ? `<input type="hidden" name="employee_id[]" value="${empId}">` : `<input type="hidden" name="employee_id[]" value="${empId}">`}
                </td>
                <td><input type="hidden" name="division[]" value="${division}"></td>
                <td><input type="hidden" name="tanggal_lembur[]" value="${tanggal}"></td>
                <td><input type="hidden" name="jam_mulai[]" value="${jamMulai}"></td>
                <td><input type="hidden" name="jam_selesai[]" value="${jamSelesai}"></td>
                <td><input type="hidden" name="durasi_lembur[]" value="${durasi}"></td>
                <td><input type="hidden" name="keterangan_lembur[]" value="${keterangan}"></td>
                <td><input type="hidden" name="makan_lembur[]" value="${makan}"></td>
            `;
            tbody.insertBefore(row, document.getElementById('row-button'));
        });
    });

    function syncMobileFromDesktop() {
        // Stub: desktop changes are authoritative; mobile form is rebuild only on add
    }
</script>
@endsection