<div id="dailyReportText" class="overlay hidden">
    <div class="modal-content">
        <h2 class="modal-title">Laporan Harian</h2>

        <textarea id="dailyReportContent"
            class="modal-textarea"
            readonly></textarea>

        <div class="button-group">
            <button class="btn btn-primary" onclick="copyDailyReport(event)">
                Salin
                <i class="material-symbols-rounded">
                    content_paste
                </i>
            </button>
            <button class="btn btn-neutral" onclick="closeModal('dailyReportText')">Tutup</button>
        </div>
    </div>
</div>
