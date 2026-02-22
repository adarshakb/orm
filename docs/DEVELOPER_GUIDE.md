# Developer Guide

This guide explains how to develop, test, and extend `orm`.

## 1) Local setup

```bash
git clone https://github.com/adarshakb/orm
cd orm
cp config.example.php config.php
composer install
```

Set environment variables (recommended) instead of hardcoding credentials:

- `ORM_HOST`
- `ORM_DB`
- `ORM_NORMAL_USER`
- `ORM_NORMAL_PASS`
- `ORM_ADMIN_USER`
- `ORM_ADMIN_PASS`
- optional: `ORM_DSN`

## 2) Run checks

```bash
composer test
php -l Database.php
php -l DataBoundObject.php
```

CI also runs composer validation, syntax checks, and PHPUnit.

## 3) Architecture

- `Database.php`
  - central static DB utility
  - prepared statement cache
  - `setConnection(PDO)` / `resetConnection()` for tests
- `DataBoundObject.php`
  - base ORM mapping layer for table-backed objects
  - supports composite keys
  - insert/save/load behaviors via relation map

## 4) Extending the ORM

To model a table, create a class that extends `DataBoundObject` and implement:

- `DefineTableName()`
- `DefineRelationMap()`
- `DefineID()`
- `DefineAutoIncrementField()`

See `examples/minimal/` for a complete minimal walkthrough.

## 5) Backward compatibility notes

The project keeps the historical static API shape in `Database` to reduce break risk.
When modernizing internals, preserve public method names/signatures unless making a major-version change.

## 6) Testing strategy

Current regression tests focus on database CRUD behavior through `Database` using SQLite in-memory.
Recommended future additions:

- coverage for `DataBoundObject` lifecycle methods
- error-path tests for invalid mappings and missing IDs
- integration tests with MySQL in CI (optional matrix)
