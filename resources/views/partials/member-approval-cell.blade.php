<td class="status-member text-center h-full text-sm
    {{ is_null($absen->member_approved)
        ? 'bg-yellow'
        : ($absen->member_approved ? 'bg-success' : 'bg-red') }}">

    @if(session()->has('employee_login') && session('employee_login'))
        <form class="member-approval-form" data-id="{{ $absen->id }}">
            @csrf
            @method('PUT')

            @if (is_null($absen->member_approved))
                <div class="flex flex-col btn-group">
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
</td>
