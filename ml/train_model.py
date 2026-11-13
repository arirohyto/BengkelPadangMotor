# train_model.py
import pandas as pd
import joblib
from sklearn.tree import DecisionTreeClassifier
from sklearn.preprocessing import LabelEncoder
import datetime as dt

# 1. Load Data
df = pd.read_csv('storage/app/ml/dataset_servis.csv')

# 2. Feature Engineering: Ubah Tanggal Jadi DURASI (Angka)
# Konversi ke datetime
df['tgl_sekarang'] = pd.to_datetime(df['tgl_sekarang'])
df['tgl_lalu'] = pd.to_datetime(df['tgl_lalu'])

# Hitung selisih hari
df['durasi_hari'] = (df['tgl_sekarang'] - df['tgl_lalu']).dt.days

# Handle data minus atau error (jika ada input salah)
df['durasi_hari'] = df['durasi_hari'].apply(lambda x: 0 if x < 0 else x)

# 3. Encoding (Ubah Teks jadi Angka untuk CART)
# Kita simpan encoder-nya agar nanti Website bisa pakai mapping yang sama
le_tipe = LabelEncoder()
df['tipe_motor_code'] = le_tipe.fit_transform(df['tipe_motor'])

le_keluhan = LabelEncoder()
df['keluhan_code'] = le_keluhan.fit_transform(df['keluhan'])

# Target (Y) juga di-encode
le_target = LabelEncoder()
df['target_code'] = le_target.fit_transform(df['rekomendasi'])

# 4. Definisi Fitur (X) dan Target (y)
# Sesuai rencana: Durasi, KM, Usia, Tipe, Keluhan
X = df[['durasi_hari', 'km_tempuh', 'usia_motor', 'tipe_motor_code', 'keluhan_code']]
y = df['target_code']

# 5. Latih Model CART
clf = DecisionTreeClassifier(criterion='gini', max_depth=5, random_state=42)
clf.fit(X, y)

# 6. Simpan Model & Encoder (Agar bisa dipanggil Laravel)
joblib.dump(clf, 'storage/app/ml/model_cart_bengkel.pkl')
joblib.dump(le_tipe, 'storage/app/ml/le_tipe.pkl')
joblib.dump(le_keluhan, 'storage/app/ml/le_keluhan.pkl')
joblib.dump(le_target, 'storage/app/ml/le_target.pkl')

print("Model berhasil dilatih! Akurasi Training:", clf.score(X, y))