function filterCutiTable() {
    const filterNama = document.getElementById('filter-nama').value.toLowerCase();
    const filterJenis = document.getElementById('filter-jenis').value.toLowerCase();
    const filterTanggal = formatTanggalKeYMD(document.getElementById('filter-tanggal').value.toLowerCase());
    const filterKeterangan = document.getElementById('filter-keterangan').value.toLowerCase();
    const filterApprovalStatus = document.getElementById('filter-approval-status').value.toLowerCase();
    const filterStatus = document.getElementById('filter-status').value.toLowerCase();
    const filterDivisi = document.getElementById('filter-divisi').value.toLowerCase();
    const filterTeam = document.getElementById('filter-team').value.toLowerCase();

    const rows = document.querySelectorAll('#cuti-table tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.id === 'no-data-row') return;
        const isWithAuth = row.cells.length === 11;

        const nama = row.cells[1].textContent.toLowerCase();
        const jenisCuti = row.cells[2].textContent.toLowerCase();
        const tanggal = formatTanggalKeYMD(row.cells[3].textContent.toLowerCase());
        const keterangan = row.cells[4].textContent.toLowerCase();
        const approval_status = row.cells[5].textContent.toLowerCase();
        const status = row.cells[isWithAuth ? 7 : 6].textContent.toLowerCase();
        const divisi = row.cells[isWithAuth ? 8 : 7].textContent.toLowerCase();
        const team = row.cells[isWithAuth ? 9 : 8].textContent.toLowerCase();

        if (
            nama.includes(filterNama) &&
            jenisCuti.includes(filterJenis) &&
            tanggal.includes(filterTanggal) &&
            keterangan.includes(filterKeterangan) &&
            approval_status.includes(filterApprovalStatus) &&
            status.includes(filterStatus) &&
            divisi.includes(filterDivisi) &&
            team.includes(filterTeam)
        ) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update nomor urut setelah filter
    let nomor = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none' && row.id !== 'no-data-row') {
            row.cells[0].textContent = nomor++;
        }
    });

    updateJumlahData('cuti-table', 'jumlah-data');
    toggleNoDataRow(visibleCount);
}

function filterEmployeeTable() {
    const filterNama = document.getElementById('filter-nama').value.toLowerCase();
    const filterStatus = document.getElementById('filter-status').value.toLowerCase();
    const filterDivisi = document.getElementById('filter-divisi').value.toLowerCase();
    const filterTeam = document.getElementById('filter-team').value.toLowerCase();

    const rows = document.querySelectorAll('#employees-table tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.id === 'no-data-row') return;

        const nama = row.cells[1].textContent.toLowerCase();
        const status = row.cells[2].textContent.toLowerCase();
        const divisi = row.cells[3].textContent.toLowerCase();
        const team = row.cells[4].textContent.toLowerCase();

        if (
            nama.includes(filterNama) &&
            status.includes(filterStatus) &&
            divisi.includes(filterDivisi) &&
            team.includes(filterTeam)
        ) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update nomor urut setelah filter
    let nomor = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none' && row.id !== 'no-data-row') {
            row.cells[0].textContent = nomor++;
        }
    });

    updateJumlahData('employees-table', 'jumlah-data');
    toggleNoDataRow(visibleCount);
}

function filterUsersTable() {
    const filterType = document.getElementById('filter-type').value.toLowerCase();
    const filterNama = document.getElementById('filter-nama').value.toLowerCase();
    const filterUsername = document.getElementById('filter-username').value.toLowerCase();
    const filterDivisi = document.getElementById('filter-divisi').value.toLowerCase();
    const filterTeam = document.getElementById('filter-team').value.toLowerCase();

    const rows = document.querySelectorAll('#users-table tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.id === 'no-data-row') return;

        const type = row.cells[1].textContent.toLowerCase();
        const nama = row.cells[2].textContent.toLowerCase();
        const username = row.cells[3].textContent.toLowerCase();
        const divisi = row.cells[4].textContent.toLowerCase();
        const team = row.cells[5].textContent.toLowerCase();

        if (
            type.includes(filterType) &&
            nama.includes(filterNama) &&
            username.includes(filterUsername) &&
            divisi.includes(filterDivisi) &&
            team.includes(filterTeam)
        ) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update nomor urut setelah filter
    let nomor = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none' && row.id !== 'no-data-row') {
            row.cells[0].textContent = nomor++;
        }
    });

    updateJumlahData('users-table', 'jumlah-data');
    toggleNoDataRow(visibleCount);
}

function filterDatesTable() {
    const filterTanggal = document.getElementById('filter-tanggal').value.toLowerCase();
    const filterJenis = document.getElementById('filter-jenis').value.toLowerCase();

    const rows = document.querySelectorAll('#dates-table tbody tr');
    let visibleCount = 0;

    rows.forEach(row => {
        if (row.id === 'no-data-row') return;

        const tanggal = row.cells[1].textContent.toLowerCase();
        const jenis = row.cells[2].textContent.toLowerCase();

        if (
            tanggal.includes(filterTanggal) &&
            jenis.includes(filterJenis)
        ) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Update nomor urut setelah filter
    let nomor = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none' && row.id !== 'no-data-row') {
            row.cells[0].textContent = nomor++;
        }
    });

    updateJumlahData('dates-table', 'jumlah-data');
    toggleNoDataRow(visibleCount);
}

function setupFilters() {
    try {
        const isCutiTable = document.getElementById('cuti-table') !== null;
        const isEmployeeTable = document.getElementById('employees-table') !== null;
        const isUsersTable = document.getElementById('users-table') !== null;
        const isDatesTable = document.getElementById('dates-table') !== null;

        if (isCutiTable) {
            document.getElementById('filter-nama').addEventListener('input', filterCutiTable);
            document.getElementById('filter-jenis').addEventListener('change', filterCutiTable);
            document.getElementById('filter-tanggal').addEventListener('change', filterCutiTable);
            document.getElementById('filter-keterangan').addEventListener('input', filterCutiTable);
            document.getElementById('filter-approval-status').addEventListener('change', filterCutiTable);
            document.getElementById('filter-status').addEventListener('change', filterCutiTable);
            document.getElementById('filter-divisi').addEventListener('change', filterCutiTable);
            document.getElementById('filter-team').addEventListener('input', filterCutiTable);
        }

        if (isEmployeeTable) {
            document.getElementById('filter-nama').addEventListener('input', filterEmployeeTable);
            document.getElementById('filter-status').addEventListener('change', filterEmployeeTable);
            document.getElementById('filter-divisi').addEventListener('change', filterEmployeeTable);
            document.getElementById('filter-team').addEventListener('input', filterEmployeeTable);
        }

        if (isUsersTable) {
            document.getElementById('filter-type').addEventListener('change', filterUsersTable);
            document.getElementById('filter-nama').addEventListener('input', filterUsersTable);
            document.getElementById('filter-username').addEventListener('input', filterUsersTable);
            document.getElementById('filter-divisi').addEventListener('change', filterUsersTable);
            document.getElementById('filter-team').addEventListener('change', filterUsersTable);
        }

        if (isDatesTable) {
            document.getElementById('filter-tanggal').addEventListener('change', filterDatesTable);
            document.getElementById('filter-jenis').addEventListener('change', filterDatesTable);
        }

        $('.select2').on('change', () => setTimeout(() => {
            if (isCutiTable) filterCutiTable();
            else if (isEmployeeTable) filterEmployeeTable();
            else if (isUsersTable) filterUsersTable();
            else if (isDatesTable) filterDatesTable();
        }, 0));

        return true;
    } catch (error) {
        return false;
    }
}

function toggleNoDataRow(visibleCount) {
    const noDataRow = document.getElementById('no-data-row');
    if (noDataRow) {
        noDataRow.classList.toggle('hidden', visibleCount !== 0);
    }
}

function formatTanggalKeYMD(tanggalString) {
    if (!tanggalString) return '';
    if (tanggalString.includes('/')) {
        const [day, month, year] = tanggalString.split('/');
        return `${year}-${month}-${day}`;
    }
    return tanggalString;
}

updateJumlahData = (tableId, jumlahId) => {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tbody tr:not(#no-data-row)');
    const count = Array.from(rows).filter(row => row.style.display !== 'none').length;
    document.getElementById(jumlahId).textContent = `Jumlah Data: ${count}`;
}

document.addEventListener('DOMContentLoaded', () => {
    setupFilters();
    if (document.getElementById('cuti-table')) filterCutiTable();
    if (document.getElementById('employees-table')) filterEmployeeTable();
    if (document.getElementById('users-table')) filterUsersTable();
});

function copyDailyReport(event) {
    const textDiv = document.getElementById('dailyReportContent');
    const copyButton = event.currentTarget;
    const text = textDiv.innerText || textDiv.textContent;

    // Gunakan textarea sementara untuk menyalin
    const textarea = document.createElement("textarea");
    textarea.value = text;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "absolute";
    textarea.style.left = "-9999px";
    document.body.appendChild(textarea);
    textarea.select();

    try {
        const successful = document.execCommand('copy');
        if (successful) {
            copyButton.innerHTML = `
                Telah Disalin
                <i class="material-symbols-rounded">
                    check_circle
                </i>
            `;
            copyButton.classList.replace('btn-primary', 'btn-secondary');
            copyButton.disabled = true;

            setTimeout(() => {
                copyButton.innerHTML = `
                    Salin
                    <i class="material-symbols-rounded">
                        content_paste
                    </i>
                `;
                copyButton.classList.replace('btn-secondary', 'btn-primary');
                copyButton.disabled = false;
            }, 2000);
        } else {
            alert("Perintah salin gagal.");
        }
    } catch (err) {
        console.error("Gagal menyalin:", err);
        alert("Gagal menyalin laporan.");
    }

    document.body.removeChild(textarea);
}
