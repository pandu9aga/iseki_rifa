@extends('layouts.app')

@section('content')
<main>

    <section class="title-button d-flex flex-row">
        <h1 class="font-bold">Data Pengganti</h1>
    </section>

    <h5>Member yang digantikan: <span style="color: #ec057d;">{{ $absensi->employee->nama }}</span></h5>
    <h5>Tanggal izin: <span style="color: #ec057d;">{{ $absensi->tanggal }}</span></h5>

    <section class="my-5">
        <h2 class="font-bold text-lg mb-3">Form Input Member Pengganti</h2>
        <br>

        <form id="replacerForm" method="POST" action="{{ route('replacements.store') }}">
            @csrf

            <input type="hidden" name="absensi_id" value="{{ $absensi->id }}">
            <div class="mb-3">
                <label for="replacer_nama" class="form-label">Nama Pengganti</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="replacer_nama" name="replacer_nama" required readonly>
                    <br>
                </div>
                <label for="replacer_nik" class="form-label">NIK Pengganti</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="replacer_nik" name="replacer_nik" required readonly>
                    <br>
                    <button type="button" id="scanNikBtn" class="btn btn-primary">Scan NIK</button>
                </div>
            </div>
            <hr>
            <div class="mb-3">
                <label for="production_number" class="form-label">Production Number</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="production_number" name="production_number" required readonly>
                    <br>
                    <button type="button" id="scanProdBtn" class="btn btn-primary">Scan Production</button>
                </div>
            </div>

            <div id="qr-reader" style="width: 100%; max-width: 400px; margin-top: 15px; display: none;"></div>

            <hr>
            <button type="submit" class="btn btn-primary mt-3">
                Submit
            </button>
        </form>
    </section>

    {{-- html5-qrcode dari CDN --}}
    <script src="{{ asset('js/html5-qrcode.min.js') }}" type="text/javascript"></script>
    <script>
        const qrReader = document.getElementById('qr-reader');

        let html5QrCode;
        let isScanning = false;
        let currentTarget = null; // "nik" atau "prod"

        document.getElementById('scanNikBtn').addEventListener('click', () => {
            startScanner("nik");
        });

        document.getElementById('scanProdBtn').addEventListener('click', () => {
            startScanner("prod");
        });

        function startScanner(target) {
            if (isScanning) {
                stopScanner();
                return;
            }

            currentTarget = target;
            qrReader.style.display = 'block';

            html5QrCode = new Html5Qrcode("qr-reader");
            const qrConfig = { fps: 10, qrbox: 250 };

            html5QrCode.start(
                { facingMode: "environment" },
                qrConfig,
                qrCodeMessage => {
                    try {
                        let parts = qrCodeMessage.split(';');
                        let value = parts[0] || '';

                        if (currentTarget === "nik") {
                            document.getElementById('replacer_nik').value = value;

                            // ambil nama berdasarkan nik
                            fetch(`/iseki_rifa/public/employee/by-nik/${value}`)
                                .then(res => res.json())
                                .then(data => {
                                    if (data && data.nama) {
                                        document.getElementById('replacer_nama').value = data.nama;
                                    } else {
                                        document.getElementById('replacer_nama').value = "Tidak ditemukan";
                                    }
                                })
                                .catch(err => {
                                    console.error("Error get employee:", err);
                                });

                        } else if (currentTarget === "prod") {
                            document.getElementById('production_number').value = value;
                        }

                        stopScanner();
                    } catch (err) {
                        console.error("QR parse error:", err);
                    }
                },
                errorMessage => {
                    // console.log(`QR Code no match: ${errorMessage}`);
                }
            ).then(() => {
                isScanning = true;
            }).catch(err => {
                console.error("Unable to start scanning", err);
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    qrReader.style.display = 'none';
                    isScanning = false;
                }).catch(err => {
                    console.error("Unable to stop scanning", err);
                });
            }
        }
    </script>
</main>
@endsection