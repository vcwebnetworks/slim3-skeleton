<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 08/06/2021 Vagner Cardoso
 */

namespace Core\Session;

use Core\Helpers\Obj;
use stdClass;

/**
 * Class Flash.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Flash
{
    /**
     * @var string
     */
    protected $key = 'vcw:flash';

    /**
     * @var object
     */
    protected $data;

    /**
     * @var object
     */
    protected $storage;

    /**
     * Flash constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        (new Session())->start();

        if (!isset($_SESSION)) {
            $this->storage[$this->key] = [];
        } else {
            $this->storage = &$_SESSION[$this->key];
        }

        $this->storage = Obj::fromArray($this->storage);

        if (isset($this->storage) && is_object($this->storage)) {
            $this->data = $this->storage;
        }

        $this->storage = new stdClass();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->data->{$name});
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (isset($this->data->{$name})) {
            return $this->data->{$name};
        }

        return $default;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $name, $value): void
    {
        if (is_array($value)) {
            $value = Obj::fromArray($value);
        }

        $this->storage->{$name} = $value;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function __unset(string $name): void
    {
        $this->remove($name);
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function remove(string $name): void
    {
        if ($this->has($name)) {
            unset($this->storage->{$name});
        }
    }

    /**
     * @return object
     */
    public function all(): object
    {
        return $this->data;
    }

    /**
     * @return void
     */
    public function clear(): void
    {
        $this->data = new stdClass();
        $this->storage = $this->data;
    }
}
