@php
    $kategoriWajibDokumen = ['CK', 'S'];
    $isPulangCepatDenganSurat = $absen->kategori === 'P' && str_starts_with($absen->keterangan ?? '', 'Dengan Surat');
    $butuhDokumen = in_array($absen->kategori, $kategoriWajibDokumen) || $isPulangCepatDenganSurat;
@endphp

@if($butuhDokumen)
    <div class="dokumen-cell" data-absen-id="{{ $absen->id }}" data-butuh-dokumen="1">
        @if(is_null($absen->member_approved) && session()->has('employee_login') && session('employee_login'))
            {{-- Belum disetujui: tampilkan slot upload --}}
            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                {{-- Slot Dokumen 1 --}}
                <div class="dokumen-slot" data-field="dokumen_1">
                    <label class="dokumen-label" for="dokumen_1_{{ $absen->id }}" title="Pilih Dokumen 1">
                        <div class="dokumen-placeholder" id="placeholder_dokumen_1_{{ $absen->id }}">
                            <i class="material-symbols-rounded" style="font-size: 22px; color: #888;">add_photo_alternate</i>
                            <span class="text-xs" style="color:#e74c3c; font-weight:600;">Dok 1 *</span>
                        </div>
                        <img class="dokumen-preview hidden" id="preview_dokumen_1_{{ $absen->id }}" alt="Dok 1">
                    </label>
                    <input type="file" name="dokumen_1" id="dokumen_1_{{ $absen->id }}"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden dokumen-input"
                        data-target="preview_dokumen_1_{{ $absen->id }}"
                        data-placeholder="placeholder_dokumen_1_{{ $absen->id }}">
                </div>
                {{-- Slot Dokumen 2 --}}
                <div class="dokumen-slot" data-field="dokumen_2">
                    <label class="dokumen-label" for="dokumen_2_{{ $absen->id }}" title="Pilih Dokumen 2 (opsional)">
                        <div class="dokumen-placeholder" id="placeholder_dokumen_2_{{ $absen->id }}">
                            <i class="material-symbols-rounded" style="font-size: 22px; color: #888;">add_photo_alternate</i>
                            <span class="text-xs" style="color:#888;">Dok 2</span>
                        </div>
                        <img class="dokumen-preview hidden" id="preview_dokumen_2_{{ $absen->id }}" alt="Dok 2">
                    </label>
                    <input type="file" name="dokumen_2" id="dokumen_2_{{ $absen->id }}"
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden dokumen-input"
                        data-target="preview_dokumen_2_{{ $absen->id }}"
                        data-placeholder="placeholder_dokumen_2_{{ $absen->id }}">
                </div>
            </div>
        @elseif($absen->dokumen_1 || $absen->dokumen_2)
            {{-- Sudah ada dokumen: tampilkan thumbnail klik untuk preview modal --}}
            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                @if($absen->dokumen_1)
                    <img src="{{ asset('dokumen_absensi/' . $absen->dokumen_1) }}" alt="Dok 1" class="dokumen-thumb dokumen-preview-btn"
                        title="Klik untuk preview Dokumen 1"
                        data-absen-id="{{ $absen->id }}"
                        data-nama="{{ $absen->employee?->nama ?? '-' }}"
                        data-dok1="{{ asset('dokumen_absensi/' . $absen->dokumen_1) }}"
                        data-dok2="{{ $absen->dokumen_2 ? asset('dokumen_absensi/' . $absen->dokumen_2) : '' }}"
                        data-can-edit="{{ (session()->has('employee_login') && session('employee_login')) ? '1' : '0' }}"
                        data-member-approved="{{ is_null($absen->member_approved) ? 'null' : ($absen->member_approved ? '1' : '0') }}">
                @endif
                @if($absen->dokumen_2)
                    <img src="{{ asset('dokumen_absensi/' . $absen->dokumen_2) }}" alt="Dok 2" class="dokumen-thumb dokumen-preview-btn"
                        title="Klik untuk preview Dokumen 2"
                        data-absen-id="{{ $absen->id }}"
                        data-nama="{{ $absen->employee?->nama ?? '-' }}"
                        data-dok1="{{ $absen->dokumen_1 ? asset('dokumen_absensi/' . $absen->dokumen_1) : '' }}"
                        data-dok2="{{ asset('dokumen_absensi/' . $absen->dokumen_2) }}"
                        data-can-edit="{{ (session()->has('employee_login') && session('employee_login')) ? '1' : '0' }}"
                        data-member-approved="{{ is_null($absen->member_approved) ? 'null' : ($absen->member_approved ? '1' : '0') }}">
                @endif
            </div>
        @else
            <span class="text-xs" style="color:#999;">Belum ada</span>
        @endif
    </div>
@else
    <div class="dokumen-cell" data-absen-id="{{ $absen->id }}" data-butuh-dokumen="0">
        <span class="text-xs" style="color:#ccc;">—</span>
    </div>
@endif
