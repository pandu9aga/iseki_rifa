<div id="dailyReportText" class="overlay hidden">
    <div class="modal-content" style="max-width:36rem;">
        <div class="modal-title">
            <i class="material-symbols-rounded" style="color:var(--primary);">description</i>
            Laporan Harian
        </div>

        <textarea id="dailyReportContent" class="modal-textarea" readonly></textarea>

        <div class="button-group">
            <button class="btn btn-primary" onclick="copyDailyReport(event)">
                <i class="material-symbols-rounded">content_paste</i>
                Salin
            </button>
            <button class="btn btn-neutral" onclick="closeModal('dailyReportText')">Tutup</button>
        </div>
    </div>
</div>
