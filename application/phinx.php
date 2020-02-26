<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 25/02/2020 Vagner Cardoso
 */

use Core\App;

try {
    // Constants
    define('ROOT', __DIR__);
    define('PUBLIC_FOLDER', __DIR__.'/../public_html');
    define('APP_FOLDER', __DIR__);
    define('RESOURCE_FOLDER', __DIR__.'/resources');
    define('BASE_URL', 'http://localhost');

    // Autoload
    require_once __DIR__.'/vendor/autoload.php';

    // Loader app
    $app = App::getInstance();
    $app->registerProviders();
    $app->registerEvents();

    // Configuration:
    //
    // @see https://book.cakephp.org/3.0/en/phinx/configuration.html

    return [
        'migration_base_class' => \Core\Phinx\Migration::class,

        'templates' => [
            'file' => __DIR__.'/storage/database/templates/Migration.php.dist',
        ],

        'paths' => [
            'migrations' => __DIR__.'/storage/database/migrations',
            'seeds' => __DIR__.'/storage/database/seeds',
        ],

        'environments' => [
            'default_migration_table' => 'migrations',
            'default_database' => env('DB_DRIVER', 'mysql'),

            'mysql' => [
                'adapter' => 'mysql',
                'host' => env('DB_HOST', 'localhost'),
                'port' => env('DB_PORT', '3306'),
                'name' => env('DB_DATABASE', ''),
                'user' => env('DB_USER', ''),
                'pass' => env('DB_PASS', ''),
                'charset' => env('DB_CHARSET', 'utf8'),
                'collation' => env('DB_COLLATE', 'utf8_general_ci'),
                'table_prefix' => false,
                'table_suffix' => false,
            ],

            'pgsql' => [
                'adapter' => 'pgsql',
                'host' => env('DB_HOST', 'localhost'),
                'port' => env('DB_PORT', '3306'),
                'name' => env('DB_DATABASE', ''),
                'user' => env('DB_USER', ''),
                'pass' => env('DB_PASS', ''),
                'charset' => env('DB_CHARSET', 'utf8'),
                'collation' => false,
                'table_prefix' => false,
                'table_suffix' => false,
            ],

            'sqlsrv' => [
                'adapter' => 'sqlsrv',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '1433'),
                'name' => env('DB_DATABASE', ''),
                'user' => env('DB_USER', ''),
                'pass' => env('DB_PASS', ''),
                'charset' => 65001, // \PDO::SQLSRV_ENCODING_UTF8
                'collation' => false,
                'table_prefix' => false,
                'table_suffix' => false,
            ],
        ],

        'version_order' => 'creation',
    ];
} catch (\Exception $e) {
    die("ERROR: {$e->getMessage()}\n");
}
