<style>
    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 800px;
        max-height: 90%;
        overflow-y: auto;
    }

    /* Scroll khusus untuk tabel body */
    .table-wrapper {
        max-height: 300px; /* bisa disesuaikan */
        overflow-y: auto;
    }

    /* Biar header tetap di atas */
    .table-wrapper thead th {
        position: sticky;
        top: 0;
        background: #f3f3f3;
        z-index: 2;
    }
</style>

<div id="popupReplacement" class="overlay hidden">
    <div class="modal-content">
        <h2 class="modal-title">Daftar Replacement</h2>

        <div class="table-wrapper mt-2">
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">No</th>
                        <th class="border px-4 py-2">NIK Pengganti</th>
                        <th class="border px-4 py-2">Nama Pengganti</th>
                        <th class="border px-4 py-2">Production Number</th>
                        <th class="border px-4 py-2">Tanggal Diganti</th>
                    </tr>
                </thead>
                <tbody id="replacementTableBody">
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="button-group mt-4 flex gap-2 justify-end">
            <button class="btn btn-neutral" onclick="closeModal('popupReplacement')">Tutup</button>
        </div>
    </div>
</div>
