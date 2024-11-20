<?php

declare(strict_types=1);

namespace App\Tests\Helpers;

trait UlidExtractorTrait
{
    private function extractRfc4122(string $string): string
    {
        $regex = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i';

        if (preg_match($regex, $string, $matches)) {
            return $matches[0];
        }

        throw new \InvalidArgumentException('Extracting ULID failed.');
    }
}
