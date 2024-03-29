<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 08/06/2021 Vagner Cardoso
 */

namespace Core;

use Exception;

/**
 * Class Event.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Event
{
    /**
     * @var \Core\Event|null
     */
    public static ?Event $instance = null;

    /**
     * @var array
     */
    protected array $events = [];

    private function __construct()
    {
    }

    public function __wakeup()
    {
    }

    /**
     * @param string          $name
     * @param callable|string $callable
     *
     * @throws \Exception
     *
     * @return void
     */
    public function on(string $name, callable | string $callable): void
    {
        if (!isset($this->events[$name])) {
            $this->events[$name] = [];
        }

        if (is_string($callable) && class_exists($callable)) {
            $callable = new $callable(self::getInstance());
        }

        if (!is_callable($callable)) {
            throw new Exception("Callable invalid in event {$name}.");
        }

        $this->events[$name][] = $callable;
    }

    /**
     * @return Event
     */
    public static function getInstance(): Event
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $name
     * @param ...    $params
     *
     * @return mixed
     */
    public function emit(string $name): mixed
    {
        $result = null;

        if (!empty($this->events[$name])) {
            if (count($this->events[$name]) > 1) {
                ksort($this->events[$name]);
            }

            $params = func_get_args();
            array_shift($params);

            foreach ($this->events[$name] as $key => $callable) {
                $result = call_user_func_array($callable, $params);
            }
        }

        return $result;
    }

    /**
     * @param string|null $name
     *
     * @return mixed
     */
    public function events(string $name = null): mixed
    {
        if (!empty($name)) {
            if (isset($this->events[$name])) {
                return $this->events[$name];
            }

            return null;
        }

        return $this->events;
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function clear(string $name = null): void
    {
        if (!empty($name) && isset($this->events[$name])) {
            $this->events[$name] = [];
        } else {
            foreach ($this->events as $key => $value) {
                $this->events[$key] = [];
            }
        }
    }
}
