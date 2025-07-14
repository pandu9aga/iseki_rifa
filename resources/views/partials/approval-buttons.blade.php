<form class="approval-form" data-id="{{ $absen->id }}">
    @csrf
    @method('PUT')
    @if (is_null($absen->is_approved))
    <div class="flex flex-col btn-group">
        <button type="button" data-value="1" class="btn bg-success text-sm rounded approve-btn">Setujui</button>
        <button type="button" data-value="0" class="btn bg-red text-sm rounded approve-btn">Tolak</button>
    </div>
    @else
    <button type="button" data-value="null" class="btn bg-red text-sm rounded approve-btn">Batalkan</button>
    @endif
</form>