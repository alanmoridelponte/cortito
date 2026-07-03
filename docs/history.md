# Cortito - Contexto del Proyecto

## Descripción

**Cortito** es una webapp tipo pastebin para compartir snippets de código y texto. El nombre es un juego de palabras: "corto" en español + el dominio `.to` (cortito.to).

## Stack Tecnológico

- **Backend**: Laravel 13, PHP 8.3
- **Base de datos**: SQLite (archivo `database/database.sqlite`)
- **Frontend**: Tailwind CSS 4, Vite 8
- **Testing**: Pest 4
- **Formato de código**: Laravel Pint

## Arquitectura de Base de Datos

### Tabla `snippets`

```sql
id              - bigint (PK, auto-increment)
user_id         - unsigned bigint (nullable, FK → users.id, nullOnDelete)
owner_token     - varchar(64) (nullable, index) -- Hash SHA-256 del token
alias           - varchar(50) (unique) -- Ej: arbol.caoba.tornillo
title           - varchar (nullable)
content         - longText
content_type    - enum('code', 'text', 'markdown', 'html') -- default: 'text'
language        - varchar(50) (nullable) -- Para syntax highlighting
is_public       - boolean (default: true)
password        - varchar (nullable) -- Hash bcrypt
views_count     - unsigned integer (default: 0)
expires_at      - timestamp (nullable, index)
created_at      - timestamp
updated_at      - timestamp
```

### Índices

- `alias` (unique)
- `user_id`
- `owner_token`
- `expires_at`

## Reglas de Negocio

### Tipos de Usuario

| Aspecto | Anónimo (Gratis) | Logueado (Premium) |
|---------|------------------|---------------------|
| TTL | 24h fijo | 7d / 30d / 90d / 1 año / nunca |
| Contenido | Code, text | Code, text, markdown, html |
| Tamaño máximo | ~64KB | ~1MB |
| Contraseña | Opcional | Opcional |
| Privacidad | Siempre público | Público o privado |
| Identificación | Cookie `cortito_owner` | `user_id` |

### Restricción de `is_public`

- **Anónimo**: `is_public` siempre `true` (forzado en `Snippet::boot()`)
- **Logueado**: `is_public` puede ser `true` o `false`

### Generación de Alias

Formato: `palabra1.palabra2.palabra3` (ej: `arbol.caoba.tornillo`)

- Pool de ~250 palabras comunes en español (`config/wordlist.php`)
- 3 palabras aleatorias concatenadas con `.`
- Solo minúsculas, números y puntos
- Se verifica unicidad en BD
- El usuario puede editarlo al crear el snippet

### Identificación de Anónimos (Owner Token)

1. **Opt-in**: Al crear snippet, se ofrece "¿Recordar tus anotadores en este navegador?"
2. **Cookie**: Si acepta, se genera UUID v4 y se guarda en cookie `cortito_owner` (1 año, httpOnly, Secure, SameSite=Lax)
3. **Hash**: En BD se almacena `hash('sha256', $token)`, nunca el token plano
4. **Consulta**: Para ver "mis anotadores", se busca por `owner_token = hash('sha256', $cookie_value)`

## Archivos Clave

### Modelos

- `app/Models/Snippet.php` - Modelo principal con boot que fuerza `is_public` en anónimos
- `app/Models/User.php` - Modelo de usuario con relación `snippets()`

### Servicios

- `app/Support/AliasGenerator.php` - Generador de alias estilo bancario
- `app/Support/OwnerToken.php` - Manejo de cookie y hash SHA-256

### Form Requests

- `app/Http/Requests/StoreAnonymousSnippetRequest.php` - Validación para anónimos
- `app/Http/Requests/StoreLoggedSnippetRequest.php` - Validación para logueados

### Configuración

- `config/wordlist.php` - Pool de palabras para alias

### Factories

- `database/factories/SnippetFactory.php` - Factory con estados: `anonymous()`, `forUser()`, `expired()`, `protected()`, `private()`

## Flujo de la Aplicación

### Crear Snippet (Anónimo)

1. Usuario ingresa contenido
2. Se genera alias automático (ej: `arbol.caoba.tornillo`)
3. Se muestra modal: "¿Recordar tus anotadores en este navegador?"
4. Si acepta → se setea cookie `cortito_owner`
5. Usuario puede editar alias (se verifica unicidad)
6. Se guarda snippet con `user_id = NULL` y `owner_token = hash(sha256, cookie)`

### Crear Snippet (Logueado)

1. Usuario ingresa contenido
2. Se genera alias automático
3. Elige TTL (7d, 30d, 90d, 1 año, nunca)
4. Opcionalmente: contraseña, privacidad
5. Se guarda snippet con `user_id = auth()->id()`

### Ver Snippet

1. Usuario entra a `cortito.to/{alias}`
2. Se incrementa `views_count`
3. Si tiene contraseña, se pide
4. Si está expirado, se muestra error
5. Si es privado y no es el dueño, se muestra error

### "Mis Anotadores" (Anónimo)

1. Se lee cookie `cortito_owner`
2. Se busca `Snippet::where('owner_token', hash('sha256', $cookie))`
3. Se muestran los snippets encontrados

## Pendiente

- [ ] Controlador de snippets (CRUD)
- [ ] Rutas web y API
- [ ] Vistas Blade
- [ ] Job de limpieza de snippets expirados
- [ ] Autenticación de usuarios
- [ ] Página "Mis snippets" para usuarios logueados
