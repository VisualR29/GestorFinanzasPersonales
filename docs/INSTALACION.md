# Instalación y base de datos

## Requisitos

- PHP 8.2+ y extensiones habituales de Laravel (`pdo`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`).
- Composer y Node.js (para `npm run build` si cambias CSS/JS con Vite).

## Pasos

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### SQLite (rápido, ya configurado en `.env.example`)

```bash
touch database/database.sqlite   # en Windows puedes crear el archivo vacío manualmente
php artisan migrate --seed
npm ci && npm run build
php artisan serve
```

### MySQL (entrega típica con XAMPP / Laragon)

1. Crea la base `finanzas_personales` en phpMyAdmin o consola MySQL.
2. En `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finanzas_personales
DB_USERNAME=root
DB_PASSWORD=
```

3. Ejecuta:

```bash
php artisan migrate:fresh --seed
npm ci && npm run build
php artisan serve
```

## Usuarios de prueba (semillas)

Tras `php artisan migrate --seed`:

| Rol | Correo | Contraseña |
|-----|--------|------------|
| admin | `admin@finanzas.test` | `password` |
| usuario | `maria.gonzalez@example.com` | `password` |
| usuario | `carlos.vega@example.com` | `password` |

Hay **3 usuarios** en total (**1 admin** y **2 usuarios**). Cada uno tiene **6 cuentas**, todas las **categorías** definidas en `config/finanzas.php` (ingreso y gasto con cada `kind`) y **decenas de movimientos** (ingresos recurrentes y gastos variados en los últimos meses), más datos de perfil (teléfono, ciudad, etc.).
