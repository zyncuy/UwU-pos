<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Validasi Error -->
            @if ($errors->any())
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm">
                    <p class="font-bold text-sm mb-1">Terjadi kesalahan input:</p>
                    <ul class="list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Alert Error Database / Sistem -->
            @if (session('error'))
                <div class="p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h2 class="text-lg font-bold text-gray-800">Tambah Kategori Baru</h2>
                    <a href="{{ route('categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900 font-semibold">
                        &larr; Kembali
                    </a>
                </div>

                <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Nama Kategori</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama kategori" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Keterangan (Opsional)</label>
                        <textarea name="description" rows="3" placeholder="Masukkan keterangan..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('categories.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg text-sm transition">
                            Batal
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow transition">
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>