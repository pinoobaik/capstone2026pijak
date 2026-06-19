<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Resep Favorit Saya 🌱</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <<div class="max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Menu Favorit Saya ❤️</h1>
                <p class="text-gray-600">Daftar resep pilihan yang kamu simpan untuk mengurangi food waste.</p>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('rekomendasi.cari') }}" class="inline-block bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 rounded-xl border border-gray-300 shadow-sm transition duration-200 text-sm">
                    🏠 Beranda
                </a>

                <a href="{{ route('rekomendasi.cari') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition duration-200 text-sm whitespace-nowrap">
                    Cari Rekomendasi Resep 🚀
                </a>
            </div>
        </div>
        @if($daftarFavorit->isEmpty())
            <div class="bg-white p-6 rounded-xl shadow-sm text-center text-gray-500">
                Kamu belum memiliki resep favorit. Yuk, cari rekomendasi resep dulu!
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($daftarFavorit as $fav)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $fav->recipe_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $fav->description }}</p>
                        </div>
                        
                        <!-- BAGIAN YANG SUDAH DIRAPIKAN (Hanya 1 Div Pembungkus & Aman Error) -->
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
    <!-- Sekarang rute resep.detail sudah terdefinisi secara resmi -->
    <a href="{{ route('resep.detail', $fav->recipe_id) }}" class="text-sm text-green-600 font-semibold hover:underline">
        Lihat Detail 📖
    </a>
</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>