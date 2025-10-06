# Changelog

All notable changes to `laravel-materialized-attributes` will be documented in this file.

## 1.0.0 - 2025-10-05

- Initial release
- Materialized attributes with opt-in persistence via `#[Materialized]` annotation
- Single JSON column storage for all materialized values
- 100% backward compatibility with existing computed attributes
- Selective invalidation of materialized values
- Artisan command for adding JSON column to tables
- Complete test coverage