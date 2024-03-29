<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 08/06/2021 Vagner Cardoso
 */

namespace Core\Interfaces;

use Closure;

/**
 * Class ServiceProvider.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
interface ServiceProvider
{
    /**
     * @return string|array
     */
    public function name();

    /**
     * @return \Closure
     */
    public function register(): Closure;
}
