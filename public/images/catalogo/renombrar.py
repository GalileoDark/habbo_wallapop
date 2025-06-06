import os
import unicodedata

def normalizar_nombre(nombre):
    # Reemplaza la ñ y Ñ explícitamente
    nombre = nombre.replace('ñ', 'n').replace('Ñ', 'N')
    # Elimina tildes usando unicodedata
    nombre_normalizado = unicodedata.normalize('NFKD', nombre).encode('ASCII', 'ignore').decode('utf-8')
    return nombre_normalizado

def renombrar_archivos(carpeta):
    for nombre_original in os.listdir(carpeta):
        ruta_original = os.path.join(carpeta, nombre_original)

        if os.path.isfile(ruta_original):
            nombre_normalizado = normalizar_nombre(nombre_original)

            if nombre_original != nombre_normalizado:
                ruta_nueva = os.path.join(carpeta, nombre_normalizado)

                # Evita sobrescribir archivos existentes
                if os.path.exists(ruta_nueva):
                    print(f"❌ Ya existe: {ruta_nueva}, no se renombra.")
                else:
                    os.rename(ruta_original, ruta_nueva)
                    print(f"✅ Renombrado: {nombre_original} → {nombre_normalizado}")

# Usa la carpeta deseada aquí
carpeta = 'raros'
renombrar_archivos(carpeta)
