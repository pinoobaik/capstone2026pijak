<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Pencarian - Zero Waste Kitchen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 min-h-screen text-gray-800">

    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4 border-b border-gray-200 pb-6">
            <div>
                <h1 class="text-3xl font-bold text-green-600 mb-1">📜 Histori Pencarian AI</h1>
                <p class="text-gray-600 text-sm">Daftar bahan makanan sisa yang pernah Anda konsultasikan ke sistem AI.</p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('rekomendasi.cari') }}" class="inline-block bg-white hover:bg-gray-50 text-gray-700 font-semibold py-2.5 px-4 rounded-xl border border-gray-300 shadow-sm transition duration-200 text-sm">
                    🏠 Beranda 
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            @if($daftarHistori->isEmpty())
                <div class="p-12 text-center text-gray-400 italic">
                    🌱 Belum ada riwayat pencarian. Ayo mulai cari rekomendasi resep dari bahan sisamu!
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 text-xs uppercase font-bold tracking-wider">
                                <th class="p-4">Waktu Pencarian</th>
                                <th class="p-4">Bahan Masakan Sisa</th>
                                <th class="p-4">Rekomendasi Utama AI</th>
                                <th class="p-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($daftarHistori as $item)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="p-4 text-gray-500 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                    </td>
                                    
                                    <td class="p-4 font-medium text-gray-900">
                                        <span class="bg-green-50 text-green-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-green-100">
                                            {{ $item->input_bahan }}
                                        </span>
                                    </td>
                                    
                                    <td class="p-4 text-gray-700">
                                        <div class="font-semibold">{{ $item->rekomendasi_resep }}</div>
                                        <div class="text-xs text-green-600 font-medium">Akurasi: {{ $item->similarity_score }}%</div>
                                    </td>
                                    
                                    <td class="p-4 text-center whitespace-nowrap">
                                        <form action="{{ route('rekomendasi.cari') }}" method="GET" class="inline">
                                            @csrf
                                            <input type="hidden" name="bahan_sisa" value="{{ $item->input_bahan }}">
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition duration-150 cursor-pointer shadow-xs">
                                                Cari Ulang 🔍
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</body>
</html>