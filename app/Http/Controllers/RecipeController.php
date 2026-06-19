<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    public function dapatkanRekomendasi(Request $request) 
    {
        $bahanSisa = $request->input('bahan_sisa');

        if (!$bahanSisa) {
            return view('rekomendasi', ['daftarResep' => null, 'bahanSisa' => '']);
        }

        try {
            // Tembak API Python 
            $response = Http::post('http://127.0.0.1:8001/rekomendasi', [
                'bahan_sisa' => $bahanSisa,
                'jumlah_rekomendasi' => 3
            ]);

            if ($response->successful()) {
                $hasilAI = $response->json();
                
                // LANGSUNG ambil semua data resep dari Python (termasuk langkah-langkah di dalamnya)
                $daftarResep = collect($hasilAI['data']); 
                
                // ==================== START: LOGIKA MODEL LOGS ====================
               if (\Illuminate\Support\Facades\Auth::check()) {
                    try {
                        // Ambil resep peringkat 1 dari AI untuk sampel log
                        $resepTerbaik = $daftarResep->first();

                        // Ambil nilai nama resep dan similarity score dari AI
                        $namaResep = $resepTerbaik['recipe_name_en'] ?? $resepTerbaik['name'] ?? 'Tidak diketahui';
                        $skorSama  = $resepTerbaik['similarity_score'] ?? 0;

                        // Langsung insert sekaligus agar kolom NOT NULL di database kamu terpenuhi semua
                        DB::table('model_logs')->insert([
                            'user_id'           => \Illuminate\Support\Facades\Auth::id(),
                            'input_bahan'       => $bahanSisa, 
                            'rekomendasi_resep' => $namaResep, 
                            'similarity_score'  => $skorSama, 
                            'created_at'        => now()
                        ]);
                    } catch (\Exception $logException) {
                        // Jika database error (misal tipe data mismatch), resep tetap tampil & error tercatat di storage/logs/laravel.log
                        \Illuminate\Support\Facades\Log::error("Gagal mencatat model log: " . $logException->getMessage());
                    }
                }
                // ==================== END: LOGIKA MODEL LOGS ====================
                
                return view('rekomendasi', compact('daftarResep', 'bahanSisa'));
            }

            return back()->with('error', 'Gagal mendapatkan rekomendasi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Server AI belum aktif.');
        }
    }

    
    public function tambahFavorit(Request $request)
    {
        $request->validate([
            'recipe_id' => 'required',
            'recipe_name' => 'required',
        ]);

        try {
            $recipeId = $request->recipe_id;
            $recipeName = $request->recipe_name;

            // memastikan resep terdaftar di tabel recipes lokal
            $resepLokalExits = DB::table('recipes')->where('id', $recipeId)->exists();
            
            if (!$resepLokalExits) {
                // Ambil langsung kiriman asli dari form blade yang sudah divalidasi formatnya
                $ingredientsData = $request->ingredients ?? json_encode(['Bahan tidak terlampir']);
                $stepsData       = $request->steps ?? json_encode(['Langkah memasak tidak terlampir']);

                DB::table('recipes')->insert([
                    'id'               => $recipeId,
                    'recipe_id_json'   => 'AI-' . $recipeId,
                    'recipe_name'      => $recipeName, 
                    'description'      => 'Resep hasil rekomendasi cerdas AI.',
                    'ingredients'      => $ingredientsData, // MENYIMPAN BAHAN ASLI PYTHON
                    'steps'            => $stepsData,       // MENYIMPAN LANGKAH ASLI PYTHON
                    'category'         => 'Umum',
                    'difficulty'       => 'Mudah',
                    'prep_time'        => '15 Menit',
                    'cook_time'        => '20 Menit',
                    'serves'           => 1,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // Simpan ke tabel resep_favorit milik user
            $sudahAda = DB::table('resep_favorit')
                ->where('user_id', \Illuminate\Support\Facades\Auth::id())
                ->where('recipe_id', $recipeId)
                ->exists();

            if (!$sudahAda) {
                DB::table('resep_favorit')->insert([
                    'user_id'    => \Illuminate\Support\Facades\Auth::id(),
                    'recipe_id'  => $recipeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return back()->with('success', 'Berhasil menambahkan resep ke favorit! 😍');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan ke favorit: ' . $e->getMessage());
        }
    }

    public function indexFavorit()
    {
        // Mengambil semua resep favorit khusus milik user yang sedang login saat ini
        $daftarFavorit = DB::table('resep_favorit')
            ->join('recipes', 'resep_favorit.recipe_id', '=', 'recipes.id') 
            ->where('resep_favorit.user_id', \Illuminate\Support\Facades\Auth::id())
            ->select('resep_favorit.id as favorit_id', 'recipes.id as recipe_id', 'recipes.recipe_name', 'recipes.description')
            ->get();

        // Mengirimkan data tersebut ke file blade 'favorit'
        return view('favorit', compact('daftarFavorit'));
    }

    public function showDetail($id)
    {
        // Mengambil data resep berdasarkan ID dari tabel recipes
        $resep = DB::table('recipes')->where('id', $id)->first();

        if (!$resep) {
            abort(404, 'Resep tidak ditemukan!');
        }

        // Mengirimkan data resep ke file blade baru bernama detail.blade.php
        return view('detail', compact('resep'));
    }


    public function indexHistori()
    {
        // Ambil riwayat pencarian dari database milik user yang sedang login
        $daftarHistori = DB::table('model_logs')
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // Dikirim ke file blade 'histori.blade.php' dengan membawa variabel $daftarHistori
        return view('histori', compact('daftarHistori'));
    }
}