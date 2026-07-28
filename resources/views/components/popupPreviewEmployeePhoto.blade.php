<div id="previewEmployeePhotoModal" class="overlay hidden" style="position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px);">
    <div class="modal-content" style="max-width:500px; width:92%; border-radius:16px; overflow:hidden; padding:0; background:#ffffff; box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.25);">

        <!-- Header Modal -->
        <div style="display:flex; align-items:center; justify-content:between; padding:1rem 1.25rem; border-bottom:1px solid #f3f4f6; background:#f9fafb;">
            <div style="display:flex; align-items:center; gap:0.625rem; flex:1;">
                <div style="width:36px; height:36px; border-radius:50%; background:var(--primary-light, #fde4ef); color:var(--primary, #ec057d); display:flex; align-items:center; justify-content:center;">
                    <i class="material-symbols-rounded" style="font-size:20px;">badge</i>
                </div>
                <div>
                    <h3 style="font-size:1rem; font-weight:700; color:#111827; margin:0; line-height:1.2;">Production Member</h3>
                    <p id="preview-counter" style="font-size:0.75rem; color:#6b7280; margin:2px 0 0 0;">Pegawai 1 dari 1</p>
                </div>
            </div>
            <button type="button" onclick="closePhotoPreviewModal()" style="background:none; border:none; width:32px; height:32px; border-radius:50%; cursor:pointer; color:#6b7280; display:flex; align-items:center; justify-content:center; transition:background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='none'">
                <i class="material-symbols-rounded">close</i>
            </button>
        </div>

        <!-- Display Foto / Preview Container -->
        <div style="position:relative; background:#111827; min-height:300px; max-height:380px; display:flex; align-items:center; justify-content:center; overflow:hidden;">
            <img id="preview-employee-img" src="" alt="Foto Pegawai" style="width:100%; height:320px; object-fit:contain; transition:all 0.3s ease;">

            <div id="preview-employee-placeholder" style="display:none; flex-direction:column; align-items:center; justify-content:center; color:#9ca3af; padding:3rem 1rem;">
                <div style="width:90px; height:90px; border-radius:50%; background:#1f2937; display:flex; align-items:center; justify-content:center; margin-bottom:0.75rem; border:2px dashed #374151;">
                    <i class="material-symbols-rounded" style="font-size:48px; color:#6b7280;">person</i>
                </div>
                <span style="font-size:0.875rem; color:#9ca3af; font-weight:500;">Tidak ada foto pegawai</span>
            </div>

            <!-- Tombol Navigasi Melayang di Atas Gambar -->
            <button type="button" id="btn-prev-photo" onclick="navigatePhotoPreview(-1)" title="Sebelumnya (Panah Kiri)"
                style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:44px; height:44px; border-radius:50%; background:rgba(0,0,0,0.5); color:#ffffff; border:1px solid rgba(255,255,255,0.2); cursor:pointer; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px); transition:all 0.2s;">
                <i class="material-symbols-rounded" style="font-size:28px;">chevron_left</i>
            </button>

            <button type="button" id="btn-next-photo" onclick="navigatePhotoPreview(1)" title="Berikutnya (Panah Kanan)"
                style="position:absolute; right:12px; top:50%; transform:translateY(-50%); width:44px; height:44px; border-radius:50%; background:rgba(0,0,0,0.5); color:#ffffff; border:1px solid rgba(255,255,255,0.2); cursor:pointer; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px); transition:all 0.2s;">
                <i class="material-symbols-rounded" style="font-size:28px;">chevron_right</i>
            </button>
        </div>

        <!-- Detail Informasi Pegawai (Nama, NIK, Divisi, dll) -->
        <div style="padding:1.25rem; background:#ffffff;">
            <h2 id="preview-employee-nama" style="font-size:1.25rem; font-weight:800; color:#111827; margin:0 0 0.5rem 0; line-height:1.3;">-</h2>

            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:1rem;">
                <span style="display:inline-flex; align-items:center; gap:0.25rem; font-size:0.75rem; font-weight:600; padding:0.35rem 0.65rem; border-radius:6px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                    <i class="material-symbols-rounded" style="font-size:16px;">badge</i>
                    <span id="preview-employee-nik">NIK: -</span>
                </span>

                <span style="display:inline-flex; align-items:center; gap:0.25rem; font-size:0.75rem; font-weight:600; padding:0.35rem 0.65rem; border-radius:6px; background:#fdf2f8; color:#be185d; border:1px solid #fbcfe8;">
                    <i class="material-symbols-rounded" style="font-size:16px;">domain</i>
                    <span id="preview-employee-divisi">Divisi: -</span>
                </span>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; padding-top:0.875rem; border-top:1px solid #f3f4f6; font-size:0.8125rem;">
                <div style="display:flex; align-items:center; gap:0.375rem; color:#4b5563;">
                    <i class="material-symbols-rounded" style="font-size:18px; color:#9ca3af;">groups</i>
                    <span style="color:#6b7280;">Tim:</span>
                    <strong id="preview-employee-team" style="color:#1f2937;">-</strong>
                </div>
                <div style="display:flex; align-items:center; gap:0.375rem; color:#4b5563;">
                    <i class="material-symbols-rounded" style="font-size:18px; color:#9ca3af;">fact_check</i>
                    <span style="color:#6b7280;">Status:</span>
                    <strong id="preview-employee-status" style="color:#1f2937;">-</strong>
                </div>
            </div>
        </div>

        <!-- Footer Tombol Navigasi Bawah -->
        <div style="padding:0.875rem 1.25rem; background:#f9fafb; border-top:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between;">
            <button type="button" id="btn-prev-photo-footer" onclick="navigatePhotoPreview(-1)" class="btn" style="display:flex; align-items:center; gap:0.375rem; padding:0.45rem 0.85rem; font-size:0.8125rem; background:#e5e7eb; color:#374151; border:none; border-radius:8px; cursor:pointer; font-weight:600;">
                <i class="material-symbols-rounded" style="font-size:18px;">arrow_back</i>
                Sebelumnya
            </button>

            <span style="font-size:0.75rem; color:#9ca3af; font-style:italic;">Panah &larr; &rarr; keyboard</span>

            <button type="button" id="btn-next-photo-footer" onclick="navigatePhotoPreview(1)" class="btn btn-primary" style="display:flex; align-items:center; gap:0.375rem; padding:0.45rem 0.85rem; font-size:0.8125rem; border:none; border-radius:8px; cursor:pointer; font-weight:600;">
                Berikutnya
                <i class="material-symbols-rounded" style="font-size:18px;">arrow_forward</i>
            </button>
        </div>
    </div>
</div>