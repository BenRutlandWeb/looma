<?php

namespace Looma\Foundation;

class Env
{
    public function __construct(protected array $data = [])
    {
        //
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->data[$key] ?? $default;

        if (is_null($value) || empty($value)) {
            return null;
        }

        switch (strtolower($value)) {
            case 'true':
                return  true;
            case 'false':
                return  false;
            case 'null':
                return  null;
        }

        if (ctype_digit($value)) {
            return (int) $value;
        }

        return $value;
    }
}
