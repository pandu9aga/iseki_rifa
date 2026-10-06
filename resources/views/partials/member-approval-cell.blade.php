@php
    $kategoriWajibDokumen = ['CK', 'S'];
    $isPulangCepatDenganSurat = $absen->kategori === 'P' && str_starts_with($absen->keterangan ?? '', 'Dengan Surat');
    $butuhDokumen = in_array($absen->kategori, $kategoriWajibDokumen) || $isPulangCepatDenganSurat;
@endphp

@if(session()->has('employee_login') && session('employee_login'))
    <form class="member-approval-form" data-id="{{ $absen->id }}" data-butuh-dokumen="{{ $butuhDokumen ? '1' : '0' }}">
        @csrf
        @method('PUT')

        @if (is_null($absen->member_approved))
            <div class="flex btn-group">
                <button type="button" data-value="1"
                    class="btn bg-success text-sm rounded member-approve-btn">
                    Setujui
                </button>
                <button type="button" data-value="0"
                    class="btn bg-red text-sm rounded member-approve-btn">
                    Tolak
                </button>
            </div>
        @else
            <button type="button" data-value="null"
                class="btn text-sm rounded member-approve-btn
                {{ is_null($absen->member_approved)
                    ? 'bg-yellow'
                    : ($absen->member_approved ? 'bg-success' : 'bg-red') }}">
                Batalkan
            </button>
        @endif
    </form>
@else
    <span>
        {{ is_null($absen->member_approved)
            ? 'Menunggu Persetujuan'
            : ($absen->member_approved ? 'Disetujui' : 'Ditolak') }}
    </span>
@endif
