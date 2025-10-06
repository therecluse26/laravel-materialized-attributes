# Laravel Materialized Attributes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/therecluse26/laravel-materialized-attributes.svg?style=flat-square)](https://packagist.org/packages/therecluse26/laravel-materialized-attributes)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/therecluse26/laravel-materialized-attributes/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/therecluse26/laravel-materialized-attributes/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/therecluse26/laravel-materialized-attributes/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/therecluse26/laravel-materialized-attributes/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/therecluse26/laravel-materialized-attributes.svg?style=flat-square)](https://packagist.org/packages/therecluse26/laravel-materialized-attributes)

Persist computed Eloquent attributes in a JSON column for reuse across requests. This package allows you to materialize expensive computed attributes without changing how you access them.

## Key Features

- **100% Backward Compatible**: Unannotated accessors work exactly as before
- **Opt-in Persistence**: Add `#[Materialized('key')]` to any accessor for automatic persistence
- **Single JSON Column**: All materialized values stored in one configurable column
- **No External Dependencies**: No Redis, separate tables, or observers required
- **Selective Invalidation**: Clear specific attributes or all at once

## Installation

Install the package via composer:

```bash
composer require therecluse26/laravel-materialized-attributes
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="materialized-config"
```

This will publish a config file at `config/materialized.php`:

```php
<?php

return [
    'column' => 'materialized',
];
```

## Usage

### 1. Add the JSON Column

Generate a migration to add the materialized column to your table:

```bash
php artisan materialize:add-column users
```

Or with a custom column name:

```bash
php artisan materialize:add-column users --column=computed_data
```

This creates a migration like:

```php
Schema::table('users', function (Blueprint $table) {
    $table->json('materialized')->nullable();
});
```

### 2. Use the Trait

Add the `Materializable` trait to your model:

```php
use TheRecluse26\MaterializedAttributes\Traits\Materializable;

class User extends Model
{
    use Materializable;
    
    protected $casts = [
        'materialized' => 'array',
    ];
}
```

### 3. Annotate Your Accessors

#### Unannotated Accessors (Normal Behavior)

```php
public function getFullNameAttribute(): string
{
    return "{$this->first_name} {$this->last_name}";
}

// Usage - computed every time
$user->full_name; // John Doe
$user->full_name; // John Doe (computed again)
```

#### Materialized Accessors (Persisted)

```php
use TheRecluse26\MaterializedAttributes\Attributes\Materialized;

#[Materialized('profile_summary')]
public function getProfileSummaryAttribute(): array
{
    return [
        'posts_count' => $this->posts()->count(),
        'comments_count' => $this->comments()->count(),
        'last_login' => $this->last_login_at?->diffForHumans(),
    ];
}

// Usage - computed once, then reused
$summary1 = $user->profile_summary; // Computes and stores
$summary2 = $user->profile_summary; // Returns from JSON (no recomputation)
```

### Multiple Materialized Attributes

You can have multiple materialized attributes on the same model:

```php
#[Materialized('metrics')]
public function getMetricsAttribute(): array
{
    return [
        'total_revenue' => $this->orders()->sum('total'),
        'avg_order_value' => $this->orders()->avg('total'),
    ];
}

#[Materialized('status_flags')]
public function getStatusFlagsAttribute(): array
{
    return [
        'is_premium' => $this->subscription?->isPremium(),
        'needs_verification' => !$this->email_verified_at,
    ];
}
```

### Invalidation

#### Clear a Specific Attribute

```php
$user->invalidateMaterialized('profile_summary');
// Next access to $user->profile_summary will recompute
```

#### Clear All Materialized Attributes

```php
$user->invalidateAllMaterialized();
// All materialized attributes will be recomputed on next access
```

## Configuration

### Custom Column Name

You can customize the column name per model by overriding the `getMaterializableColumn()` method:

```php
class User extends Model
{
    use Materializable;
    
    protected $casts = [
        'computed_data' => 'array', // Make sure to cast your custom column
    ];
    
    protected function getMaterializableColumn(): string
    {
        return 'computed_data';
    }
}
```

Or globally via config in `config/materialized.php` (though per-model override is recommended):

```php
return [
    'column' => 'computed_data',
];
```

Generate migration with custom column name:

```bash
php artisan materialize:add-column users --column=computed_data
```

## Commands

### Add Materialized Column

```bash
php artisan materialize:add-column {table} {--column=materialized}
```

Creates a timestamped migration that adds a nullable JSON column.

### Refresh Materialized Attributes (Optional)

```bash
php artisan materialize:refresh {--model=*}
```

Scaffold command for recomputing materialized attributes across models.

## How It Works

1. **Interception**: The `Materializable` trait overrides `getAttribute()` to detect annotated accessors
2. **Storage**: Computed values are stored as JSON in a single database column
3. **Caching**: The JSON payload is decoded once per request and cached in memory
4. **Persistence**: Values are written to the database only when changed

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [TheRecluse26](https://github.com/therecluse26)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.