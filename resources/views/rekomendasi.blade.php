<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zero Waste Kitchen - Rekomendasi AI</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">

    <div class="max-w-4xl mx-auto py-12 px-4">
        <!-- Header Area dengan Tombol Pindah Halaman & Autentikasi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4 border-b border-gray-200 pb-6">
            <div>
                <h1 class="text-3xl font-bold text-green-600 mb-1">🌱 Zero Waste Kitchen</h1>
                <p class="text-gray-600 text-sm">Masukkan bahan makanan sisa di kulkasmu, AI akan merekomendasikan resep terbaik!</p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('landing') }}" class="inline-block bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 rounded-xl border border-gray-300 shadow-sm transition duration-200 text-sm">
                    🏠 Beranda
                </a>

                @auth
                    <!-- TAMPIL JIKA USER SUDAH LOGIN -->
                    <!-- ==================== START: TOMBOL HISTORI PENCARIAN ==================== -->
                    <a href="{{ route('histori.index') }}" class="inline-block bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 rounded-xl border border-gray-300 shadow-sm transition duration-200 text-sm whitespace-nowrap hover:text-green-600">
                        Histori Pencarian 📜
                    </a>
                    <!-- ==================== END: TOMBOL HISTORI PENCARIAN ==================== -->

                    <a href="{{ route('favorit.index') }}" class="inline-block bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 rounded-xl border border-gray-300 shadow-sm transition duration-200 text-sm whitespace-nowrap">
                        Menu Favorit Saya ❤️
                    </a>

                    <!-- Form Logout Aman Menggunakan Metode POST -->
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2.5 px-4 rounded-xl border border-red-200 shadow-sm transition duration-200 text-sm cursor-pointer whitespace-nowrap">
                            Logout 🚪
                        </button>
                    </form>
                @else
                    <!-- TAMPIL JIKA USER BELUM LOGIN -->
                    <a href="{{ route('login') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition duration-200 text-sm whitespace-nowrap">
                        Login 🔑
                    </a>
                @endauth
            </div>
        </div>

        <!-- Form Pencarian Bahan -->
        <form action="{{ route('rekomendasi.cari') }}" method="GET" class="bg-white p-6 rounded-xl shadow-md mb-8">
            <div class="mb-4">
                <label for="bahan_sisa" class="block font-medium text-gray-700 mb-2">Bahan Makanan (Pisahkan dengan koma):</label>
                <input type="text" id="bahan_sisa" name="bahan_sisa" value="{{ $bahanSisa }}" 
                       placeholder="Contoh: ayam, bawang, cabai, telur" 
                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:outline-none" required>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                Cari Rekomendasi Resep 🚀
            </button>
        </form>

        <!-- Notifikasi Error -->
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Menampilkan Hasil Rekomendasi -->
        @if($daftarResep)
            <h2 class="text-xl font-bold mb-4 text-gray-700">Hasil Rekomendasi untuk: <span class="text-green-600">"{{ $bahanSisa }}"</span></h2>
            
            @if($daftarResep->isEmpty())
                <p class="text-gray-500 bg-white p-6 rounded-xl text-center shadow-sm">Maaf, tidak ada resep yang cocok dengan bahan tersebut di database.</p>
            @else
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($daftarResep as $resep)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-5 flex flex-col justify-between">
                            <div>
                                <!-- 1. Menampilkan Gambar Kuliner -->
                                @if(isset($resep['image']))
                                    <img src="{{ $resep['image'] }}" alt="{{ $resep['recipe_name_en'] ?? $resep['name'] ?? 'Resep' }}" class="w-full h-40 object-cover rounded-lg mb-4">
                                @endif

                                <!-- 2. Judul Resep -->
                                <h3 class="font-bold text-lg text-gray-900 mb-1">
                                    {{ $resep['recipe_name_en'] ?? $resep['name'] ?? 'Judul Tidak Tersedia' }}
                                </h3>
                                <p class="text-xs text-green-600 mb-4 font-semibold">Akurasi: {{ $resep['similarity_score'] ?? '0' }}%</p>
                                
                                <hr class="border-gray-100 mb-3">

                                <!-- 3. Menampilkan Bahan-Bahan -->
                                <div class="mb-4">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Bahan yang dibutuhkan:</h4>
                                    <ul class="text-xs text-gray-600 list-disc list-inside space-y-0.5">
                                        @if(isset($resep['ingredients']) && is_array($resep['ingredients']))
                                            @foreach($resep['ingredients'] as $bahan)
                                                <li>{{ $bahan }}</li>
                                            @endforeach
                                        @elseif(isset($resep['ingredients']) && is_string($resep['ingredients']))
                                            <li>{{ $resep['ingredients'] }}</li>
                                        @else
                                            <li class="italic text-gray-400">Bahan tidak tersedia</li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- 4. Menampilkan Langkah Memasak -->
                                <div class="mb-4">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Langkah Memasak:</h4>
                                    <ol class="text-xs text-gray-600 list-decimal list-inside space-y-1">
                                        @if(isset($resep['steps']) && is_array($resep['steps']))
                                            @foreach($resep['steps'] as $langkah)
                                                <li>{{ $langkah }}</li>
                                            @endforeach
                                        @elseif(isset($resep['instructions']) && is_array($resep['instructions']))
                                            @foreach($resep['instructions'] as $langkah)
                                                <li>{{ $langkah }}</li>
                                            @endforeach
                                        @else
                                            <li class="italic text-gray-400">Langkah memasak tidak terlampir</li>
                                        @endif
                                    </ol>
                                </div>
                            </div>

                            <!-- Area Notifikasi Sukses/Info Khusus untuk Fitur Favorit -->
                            @if(session('success'))
                                <div class="bg-green-100 text-green-700 p-2 text-xs rounded-lg mb-2 text-center font-medium mt-3">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if(session('info'))
                                <div class="bg-blue-100 text-blue-700 p-2 text-xs rounded-lg mb-2 text-center font-medium mt-3">
                                    {{ session('info') }}
                                </div>
                            @endif

                            <div>
                                <!-- PROTEKSI: Cek Status Login User -->
                                @auth
                                    <!-- Jika SUDAH login, berikan Form Submit Tambah Favorit -->
                                    <form action="{{ route('favorit.tambah') }}" method="POST" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="recipe_id" value="{{ $resep['id'] ?? $resep['recipe_id'] }}">
                                        <input type="hidden" name="recipe_name" value="{{ $resep['recipe_name_en'] ?? $resep['name'] ?? $resep['recipe_name'] ?? 'Resep Rekomendasi AI' }}"> 
                                        <input type="hidden" name="ingredients" value="{{ isset($resep['ingredients']) ? (is_array($resep['ingredients']) ? json_encode($resep['ingredients']) : $resep['ingredients']) : '' }}">
                                        <input type="hidden" name="steps" value="{{ isset($resep['steps']) ? (is_array($resep['steps']) ? json_encode($resep['steps']) : $resep['steps']) : (isset($resep['instructions']) ? (is_array($resep['instructions']) ? json_encode($resep['instructions']) : $resep['instructions']) : '') }}">
                                        
                                        <button type="submit" class="w-full text-center text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg py-2 transition cursor-pointer flex items-center justify-center gap-1">
                                            ❤️ Simpan ke Favorit
                                        </button>
                                    </form>
                                @else
                                    <!-- Jika BELUM login, alihkan tombol ke Halaman Login -->
                                    <a href="{{ route('login') }}" class="w-full text-center text-sm font-semibold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-lg py-2 transition flex items-center justify-center gap-1 mt-3 border border-gray-200 shadow-xs">
                                        🔒 Login untuk Simpan Favorit
                                    </a>
                                @endauth

                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

</body>
</html>