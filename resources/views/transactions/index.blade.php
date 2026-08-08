<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="p-4 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm font-semibold rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm font-semibold rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h2 class="text-xl font-bold text-gray-800">Riwayat Transaksi</h2>
                    <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow transition">
                        + Transaksi Baru
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b text-xs font-semibold text-gray-600 uppercase">
                                <th class="py-3 px-4">INVOICE</th>
                                <th class="py-3 px-4">KASIR</th>
                                <th class="py-3 px-4">TOTAL</th>
                                <th class="py-3 px-4">BAYAR</th>
                                <th class="py-3 px-4">KEMBALIAN</th>
                                <th class="py-3 px-4">TANGGAL</th>
                                <th class="py-3 px-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3 px-4 font-bold text-blue-600">{{ $trx->invoice ?? ('TRX-' . $trx->id) }}</td>
                                    <td class="py-3 px-4">{{ $trx->user->name ?? 'Kasir System' }}</td>
                                    <td class="py-3 px-4 font-semibold">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">Rp {{ number_format($trx->pay_amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">Rp {{ number_format($trx->change_amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-xs text-gray-500">{{ $trx->created_at ? $trx->created_at->format('d M Y H:i') : '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs px-3 py-1 rounded transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-gray-400">Belum ada data transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>