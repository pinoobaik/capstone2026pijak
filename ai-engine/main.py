# import os
# import re
# import pandas as pd
# import pickle
# from fastapi import FastAPI, HTTPException
# from pydantic import BaseModel

# app = FastAPI(title="Zero Waste Kitchen - AI Engine Indonesian Saved Model")

# # 1. Tentukan BASE_DIR and PATH file model
# BASE_DIR = os.path.dirname(os.path.abspath(__file__))
# PATH_DATASET = os.path.join(BASE_DIR, 'all_cleaned_data.csv')
# PATH_VECTORIZER = os.path.join(BASE_DIR, 'vectorizer.pkl')
# PATH_MODEL_KNN = os.path.join(BASE_DIR, 'model_knn.pkl')

# # Inisialisasi variabel global sebagai cadangan
# vectorizer = None
# model_knn = None
# df = pd.DataFrame()

# # 2. Load Dataset CSV untuk mengambil data teks resep
# try:
#     if os.path.exists(PATH_DATASET):
#         df = pd.read_csv(PATH_DATASET, sep=',', on_bad_lines='skip')
#         df.columns = df.columns.str.strip()
#         print("✓ Dataset CSV Berhasil Dimuat.")
#     else:
#         print(f"❌ File tidak ditemukan di jalur: {PATH_DATASET}")
# except Exception as e:
#     print(f"❌ Gagal membaca file CSV: {e}")

# # 3. LOAD MODEL YANG SUDAH JADI DARI GOOGLE COLAB (.pkl)
# try:
#     if os.path.exists(PATH_VECTORIZER) and os.path.exists(PATH_MODEL_KNN):
#         with open(PATH_VECTORIZER, 'rb') as f:
#             vectorizer = pickle.load(f)
#         with open(PATH_MODEL_KNN, 'rb') as f:
#             model_knn = pickle.load(f)
#         print("✓ Model AI (.pkl) Berhasil Dimuat. Siap Melayani Request!")
#     else:
#         print("❌ File model .pkl tidak ditemukan. Pastikan file pkl hasil download dari Colab sudah ditaruh di folder 'ai-engine'!")
# except Exception as e:
#     print(f"❌ Gagal memuat file model AI: {e}")

# class RecommendationRequest(BaseModel):
#     bahan_sisa: str
#     jumlah_rekomendasi: int = 3

# @app.post("/rekomendasi")
# def get_recommendation(payload: RecommendationRequest):
#     if not payload.bahan_sisa or not payload.bahan_sisa.strip():
#         raise HTTPException(status_code=400, detail="bahan_sisa tidak boleh kosong")
#     input_user = payload.bahan_sisa.strip().lower()

#    # Cek apakah vectorizer sudah fit, jika belum, lakukan fit ulang
#     if vectorizer is not None and not hasattr(vectorizer, 'idf_'):
#         print("⚠️ Vectorizer belum fit, melakukan fit ulang dari dataset...")
#         # Gunakan kolom yang sama seperti di Colab (bahan_bersih jika ada, atau Ingredients)
#         if 'bahan_bersih' in df.columns:
#             corpus = df['bahan_bersih'].fillna('').astype(str)
#         else:
#             # Asumsikan preprocessing dilakukan di sini juga
#             # Kita perlu menerapkan fungsi preprocessing yang sama seperti di Colab
#             def advanced_preprocessing(text):
#                 if pd.isna(text):
#                     return ""
#                 text = str(text).replace('--', ' ').replace(';', ' ')
#                 text = text.lower().strip()
#                 return text
#             corpus = df['Ingredients'].fillna('').astype(str).apply(advanced_preprocessing)
#         vectorizer.fit(corpus)
#         print("✅ Vectorizer berhasil di-fit ulang.")
    
#     if len(df) == 0:
#         raise HTTPException(status_code=500, detail="Dataset kosong")

#     if df.empty or vectorizer is None or model_knn is None:
#         raise HTTPException(
#             status_code=500, 
#             detail="Dataset atau Model AI gagal dimuat di server."
#         )

#     input_user = payload.bahan_sisa.lower()

#     # Hitung KNN Kemiripan memakai model pkl yang di-load
#     input_vektor = vectorizer.transform([input_user])
#     k_neighbors = min(payload.jumlah_rekomendasi, len(df))
#     distances, indices = model_knn.kneighbors(input_vektor, n_neighbors=k_neighbors)

#     json_response = []

#     for i in range(len(indices[0])):
#         idx = indices[0][i]
#         kemiripan = 1 - distances[0][i]
#         id_database_mysql = int(idx) + 1 
        
#         raw_name = str(df['Title'].iloc[idx]) if 'Title' in df.columns else "Resep Tanpa Nama"
#         raw_ingredients = str(df['Ingredients'].iloc[idx]) if 'Ingredients' in df.columns else ""
#         raw_steps = str(df['Steps'].iloc[idx]) if 'Steps' in df.columns else ""
#         raw_url = str(df['URL'].iloc[idx]) if 'URL' in df.columns else "#"

#         # Pemrosesan Kolom Ingredients (Hasil berupa List/Array)
#         if raw_ingredients:
#             ingredients_list = [b.strip() for b in re.split(r'--|;', raw_ingredients) if b.strip()]
#         else:
#             ingredients_list = []
        
#         # 🔥 PERBAIKAN UTAMA: Kembalikan langkah menjadi List/Array murni agar lolos validasi JSON MySQL
#         if raw_steps:
#             raw_steps_split = re.split(r'--|;', raw_steps)
#             steps_list = [s.strip() for s in raw_steps_split if s.strip() and s.strip() != ';']
#         else:
#             steps_list = []

#         # Proteksi jika teks langkah kosong
#         if not steps_list:
#             steps_list = ["Potong bahan sesuai selera.", "Tumis atau rebus hingga matang.", "Angkat dan sajikan."]
#         else:
#             # Kosmetik ringan: Pastikan huruf pertama di langkah pertama menggunakan huruf kapital
#             steps_list[0] = steps_list[0][0].upper() + steps_list[0][1:]

#         full_url = raw_url
#         if raw_url.startswith('/'):
#             full_url = f"https://cookpad.com{raw_url}"

#         json_response.append({
#             "id": id_database_mysql,
#             "recipe_id_json": str(idx),
#             "recipe_name_en": raw_name, 
#             "similarity_score": round(float(kemiripan) * 100, 2),
#             "ingredients": ingredients_list,    # Output format: ["Bahan A", "Bahan B"]
#             "steps": steps_list,                # 🔥 Output format sekarang: ["Langkah 1", "Langkah 2"]
#             "url": full_url
#         })

#     return {
#         "status": "success",
#         "data": json_response
#     }

import os
import re
import pandas as pd
import pickle
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel

app = FastAPI(title="Zero Waste Kitchen - AI Engine")

# ==================== KONFIGURASI PATH ====================
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
PATH_DATASET = os.path.join(BASE_DIR, 'all_cleaned_data.csv')
PATH_MODEL_KNN = os.path.join(BASE_DIR, 'model_knn.pkl')

# ==================== GLOBAL VARIABLES ====================
vectorizer = None
model_knn = None
df = pd.DataFrame()

# ==================== PREPROCESSING (SAMA DENGAN COLAB) ====================
def advanced_preprocessing(text):
    """Membersihkan teks bahan dengan aturan yang sama seperti di Colab."""
    if pd.isna(text):
        return ""
    text = str(text)
    text = text.replace('--', ' ').replace(';', ' ')
    text = text.lower()
    text = text.strip()
    return text

# ==================== LOAD DATASET ====================
try:
    if os.path.exists(PATH_DATASET):
        df = pd.read_csv(PATH_DATASET, sep=',', on_bad_lines='skip')
        df.columns = df.columns.str.strip()
        print(f"✓ Dataset CSV berhasil dimuat: {len(df)} baris.")
    else:
        print(f"❌ File dataset tidak ditemukan: {PATH_DATASET}")
except Exception as e:
    print(f"❌ Gagal membaca dataset: {e}")

# ==================== LOAD MODEL KNN ====================
try:
    if os.path.exists(PATH_MODEL_KNN):
        with open(PATH_MODEL_KNN, 'rb') as f:
            model_knn = pickle.load(f)
        print("✓ Model KNN berhasil dimuat.")
    else:
        print(f"❌ File model KNN tidak ditemukan: {PATH_MODEL_KNN}")
except Exception as e:
    print(f"❌ Gagal memuat model KNN: {e}")

# ==================== BUAT VECTORIZER DARI DATASET (FRESH) ====================
def build_vectorizer_from_dataset(dataframe):
    """Membuat dan melatih TfidfVectorizer dari dataset yang sudah dimuat."""
    if dataframe.empty:
        print("❌ Dataset kosong, tidak bisa membuat vectorizer.")
        return None

    if 'Ingredients' not in dataframe.columns:
        print("❌ Kolom 'Ingredients' tidak ditemukan di dataset.")
        return None

    # Preprocessing semua bahan
    corpus = dataframe['Ingredients'].fillna('').astype(str).apply(advanced_preprocessing)

    # Cek apakah ada teks yang bermakna
    if corpus.str.strip().eq('').all():
        print("❌ Semua bahan kosong setelah preprocessing, vectorizer tidak bisa dilatih.")
        return None

    # Buat dan fit vectorizer
    from sklearn.feature_extraction.text import TfidfVectorizer
    vec = TfidfVectorizer()
    vec.fit(corpus)
    print(f"✅ Vectorizer berhasil dilatih dengan {len(vec.get_feature_names_out())} fitur.")
    return vec

# Hanya buat jika dataset dan kolom tersedia
if not df.empty and 'Ingredients' in df.columns:
    vectorizer = build_vectorizer_from_dataset(df)
else:
    vectorizer = None
    print("⚠️ Vectorizer tidak dibuat karena dataset tidak valid.")

# ==================== ENDPOINT HEALTH ====================
@app.get("/")
def health():
    return {
        "status": "running",
        "model_loaded": model_knn is not None,
        "vectorizer_ready": vectorizer is not None,
        "dataset_loaded": not df.empty
    }

# ==================== REQUEST MODEL ====================
class RecommendationRequest(BaseModel):
    bahan_sisa: str
    jumlah_rekomendasi: int = 3

# ==================== ENDPOINT REKOMENDASI ====================
@app.post("/rekomendasi")
def get_recommendation(payload: RecommendationRequest):
    # Validasi input
    if not payload.bahan_sisa or not payload.bahan_sisa.strip():
        raise HTTPException(status_code=400, detail="bahan_sisa tidak boleh kosong")

    # Cek ketersediaan model dan vectorizer
    if model_knn is None:
        raise HTTPException(status_code=500, detail="Model KNN belum dimuat.")
    if vectorizer is None:
        raise HTTPException(status_code=500, detail="Vectorizer belum siap.")
    if df.empty:
        raise HTTPException(status_code=500, detail="Dataset kosong.")

    # Bersihkan input pengguna
    input_user = payload.bahan_sisa.strip().lower()
    input_bersih = advanced_preprocessing(input_user)

    # Transform input ke vektor TF-IDF
    try:
        input_vektor = vectorizer.transform([input_bersih])
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Gagal transform input: {str(e)}")

    # Tentukan jumlah tetangga
    k = min(payload.jumlah_rekomendasi, len(df))
    if k <= 0:
        raise HTTPException(status_code=400, detail="Jumlah rekomendasi tidak valid.")

    # Cari tetangga terdekat
    try:
        distances, indices = model_knn.kneighbors(input_vektor, n_neighbors=k)
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Gagal mencari rekomendasi: {str(e)}")

    # Bangun respons JSON
    result = []
    for i in range(len(indices[0])):
        idx = indices[0][i]
        similarity = 1 - distances[0][i]  # cosine similarity

        # Ambil data dari dataset
        raw_name = str(df['Title'].iloc[idx]) if 'Title' in df.columns else "Resep Tanpa Nama"
        raw_ingredients = str(df['Ingredients'].iloc[idx]) if 'Ingredients' in df.columns else ""
        raw_steps = str(df['Steps'].iloc[idx]) if 'Steps' in df.columns else ""
        raw_url = str(df['URL'].iloc[idx]) if 'URL' in df.columns else "#"

        # Parse ingredients dan steps menjadi list
        ingredients_list = [b.strip() for b in re.split(r'--|;', raw_ingredients) if b.strip()]
        steps_list = [s.strip() for s in re.split(r'--|;', raw_steps) if s.strip() and s.strip() != ';']

        # Fallback jika steps kosong
        if not steps_list:
            steps_list = ["Potong bahan sesuai selera.", "Tumis atau rebus hingga matang.", "Angkat dan sajikan."]

        # Perbaiki case huruf pertama pada langkah pertama
        if steps_list and len(steps_list[0]) > 0:
            steps_list[0] = steps_list[0][0].upper() + steps_list[0][1:]

        # Perbaiki URL
        full_url = raw_url
        if raw_url.startswith('/'):
            full_url = f"https://cookpad.com{raw_url}"

        result.append({
            "id": int(idx) + 1,  # Sesuai dengan ID di database Laravel
            "recipe_id_json": str(idx),
            "recipe_name_en": raw_name,
            "similarity_score": round(similarity * 100, 2),
            "ingredients": ingredients_list,
            "steps": steps_list,
            "url": full_url
        })

    return {
        "status": "success",
        "data": result
    }
    
@app.get("/")
def health():
    return {
        "status": "running",
        "model_loaded": model_knn is not None,
        "dataset_loaded": not df.empty
    }