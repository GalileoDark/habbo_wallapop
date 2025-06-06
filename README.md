# 🧱 Habbo Wallapop

Este es mi **proyecto de final de grado superior**.  
Se trata de una plataforma inspirada en Wallapop, pero ambientada en el universo de **Habbo Hotel**. Su finalidad es puramente **educativa y de autoaprendizaje**.

> ⚠️ Todas las imágenes utilizadas pertenecen a sus respectivos dueños. Este proyecto **no tiene fines comerciales**.

---

## 🚀 Tecnologías utilizadas

- PHP
- Symfony
- Doctrine ORM
- Twig
- Docker
- Composer
- PHPStorm (IDE recomendado)

---

## 🧩 ¿Qué es Habbo Wallapop?

Habbo Wallapop es una aplicación donde los usuarios pueden:

- Publicar objetos del universo Habbo para vender o mostrar.
- Consultar un catálogo con precios orientativos y referencias.
- Comunicarse con otros usuarios interesados mediante mensajes.

El objetivo es ofrecer un entorno más claro sobre la economía del juego, fomentando un intercambio sano y orientado.

---

## 🛠️ Requisitos previos

Antes de empezar asegúrate de tener instalado en tu máquina:

- PHP 8.x o superior
- Composer
- Symfony CLI
- Docker Desktop
- Un IDE como PHPStorm o Visual Studio Code

---

## 🧪 Pasos para desplegar el proyecto

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/habbo-wallapop.git
cd habbo-wallapop
```

### 2. Crear y configurar contenedor Docker

- Abre Docker Desktop.
- Crea un contenedor con MySQL (puedes usar Docker Compose o hacerlo manualmente).
- Apunta la configuración del entorno al contenedor (usuario, contraseña, puerto, base de datos).

### 3. Configurar archivo `.env.local`

Crea el archivo `.env.local` en la raíz del proyecto y configura la conexión a la base de datos:

```dotenv
DATABASE_URL="mysql://usuario:contraseña@127.0.0.1:puerto/nombre_basededatos"
```

> Sustituye los valores con los que hayas usado en Docker.

### 4. Instalar dependencias

```bash
composer install
```

### 5. Importar datos de la base de datos

- Ubica el archivo `backup.sql` dentro del proyecto.
- Abre una terminal con acceso al contenedor de MySQL.
- Importa los datos con:

```bash
docker exec -i nombre_contenedor mysql -u usuario -p nombre_basededatos < backup.sql
```

### 6. Iniciar el servidor

Con Symfony CLI:

```bash
symfony server:start
```

O con el servidor embebido de PHP:

```bash
php -S localhost:8000 -t public
```

---

## 📚 Funcionalidades

- 🧍‍♂️ Registro y autenticación de usuarios.
- 📦 Creación de publicaciones con imágenes, título, descripción y precios.
- 💬 Sistema de mensajería privada entre usuarios.
- 🏷️ Catálogo de objetos de Habbo con valor orientativo.
- 🔍 Filtros de búsqueda y categorías.

---

## 🎯 Objetivo del proyecto

La idea es tener un espacio web para que los jugadores de Habbo puedan:

- Publicar sus objetos a modo de tienda o vitrina.
- Consultar los valores estimados del mercado.
- Contactar con otros usuarios para negociar.

Esto pretende ayudar a construir una **economía más transparente y justa dentro del juego**.

---

## 📝 Notas finales

Este proyecto ha sido desarrollado con fines educativos como parte del **proyecto final de ciclo formativo de grado superior**.

> ⚠️ **Disclaimer**: No está afiliado ni asociado de ninguna manera con Sulake, Habbo Hotel o sus marcas.  
> Proyecto de aprendizaje sin ánimo de lucro.
