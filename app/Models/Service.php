<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'price',
        'duration_minutes',
        'is_active',
        'availability'
    ];

    protected $casts = [
        'availability' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Accessors
    public function getDurationAttribute()
    {
        return $this->duration_minutes;
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate a shareable booking URL for this service
     */
    public function getShareableBookingUrl()
    {
        return route('booking.service', [
            'company' => $this->company->slug ?? $this->company->id,
            'service' => $this->id
        ]);
    }

    /**
     * Alias for getShareableBookingUrl() for consistency
     */
    public function getBookingUrl()
    {
        return $this->getShareableBookingUrl();
    }

    /**
     * Generate social media sharing data
     */
    public function getSocialMediaShareData()
    {
        $url = $this->getShareableBookingUrl();
        $companyName = $this->company->name;
        
        return [
            'url' => $url,
            'title' => "Book {$this->name} with {$companyName}",
            'description' => $this->description ?? "Professional {$this->name} service available for booking.",
            'hashtags' => $this->generateHashtags(),
            'facebook_url' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($url),
            'twitter_url' => "https://twitter.com/intent/tweet?url=" . urlencode($url) . "&text=" . urlencode("Book {$this->name} with {$companyName}") . "&hashtags=" . urlencode(implode(',', $this->generateHashtags())),
            'linkedin_url' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($url),
            'whatsapp_url' => "https://wa.me/?text=" . urlencode("Book {$this->name} with {$companyName} - {$url}"),
            'email_url' => "mailto:?subject=" . urlencode("Book {$this->name} with {$companyName}") . "&body=" . urlencode("I found this great service: {$this->name}\n\nBook now: {$url}"),
        ];
    }

    /**
     * Generate relevant hashtags for social media
     */
    private function generateHashtags()
    {
        $hashtags = ['BookNow', 'Service'];
        
        // Add service name words as hashtags
        $serviceWords = explode(' ', $this->name);
        foreach ($serviceWords as $word) {
            $clean = preg_replace('/[^a-zA-Z0-9]/', '', $word);
            if (strlen($clean) > 2) {
                $hashtags[] = ucfirst(strtolower($clean));
            }
        }
        
        // Add company-related hashtags
        if ($this->company->name) {
            $companyWords = explode(' ', $this->company->name);
            foreach ($companyWords as $word) {
                $clean = preg_replace('/[^a-zA-Z0-9]/', '', $word);
                if (strlen($clean) > 2) {
                    $hashtags[] = ucfirst(strtolower($clean));
                }
            }
        }
        
        return array_unique(array_slice($hashtags, 0, 5)); // Limit to 5 hashtags
    }
}
