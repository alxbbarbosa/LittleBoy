<?php

namespace Abbarbosa\LittleBoy\Framework;

/**
 * Collection
 *
 * @author Alexandre Bezerra Barbosa
 */
class Collection implements \Iterator
{
    private $values;
    private $index;
    private $position;
    private $max;
    private $result;

    public function __construct(array $values)
    {
        $this->values   = $values;
        $this->index    = array_keys($values);
        $this->position = 0;
        $this->max      = count($values) - 1;
    }

    public function current()
    {
        return $this->values[$this->index[$this->position]];
    }

    public function key(): \scalar
    {
        return $this->index[$this->position];
    }

    public function next(): void
    {
        if ($this->position < $this->max) {
            $this->position += 1;
        }
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->values[$this->index]);
    }

    public function count()
    {
        return count($this->values);
    }

    public function exists($key)
    {
        return !!array_search($key, $this->values);
    }

    public function get($key = null)
    {
        if (is_null($key) && !is_null($this->result)) {
            $return       = $this->result;
            $this->result = null;
            return $return;
        } else {
            $this->result = null;
            if ($this->exists($key)) {
                return $this->values[$key];
            }
        }
    }

    public function lastKey()
    {
        return array_key_last($this->values);
    }

    public function lastValue()
    {
        return $this->values[$this->lastKey()];
    }

    public function firstKey()
    {
        return array_key_first($this->values);
    }

    public function firstValue()
    {
        return $this->values[$this->firstKey()];
    }

    public function remove($key = null)
    {
        if (is_null($key) && !is_null($this->result)) {
            foreach ($this->result as $k => $v) {
                unset($this->values[$k]);
            }
        } else {
            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    unset($this->values[$k]);
                }
            } else {
                if ($this->exists($key)) {
                    unset($this->values[$key]);
                }
            }
        }
        return $this;
    }

    public function reset()
    {
        $this->rewind();
        return $this;
    }

    public function each()
    {
        $return = ['key' => $this->key(), 'value' => $this->current()];
        $this->next();
        return $return;
    }

    public function where($key, $param1, $param2 = null)
    {
        if (is_null($param2)) {
            $param2 = $param1;
            $param1 = '=';
        }

        $result  = [];
        $include = false;
        foreach ($this->values as $keyCurrent => $value) {
            if ($key == $keyCurrent) {
                switch ($param1) {
                    case '=':
                        $include = ($value == $param2);
                        break;
                    case '!=':
                        $include = ($value != $param2);
                        break;
                    case '<':
                        $include = ($value < $param2);
                        break;
                    case '>':
                        $include = ($value > $param2);
                        break;
                    case '>=':
                        $include = ($value >= $param2);
                        break;
                    case '<=':
                        $include = ($value <= $param2);
                        break;
                }
                if ($include) {
                    $result[$keyCurrent] = $value;
                    $include             = false;
                }
            }
        }
        if (count($result) > 0) {
            $this->result = $result;
        }
        return $this;
    }

    public function toArray()
    {
        if (!is_null($this->result)) {
            $return       = $this->result;
            $this->result = null;
            return $return;
        }
        return $this->values;
    }

    public function first()
    {
        if (!is_null($this->result)) {
            $return       = array_shift($this->result);
            $this->result = null;
            return $return;
        }
        return $this->firstValue();
    }

    public function addArray(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_scalar($value)) {
                if (array_key_exists($key, $this->values)) {
                    unset($this->values[$key]);
                    unset($this->index[array_search($key, $this->index)]);
                }
                $this->values[$key] = $value;
                $this->index[]      = $key;
            }
        }
    }

    public function add($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            throw new Exception('Valor não permitido');
        }

        if (is_array($value)) {
            $this->addArray($value);
        } else {
            $key = 0;
            do {
                //
            } while ($this->exists($key++));
            $this->values[$key] = $value;
            $this->index[]      = $key;
        }
        $this->max = count($values) - 1;
        $this->rewind();
        return $this;
    }
}