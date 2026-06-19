<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Resep 📖</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <a href="{{ route('favorit.index') }}" class="text-sm text-green-600 font-semibold hover:underline mb-4 inline-block">
            ← Kembali ke Favorit
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $resep->recipe_name }}</h1>
        <p class="text-gray-500 text-sm mb-6">{{ $resep->description }}</p>

        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-800 mb-2">🌱 Bahan-bahan:</h3>
            <p class="text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
                {{ is_array(json_decode($resep->ingredients)) ? implode("\n", json_decode($resep->ingredients)) : $resep->ingredients }}
            </p>
        </div>

        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">🍳 Langkah Memasak:</h3>
            <p class="text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100 whitespace-pre-line">
                {{ is_array(json_decode($resep->steps)) ? implode("\n", json_decode($resep->steps)) : $resep->steps }}
            </p>
        </div>
    </div>
</body>
</html>