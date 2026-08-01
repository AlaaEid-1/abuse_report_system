<?php

namespace App\Services;

use App\Models\Report;

class TrackingCodeGenerator
{
    /**
     * Generate a unique, readable tracking code formatted as SV-XXXX-XXXX-XXXX
     * and return both the raw code and its SHA-256 hash.
     *
     * @return array{code: string, hash: string}
     */
    public function generate(): array
    {
        do {
            $chunk1 = $this->randomChunk();
            $chunk2 = $this->randomChunk();
            $chunk3 = $this->randomChunk();

            $code = sprintf('SV-%s-%s-%s', $chunk1, $chunk2, $chunk3);
            $hash = hash('sha256', $code);

            $exists = Report::where('tracking_hash', $hash)->exists();
        } while ($exists);

        return [
            'code' => $code,
            'hash' => $hash,
        ];
    }

    /**
     * Hash a raw tracking code string for lookup.
     */
    public function hash(string $code): string
    {
        $normalized = strtoupper(trim($code));

        return hash('sha256', $normalized);
    }

    /**
     * Generate a 4-character random uppercase string excluding confusing characters (0, O, 1, I, L).
     */
    private function randomChunk(int $length = 4): string
    {
        $pool = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}
