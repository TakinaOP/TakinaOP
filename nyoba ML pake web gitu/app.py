from flask import Flask, render_template, request, jsonify
import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler

# Metrics & Models
from sklearn.metrics import accuracy_score, mean_squared_error, mean_absolute_error
from sklearn.tree import DecisionTreeClassifier
from sklearn.ensemble import RandomForestClassifier, RandomForestRegressor
from sklearn.svm import SVC, SVR
from sklearn.naive_bayes import GaussianNB
from sklearn.linear_model import LogisticRegression, LinearRegression, Ridge
from sklearn.neighbors import KNeighborsRegressor
from xgboost import XGBRegressor
from sklearn.neural_network import MLPRegressor

# Deep Learning Check
try:
    import tensorflow as tf
    from tensorflow.keras.models import Sequential
    from tensorflow.keras.layers import Dense, SimpleRNN, LSTM
    TF_AVAILABLE = True
except ImportError:
    TF_AVAILABLE = False
    print("WARNING: TensorFlow belum terinstall. Fitur DL dimatikan.")

app = Flask(__name__)

# --- GLOBAL VARIABLES ---
models = {}
metrics = {}
scaler = StandardScaler()
map_data_json = []

def generate_data():
    """Membuat data dummy yang PASTI memiliki 2 kelas (Hujan & Tidak)"""
    np.random.seed(42)
    n = 500
    
    # Generate Fitur
    lat = np.random.uniform(-7.8, -6.2, n)
    lon = np.random.uniform(106.5, 108.8, n)
    suhu = np.random.uniform(18, 35, n)
    lembab = np.random.uniform(50, 99, n)
    tekanan = np.random.uniform(1000, 1015, n)
    angin = np.random.uniform(0, 20, n)
    
    # --- PERBAIKAN RUMUS AGAR TIDAK ERROR VALUEERROR ---
    # Kita buat logic sederhana:
    # Setengah data pertama kita paksa hujan ringan/tidak hujan
    # Setengah data kedua kita paksa hujan lebat
    half = n // 2
    
    hujan_mm = np.zeros(n)
    
    # Grup 1: Hujan Kecil (0 - 5 mm)
    hujan_mm[:half] = np.random.uniform(0, 4, half)
    
    # Grup 2: Hujan Besar (6 - 50 mm)
    hujan_mm[half:] = np.random.uniform(6, 50, half)
    
    # Shuffle (Aduk data agar tidak urut)
    indices = np.arange(n)
    np.random.shuffle(indices)
    
    lat = lat[indices]
    lon = lon[indices]
    suhu = suhu[indices]
    lembab = lembab[indices]
    tekanan = tekanan[indices]
    angin = angin[indices]
    hujan_mm = hujan_mm[indices]
    
    # Threshold Klasifikasi: > 5mm = Hujan (1), <= 5mm = Tidak Hujan (0)
    hujan_class = np.where(hujan_mm > 5, 1, 0)
    
    df = pd.DataFrame({
        'lat': lat, 'lon': lon, 'suhu': suhu, 'lembab': lembab, 
        'tekanan': tekanan, 'angin': angin,
        'hujan_mm': hujan_mm, 'hujan_class': hujan_class
    })
    return df

def train_all_models():
    global models, metrics, scaler, map_data_json
    
    df = generate_data()
    map_data_json = df.to_dict(orient='records') # Simpan data untuk peta
    
    X = df[['lat', 'lon', 'suhu', 'lembab', 'tekanan', 'angin']]
    y_class = df['hujan_class']
    y_reg = df['hujan_mm']
    
    # Scaling
    X_scaled = scaler.fit_transform(X)
    X_train, X_test, yc_train, yc_test, yr_train, yr_test = train_test_split(X_scaled, y_class, y_reg, test_size=0.2, random_state=42)
    
    print("--- SEDANG MELATIH MODEL (TUNGGU SEBENTAR) ---")

    # 1. KLASIFIKASI
    cls_models = {
        'Logistic Regression': LogisticRegression(),
        'Decision Tree': DecisionTreeClassifier(),
        'Random Forest Cls': RandomForestClassifier(),
        'SVM Cls': SVC(probability=True),
        'Naive Bayes': GaussianNB()
    }
    for name, model in cls_models.items():
        try:
            model.fit(X_train, yc_train)
            acc = accuracy_score(yc_test, model.predict(X_test))
            models[name] = model
            metrics[name] = {'type': 'Klasifikasi', 'accuracy': round(acc * 100, 2)}
        except Exception as e:
            print(f"Gagal melatih {name}: {e}")

    # 2. REGRESI
    reg_models = {
        'Linear Regression': LinearRegression(), 'Ridge': Ridge(), 'KNN': KNeighborsRegressor(),
        'SVR': SVR(), 'Random Forest Reg': RandomForestRegressor(), 'XGBoost': XGBRegressor(),
        'ANN (MLP)': MLPRegressor(max_iter=500)
    }
    for name, model in reg_models.items():
        try:
            model.fit(X_train, yr_train)
            pred = model.predict(X_test)
            metrics[name] = {'type': 'Regresi', 
                             'rmse': round(np.sqrt(mean_squared_error(yr_test, pred)), 2),
                             'mae': round(mean_absolute_error(yr_test, pred), 2)}
            models[name] = model
        except: pass

    # 3. DEEP LEARNING
    if TF_AVAILABLE:
        X_train_dl = X_train.reshape((X_train.shape[0], 1, X_train.shape[1]))
        lstm = Sequential([LSTM(50, activation='relu', input_shape=(1, 6)), Dense(1)])
        lstm.compile(optimizer='adam', loss='mse')
        lstm.fit(X_train_dl, yr_train, epochs=3, verbose=0)
        models['LSTM'] = lstm
        metrics['LSTM'] = {'type': 'Deep Learning', 'rmse': 'N/A', 'mae': 'N/A'}

    print("--- SELESAI ---")

train_all_models()

@app.route('/')
def index():
    return render_template('index.html', metrics=metrics)

@app.route('/api/map-data')
def get_map_data():
    return jsonify(map_data_json)

@app.route('/predict', methods=['POST'])
def predict():
    data = request.json
    model_name = data['model_name']
    features = np.array([[float(data['lat']), float(data['lon']), float(data['suhu']), 
                          float(data['lembab']), float(data['tekanan']), float(data['angin'])]])
    features_scaled = scaler.transform(features)
    
    result = 0
    tipe = "Unknown"
    
    if model_name in models:
        model = models[model_name]
        if model_name == 'LSTM':
            features_dl = features_scaled.reshape((1, 1, 6))
            pred = model.predict(features_dl)
            result = float(pred[0][0])
            tipe = "Curah Hujan (mm)"
        elif metrics[model_name]['type'] == 'Klasifikasi':
            pred = model.predict(features_scaled)
            result = "Hujan" if pred[0] == 1 else "Tidak Hujan"
            tipe = "Status"
        else:
            pred = model.predict(features_scaled)
            result = round(float(pred[0]), 2)
            tipe = "Curah Hujan (mm)"
            
    return jsonify({'result': result, 'tipe': tipe, 'model': model_name})

if __name__ == '__main__':
    app.run(debug=True)