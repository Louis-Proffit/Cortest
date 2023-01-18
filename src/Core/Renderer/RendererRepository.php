<?php

namespace App\Core\Renderer;

use App\Core\Renderer\Values\RendererBatonnets;

class RendererRepository
{

    const INDEX = [0];
    private array $values;

    public function __construct(
        private readonly RendererBatonnets $renderer_batonnets
    )
    {
        $this->values = [
            0 => $renderer_batonnets
        ];
    }

    public function sampleIndex(): int
    {
        return 0;
    }

    public function indexExists(int $index): bool
    {
        return in_array($index, array_keys($this->values));
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

    public function nomToRenderer(): array
    {
        return array_combine(
            array_map(fn(Renderer $renderer) => $renderer->getNom(), $this->values),
            $this->values
        );
    }
}