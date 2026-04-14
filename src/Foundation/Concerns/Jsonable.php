<?php

namespace Looma\Foundation\Concerns;

interface Jsonable
{
    public function toJson(int $options = 0): string;
}
