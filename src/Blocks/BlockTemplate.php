<?php

namespace Looma\Blocks;

use JsonSerializable;
use Looma\Foundation\Concerns\Arrayable;
use Looma\Foundation\Concerns\Jsonable;
use Stringable;

final class BlockTemplate implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    public function __construct(
        public array $innerBlocks = [],
    ) {
        //
    }

    public function toArray(): array
    {
        return array_map(fn($block) => $block->toArray(), $this->innerBlocks);
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this, $options);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}
