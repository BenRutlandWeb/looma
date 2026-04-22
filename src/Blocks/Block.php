<?php

namespace Looma\Blocks;

use Looma\Blocks\Concerns\Align;
use Looma\Blocks\Concerns\TemplateLock;
use Looma\Foundation\Concerns\Arrayable;

final class Block implements Arrayable
{
    public function __construct(
        public string $name,
        public array $attributes = [],
        public array $innerBlocks = [],
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            $this->name,
            $this->attributes,
            array_map(fn($block) => $block->toArray(), $this->innerBlocks),
        ];
    }

    public function attributes(array $attributes = []): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    public function innerBlocks(self ...$innerBlocks): self
    {
        $this->innerBlocks = $innerBlocks;

        return $this;
    }

    public function attribute(string $key, mixed $value = null): self
    {
        return $this->attributes([$key => $value]);
    }

    public function templateLock(TemplateLock $type = TemplateLock::CONTENT_ONLY): self
    {
        $value = $type === TemplateLock::NONE ? false : $type->value;

        return $this->attribute('templateLock', $value);
    }

    public function lock(bool $move = true, bool $remove = true): self
    {
        return $this->attribute('lock', ['move' => $move, 'remove' => $remove]);
    }

    public function align(Align $align = Align::NONE): self
    {
        return $this->attribute('align', $align->value);
    }

    public static function __callStatic(string $name, array $parameters = []): self
    {
        $name = str_contains($name, '_') ? str_replace('_', '/', $name) : 'core/' . $name;

        $name = strtolower(preg_replace('/[A-Z]/', '-$0', $name));

        return new self($name, ...$parameters);
    }

    public function __call(string $key, array $values = []): self
    {
        return $this->attribute($key, $values[0] ?? null);
    }

    public static function template(self ...$innerBlocks): BlockTemplate
    {
        return new BlockTemplate($innerBlocks);
    }
}
