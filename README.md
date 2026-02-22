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
- PHP 7.4+
- PDO + MySQL extension

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

## Security notes
- Do not commit real DB credentials.
- Prefer environment-variable based configuration.
- Review `SECURITY.md` for reporting vulnerabilities.

## Contributing
See `CONTRIBUTING.md`.

## License
MIT — see `LICENSE`.
