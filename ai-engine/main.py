import os
import re
import pandas as pd
import pickle
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel

app = FastAPI(title="Zero Waste Kitchen - AI Engine Indonesian Saved Model")

# 1. Tentukan BASE_DIR and PATH file model
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
PATH_DATASET = os.path.join(BASE_DIR, 'all_cleaned_data.csv')
PATH_VECTORIZER = os.path.join(BASE_DIR, 'vectorizer.pkl')
PATH_MODEL_KNN = os.path.join(BASE_DIR, 'model_knn.pkl')

# Inisialisasi variabel global sebagai cadangan
vectorizer = None
model_knn = None
df = pd.DataFrame()

# 2. Load Dataset CSV untuk mengambil data teks resep
try:
    if os.path.exists(PATH_DATASET):
        df = pd.read_csv(PATH_DATASET, sep=',', on_bad_lines='skip')
        df.columns = df.columns.str.strip()
        print("✓ Dataset CSV Berhasil Dimuat.")
    else:
        print(f"❌ File tidak ditemukan di jalur: {PATH_DATASET}")
except Exception as e:
    print(f"❌ Gagal membaca file CSV: {e}")

# 3. LOAD MODEL YANG SUDAH JADI DARI GOOGLE COLAB (.pkl)
try:
    if os.path.exists(PATH_VECTORIZER) and os.path.exists(PATH_MODEL_KNN):
        with open(PATH_VECTORIZER, 'rb') as f:
            vectorizer = pickle.load(f)
        with open(PATH_MODEL_KNN, 'rb') as f:
            model_knn = pickle.load(f)
        print("✓ Model AI (.pkl) Berhasil Dimuat. Siap Melayani Request!")
    else:
        print("❌ File model .pkl tidak ditemukan. Pastikan file pkl hasil download dari Colab sudah ditaruh di folder 'ai-engine'!")
except Exception as e:
    print(f"❌ Gagal memuat file model AI: {e}")

class RecommendationRequest(BaseModel):
    bahan_sisa: str
    jumlah_rekomendasi: int = 3

@app.post("/rekomendasi")
def get_recommendation(payload: RecommendationRequest):
    if not payload.bahan_sisa or not payload.bahan_sisa.strip():
        raise HTTPException(status_code=400, detail="bahan_sisa tidak boleh kosong")
    input_user = payload.bahan_sisa.strip().lower()

    if vectorizer is None or model_knn is None or df.empty:
        raise HTTPException(status_code=500, detail="Model belum siap")
    
    if len(df) == 0:
        raise HTTPException(status_code=500, detail="Dataset kosong")

    if df.empty or vectorizer is None or model_knn is None:
        raise HTTPException(
            status_code=500, 
            detail="Dataset atau Model AI gagal dimuat di server."
        )

    input_user = payload.bahan_sisa.lower()

    # Hitung KNN Kemiripan memakai model pkl yang di-load
    input_vektor = vectorizer.transform([input_user])
    k_neighbors = min(payload.jumlah_rekomendasi, len(df))
    distances, indices = model_knn.kneighbors(input_vektor, n_neighbors=k_neighbors)

    json_response = []

    for i in range(len(indices[0])):
        idx = indices[0][i]
        kemiripan = 1 - distances[0][i]
        id_database_mysql = int(idx) + 1 
        
        raw_name = str(df['Title'].iloc[idx]) if 'Title' in df.columns else "Resep Tanpa Nama"
        raw_ingredients = str(df['Ingredients'].iloc[idx]) if 'Ingredients' in df.columns else ""
        raw_steps = str(df['Steps'].iloc[idx]) if 'Steps' in df.columns else ""
        raw_url = str(df['URL'].iloc[idx]) if 'URL' in df.columns else "#"

        # Pemrosesan Kolom Ingredients (Hasil berupa List/Array)
        if raw_ingredients:
            ingredients_list = [b.strip() for b in re.split(r'--|;', raw_ingredients) if b.strip()]
        else:
            ingredients_list = []
        
        # 🔥 PERBAIKAN UTAMA: Kembalikan langkah menjadi List/Array murni agar lolos validasi JSON MySQL
        if raw_steps:
            raw_steps_split = re.split(r'--|;', raw_steps)
            steps_list = [s.strip() for s in raw_steps_split if s.strip() and s.strip() != ';']
        else:
            steps_list = []

        # Proteksi jika teks langkah kosong
        if not steps_list:
            steps_list = ["Potong bahan sesuai selera.", "Tumis atau rebus hingga matang.", "Angkat dan sajikan."]
        else:
            # Kosmetik ringan: Pastikan huruf pertama di langkah pertama menggunakan huruf kapital
            steps_list[0] = steps_list[0][0].upper() + steps_list[0][1:]

        full_url = raw_url
        if raw_url.startswith('/'):
            full_url = f"https://cookpad.com{raw_url}"

        json_response.append({
            "id": id_database_mysql,
            "recipe_id_json": str(idx),
            "recipe_name_en": raw_name, 
            "similarity_score": round(float(kemiripan) * 100, 2),
            "ingredients": ingredients_list,    # Output format: ["Bahan A", "Bahan B"]
            "steps": steps_list,                # 🔥 Output format sekarang: ["Langkah 1", "Langkah 2"]
            "url": full_url
        })

    return {
        "status": "success",
        "data": json_response
    }
    
@app.get("/")
def health():
    return {
        "status": "running",
        "model_loaded": model_knn is not None,
        "dataset_loaded": not df.empty
    }