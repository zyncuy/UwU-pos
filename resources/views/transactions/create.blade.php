<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-800">Transaksi Baru</h2>
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">
                        &larr; Kembali
                    </a>
                </div>

                @if(session('error'))
                    <div class="p-4 mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Total Harga (Rp)</label>
                        <input type="number" name="total_price" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required placeholder="Contoh: 50000">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Bayar (Rp)</label>
                        <input type="number" name="pay_amount" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required placeholder="Contoh: 100000">
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        Simpan Transaksi
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>