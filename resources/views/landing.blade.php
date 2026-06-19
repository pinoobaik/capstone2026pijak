<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zero Waste Kitchen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">
    <nav class="bg-white shadow-sm py-4 px-6 flex justify-between items-center max-w-6xl mx-auto rounded-b-xl">
        <span class="text-xl font-bold text-green-600">🌱 Zero Waste Kitchen</span>
        <div class="space-x-4">
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600 font-medium">Masuk</a>
            <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">Daftar</a>
        </div>
    </nav>

    <header class="max-w-4xl mx-auto text-center py-20 px-4">
        <h1 class="text-5xl font-black text-gray-900 leading-tight mb-4">
            Jangan Buang Bahan Makananmu, <span class="text-green-600">Jadikan Masakan Lezat!</span>
        </h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
            Teknologi AI kami siap mencarikan rekomendasi resep terbaik berdasarkan bahan sisa masakan yang ada di dalam kulkasmu. Hemat pengeluaran, kurangi sampah makanan!
        </p>
        <a href="{{ route('rekomendasi.cari') }}" class="bg-green-600 hover:bg-green-700 text-white text-lg font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition duration-200">
            Mulai Cari Rekomendasi 🚀
        </a>
    </header>
</body>
</html>