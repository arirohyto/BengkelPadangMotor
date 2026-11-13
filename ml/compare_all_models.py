import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.tree import DecisionTreeClassifier
from sklearn.neighbors import KNeighborsClassifier
from sklearn.naive_bayes import GaussianNB
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.svm import SVC
from sklearn.metrics import accuracy_score, f1_score
import time
import warnings
warnings.filterwarnings('ignore')

def run_comparison():
    # 1. Load Data
    import os
    dataset_path = 'ml/dataset_servis1.csv'
    if not os.path.exists(dataset_path):
        dataset_path = 'dataset_servis1.csv'
    df = pd.read_csv(dataset_path, sep=';', encoding='utf-8')
    
    # Clean column names mapping
    if 'Durasi' in df.columns:
        df = df.rename(columns={
            'Durasi': 'durasi_hari',
            'KM': 'km_tempuh',
            'Usia': 'usia_motor',
            'Tipe': 'tipe_motor',
            'Keluhan': 'keluhan',
            'Rekomendasi': 'rekomendasi'
        })
        
    feature_cols = ['durasi_hari', 'km_tempuh', 'usia_motor', 'tipe_motor', 'keluhan']
    target_col = 'rekomendasi'
    
    # 2. Preprocessing & Encoding
    X = df[feature_cols].copy()
    y = df[target_col].copy()
    
    # Label Encoders
    for col in ['tipe_motor', 'keluhan']:
        le = LabelEncoder()
        X[col] = le.fit_transform(X[col].astype(str))
        
    le_target = LabelEncoder()
    y = le_target.fit_transform(y.astype(str))
    
    # 3. Train-Test Split (80% Train, 20% Test)
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42, stratify=y
    )
    
    # 4. Define Models
    models = {
        'CART (Decision Tree - Gini)': DecisionTreeClassifier(criterion='gini', max_depth=4, random_state=42),
        'C4.5 (Decision Tree - Entropy)': DecisionTreeClassifier(criterion='entropy', max_depth=4, random_state=42),
        'Random Forest': RandomForestClassifier(n_estimators=100, max_depth=4, random_state=42),
        'Gradient Boosting': GradientBoostingClassifier(n_estimators=100, max_depth=4, random_state=42),
        'Support Vector Machine (SVM)': SVC(kernel='rbf', probability=True, random_state=42),
        'Naive Bayes': GaussianNB(),
        'K-Nearest Neighbors (KNN)': KNeighborsClassifier(n_neighbors=5)
    }
    
    results = []
    
    # 5. Train & Evaluate
    for name, clf in models.items():
        start_time = time.time()
        clf.fit(X_train, y_train)
        train_time = (time.time() - start_time) * 1000
        
        y_pred = clf.predict(X_test)
        
        acc = accuracy_score(y_test, y_pred)
        f1_macro = f1_score(y_test, y_pred, average='macro')
        
        results.append({
            'Algoritma': name,
            'Akurasi (%)': acc * 100,
            'F1-Score Macro (%)': f1_macro * 100,
            'Waktu Training (ms)': train_time
        })
        
    print("\n" + "="*85)
    print(f"{'Algoritma':<32} | {'Akurasi':<10} | {'F1-Score':<10} | {'Waktu Training':<15}")
    print("="*85)
    for r in results:
        print(f"{r['Algoritma']:<32} | {r['Akurasi (%)']:.2f}%     | {r['F1-Score Macro (%)']:.2f}%     | {r['Waktu Training (ms)']:.4f} ms")
    print("="*85 + "\n")
        
if __name__ == '__main__':
    run_comparison()
