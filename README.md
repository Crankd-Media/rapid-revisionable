# Rapid Revisions

## LOAD JS FILES

ddev composer require crankd/rapid-revisions

php artisan vendor:publish --provider="Crankd\RapidRevisions\RapidRevisionsProvider"




## LOCAL DEV SETUP

Crankd\RapidRevisions\RapidRevisionsProvider::class,

<pre>
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/",
            "Crankd\\RapidRevisions\\": "packages/crankd/rapid-revisions/src"
        }
    },
    </pre>

import "../../packages/crankd/rapid-revisions/resources/js/rapid-revisions";
import "../../packages/crankd/rapid-revisions/resources/css/rapid-revisions.css";
