@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-3 mb-4">
        <!-- Penjualan Hari Ini -->
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Penjualan Hari Ini</h6>
                            <h3 class="fw-bold m-0">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-wallet2 fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Penjualan -->
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Penjualan</h6>
                            <h3 class="fw-bold m-0">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-cash-stack fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaksi Hari Ini -->
        <div class="col-md-4">
            <div class="card bg-warning text-white shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Transaksi Hari Ini</h6>
                            <h3 class="fw-bold m-0">{{ $todayTransactions ?? 0 }}</h3>
                        </div>
                        <i class="bi bi-receipt fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Produk & Kategori -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center p-4">
                    <div>
                        <h6 class="text-muted mb-1">Total Produk</h6>
                        <h4 class="fw-bold text-dark m-0">{{ $totalProducts ?? 0 }} Item</h4>
                    </div>
                    <i class="bi bi-box-seam fs-1 text-primary"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center p-4">
                    <div>
                        <h6 class="text-muted mb-1">Total Kategori</h6>
                        <h4 class="fw-bold text-dark m-0">{{ $totalCategories ?? 0 }} Kategori</h4>
                    </div>
                    <i class="bi bi-tags fs-1 text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection