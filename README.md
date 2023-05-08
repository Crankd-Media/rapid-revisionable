# Rapid Revisions

## Install

ddev composer require crankd/rapid-revisions
ddev php artisan vendor:publish --provider="Crankd\RapidRevisions\RapidRevisionsProvider"
ddev php artisan migrate

## How To Use


```php

use Crankd\RapidRevisions\Traits\RevisionableTrait;

class ProductImport extends Model
{
    use RevisionableTrait;
}
```

```php
Route::get('product-imports/{productImport}/preview', [ProductImportController::class, "previewVersion"])->name('product-imports.preview');
Route::post('product-imports/restore/{revision:id}', [ProductImportController::class, "restoreVersion"])->name('product-imports.restore');
```

```php
class ProductImportController extends Controller
{
    ...

    public function restoreVersion(Request $request, Revision $revision)
    {
        ProductImport::restoreRevision($revision);
    }

    public function previewVersion(Request $request, $productImportId)
    {
        $productImport = ProductImport::withoutGlobalScope('revisionable')->find($productImportId);
    }
}

```

### Tabs

```php
<x-rapid-revisions::tab-group-render-revisions :model="$productImport"
        previewRoute="product-imports.preview"
        restoreRoute="product-imports.restore" />
```

### Details

```php
<x-rapid-revisions::details :model="$productImport"
        previewRoute="product-imports.preview"
        restoreRoute="product-imports.restore" />
```