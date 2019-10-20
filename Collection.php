<?php declare(strict_types=1);
/**
 * Part of phoenix project.
 *
 * @copyright  Copyright (C) 2019 $Asikart.
 * @license    LGPL-2.0-or-later
 */

namespace Windwalker\Data;

use Windwalker\Data\Traits\CollectionTrait;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Iterator\ArrayObject;

/**
 * The Collection class.
 *
 * @method mixed current()
 * @method mixed next()
 * @method mixed prev()
 * @method mixed end()
 * @method mixed reset()
 * @method mixed key()
 * @method mixed firstKey()
 * @method mixed lastKey()
 * @method bool  include($value, bool $strict = false) Alias of contains()
 *
 * @since  3.5
 */
class Collection extends ArrayObject implements DataInterface
{
    use CollectionTrait;

    /**
     * explode
     *
     * @param string   $delimiter
     * @param string   $string
     * @param int|null $limit
     *
     * @return  static
     *
     * @since  3.5.6
     */
    public static function explode(string $delimiter, string $string, ?int $limit = null): self
    {
        if ($limit === null) {
            $arr = explode($delimiter, $string);
        } else {
            $arr = explode($delimiter, $string, $limit);
        }

        return new static($arr);
    }

    /**
     * toCollections
     *
     * @param array $items
     *
     * @return  Collection[]
     *
     * @since  3.5
     */
    public static function toCollections(iterable $items): iterable
    {
        foreach ($items as $k => $item) {
            $items[$k] = new static($item);
        }

        return $items;
    }

    /**
     * Bind the data into this object.
     *
     * @param   mixed   $values       The data array or object.
     * @param   boolean $replaceNulls Replace null or not.
     *
     * @return  static Return self to support chaining.
     */
    public function bind($values, $replaceNulls = false)
    {
        if ($values === null) {
            return $this;
        }

        // Check properties type.
        if (!is_array($values) && !is_object($values)) {
            throw new \InvalidArgumentException(sprintf('Please bind array or object, %s given.', gettype($values)));
        }

        // If is Traversable, get iterator.
        if ($values instanceof \Traversable) {
            $values = iterator_to_array($values);
        } elseif (is_object($values)) {
            // If is object, convert it to array
            $values = get_object_vars($values);
        }

        // Bind the properties.
        foreach ($values as $field => $value) {
            // Check if the value is null and should be bound.
            if ($value === null && !$replaceNulls) {
                continue;
            }

            // Set the property.
            $this->offsetSet($field, $value);
        }

        return $this;
    }

    /**
     * Is this object empty?
     *
     * @return  boolean
     */
    public function isNull()
    {
        return $this->storage === [];
    }

    /**
     * Is this object has properties?
     *
     * @return  boolean
     */
    public function notNull()
    {
        return $this->storage !== [];
    }

    /**
     * Dump all data as array
     *
     * @return  array
     */
    public function dump()
    {
        return $this->getArrayCopy();
    }

    /**
     * dumpAll
     *
     * @return  array
     *
     * @since  3.5.13
     */
    public function dumpAll(): array
    {
        return Arr::toArray($this->getArrayCopy(), true);
    }

    /**
     * keys
     *
     * @param string|null $search
     * @param bool|null   $strict
     *
     * @return  Collection
     *
     * @since  3.5
     */
    public function keys(?string $search = null, ?bool $strict = null): Collection
    {
        if (func_get_args()['search'] ?? false) {
            return array_keys($this->storage, $search, (bool) $strict);
        }

        return new static(array_keys($this->storage));
    }

    /**
     * column
     *
     * @param string      $name
     * @param string|null $key
     *
     * @return  static
     *
     * @since  3.5
     */
    public function column(string $name, ?string $key = null): self
    {
        return new static(array_column($this->storage, $name, $key));
    }

    /**
     * combine
     *
     * @param array|static $values
     *
     * @return  static
     *
     * @since  3.5
     */
    public function combine($values): self
    {
        return new static(array_combine($this->storage, Arr::toArray($values)));
    }

    /**
     * diff
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function diff(...$args): self
    {
        $args = array_map([Arr::class, 'toArray'], $args);

        return new static(array_diff($this->storage, ...$args));
    }

    /**
     * diffKeys
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function diffKeys(...$args): self
    {
        $args = array_map([Arr::class, 'toArray'], $args);

        return new static(array_diff_key($this->storage, ...$args));
    }

    /**
     * fill
     *
     * @param int   $start
     * @param int   $num
     * @param mixed $value
     *
     * @return  Collection
     *
     * @since  3.5
     */
    public function fill(int $start, int $num, $value): self
    {
        return new static(array_fill($start, $num, $value));
    }

    /**
     * fillKeys
     *
     * @param array $keys
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function fillKeys(array $keys, $value): self
    {
        return new static(array_fill_keys($keys, $value));
    }

    /**
     * flip
     *
     * @return  static
     *
     * @since  3.5
     */
    public function flip(): self
    {
        return new static(array_flip($this->storage));
    }

    /**
     * intersect
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function intersect(...$args): self
    {
        $args = array_map([Arr::class, 'toArray'], $args);

        return new static(array_intersect($this->storage, ...$args));
    }

    /**
     * intersectKey
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function intersectKey(...$args): self
    {
        $args = array_map([Arr::class, 'toArray'], $args);

        return new static(array_intersect_key($this->storage, ...$args));
    }

    /**
     * merge
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function merge(...$args): self
    {
        $args = array_map([Arr::class, 'toArray'], $args);

        return new static(array_merge($this->storage, ...$args));
    }

    /**
     * mergeRecursive
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function mergeRecursive(...$args): self
    {
        $new = $this->bindNewInstance($this);

        return array_map([$new, 'bind'], $args);
    }

    /**
     * pad
     *
     * @param int   $size
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function pad(int $size, $value): self
    {
        return new static(array_pad($this->storage, $size, $value));
    }

    /**
     * leftPad
     *
     * @param int   $size
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function leftPad(int $size, $value): self
    {
        return $this->pad(-$size, $value);
    }

    /**
     * leftPad
     *
     * @param int   $size
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function rightPad(int $size, $value): self
    {
        return $this->pad($size, $value);
    }

    /**
     * pop
     *
     * @return  mixed
     *
     * @since  3.5
     */
    public function pop()
    {
        return array_pop($this->storage);
    }

    /**
     * shift
     *
     * @return  mixed
     *
     * @since  3.5
     */
    public function shift()
    {
        return array_shift($this->storage);
    }

    /**
     * push
     *
     * @param mixed ...$value
     *
     * @return  int
     *
     * @since  3.5
     */
    public function push(...$value): int
    {
        return array_push($this->storage, ...$value);
    }

    /**
     * unshift
     *
     * @param mixed ...$value
     *
     * @return  int
     *
     * @since  3.5
     */
    public function unshift(...$value): int
    {
        return array_unshift($this->storage, ...$value);
    }

    /**
     * concat
     *
     * @param  mixed  ...$args
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function append(...$args): self
    {
        $new = $this->storage;
        array_push($new, ...$args);

        return new static($new);
    }

    /**
     * concatStart
     *
     * @param  mixed  ...$args
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function prepend(...$args): self
    {
        $new = $this->storage;
        array_unshift($new, ...$args);

        return new static($new);
    }

    /**
     * removeEnd
     *
     * @param  int  $num
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function removeLast($num = 1): self
    {
        return $this->bindNewInstance($this->storage)->splice(0, -$num);
    }

    /**
     * removeStart
     *
     * @param  int  $num
     *
     * @return  $this
     *
     * @since  3.5.13
     */
    public function removeFirst($num = 1): self
    {
        return $this->bindNewInstance($this->storage)->splice($num);
    }

    /**
     * rand
     *
     * @param int $num
     *
     * @return  static
     *
     * @since  3.5
     */
    public function rand(int $num = 1): self
    {
        return new static(array_rand($this->storage, $num));
    }

    /**
     * reduce
     *
     * @param callable $callable
     * @param mixed    $initial
     *
     * @return  mixed
     *
     * @since  3.5
     */
    public function reduce(callable $callable, $initial = null)
    {
        return array_reduce($this->storage, $callable, $initial);
    }

    /**
     * replace
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function replace(...$args): self
    {
        $args = array_map([Arr::class, 'toArray'], $args);

        return new static(array_replace($this->storage, ...$args));
    }

    /**
     * replaceRecursive
     *
     * @param array[]|static[] ...$args
     *
     * @return  static
     *
     * @since  3.5
     */
    public function replaceRecursive(...$args): self
    {
        $args = self::allToArray($args);

        return new static(array_replace_recursive($this->storage, ...$args));
    }

    /**
     * reverse
     *
     * @param bool $preserveKeys
     *
     * @return  static
     *
     * @since  3.5
     */
    public function reverse(bool $preserveKeys = false): self
    {
        return new static(array_reverse($this->storage, $preserveKeys));
    }

    /**
     * search
     *
     * @param mixed $value
     * @param bool  $strict
     *
     * @return  false|int|string
     *
     * @since  3.5
     */
    public function search($value, bool $strict = false)
    {
        return array_search($value, $this->storage, $strict);
    }

    /**
     * indexOf
     *
     * @param mixed $value
     * @param bool  $strict
     *
     * @return  int
     *
     * @since  3.5.2
     */
    public function indexOf($value, bool $strict = false): int
    {
        $r = $this->search($value, $strict);

        return (int) ($r === false ? -1 : $r);
    }

    /**
     * slice
     *
     * @param int      $offset
     * @param int|null $length
     * @param bool     $preserveKeys
     *
     * @return  static
     *
     * @since  3.5
     */
    public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
    {
        return new static(array_slice($this->storage, ...func_get_args()));
    }

    /**
     * slice
     *
     * @param int      $offset
     * @param int|null $length
     * @param mixed    $replacement
     *
     * @return  static
     *
     * @since  3.5
     */
    public function splice(int $offset, ?int $length = null, $replacement = null): self
    {
        return new static(array_splice($this->storage, ...func_get_args()));
    }

    /**
     * insertAfter
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function insertAfter(int $key, $value): self
    {
        return $this->bindNewInstance($this->storage)->splice($key + 1, 0, $value);
    }

    /**
     * insertBefore
     *
     * @param int   $key
     * @param mixed $value
     *
     * @return  static
     *
     * @since  3.5
     */
    public function insertBefore(int $key, $value): self
    {
        return $this->bindNewInstance($this->storage)->splice($key, 0, $value);
    }

    /**
     * only
     *
     * @param array|string $fields
     *
     *
     * @return  static
     */
    public function only($fields)
    {
        $fields = (array) $fields;

        $new = $this->getNewInstance();

        foreach ($fields as $origin => $field) {
            if (is_numeric($origin)) {
                $new[$field] = $this[$field];
            } else {
                $new[$field] = $this[$origin];
            }
        }

        return $new;
    }

    /**
     * except
     *
     * @param array|string $fields
     *
     * @return  static
     *
     * @since  3.5.13
     */
    public function except($fields)
    {
        $fields = (array) $fields;

        $new = $this->getNewInstance();

        foreach ($fields as $origin => $field) {
            unset($new[$field]);
        }

        return $new;
    }

    /**
     * sum
     *
     * @return  float|int
     *
     * @since  3.5
     */
    public function sum()
    {
        return array_sum($this->storage);
    }

    /**
     * unique
     *
     * @param int $sortFlags
     *
     * @return  static
     *
     * @since  3.5
     */
    public function unique($sortFlags = SORT_STRING): self
    {
        return new static(array_unique($this->storage, $sortFlags));
    }

    /**
     * walk
     *
     * @param callable $callable
     * @param mixed    $userdata
     *
     * @return  bool
     *
     * @since  3.5
     */
    public function walk(callable $callable, $userdata = null): bool
    {
        return array_walk($this->storage, $callable, $userdata);
    }

    /**
     * walkRecursive
     *
     * @param callable $callable
     * @param mixed    $userdata
     *
     * @return  bool
     *
     * @since  3.5
     */
    public function walkRecursive(callable $callable, $userdata = null): bool
    {
        return array_walk_recursive($this->storage, $callable, $userdata);
    }

    /**
     * current
     *
     * @param mixed $value
     * @param bool  $strict
     *
     * @return  bool
     *
     * @since  3.5
     */
    public function contains($value, bool $strict = false): bool
    {
        return (bool) in_array($value, $this->storage, $strict);
    }

    /**
     * keyExists
     *
     * @param mixed $key
     *
     * @return  bool
     *
     * @since  3.5
     */
    public function keyExists($key): bool
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * implode
     *
     * @param string $glue
     *
     * @return  string
     *
     * @since  3.5.1
     */
    public function implode(string $glue): string
    {
        return implode($glue, $this->storage);
    }

    /**
     * range
     *
     * @param mixed     $start
     * @param mixed     $end
     * @param int|float $step
     *
     * @return  static
     *
     * @since  3.5
     */
    public static function range($start, $end, $step = 1): self
    {
        return new static(range($start, $end, $step));
    }

    /**
     * shuffle
     *
     * @return  static
     *
     * @since  3.5
     */
    public function shuffle(): self
    {
        $new = $this->storage;

        shuffle($new);

        return new static($new);
    }

    /**
     * __call
     *
     * @param string $name
     * @param array  $args
     *
     * @return  mixed
     *
     * @since  3.5
     */
    public function __call($name, $args)
    {
        $name = strtolower($name);

        // Simple apply storage
        $methods = [
            'current' => 'current',
            'next' => 'next',
            'prev' => 'prev',
            'end' => 'end',
            'reset' => 'reset',
            'key' => 'key',
            'firstkey' => 'array_key_first',
            'lastkey' => 'array_key_last'
        ];

        if (array_key_exists($name, $methods)) {
            return $methods[$name]($this->storage, ...$args);
        }

        // Alias
        $methods = [
            'indexof' => 'search',
            'include' => 'contains',
        ];

        if (array_key_exists($name, $methods)) {
            return $this->{$methods[$name]}(...$args);
        }

        throw new \BadMethodCallException(
            sprintf(
                'Method: %s() not found in %s',
                $name,
                static::class
            )
        );
    }
}
