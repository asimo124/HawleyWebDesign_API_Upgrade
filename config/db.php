<?php

return [
    'class' => 'yii\db\Connection',
    // Env vars are set by the recipes_laravel Docker stack; the fallbacks are the server values.
    'dsn' => 'mysql:host=' . (getenv('API_DB_HOST') ?: 'localhost')
        . ';port=' . (getenv('API_DB_PORT') ?: '3306')
        . ';dbname=' . (getenv('API_DB_DATABASE') ?: 'api_db'),
    'username' => getenv('API_DB_USERNAME') ?: 'root',
    'password' => getenv('API_DB_PASSWORD') ?: 'gortex!22',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
