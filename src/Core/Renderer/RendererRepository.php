<?php

namespace App\Core\Renderer;

use App\Core\Renderer\Values\RendererBatonnets;

class RendererRepository
{

    const INDEX_BATONNETS = 0;
    const INDEX = [self::INDEX_BATONNETS];
    private array $values;

    public function __construct(
        RendererBatonnets $renderer_batonnets
    )
    {
        $this->values = [
            0 => $renderer_batonnets
        ];
    }

    public function sampleIndex(): int
    {
        return self::INDEX_BATONNETS;
    }

    public function fromIndex(int $index): Renderer
    {
        return $this->values[$index];
    }

    public function nomToIndex(): array
    {
        return array_combine(
            array_map(fn(Renderer $renderer) => $renderer->getNom(), $this->values),
            array_keys($this->values)
        );
    }
}