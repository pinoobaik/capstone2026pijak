import pickle

with open("vectorizer.pkl", "rb") as f:
    vectorizer = pickle.load(f)

print(type(vectorizer))
print("idf_ ada:", hasattr(vectorizer, "idf_"))

if hasattr(vectorizer, "idf_"):
    print("Vectorizer sudah fitted")
else:
    print("Vectorizer BELUM fitted")