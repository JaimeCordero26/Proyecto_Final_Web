# 🛒 Tienda Virtual - Proyecto Final

## 📋 Información del Proyecto

**Universidad:** [Nombre de la Universidad]  
**Curso:** Desarrollo Web con PHP  
**Docente:** Milena Vargas Blanco  
**Fecha de Entrega:** 30 de agosto 2025  
**Valor:** 30% de la calificación final  

**Integrantes del Grupo:**
DANIEL SABORIO- 
ALEJANDRO CORDERO- 
CRISTIAN ROJAS- 
RAUL Quesada
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
```
├── app
│   ├── Console
│   ├── Events
│   ├── Filament
│   ├── Http
│   ├── Listeners
│   ├── Livewire
│   ├── Models
│   ├── Policies
│   ├── Providers
│   └── View
├── artisan
├── bootstrap
│   ├── app.php
│   ├── cache
│   └── providers.php
├── composer.json
├── composer.lock
├── config
│   ├── app.php
│   ├── auth.php
│   ├── cache.php
│   ├── database.php
│   ├── dompdf.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── mail.php
│   ├── permission.php
│   ├── queue.php
│   ├── services.php
│   └── session.php
├── database
│   ├── database.sqlite
│   ├── factories
│   ├── migrations
│   └── seeders
├── node_modules
│   ├── @alloc
│   ├── alpinejs
│   ├── @ampproject
│   ├── ansi-regex
│   ├── ansi-styles
│   ├── anymatch
│   ├── any-promise
│   ├── arg
│   ├── asynckit
│   ├── autoprefixer
│   ├── axios
│   ├── balanced-match
│   ├── binary-extensions
│   ├── brace-expansion
│   ├── braces
│   ├── browserslist
│   ├── call-bind-apply-helpers
│   ├── camelcase-css
│   ├── caniuse-lite
│   ├── chalk
│   ├── chokidar
│   ├── chownr
│   ├── cliui
│   ├── color-convert
│   ├── color-name
│   ├── combined-stream
│   ├── commander
│   ├── concurrently
│   ├── cross-spawn
│   ├── cssesc
│   ├── delayed-stream
│   ├── detect-libc
│   ├── didyoumean
│   ├── dlv
│   ├── dunder-proto
│   ├── eastasianwidth
│   ├── electron-to-chromium
│   ├── emoji-regex
│   ├── enhanced-resolve
│   ├── @esbuild
│   ├── esbuild
│   ├── escalade
│   ├── es-define-property
│   ├── es-errors
│   ├── es-object-atoms
│   ├── es-set-tostringtag
│   ├── fast-glob
│   ├── fastq
│   ├── fdir
│   ├── fill-range
│   ├── follow-redirects
│   ├── foreground-child
│   ├── form-data
│   ├── fraction.js
│   ├── function-bind
│   ├── get-caller-file
│   ├── get-intrinsic
│   ├── get-proto
│   ├── glob
│   ├── glob-parent
│   ├── gopd
│   ├── graceful-fs
│   ├── has-flag
│   ├── hasown
│   ├── has-symbols
│   ├── has-tostringtag
│   ├── @isaacs
│   ├── is-binary-path
│   ├── is-core-module
│   ├── isexe
│   ├── is-extglob
│   ├── is-fullwidth-code-point
│   ├── is-glob
│   ├── is-number
│   ├── jackspeak
│   ├── jiti
│   ├── @jridgewell
│   ├── laravel-vite-plugin
│   ├── lightningcss
│   ├── lightningcss-linux-x64-gnu
│   ├── lightningcss-linux-x64-musl
│   ├── lilconfig
│   ├── lines-and-columns
│   ├── lodash
│   ├── lru-cache
│   ├── magic-string
│   ├── math-intrinsics
│   ├── merge2
│   ├── micromatch
│   ├── mime-db
│   ├── mime-types
│   ├── minimatch
│   ├── minipass
│   ├── mini-svg-data-uri
│   ├── minizlib
│   ├── mkdirp
│   ├── mz
│   ├── nanoid
│   ├── @nodelib
│   ├── node-releases
│   ├── normalize-path
│   ├── normalize-range
│   ├── object-assign
│   ├── object-hash
│   ├── package-json-from-dist
│   ├── path-key
│   ├── path-parse
│   ├── path-scurry
│   ├── picocolors
│   ├── picomatch
│   ├── pify
│   ├── pirates
│   ├── postcss
│   ├── postcss-import
│   ├── postcss-js
│   ├── postcss-load-config
│   ├── postcss-nested
│   ├── postcss-selector-parser
│   ├── postcss-value-parser
│   ├── proxy-from-env
│   ├── queue-microtask
│   ├── read-cache
│   ├── readdirp
│   ├── require-directory
│   ├── resolve
│   ├── reusify
│   ├── @rollup
│   ├── rollup
│   ├── run-parallel
│   ├── rxjs
│   ├── shebang-command
│   ├── shebang-regex
│   ├── shell-quote
│   ├── signal-exit
│   ├── source-map-js
│   ├── string-width
│   ├── string-width-cjs
│   ├── strip-ansi
│   ├── strip-ansi-cjs
│   ├── sucrase
│   ├── supports-color
│   ├── supports-preserve-symlinks-flag
│   ├── @tailwindcss
│   ├── tailwindcss
│   ├── tapable
│   ├── tar
│   ├── thenify
│   ├── thenify-all
│   ├── tinyglobby
│   ├── to-regex-range
│   ├── tree-kill
│   ├── ts-interface-checker
│   ├── tslib
│   ├── @types
│   ├── update-browserslist-db
│   ├── util-deprecate
│   ├── vite
│   ├── vite-plugin-full-reload
│   ├── @vue
│   ├── which
│   ├── wrap-ansi
│   ├── wrap-ansi-cjs
│   ├── y18n
│   ├── yallist
│   ├── yaml
│   ├── yargs
│   └── yargs-parser
├── package.json
├── package-lock.json
├── phpunit.xml
├── postcss.config.js
├── public
│   ├── apple-touch-icon.png
│   ├── build
│   ├── css
│   ├── favicon.ico
│   ├── favicon.svg
│   ├── hot
│   ├── images
│   ├── index.php
│   ├── js
│   ├── robots.txt
│   └── storage -> /home/alecor/Documents/aaaaa/Proyecto_Final_Web/storage/app/public
├── README.md
├── resources
│   ├── css
│   ├── js
│   └── views
├── routes
│   ├── auth.php
│   ├── console.php
│   └── web.php
├── storage
│   ├── app
│   ├── framework
│   └── logs
├── tailwind.config.js
├── tests
│   ├── Feature
│   ├── Pest.php
│   ├── TestCase.php
│   └── Unit
├── vendor
│   ├── anourvalar
│   ├── autoload.php
│   ├── barryvdh
│   ├── bezhansalleh
│   ├── bin
│   ├── blade-ui-kit
│   ├── brianium
│   ├── brick
│   ├── carbonphp
│   ├── composer
│   ├── danharrin
│   ├── dflydev
│   ├── doctrine
│   ├── dompdf
│   ├── dragonmantank
│   ├── egulias
│   ├── fakerphp
│   ├── fidry
│   ├── filament
│   ├── filp
│   ├── fruitcake
│   ├── graham-campbell
│   ├── guzzlehttp
│   ├── hamcrest
│   ├── jean85
│   ├── kirschbaum-development
│   ├── laravel
│   ├── league
│   ├── livewire
│   ├── masterminds
│   ├── mockery
│   ├── monolog
│   ├── myclabs
│   ├── nesbot
│   ├── nette
│   ├── nikic
│   ├── nunomaduro
│   ├── openspout
│   ├── pestphp
│   ├── pest-plugins.json
│   ├── phar-io
│   ├── phpdocumentor
│   ├── phpoption
│   ├── phpstan
│   ├── phpunit
│   ├── psr
│   ├── psy
│   ├── ralouphie
│   ├── ramsey
│   ├── ryangjchandler
│   ├── sabberworm
│   ├── sebastian
│   ├── spatie
│   ├── staabm
│   ├── stripe
│   ├── symfony
│   ├── ta-tikoma
│   ├── theseer
│   ├── tijsverkoyen
│   ├── vlucas
│   ├── voku
│   └── webmozart
└── vite.config.js

```

```bash
# Instalar dependencias de Composer y Node.js
composer install
npm install

# En caso de no tener vite
npm install --save-dev vite laravel-vite-plugin
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
