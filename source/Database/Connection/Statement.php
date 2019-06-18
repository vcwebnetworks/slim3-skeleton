<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 18/06/2019 Vagner Cardoso
 */

namespace Core\Database\Connection;

use Core\Helpers\Helper;

/**
 * Class Statement.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Statement extends \PDOStatement
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * Statement constructor.
     *
     * @param \PDO $pdo
     */
    protected function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function lastInsertId($name = null)
    {
        return $this->pdo->lastInsertId($name);
    }

    /**
     * @return int
     */
    public function rowCount(): int
    {
        $rowCount = parent::rowCount();

        if (-1 === $rowCount) {
            $rowCount = count($this->fetchAll());
        }

        return (int)$rowCount;
    }

    /**
     * @param int   $fetchStyle
     * @param mixed $fetchArgument
     * @param array $ctorArgs
     *
     * @return array|object
     */
    public function fetchAll($fetchStyle = null, $fetchArgument = null, $ctorArgs = null)
    {
        if (!empty($fetchStyle) || !empty($fetchArgument)) {
            if (class_exists($fetchStyle)) {
                $fetchArgument = $fetchStyle;
                $fetchStyle = \PDO::FETCH_CLASS;
            }

            if (!empty($fetchArgument)) {
                $fetchStyle = \PDO::FETCH_CLASS;
                $fetchArgument = !class_exists($fetchArgument)
                    ? 'stdClass'
                    : $fetchArgument;
            }

            if (\PDO::FETCH_CLASS === $fetchStyle) {
                return parent::fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
            }

            if (in_array($fetchStyle, [\PDO::FETCH_ASSOC, \PDO::FETCH_NUM, \PDO::FETCH_OBJ])) {
                return parent::fetchAll($fetchStyle);
            }

            return parent::fetchAll($fetchStyle, $fetchArgument);
        }

        return parent::fetchAll();
    }

    /**
     * @param mixed $fetchStyle
     * @param int   $cursorOrientation
     * @param int   $cursorOffset
     *
     * @return mixed
     */
    public function fetch($fetchStyle = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        if ($this->isFetchObject($fetchStyle) || class_exists($fetchStyle)) {
            if (!class_exists($fetchStyle)) {
                $fetchStyle = 'stdClass';
            }

            return parent::fetchObject($fetchStyle);
        }

        return parent::fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * @param array|string $bindings
     */
    public function bindValues($bindings): void
    {
        Helper::parseStr($bindings, $bindings);

        if (!empty($bindings)) {
            foreach ($bindings as $key => $value) {
                if (is_string($key) && in_array($key, ['limit', 'offset', 'l', 'o'])) {
                    $value = (int)$value;
                }

                if (empty($value) && '0' != $value) {
                    $value = null;
                }

                $this->bindValue(
                    (is_string($key) ? ":{$key}" : ((int)$key + 1)),
                    filter_var($value, FILTER_DEFAULT),
                    (is_int($value) ? \PDO::PARAM_BOOL : (is_bool($value) ? \PDO::PARAM_BOOL : \PDO::PARAM_STR))
                );
            }
        }
    }

    /**
     * @param int $style
     *
     * @return bool
     */
    public function isFetchObject($style = null): bool
    {
        $allowed = [\PDO::FETCH_OBJ, \PDO::FETCH_CLASS];
        $fetchMode = $style ?: $this->pdo->getAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE);

        if (in_array($fetchMode, $allowed)) {
            return true;
        }

        return false;
    }
}