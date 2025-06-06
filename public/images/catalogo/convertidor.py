from PIL import Image
import os

carpeta_origen = 'japon'
formato_origen = '.png'
formato_destino = '.gif'

carpeta_destino = os.path.join(carpeta_origen, 'convertidas')
os.makedirs(carpeta_destino, exist_ok=True)

for archivo in os.listdir(carpeta_origen):
    if archivo.lower().endswith(formato_origen):
        nombre_sin_ext = os.path.splitext(archivo)[0]
        ruta_guardado = os.path.join(carpeta_destino, f"{nombre_sin_ext}{formato_destino}")

        if os.path.exists(ruta_guardado):
            print(f"Saltado (ya existe): {nombre_sin_ext}{formato_destino}")
            continue

        ruta_imagen = os.path.join(carpeta_origen, archivo)
        imagen = Image.open(ruta_imagen)

        # Asegura modo P (palette) y mantiene transparencia
        imagen = imagen.convert('RGBA')
        fondo = Image.new("RGBA", imagen.size, (255, 255, 255, 0))  # Fondo totalmente transparente
        imagen = Image.alpha_composite(fondo, imagen).convert('P', palette=Image.ADAPTIVE)

        imagen.save(ruta_guardado, format='GIF', transparency=0)
        print(f"Convertida: {archivo} -> {nombre_sin_ext}{formato_destino}")
