# orm

A lightweight PHP ORM-style helper for CRUD operations on MySQL using PDO.

## Status
Hobby project, provided as-is.

## Features
- CRUD support (create, read, update, delete)
- Composite primary key support
- Relation mapping between table fields and object properties
- Reusable database connection and prepared statement caching

## Requirements
- PHP 8.1+
- PDO + MySQL extension
- Composer (for dev/test workflows)

## Quick start
1. Copy `config.example.php` to `config.php` (or set environment variables).
2. Set these environment variables as needed:
   - `ORM_HOST`
   - `ORM_DB`
   - `ORM_NORMAL_USER`
   - `ORM_NORMAL_PASS`
   - `ORM_ADMIN_USER`
   - `ORM_ADMIN_PASS`
3. Use classes in this repo from your own model classes that extend `DataBoundObject`.

## Project layout
- `Database.php` — database connection and query helpers
- `DataBoundObject.php` — core ORM behavior
- `config.php` / `config.example.php` — runtime configuration
- `examples/` — sample usage
- `docs/DEVELOPER_GUIDE.md` — developer-facing architecture/setup guide
- `docs/EXAMPLES.md` — examples index

## Security notes
- Do not commit real DB credentials.
- Prefer environment-variable based configuration.
- Review `SECURITY.md` for reporting vulnerabilities.

## Testing
```bash
composer install
composer test
```

## Documentation
- Developer guide: `docs/DEVELOPER_GUIDE.md`
- Examples index: `docs/EXAMPLES.md`

## Contributing
See `CONTRIBUTING.md`.

## License
MIT — see `LICENSE`.
