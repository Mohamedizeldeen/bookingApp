<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'date',
        'metric_type',
        'metric_name',
        'value',
        'metadata',
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the company that owns the analytics data
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope to filter by metric type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('metric_type', $type);
    }

    /**
     * Scope to filter by metric name
     */
    public function scopeOfMetric($query, string $metric)
    {
        return $query->where('metric_name', $metric);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent data
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days));
    }

    /**
     * Get analytics summary for a company
     */
    public static function getSummary($companyId, $startDate = null, $endDate = null)
    {
        $query = static::where('company_id', $companyId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } else {
            $query->where('date', '>=', now()->subDays(30));
        }

        return $query->get()->groupBy(['metric_type', 'metric_name']);
    }

    /**
     * Record analytics data
     */
    public static function record($companyId, $date, $metricType, $metricName, $value, $metadata = null)
    {
        return static::updateOrCreate(
            [
                'company_id' => $companyId,
                'date' => $date,
                'metric_type' => $metricType,
                'metric_name' => $metricName,
            ],
            [
                'value' => $value,
                'metadata' => $metadata,
            ]
        );
    }

    /**
     * Get trend data for a metric
     */
    public static function getTrend($companyId, $metricType, $metricName, $days = 30)
    {
        return static::where('company_id', $companyId)
                    ->where('metric_type', $metricType)
                    ->where('metric_name', $metricName)
                    ->where('date', '>=', now()->subDays($days))
                    ->orderBy('date')
                    ->get();
    }
}
