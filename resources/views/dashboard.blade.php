<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Ringkasan Penjualan & Transaksi -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Penjualan Hari Ini -->
                <div class="bg-blue-600 text-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-blue-100 mb-1">Penjualan Hari Ini</div>
                    <div class="text-3xl font-bold">Rp {{ number_format($todaySales ?? 0, 0, ',', '.') }}</div>
                </div>

                <!-- Total Penjualan -->
                <div class="bg-emerald-600 text-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-emerald-100 mb-1">Total Penjualan</div>
                    <div class="text-3xl font-bold">Rp {{ number_format($totalSales ?? 0, 0, ',', '.') }}</div>
                </div>

                <!-- Transaksi Hari Ini -->
                <div class="bg-amber-500 text-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-amber-100 mb-1">Transaksi Hari Ini</div>
                    <div class="text-3xl font-bold">{{ $todayTransactions ?? 0 }}</div>
                </div>
            </div>

            <!-- Ringkasan Produk & Kategori -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    <div class="text-sm font-medium text-gray-500">Total Produk</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalProducts ?? 0 }} Item</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    <div class="text-sm font-medium text-gray-500">Total Kategori</div>
                    <div class="text-2xl font-bold text-gray-800 mt-1">{{ $totalCategories ?? 0 }} Kategori</div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>