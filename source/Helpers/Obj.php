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

use JsonSerializable;
use SimpleXMLElement;
use stdClass;

/**
 * Class Obj.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Obj
{
    /**
     * @param array|object $array
     *
     * @return object
     */
    public static function fromArray($array)
    {
        $object = new stdClass();

        if (is_object($array)) {
            return $array;
        }

        if (!is_array($array)) {
            return $object;
        }

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $object->{$key} = self::fromArray($value);
            } else {
                $object->{$key} = isset($value) ? $value : null;
            }
        }

        return $object;
    }

    /**
     * @param object      $object
     * @param string|null $name
     * @param mixed       $default
     *
     * @return mixed
     */
    public static function get(object $object, ?string $name = null, $default = null)
    {
        if (empty($name)) {
            return $object;
        }

        foreach (explode('.', $name) as $segment) {
            if (!is_object($object) || !isset($object->{$segment})) {
                return $default;
            }

            $object = $object->{$segment};
        }

        return $object;
    }

    /**
     * @param object|array $object
     *
     * @return string
     */
    public static function toJson($object): string
    {
        return json_encode(self::toArray($object));
    }

    /**
     * @param object|array $object
     *
     * @return array
     */
    public static function toArray($object): array
    {
        $array = [];

        if (!is_object($object) && !is_array($object)) {
            return $array;
        }

        foreach ($object as $key => $value) {
            if ($value instanceof SimpleXMLElement) {
                $value = strval($value);
            }

            if (is_object($value) || is_array($value)) {
                if ($value instanceof JsonSerializable) {
                    $array[$key] = $value->jsonSerialize();
                } else {
                    $array[$key] = self::toArray($value);
                }
            } else {
                if (isset($key)) {
                    $array[$key] = $value;
                }
            }
        }

        return $array;
    }
}
