<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body { font-family: sans-serif; font-size: 12px; }
        table { border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #eee; }
        .transaksi {
            width: 100%;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Keuangan</h2>

    <table style="margin-bottom: 30px;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 6px;">Nama Meja</th>
                <th style="border: 1px solid #000; padding: 6px;">Total Durasi (jam)</th>
                <th style="border: 1px solid #000; padding: 6px;">Total Pendapatan</th>
                <th style="border: 1px solid #000; padding: 6px;">Persentase dari Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataMeja as $meja)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $meja['nama_meja'] }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: right;">{{ rtrim(rtrim($meja['total_durasi'], '0'), '.') }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: right;">Rp {{ number_format($meja['total_pendapatan'], 0, ',', '.') }}</td>
                    <td style="border: 1px solid #000; padding: 6px; text-align: right;">{{ number_format($meja['persentase'], 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="border: 1px solid #000; padding: 6px; text-align: center;">Tidak ada data meja.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="transaksi">
        <thead>
            <tr>
                <th colspan="5" style="text-align: right; padding: 6px;">
                    <strong>Total Pendapatan: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong>
                </th>
            </tr>
            <tr>
                <th>Tanggal</th>
                <th>Nama Pelanggan</th>
                <th>Meja</th>
                <th>Durasi (jam)</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $transaksi)
                <tr>
                    <td>{{ $transaksi->created_at->format('Y-m-d H:i') }} - {{ $transaksi->waktu_selesai->format('Y-m-d H:i') }}</td>
                    <td>{{ $transaksi->nama_pelanggan }}</td>
                    <td>{{ $transaksi->meja->nama_meja ?? 'Meja #' . $transaksi->meja_id }}</td>
                    <td>{{ $transaksi->durasi }}</td>
                    <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
