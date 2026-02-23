# prueba-tecnica-uniclaretiana
Prueba tecnica para uniclaretiana


# 🏗 Arquitectura del Proyecto

El sistema está construido bajo:

- Laravel (MVC)
- Eloquent ORM
- Migraciones versionadas
- Factories y Seeders
- Relaciones normalizadas
- Vue 3 Inertia
- Docker

---

# 🐳 Entorno Docker

El proyecto está completamente dockerizado para facilitar su ejecución sin instalar PHP o MySQL manualmente.

## Servicios incluidos

- PHP (Laravel)
- SqLite ó MySQL
- Servidor Web (Nginx o Apache según configuración)
- Composer
- Node (si aplica frontend)

---

# 🚀 Instalación con Docker

## 1️⃣ Clonar repositorio

```bash
git clone https://github.com/Jesus-David-Vidal-Ramirez/prueba-tecnica-uniclaretiana.git

cd prueba-tecnica-uniclaretiana

docker-compose up -d --build

docker exec -it nombre_contenedor_app bash

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan db:seed
```

---

## 📥 Instalación (Local)

Clona el repositorio:

```bash
git clone https://github.com/Jesus-David-Vidal-Ramirez/prueba-tecnica-uniclaretiana.git

cd prueba-tecnica-uniclaretiana

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan db:seed

npm install && npm run build

composer run dev
```

# Notas

Si desean usar una base de datos distinta a SqLite cambiar el DB_CONNECTION=mysql
y enviar las configuraciones contraseña, usuario, nombre de la base, puerto, igual mente si desean correr la aplicacion
con docker y no tienen problema al crear depronto el volumen o la network proceder a crearlas

```bash
docker volume create NOMBRE_VOLUMEN
docker network create NOMBRE_NETWORK
```

Cabe resaltar que si el nombre del volume y la network deben ser los mismo que en el docker-compose.yml
