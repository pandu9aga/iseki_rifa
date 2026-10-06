{{-- Modal Preview Dokumen Ijin --}}
<div id="previewDokumenModal" class="overlay hidden" style="position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.7); backdrop-filter:blur(6px);">
    <div class="modal-content" style="max-width:640px; width:94%; border-radius:16px; overflow:hidden; padding:0; background:#ffffff; box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.35); animation: dokumenModalIn 0.25s ease;">

        {{-- Header --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid #f3f4f6; background:#f9fafb;">
            <div style="display:flex; align-items:center; gap:0.625rem;">
                <div style="width:36px; height:36px; border-radius:50%; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center;">
                    <i class="material-symbols-rounded" style="font-size:20px;">description</i>
                </div>
                <div>
                    <h3 style="font-size:1rem; font-weight:700; color:#111827; margin:0; line-height:1.2;">Preview Dokumen Ijin</h3>
                    <p id="preview-dokumen-nama" style="font-size:0.75rem; color:#6b7280; margin:2px 0 0 0;">-</p>
                </div>
            </div>
            <button type="button" onclick="closeDokumenPreviewModal()" style="background:none; border:none; width:32px; height:32px; border-radius:50%; cursor:pointer; color:#6b7280; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='none'">
                <i class="material-symbols-rounded">close</i>
            </button>
        </div>

        {{-- Dokumen Display Area --}}
        <div style="padding:1.25rem; background:#ffffff;">
            {{-- Dokumen 1 --}}
            <div id="preview-dok1-section" style="margin-bottom:1rem;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                    <span style="font-size:0.8125rem; font-weight:700; color:#374151; display:flex; align-items:center; gap:0.375rem;">
                        <i class="material-symbols-rounded" style="font-size:16px; color:#2563eb;">image</i>
                        Dokumen 1 <span style="color:#e74c3c; font-size:0.7rem;">(Wajib)</span>
                    </span>
                    {{-- Tombol Edit (hanya untuk member) --}}
                    <label id="edit-dok1-btn" class="dokumen-edit-btn hidden" for="edit-dokumen-1-input" style="display:none; align-items:center; gap:0.25rem; font-size:0.75rem; font-weight:600; padding:0.3rem 0.65rem; border-radius:6px; background:#fef3c7; color:#92400e; border:1px solid #fcd34d; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                        <i class="material-symbols-rounded" style="font-size:14px;">edit</i>
                        Ganti
                    </label>
                    <input type="file" id="edit-dokumen-1-input" accept="image/jpeg,image/png,image/webp" class="hidden" style="display:none;">
                </div>
                <div id="preview-dok1-container" style="position:relative; background:#111827; border-radius:12px; overflow:hidden; min-height:200px; max-height:400px; display:flex; align-items:center; justify-content:center; cursor:zoom-in;" onclick="zoomDokumenImage(this.querySelector('img'))">
                    <img id="preview-dok1-img" src="" alt="Dokumen 1" style="width:100%; max-height:400px; object-fit:contain; transition:all 0.3s ease;">
                    <div id="preview-dok1-placeholder" style="display:none; flex-direction:column; align-items:center; justify-content:center; color:#9ca3af; padding:2rem;">
                        <i class="material-symbols-rounded" style="font-size:48px; color:#4b5563;">hide_image</i>
                        <span style="font-size:0.8125rem; margin-top:0.5rem;">Tidak ada dokumen 1</span>
                    </div>
                </div>
            </div>

            {{-- Dokumen 2 --}}
            <div id="preview-dok2-section">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
                    <span style="font-size:0.8125rem; font-weight:700; color:#374151; display:flex; align-items:center; gap:0.375rem;">
                        <i class="material-symbols-rounded" style="font-size:16px; color:#8b5cf6;">image</i>
                        Dokumen 2 <span style="color:#6b7280; font-size:0.7rem;">(Opsional)</span>
                    </span>
                    {{-- Tombol Edit (hanya untuk member) --}}
                    <label id="edit-dok2-btn" class="dokumen-edit-btn hidden" for="edit-dokumen-2-input" style="display:none; align-items:center; gap:0.25rem; font-size:0.75rem; font-weight:600; padding:0.3rem 0.65rem; border-radius:6px; background:#fef3c7; color:#92400e; border:1px solid #fcd34d; cursor:pointer; transition:all 0.2s;" onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                        <i class="material-symbols-rounded" style="font-size:14px;">edit</i>
                        Ganti
                    </label>
                    <input type="file" id="edit-dokumen-2-input" accept="image/jpeg,image/png,image/webp" class="hidden" style="display:none;">
                </div>
                <div id="preview-dok2-container" style="position:relative; background:#111827; border-radius:12px; overflow:hidden; min-height:200px; max-height:400px; display:flex; align-items:center; justify-content:center; cursor:zoom-in;" onclick="zoomDokumenImage(this.querySelector('img'))">
                    <img id="preview-dok2-img" src="" alt="Dokumen 2" style="width:100%; max-height:400px; object-fit:contain; transition:all 0.3s ease;">
                    <div id="preview-dok2-placeholder" style="display:none; flex-direction:column; align-items:center; justify-content:center; color:#9ca3af; padding:2rem;">
                        <i class="material-symbols-rounded" style="font-size:48px; color:#4b5563;">hide_image</i>
                        <span style="font-size:0.8125rem; margin-top:0.5rem;">Tidak ada dokumen 2</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div id="preview-dokumen-footer" style="padding:0.875rem 1.25rem; background:#f9fafb; border-top:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:0.75rem; color:#9ca3af; font-style:italic;">Klik gambar untuk memperbesar</span>
            <div style="display:flex; gap:0.5rem;">
                {{-- Tombol Simpan (hanya untuk member) --}}
                <button type="button" id="save-dokumen-btn" class="btn btn-primary hidden" onclick="saveDokumenEdit()" style="display:none; align-items:center; gap:0.375rem; padding:0.45rem 0.85rem; font-size:0.8125rem; border:none; border-radius:8px; cursor:pointer; font-weight:600;">
                    <i class="material-symbols-rounded" style="font-size:18px;">save</i>
                    Simpan Perubahan
                </button>
                <button type="button" onclick="closeDokumenPreviewModal()" class="btn" style="display:flex; align-items:center; gap:0.375rem; padding:0.45rem 0.85rem; font-size:0.8125rem; background:#e5e7eb; color:#374151; border:none; border-radius:8px; cursor:pointer; font-weight:600;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Zoom Gambar (Full Screen) --}}
<div id="zoomDokumenModal" class="hidden" style="position:fixed; inset:0; z-index:10000; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.9); cursor:zoom-out;" onclick="closeZoomDokumen()">
    <img id="zoom-dokumen-img" src="" alt="Zoom" style="max-width:95%; max-height:95%; object-fit:contain; border-radius:8px; box-shadow:0 0 40px rgba(0,0,0,0.5);">
    <button type="button" onclick="closeZoomDokumen()" style="position:absolute; top:1rem; right:1rem; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); width:40px; height:40px; border-radius:50%; cursor:pointer; color:#ffffff; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
        <i class="material-symbols-rounded" style="font-size:24px;">close</i>
    </button>
</div>

<style>
    @keyframes dokumenModalIn {
        from { transform: scale(0.95) translateY(10px); opacity: 0; }
        to { transform: scale(1) translateY(0); opacity: 1; }
    }
</style>
