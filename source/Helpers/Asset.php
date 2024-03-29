<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 08/06/2021 Vagner Cardoso
 */

namespace Core\Helpers;

/**
 * Class Asset.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Asset
{
    /**
     * @param string $path
     * @param string $baseUrl
     * @param bool   $version
     *
     * @return string|null
     */
    public static function path(string $path, string $baseUrl = '', bool $version = false): ?string
    {
        $path = ((!empty($path[0]) && '/' !== $path[0]) ? "/{$path}" : $path);
        $absolutePath = Path::public_html($path);

        if (!file_exists($absolutePath)) {
            return null;
        }

        $hash = '?v='.substr(md5_file($absolutePath), 0, 15);
        $version = ($version ? $hash : '');

        return "{$baseUrl}{$path}{$version}";
    }

    /**
     * @param array|string $files
     *
     * @return string|null
     */
    public static function source($files): ?string
    {
        $contents = [];

        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $file = Path::public_html(((!empty($file[0]) && '/' !== $file[0]) ? "/{$file}" : $file));

            if (file_exists($file)) {
                $contents[] = file_get_contents($file);
            }
        }

        return implode('', $contents);
    }
}
