<?php

declare(strict_types=1);

namespace App\Domain\Interface;

use App\Domain\ValueObject\PathResult;

interface PathFindingAlgorithm
{
    public function findPath(array $graph, string $start, string $end): PathResult;
}
