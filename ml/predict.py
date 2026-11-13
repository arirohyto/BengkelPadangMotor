import sys
import joblib
import pandas as pd

try:
    durasi_input = int(sys.argv[1])
    km_input = int(sys.argv[2])
    usia_input = int(sys.argv[3])
    tipe_input = sys.argv[4]     
    keluhan_input = sys.argv[5]  

    model_data = joblib.load('ml/cart_model.joblib')
    model = model_data['model']

    encoders_data = joblib.load('ml/encoders.joblib')
    le_tipe = encoders_data['label_encoders']['tipe_motor']
    le_keluhan = encoders_data['label_encoders']['keluhan']
    le_target = encoders_data['target_encoder']

    try:
        tipe_code = le_tipe.transform([tipe_input])[0]
        keluhan_code = le_keluhan.transform([keluhan_input])[0]
    except ValueError:
        tipe_code = 0 
        keluhan_code = 0

    X_new = pd.DataFrame([[durasi_input, km_input, usia_input, tipe_code, keluhan_code]], 
                         columns=['durasi_hari', 'km_tempuh', 'usia_motor', 'tipe_motor', 'keluhan'])

    prediction_code = model.predict(X_new)[0]
    
    hasil_teks = le_target.inverse_transform([prediction_code])[0]

    print(hasil_teks)

except Exception as e:
    print("Error:", str(e))