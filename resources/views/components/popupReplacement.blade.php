<div id="popupReplacement" class="overlay hidden">
    <div class="modal-content" style="max-width:800px;max-height:90vh;">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--primary);">swap_horiz</i>
            Daftar Replacement
        </div>

        <div class="table-scroll-wrapper" style="max-height:300px;border-radius:var(--radius-sm);">
            <table class="w-full">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK Pengganti</th>
                        <th>Nama Pengganti</th>
                        <th>Production Number</th>
                        <th>Tanggal Diganti</th>
                    </tr>
                </thead>
                <tbody id="replacementTableBody">
                    <tr>
                        <td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="button-group">
            <button class="btn btn-neutral" onclick="closeModal('popupReplacement')">Tutup</button>
        </div>
    </div>
</div>
