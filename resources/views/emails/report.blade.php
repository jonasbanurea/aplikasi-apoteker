<p>Halo,</p>
<p>Terlampir laporan terbaru sesuai periode yang dipilih.</p>
<ul>
    <li>Periode: {{ optional($filters['start'])->format('d M Y') ?? '-' }} s/d {{ optional($filters['end'])->format('d M Y') ?? '-' }}</li>
    <li>Dibuat pada: {{ now()->format('d M Y H:i') }}</li>
</ul>
<p>Terima kasih.</p>
