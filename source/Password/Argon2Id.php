<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 08/06/2021 Vagner Cardoso
 */

namespace Core\Password;

/**
 * Class Argon2Id.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Argon2Id extends Argon
{
    /**
     * @return int|string
     */
    public function algorithm()
    {
        return PASSWORD_ARGON2ID;
    }
}
