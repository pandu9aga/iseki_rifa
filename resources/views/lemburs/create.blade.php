@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('lemburs.store') }}" method="POST">
        @csrf

        <br>
        <h3 class="mb-3">Tambah Jadwal Lembur</h3>

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
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="number">1</td>
                    <td>
                        <select name="employee_id" id="employee_id" class="form-control select2" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}" data-division="{{ $emp->division->nama ?? '-' }}">
                                    {{ $emp->nama }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" id="division" name="division" class="form-control" readonly></td>
                    <td><input type="date" name="tanggal_lembur" class="form-control" required></td>
                    <td><input type="time" name="jam_mulai" class="form-control"></td>
                    <td><input type="time" name="jam_selesai" class="form-control"></td>
                    <td><input type="text" name="durasi_lembur" class="form-control" placeholder="Durasi jam"></td>
                    <td><textarea name="keterangan_lembur" class="form-control" placeholder="Isi pekerjaan"></textarea></td>
                    {{-- <td><input type="text" name="makan_lembur" class="form-control" placeholder="Ya/Tidak (Opsional)"></td> --}}
                    <td>
                        <select name="makan_lembur" class="form-control" required>
                            <option value="tidak">tidak</option>
                            <option value="ya">ya</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <br>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('lemburs.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#employee_id').select2();

        // Event ketika pilihan berubah di Select2
        $('#employee_id').on('select2:select', function(e) {
            let data = e.params.data; 
            // Ambil attribute division dari option asli
            let division = $(e.target).find("option[value='" + data.id + "']").data('division');
            $('#division').val(division || '');
        });
    });
</script>
@endsection
