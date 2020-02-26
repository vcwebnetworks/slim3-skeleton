<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 26/02/2020 Vagner Cardoso
 */

namespace App\Providers;

use Core\Database\Database;

/**
 * Class DatabaseProvider.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class DatabaseProvider extends Provider
{
    /**
     * @return array
     */
    public function name(): array
    {
        return ['db', 'database'];
    }

    /**
     * @return \Closure
     */
    public function register(): \Closure
    {
        return function () {
            // Connect instance
            $database = new Database();
            $database->setDefaultDriver(config('database.default', 'mysql'));

            // Add connections config
            foreach (config('database.connections') as $driver => $config) {
                $database->addConnection($driver, $config);
            }

            return $database->connection();
        };
    }
}
