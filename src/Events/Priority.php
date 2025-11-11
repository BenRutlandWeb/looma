<?php

namespace Looma\Events;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
final readonly class Priority
{
    public const int LOW = 100;
    public const int NORMAL = 10;
    public const int HIGH = 1;

    public function __construct(public int $priority = self::NORMAL)
    {
        //
    }
}
