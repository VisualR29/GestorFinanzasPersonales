# Tabla de accesos por rol (aplicación web)

| Ruta (método) | Nombre | `admin` | `usuario` | Notas |
|---------------|--------|---------|-----------|-------|
| `/` (GET) | Home | Público (redirige a dashboard si hay sesión) | Igual | — |
| `/login`, `/register` (GET/POST) | Breeze | Público | Público | Autenticación |
| `/dashboard` (GET) | Panel | Sí | Sí | Requiere `auth` + `verified` |
| `/cuentas` CRUD | Cuentas | Sí (ve todas) | Sí (solo propias) | Middleware `auth`, `verified` |
| `/categorias` CRUD | Categorías | Sí (ve todas) | Sí (solo propias) | Idem |
| `/ingresos` (GET), `/ingresos/nuevo` (GET) | Atajo ingresos | Sí | Sí | Redirige a `/movimientos?tipo=ingreso` y `/movimientos/nuevo?tipo=ingreso` |
| `/ingresos` (POST) | Registrar ingreso (compat.) | Sí | Sí | Misma validación que movimiento con categoría ingreso; redirige al listado filtrado |
| `/movimientos` CRUD | Movimientos | Sí (ve todos) | Sí (solo propios) | `?tipo=ingreso` o `?tipo=gasto` filtra la lista y muestra totales |
| `/admin/usuarios` (GET) | Lista usuarios | Sí | **No** | Middleware `auth`, `verified`, `role:admin` |
| `/admin/usuarios/{user}/rol` (PATCH) | Actualizar rol de usuario | Sí | **No** | `role`: `admin` o `usuario`; no deja sin ningún admin |
| `/profile` | Perfil Breeze | Sí | Sí | Middleware `auth` |

# API REST (`/api`)

Todas las rutas bajo `api/*` devuelven **JSON**.

| Endpoint | Métodos | Autenticación | `admin` | `usuario` |
|----------|---------|---------------|---------|-----------|
| `/api/login` | POST | No (público) | Emite token | Emite token |
| `/api/logout` | POST | Bearer token (Sanctum) | Cierra token actual | Igual |
| `/api/accounts` | GET, POST | Sanctum | Lista/crea para cualquier usuario (opcional `user_id`) | Solo su `user_id` |
| `/api/accounts/{id}` | GET, PUT/PATCH, DELETE | Sanctum | Cualquier cuenta | Solo cuentas propias |
| `/api/categories` | GET, POST | Sanctum | Campo opcional `kind` | Igual |
| `/api/categories/{id}` | GET, PUT/PATCH, DELETE | Sanctum | Idem | Idem |
| `/api/transactions` | GET, POST | Sanctum | Igual | Igual |
| `/api/transactions/{id}` | GET, PUT/PATCH, DELETE | Sanctum | Idem | Idem |

# Postman (referencia)

1. `POST /api/login` con JSON: `{"email":"admin@finanzas.test","password":"password"}` (ver semillas en `DatabaseSeeder`).
2. Copiar `token` de la respuesta.
3. Peticiones siguientes: cabecera `Authorization: Bearer {token}` y `Accept: application/json`.
