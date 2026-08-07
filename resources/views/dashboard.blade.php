@extends('layouts.app-pos')

@section('content')
<!-- Ringkasan Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Penjualan Hari Ini</h6>
                        <h3 class="fw-bold m-0">Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                    </div>
                    <i class="bi bi-wallet2 fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Jenis Produk</h6>
                        <h3 class="fw-bold m-0">{{ $totalProducts }} Produk</h3>
                    </div>
                    <i class="bi bi-box-seam fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-warning text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Transaksi Selesai</h6>
                        <h3 class="fw-bold m-0">{{ $totalTransactions }}</h3>
                    </div>
                    <i class="bi bi-cart-check fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Penjualan -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-bold py-3">
        <i class="bi bi-graph-up-arrow me-1 text-primary"></i> Grafik Penjualan 7 Hari Terakhir
    </div>
    <div class="card-body p-4">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

<!-- Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($salesData);

    const labels = salesData.map(item => item.date);
    const totals = salesData.map(item => item.total);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: totals,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endsection