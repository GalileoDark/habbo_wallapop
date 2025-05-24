import os
import requests
from bs4 import BeautifulSoup
import re

# Configuración
url = "https://habtium.es/furni/category/113"
output_folder = "ventanas"
headers = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36"
}

os.makedirs(output_folder, exist_ok=True)

def clean_filename(name):
    name = re.sub(r'[\\/*?:"<>|]', "", name)
    return name.replace(" ", "_").lower()

def get_unique_filename(folder, base_name, extension):
    """Genera nombres de archivo únicos añadiendo sufijos numéricos"""
    counter = 1
    while True:
        if counter == 1:
            filename = f"{base_name}{extension}"
        else:
            filename = f"{base_name}_{counter}{extension}"
        
        if not os.path.exists(os.path.join(folder, filename)):
            return filename
        counter += 1

try:
    response = requests.get(url, headers=headers)
    response.raise_for_status()
    soup = BeautifulSoup(response.text, 'html.parser')
except Exception as e:
    print(f"❌ Error al cargar la página: {e}")
    exit()

furni_boxes = soup.find_all('div', class_='furni-box')

if not furni_boxes:
    print("⚠️ No se encontraron furnis.")
    exit()

# Diccionario para rastrear nombres usados
used_names = {}

for box in furni_boxes:
    name_div = box.find('div', class_='name')
    if not name_div:
        continue
        
    original_name = name_div.text.strip()
    clean_name = clean_filename(original_name)
    
    # Procesar imagen GRANDE
    big_style = box.get('style', '')
    if 'background-image' in big_style:
        big_url = big_style.split("url('")[1].split("')")[0]
        extension = os.path.splitext(big_url)[1]
        
        # Generar nombre único
        big_filename = get_unique_filename(output_folder, clean_name, extension)
        
        try:
            img_data = requests.get(big_url, headers=headers).content
            with open(os.path.join(output_folder, big_filename), 'wb') as f:
                f.write(img_data)
            print(f"✅ Grande: {big_filename}")
            used_names[big_filename] = True
        except Exception as e:
            print(f"❌ Error al descargar {big_url}: {e}")

    # Procesar icono PEQUEÑO
    icon = box.find('div', class_='icon')
    if icon:
        small_style = icon.get('style', '')
        if 'background-image' in small_style:
            small_url = small_style.split("url('")[1].split("')")[0]
            extension = os.path.splitext(small_url)[1]
            
            # Generar nombre único para el icono
            small_filename = get_unique_filename(output_folder, f"{clean_name}_icono", extension)
            
            try:
                img_data = requests.get(small_url, headers=headers).content
                with open(os.path.join(output_folder, small_filename), 'wb') as f:
                    f.write(img_data)
                print(f"✅ Icono: {small_filename}")
                used_names[small_filename] = True
            except Exception as e:
                print(f"❌ Error al descargar {small_url}: {e}")

print(f"\n🎉 ¡Descarga completada! Todas las imágenes en: {os.path.abspath(output_folder)}")