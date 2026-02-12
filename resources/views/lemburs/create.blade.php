@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('lemburs.store') }}" method="POST">
        @csrf

        <h3 class="mb-3">Tambah Jadwal Lembur</h3>

        <!-- WRAPPER SCROLL HORIZONTAL -->
        <div class="table-responsive" style="overflow-x:auto;">
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
                            <select name="employee_id[]" class="form-control select2 employee-select" required>
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    data-division="{{ $emp->division->nama ?? '-' }}">
                                    {{ $emp->nama }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="division[]" class="form-control division" readonly></td>
                        <td><input type="date" name="tanggal_lembur[]" class="form-control" required></td>
                        <td><input type="time" name="jam_mulai[]" class="form-control"></td>
                        <td><input type="time" name="jam_selesai[]" class="form-control"></td>
                        <td>
                            <input type="number" name="durasi_lembur[]" class="form-control" placeholder="Durasi (jam)"
                                min="0" step="0.01" required>
                        </td>
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

        <button type="submit" class="btn btn-primary mt-2">Simpan</button>
        <a href="{{ route('lemburs.index') }}" class="btn btn-secondary mt-2">Batal</a>
    </form>
</div>
<script>
    $(document).ready(function() {
        // Inisialisasi select2 awal
        $('.select2').select2();

        // Fungsi update divisi saat pilih karyawan
        $(document).on('change', '.employee-select', function() {
            let division = $(this).find('option:selected').data('division') || '';
            $(this).closest('tr').find('.division').val(division);
        });

        const tableBody = document.querySelector('#lembur-table tbody');

        // Fungsi tambah baris baru
        function addRow() {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td class="number"></td>
            <td>
                <select name="employee_id[]" class="form-control select2 employee-select" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" data-division="{{ $emp->division->nama ?? '-' }}">
                            {{ $emp->nama }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td><input type="text" name="division[]" class="form-control division" readonly></td>
            <td><input type="date" name="tanggal_lembur[]" class="form-control" required></td>
            <td><input type="time" name="jam_mulai[]" class="form-control"></td>
            <td><input type="time" name="jam_selesai[]" class="form-control"></td>
            <td>
                <input type="number" name="durasi_lembur[]" 
                    class="form-control" 
                    placeholder="Durasi (jam)" 
                    min="0" step="0.1" required>
            </td>            <td>
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

            // Inisialisasi select2 di baris baru
            $(row).find('.select2').select2();

            updateRowNumbers();
        }

        // Event tombol tambah baris
        $('#add-row').click(addRow);

        // Event tombol hapus baris
        $(document).on('click', '.delete-row', function() {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });

        // Update nomor baris
        function updateRowNumbers() {
            tableBody.querySelectorAll('tr:not(#row-button)').forEach((row, index) => {
                row.querySelector('td.number').textContent = index + 1;
            });
        }
    });
</script>
@endsection