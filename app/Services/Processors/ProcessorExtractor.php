<?php

namespace App\Services\Processors;

use Illuminate\Support\Collection;

/**
 * Abstract base class for extracting data from processor-specific webhook payloads.
 * Each processor (Givebutter, Stripe, etc.) should have its own extractor implementation.
 */
abstract class ProcessorExtractor
{
    /**
     * Extract standardized donation data from processor-specific payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    abstract public function extract(array $payload): array;

    /**
     * Get the processor name (e.g., 'givebutter', 'stripe').
     */
    abstract public function getProcessorName(): string;

    /**
     * Get field mappings for this processor from the database.
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function getMappings(): Collection
    {
        return \App\Models\ProcessorMapping::where('processor', $this->getProcessorName())
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();
    }

    /**
     * Extract value from payload using dot notation path.
     *
     * @param  array<string, mixed>  $payload
     * @return mixed
     */
    protected function getValueByPath(array $payload, string $path)
    {
        $keys = explode('.', $path);
        $value = $payload;

        foreach ($keys as $key) {
            if (! is_array($value) || ! array_key_exists($key, $value)) {
                return null;
            }

            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Apply transformation to extracted value.
     *
     * @param  mixed  $value
     * @param  array<string, mixed>|null  $config
     * @return mixed
     */
    protected function transformValue($value, string $type, ?array $config = null)
    {
        return match ($type) {
            'direct' => $value,
            'callback' => $this->applyCallback($value, $config),
            'lookup' => $this->applyLookup($value, $config),
            'computed' => $this->applyComputed($value, $config),
            default => $value,
        };
    }

    /**
     * Apply callback transformation.
     *
     * @param  mixed  $value
     * @param  array<string, mixed>|null  $config
     * @return mixed
     */
    protected function applyCallback($value, ?array $config)
    {
        if (! $config || ! isset($config['callback'])) {
            return $value;
        }

        $callback = $config['callback'];

        if (is_callable($callback)) {
            return $callback($value);
        }

        return $value;
    }

    /**
     * Apply lookup transformation (e.g., find campaign by external ID).
     *
     * @param  mixed  $value
     * @param  array<string, mixed>|null  $config
     * @return mixed
     */
    protected function applyLookup($value, ?array $config)
    {
        if (! $config || ! isset($config['model'], $config['field'])) {
            return $value;
        }

        $model = $config['model'];
        $field = $config['field'];

        $record = $model::where($field, $value)->first();

        return $record?->id ?? $value;
    }

    /**
     * Apply computed transformation (e.g., calculate net amount).
     *
     * @param  mixed  $value
     * @param  array<string, mixed>|null  $config
     * @return mixed
     */
    protected function applyComputed($value, ?array $config)
    {
        if (! $config || ! isset($config['formula'])) {
            return $value;
        }

        // Simple formula evaluation - extend as needed
        $formula = $config['formula'];

        return match ($formula) {
            'amount_minus_fee' => $this->calculateNetAmount($value, $config),
            default => $value,
        };
    }

    /**
     * Calculate net amount (amount - fee).
     *
     * @param  mixed  $amount
     * @param  array<string, mixed>  $config
     */
    protected function calculateNetAmount($amount, array $config): float
    {
        $fee = $config['fee'] ?? 0;
        $feePercent = $config['fee_percent'] ?? 0;

        $feeAmount = $fee + ($amount * $feePercent / 100);

        return max(0, (float) $amount - $feeAmount);
    }
}
