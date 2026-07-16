# cortito

Acortador de enlaces + pastebin con onda argentina. Pegás un texto o una URL, te queda un alias corto tipo `banco.pera.rio` y lo compartís. Sin cuenta obligatoria.

Corre en Laravel 13 / PHP 8.5, Tailwind v4 + Alpine, servido por Herd en **https://cortito.test**.

## Qué hace

- **Acortar URLs**: pegás un link, te devuelve un alias corto que redirige (302).
- **Compartir texto / snippets**: notas, código (con lenguaje), lo que sea. Se muestra en una página propia.
- **Alias estilo bancario**: se autogenera como `palabra.palabra.palabra` desde una wordlist (`config/wordlist.php`). Podés reroll o escribir uno propio (`checkAlias` valida formato y disponibilidad en vivo).
- **Sin registro**: la propiedad de tus cortitos se guarda con un *owner token* (cookie + sesión), así podés editarlos/borrarlos después. Requiere aceptar el consentimiento de cookies.
- **Protección con contraseña**: opcional, hasheada. 5 intentos por IP y quedás bloqueado 15 min.
- **Expiración (TTL)**: anónimos expiran a las 24 h; usuarios registrados eligen 7d / 30d / 90d / 1a / nunca.
- **Contador de vistas** y marca de "editado".

### Límites

| | Anónimo | Registrado |
|---|---|---|
| Cortitos activos | 10 por owner token (y 50 por IP) | ilimitados |
| Tamaño de contenido | 5 KB | 1 MB |
| TTL | 24 h fijo | configurable |
| Público/privado | siempre público | elegible |

Todas las rutas tienen *rate limiting* por IP (crear, ver, editar, borrar, chequeo de alias, intento de contraseña) — definidos en `app/Providers/AppServiceProvider.php`.

## Cómo funciona (flujo)

Todo pasa por `SnippetController` (`routes/web.php`):

1. **`GET /`** — home: genera un alias sugerido, muestra tus cortitos (por owner token o por cuenta) y el form.
2. **`POST /snippets`** — crea el cortito. Si es anónimo, valida consentimiento de cookies, aplica límites por owner token / IP, setea la cookie de propiedad y TTL de 24 h. Si es URL redirige a home; si es texto, a la página del cortito.
3. **`GET /{alias}`** — lo muestra. Si es URL, redirige al destino. Si está protegido, pide contraseña (`POST /{alias}`). Si expiró, vuelve a home con aviso.
4. **`GET /{alias}/edit` · `PUT /{alias}` · `DELETE /{alias}`** — editar/borrar, sólo si el owner token o el `user_id` coinciden (`Snippet::canBeEditedBy`).

La propiedad anónima está en `app/Support/OwnerToken.php` (genera/hashea el token, lo lee de cookie o sesión). Los alias, en `app/Support/AliasGenerator.php`.

## Puesta en marcha

Con [Herd](https://herd.laravel.com) el sitio ya está en `https://cortito.test`. Setup inicial:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev        # o: npm run build para producción
```

Desarrollo con todo junto (server + queue + vite):

```bash
composer run dev
```

## Tests

```bash
php artisan test --compact
```

Pest 4. La cobertura del dominio está en `tests/Feature/SnippetControllerTest.php`.

## Stack

- **Laravel 13** / **PHP 8.5**
- **Tailwind CSS v4** + **Alpine.js** (Vite)
- SQLite por defecto (`database/database.sqlite`)
- Cola y cache sobre base de datos
