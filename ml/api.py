"""
FLASK API - CART Prediction Service

Dataset Format:
- tgl_sekarang: Tanggal servis sekarang
- tgl_lalu: Tanggal servis terakhir
- durasi_hari: Selisih hari antara servis
- km_tempuh: Kilometer saat ini
- usia_motor: Usia motor dalam tahun
- tipe_motor: Matic, Bebek, Sport
- keluhan: Rutin, Mesin, CVT, dll
- rekomendasi: Target prediksi

Endpoints:
- GET  /             : Health check
- POST /train        : Train model dengan dataset
- POST /predict      : Prediksi rekomendasi servis
- GET  /model-info   : Info tentang model yang sudah ditraining

Cara menjalankan:
    cd ml
    python api.py
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import numpy as np
from sklearn.tree import DecisionTreeClassifier, export_text
from sklearn.preprocessing import LabelEncoder
from sklearn.model_selection import train_test_split, GridSearchCV, StratifiedKFold
from sklearn.metrics import accuracy_score, classification_report, f1_score
import joblib
import os

app = Flask(__name__)
CORS(app)

model = None
label_encoders = {}
target_encoder = None
model_info = {}

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATASET_PATH = os.path.join(BASE_DIR, 'dataset_servis1.csv')
MODEL_PATH = os.path.join(BASE_DIR, 'cart_model.joblib')
ENCODERS_PATH = os.path.join(BASE_DIR, 'encoders.joblib')

COLUMN_MAPPING = {
    'durasi': 'durasi_hari',
    'km': 'km_tempuh',
    'usia': 'usia_motor',
    'tipe': 'tipe_motor',
    'keluhan': 'keluhan',
    'target': 'rekomendasi'
}

FEATURE_COLUMNS = ['durasi_hari', 'km_tempuh', 'usia_motor', 'tipe_motor', 'keluhan']
TARGET_COLUMN = 'rekomendasi'
CATEGORICAL_COLS = ['tipe_motor', 'keluhan']


def load_or_train_model():
    global model, label_encoders, target_encoder, model_info
    
    if os.path.exists(MODEL_PATH) and os.path.exists(ENCODERS_PATH):
        try:
            saved_data = joblib.load(MODEL_PATH)
            model = saved_data['model']
            model_info = saved_data.get('info', {})
            
            encoders_data = joblib.load(ENCODERS_PATH)
            label_encoders = encoders_data['label_encoders']
            target_encoder = encoders_data['target_encoder']
            
            print("[OK] Model loaded from file")
            return True
        except Exception as e:
            print(f"Error loading model: {e}")
    
    if os.path.exists(DATASET_PATH):
        train_model_from_csv()
        return True
    
    return False


def train_model_from_csv():
    global model, label_encoders, target_encoder, model_info
    
    print("Training model from CSV...")
    
    try:
        df = pd.read_csv(DATASET_PATH, sep=';', encoding='utf-8')
    except Exception as e:
        print(f"Gagal membaca CSV: {e}")
        return None
    
    print(f"Dataset loaded: {len(df)} rows")

    if 'no' in df.columns:
        df = df.drop(columns=['no'])

    print(f"Dataset loaded: {len(df)} rows")
    print(f"Columns: {list(df.columns)}")
    
    if 'Durasi' in df.columns:
        df = df.rename(columns={
            'Durasi': 'durasi_hari',
            'KM': 'km_tempuh',
            'Usia': 'usia_motor',
            'Tipe': 'tipe_motor',
            'Keluhan': 'keluhan',
            'Rekomendasi': 'rekomendasi'
        })
    
    for col in FEATURE_COLUMNS + [TARGET_COLUMN]:
        if col not in df.columns:
            print(f"ERROR: Missing column '{col}' in dataset")
            print(f"Available columns: {list(df.columns)}")
            return None
    
    X = df[FEATURE_COLUMNS].copy()
    y = df[TARGET_COLUMN].copy()
    
    mask = X.notna().all(axis=1) & y.notna()
    X = X[mask]
    y = y[mask]
    
    print(f"Training samples after cleaning: {len(X)}")
    
    X_train, X_test, y_train_str, y_test_str = train_test_split(
        X, y, test_size=0.2, random_state=42, stratify=y
    )
    
    X_train = X_train.copy()
    X_test = X_test.copy()
    
    label_encoders = {}
    for col in CATEGORICAL_COLS:
        le = LabelEncoder()
        le.fit(X_train[col].astype(str))
        
        X_train[col] = le.transform(X_train[col].astype(str))
        
        known_classes = set(le.classes_)
        X_test[col] = X_test[col].astype(str).apply(
            lambda x: le.transform([x])[0] if x in known_classes else le.transform([le.classes_[0]])[0]
        )
        
        label_encoders[col] = le
        print(f"  {col} classes: {list(le.classes_)}")
    
    target_encoder = LabelEncoder()
    y_train = target_encoder.fit_transform(y_train_str.astype(str))
    
    known_targets = set(target_encoder.classes_)
    y_test = y_test_str.astype(str).apply(
        lambda x: target_encoder.transform([x])[0] if x in known_targets else target_encoder.transform([target_encoder.classes_[0]])[0]
    ).values
    
    print(f"  Target classes: {list(target_encoder.classes_)}")
    
    unique, counts = np.unique(y_train, return_counts=True)
    print(f"  Train Class distribution:")
    for cls_idx, cnt in zip(unique, counts):
        print(f"    {target_encoder.classes_[cls_idx]}: {cnt} ({cnt/len(y_train)*100:.1f}%)")


    param_grid = {
        'max_depth': [3, 4, 5, 6],
        'min_samples_split': [5, 10, 15],
        'min_samples_leaf': [2, 4, 8]
    }
    
    base_model = DecisionTreeClassifier(
        criterion='gini',           
        class_weight='balanced',       
        random_state=42
    )
    
    cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
    
    grid_search = GridSearchCV(
        estimator=base_model,
        param_grid=param_grid,
        cv=cv,
        scoring='f1_macro',         
        n_jobs=-1,
        refit=True
    )
    
    n_combinations = len(param_grid['max_depth']) * len(param_grid['min_samples_split']) * len(param_grid['min_samples_leaf'])
    print(f"\n  Running GridSearchCV (5 folds x {n_combinations} combinations = {5 * n_combinations} fits)...")
    
    grid_search.fit(X_train, y_train)
    
    model = grid_search.best_estimator_
    best_params = grid_search.best_params_
    best_cv_score = grid_search.best_score_
    
    print(f"  Best parameters: {best_params}")
    print(f"  Best CV F1-Score (macro): {best_cv_score:.4f}")
    
    y_pred = model.predict(X_test)
    accuracy = accuracy_score(y_test, y_pred)
    f1_macro = f1_score(y_test, y_pred, average='macro')
    
    class_report = classification_report(
        y_test, y_pred,
        target_names=target_encoder.classes_,
        output_dict=True
    )
    class_report_text = classification_report(
        y_test, y_pred,
        target_names=target_encoder.classes_
    )
    print(f"\n  Classification Report (Test Set):")
    print(class_report_text)
    
    tree_rules = export_text(model, feature_names=FEATURE_COLUMNS)
    
    feature_importances = {
        FEATURE_COLUMNS[i]: float(model.feature_importances_[i])
        for i in range(len(FEATURE_COLUMNS))
    }
    
    clean_class_report = {}
    for key, value in class_report.items():
        if isinstance(value, dict):
            clean_class_report[key] = {k: float(v) for k, v in value.items()}
        else:
            clean_class_report[key] = float(value)

    clean_best_params = {}
    for k, v in best_params.items():
        if isinstance(v, (np.integer, int)):
            clean_best_params[k] = int(v)
        else:
            clean_best_params[k] = v

    model_info = {
        'accuracy': float(accuracy),
        'f1_score_macro': float(f1_macro),
        'best_cv_f1_score': float(best_cv_score),
        'best_params': clean_best_params,
        'n_samples': int(len(df)),
        'n_train': int(len(X_train)),
        'n_test': int(len(X_test)),
        'n_features': int(len(FEATURE_COLUMNS)),
        'feature_names': FEATURE_COLUMNS,
        'feature_importances': feature_importances,
        'target_classes': list(target_encoder.classes_),
        'tree_depth': int(model.get_depth()),
        'n_leaves': int(model.get_n_leaves()),
        'tree_rules': tree_rules,
        'classification_report': clean_class_report,
        'tipe_classes': list(label_encoders['tipe_motor'].classes_),
        'keluhan_classes': list(label_encoders['keluhan'].classes_),
    }
    
    joblib.dump({'model': model, 'info': model_info}, MODEL_PATH)
    joblib.dump({
        'label_encoders': label_encoders,
        'target_encoder': target_encoder
    }, ENCODERS_PATH)
    
    print(f"\n[OK] Model trained successfully!")
    print(f"  Accuracy: {accuracy:.2%}")
    print(f"  F1-Score (Macro): {f1_macro:.4f}")
    print(f"  Best Params: {best_params}")
    print(f"  Tree depth: {model.get_depth()}")
    print(f"  Leaf nodes: {model.get_n_leaves()}")
    print(f"  Feature importances: {feature_importances}")
    print(f"  Model saved to: {MODEL_PATH}")
    
    return accuracy


@app.route('/', methods=['GET'])
def health_check():
    return jsonify({
        'status': 'ok',
        'service': 'CART Prediction API - Bengkel Padang Motor',
        'model_loaded': model is not None,
        'dataset_columns': FEATURE_COLUMNS + [TARGET_COLUMN]
    })


@app.route('/train', methods=['POST'])
def train():
    try:
        accuracy = train_model_from_csv()
        if accuracy is None:
            return jsonify({
                'success': False,
                'error': 'Training failed. Check dataset format.'
            }), 500
            
        return jsonify({
            'success': True,
            'message': 'Model trained successfully',
            'accuracy': accuracy,
            'info': model_info
        })
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 500


@app.route('/predict', methods=['POST'])
def predict():
    try:
        if model is None:
            if not load_or_train_model():
                return jsonify({'success': False, 'error': 'Model not available'}), 500
                
        data = request.json
        if not data:
            return jsonify({'success': False, 'error': 'No JSON data provided'}), 400
            
        durasi = int(data.get('durasi', 0))
        km = int(data.get('km', 0))
        usia = int(data.get('usia', 0))
        tipe = str(data.get('tipe', ''))
        keluhan = str(data.get('keluhan', ''))
        
        X_df = pd.DataFrame({
            'durasi_hari': [durasi],
            'km_tempuh': [km],
            'usia_motor': [usia],
            'tipe_motor': [tipe],
            'keluhan': [keluhan]
        })
        
        for col in CATEGORICAL_COLS:
            le = label_encoders.get(col)
            if le:
                known_classes = set(le.classes_)
                X_df[col] = X_df[col].apply(lambda x: le.transform([x])[0] if x in known_classes else le.transform([le.classes_[0]])[0])
            else:
                 X_df[col] = 0
                
        pred_code = model.predict(X_df[FEATURE_COLUMNS])[0]
        proba = model.predict_proba(X_df[FEATURE_COLUMNS])[0]
        
        confidence = float(proba.max())
        hasil = target_encoder.inverse_transform([pred_code])[0]
        
        target_classes = target_encoder.classes_
        probabilities = {str(target_classes[i]): float(proba[i]) for i in range(len(target_classes))}
        
        return jsonify({
            'success': True,
            'prediction': str(hasil),
            'confidence': confidence,
            'probabilities': probabilities
        })
    except Exception as e:
        return jsonify({'success': False, 'error': str(e)}), 500


@app.route('/categories', methods=['GET'])
def get_categories():
    if model is None:
        load_or_train_model()
        
    try:
        return jsonify({
            'success': True,
            'tipe': list(label_encoders['tipe_motor'].classes_) if 'tipe_motor' in label_encoders else [],
            'keluhan': list(label_encoders['keluhan'].classes_) if 'keluhan' in label_encoders else [],
            'rekomendasi': list(target_encoder.classes_) if target_encoder else []
        })
    except Exception:
        return jsonify({'success': False}), 500


@app.route('/model-info', methods=['GET'])
def get_model_info():
    if model is None:
        load_or_train_model()
    return jsonify({'success': True, 'info': model_info})
    

if __name__ == '__main__':
    print("Loading CART model...")
    load_or_train_model()
    print("Starting Flask API for Prediction...")
    app.run(host='0.0.0.0', port=5000)