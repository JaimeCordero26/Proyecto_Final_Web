# 🛒 Tienda Virtual - Proyecto Final

## 📋 Información del Proyecto

**Universidad:** [Nombre de la Universidad]  
**Curso:** Desarrollo Web con PHP  
**Docente:** Milena Vargas Blanco  
**Fecha de Entrega:** 30 de agosto 2025  
**Valor:** 30% de la calificación final  

**Integrantes del Grupo:**
DANIEL SABORIO
ALEJANDRO CORDERO
CRISTIAN ROJAS
RAUL
---

## 🎯 Descripción del Proyecto

Este proyecto consiste en el desarrollo de una tienda virtual completa utilizando PHP con el framework Laravel. La aplicación permite a los usuarios registrarse, navegar por categorías de productos, gestionar un carrito de compras, realizar transacciones seguras y administrar sus pedidos.

### ✨ Funcionalidades Implementadas

#### 🔐 1. Autenticación y Gestión de Usuarios
- ✅ Registro de usuarios nuevos
- ✅ Inicio y cierre de sesión seguros
- ✅ Perfil de usuario con edición de datos personales
- ✅ Historial de pedidos por usuario

#### 🛍️ 2. Catálogo de Productos
- ✅ Categorización de productos (Electrónica, Ropa, Hogar, etc.)
- ✅ Listado de productos con descripción, precio e imágenes
- ✅ Búsqueda y filtrado por nombre, categoría y precio
- ✅ Paginación de resultados

#### 🛒 3. Carrito de Compras
- ✅ Agregar, eliminar y actualizar productos
- ✅ Cálculo automático de totales (subtotal, impuestos, envío)
- ✅ Persistencia para usuarios registrados y guests
- ✅ Gestión de cantidades

#### 💳 4. Proceso de Compra
- ✅ Pasarela de pago simulada
- ✅ Opciones de pago: Tarjeta de crédito, PayPal
- ✅ Generación de facturas con identificación de usuario
- ✅ Confirmación de pedido con número de seguimiento
- ✅ Limpieza automática del carrito después de la compra

#### 📊 5. Reportes y Administración
- ✅ Generación de reportes de ventas
- ✅ Panel de administración con Filament
- ✅ Gestión de productos, categorías y órdenes

#### 🔒 6. Seguridad
- ✅ Validación de entradas contra SQL injection y XSS
- ✅ Contraseñas cifradas con bcrypt
- ✅ Sesiones seguras
- ✅ Certificado SSL implementado
- ✅ Middleware de autenticación

---

## 🛠️ Tecnologías Utilizadas

### Backend
- **PHP 8.2+** con Laravel 10.x
- **MySQL** como base de datos principal
- **Laravel Breeze** para autenticación
- **Laravel Filament** para panel administrativo

### Frontend
- **HTML5** con Blade templates
- **Tailwind CSS** para estilos
- **JavaScript** vanilla para interacciones
- **Vite** como bundler de assets

### Servicios
- **Apache** como servidor web
- **GitHub Pages** para hosting
- **Let's Encrypt** para certificado SSL gratuito

---

## 📦 Estructura del Proyecto
ProyectoFinal/
├── app/
│ ├── Filament/ # Recursos del panel administrativo
│ ├── Http/
│ │ ├── Controllers/ # Controladores de la aplicación
│ │ └── Middleware/ # Middlewares personalizados
│ ├── Models/ # Modelos Eloquent
│ ├── Policies/ # Políticas de autorización
│ ├── Providers/ # Service Providers
│ └── Services/ # Servicios de negocio
├── bootstrap/
│ ├── app.php # Inicialización de la aplicación
│ └── cache/ # Cache de la aplicación
├── config/ # Archivos de configuración
├── database/
│ ├── migrations/ # Migraciones de base de datos
│ ├── seeders/ # Seeders para datos de prueba
│ └── factories/ # Factories para testing
├── public/ # Archivos públicos accesibles
│ ├── css/ # Stylesheets compilados
│ ├── js/ # JavaScript compilado
│ └── storage/ # Enlace simbólico a storage
├── resources/
│ ├── views/ # Vistas Blade
│ ├── css/ # CSS fuente (Tailwind)
│ └── js/ # JavaScript fuente
├── routes/ # Definición de rutas
├── storage/ # Almacenamiento de archivos
├── tests/ # Pruebas automatizadas
├── vendor/ # Dependencias de Composer
└── vite.config.js # Configuración de Vite


```bash
# Instalar dependencias de Composer y Node.js
composer install
npm install

# Copiar archivo de entorno
cp .env.example .env

# Generar key de la aplicación
php artisan key:generate

#.ENV
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Ejecutar migraciones
php artisan migrate --seed

# Crear enlace simbólico para almacenamiento
php artisan storage:link

# Instalar y configurar permisos (opcional para admin)
composer require bezhansalleh/filament-shield
php artisan shield:install
php artisan shield:generate --all
php artisan db:seed --class=ShieldSeeder

# Limpiar cachés
php artisan optimize:clear
php artisan route:clear
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan permission:cache-reset

# Optimizar aplicación
php artisan optimize

# Sincronizar productos desde API externa
php artisan platzi:sync --download-images --max=60

php artisan serve
npm run dev

#PROBLEMAS POSIBLES
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
npm run build

php artisan storage:link

php artisan platzi:sync --dry-run

php artisan platzi:sync

php artisan platzi:sync --download-images

php artisan platzi:sync --max=60

php artisan optimize:clear

php artisan migrate --seed
