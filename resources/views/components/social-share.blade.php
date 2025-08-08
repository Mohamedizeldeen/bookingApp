<!-- Social Media Sharing Component -->
<div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
        </svg>
        Share Booking Link
    </h3>
    
    <!-- Booking URL Display -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Booking URL</label>
        <div class="flex items-center">
            <input type="text" 
                   value="{{ $shareData['url'] }}" 
                   id="booking-url-{{ $serviceId ?? 'default' }}"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm"
                   readonly>
            <button onclick="copyBookingUrl('{{ $serviceId ?? 'default' }}')" 
                    class="px-4 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-1">Share this link to allow customers to book directly</p>
    </div>

    <!-- Social Media Buttons -->
    <div class="space-y-3">
        <h4 class="text-sm font-medium text-gray-700">Share on Social Media</h4>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <!-- Facebook -->
            <a href="{{ $shareData['facebook_url'] }}" 
               target="_blank" 
               rel="noopener noreferrer"
               class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </a>

            <!-- Twitter -->
            <a href="{{ $shareData['twitter_url'] }}" 
               target="_blank" 
               rel="noopener noreferrer"
               class="flex items-center justify-center px-4 py-2 bg-blue-400 text-white rounded-md hover:bg-blue-500 transition-colors text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
                Twitter
            </a>

            <!-- LinkedIn -->
            <a href="{{ $shareData['linkedin_url'] }}" 
               target="_blank" 
               rel="noopener noreferrer"
               class="flex items-center justify-center px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 transition-colors text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
                LinkedIn
            </a>

            <!-- WhatsApp -->
            <a href="{{ $shareData['whatsapp_url'] }}" 
               target="_blank" 
               rel="noopener noreferrer"
               class="flex items-center justify-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                </svg>
                WhatsApp
            </a>
        </div>

        <!-- Email Share -->
        <div class="pt-2">
            <a href="{{ $shareData['email_url'] }}" 
               class="flex items-center justify-center w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Share via Email
            </a>
        </div>
    </div>

    <!-- QR Code Section -->
    @if(isset($showQrCode) && $showQrCode)
    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="flex items-center justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700">QR Code</h4>
            <button onclick="generateQRCode('{{ $shareData['url'] }}', '{{ $serviceId ?? 'default' }}')" 
                    class="text-sm text-blue-600 hover:text-blue-800">
                Generate QR Code
            </button>
        </div>
        <div id="qr-code-{{ $serviceId ?? 'default' }}" class="justify-center hidden">
            <!-- QR Code will be generated here -->
        </div>
    </div>
    @endif

    <!-- Share Statistics (if available) -->
    @if(isset($shareStats) && $shareStats)
    <div class="mt-6 pt-6 border-t border-gray-200">
        <h4 class="text-sm font-medium text-gray-700 mb-2">Share Statistics</h4>
        <div class="text-xs text-gray-500">
            <p>Total clicks: {{ $shareStats['clicks'] ?? 0 }}</p>
            <p>Bookings from shares: {{ $shareStats['conversions'] ?? 0 }}</p>
        </div>
    </div>
    @endif
</div>

<!-- Copy Success Toast -->
<div id="copy-toast-{{ $serviceId ?? 'default' }}" 
     class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50">
    <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        Booking URL copied to clipboard!
    </div>
</div>

@push('scripts')
<script>
function copyBookingUrl(serviceId) {
    const input = document.getElementById(`booking-url-${serviceId}`);
    const toast = document.getElementById(`copy-toast-${serviceId}`);
    
    // Select and copy the text
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success toast
        toast.classList.remove('translate-x-full');
        
        // Hide toast after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
        }, 3000);
        
        // Track copy event (if analytics are implemented)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'booking_url_copied', {
                'service_id': serviceId
            });
        }
    } catch (err) {
        console.error('Failed to copy text: ', err);
        alert('Failed to copy URL. Please copy manually.');
    }
    
    // Deselect text
    input.blur();
}

function generateQRCode(url, serviceId) {
    const container = document.getElementById(`qr-code-${serviceId}`);
    
    // Clear existing QR code
    container.innerHTML = '';
    
    // Simple QR code generation using a free API
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(url)}`;
    
    const img = document.createElement('img');
    img.src = qrCodeUrl;
    img.alt = 'QR Code for booking';
    img.className = 'border border-gray-200 rounded';
    
    container.appendChild(img);
    container.classList.remove('hidden');
    container.classList.add('flex');
    
    // Add download link
    const downloadLink = document.createElement('a');
    downloadLink.href = qrCodeUrl;
    downloadLink.download = `booking-qr-code-${serviceId}.png`;
    downloadLink.className = 'block text-center text-sm text-blue-600 hover:text-blue-800 mt-2';
    downloadLink.textContent = 'Download QR Code';
    
    container.appendChild(downloadLink);
}

// Track social media shares (if analytics are implemented)
function trackShare(platform, serviceId) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'share', {
            'method': platform,
            'content_type': 'booking_link',
            'service_id': serviceId
        });
    }
}

// Add click tracking to social media links
document.addEventListener('DOMContentLoaded', function() {
    const serviceId = '{{ $serviceId ?? "default" }}';
    
    document.querySelectorAll('a[href*="facebook.com"]').forEach(link => {
        link.addEventListener('click', () => trackShare('facebook', serviceId));
    });
    
    document.querySelectorAll('a[href*="twitter.com"]').forEach(link => {
        link.addEventListener('click', () => trackShare('twitter', serviceId));
    });
    
    document.querySelectorAll('a[href*="linkedin.com"]').forEach(link => {
        link.addEventListener('click', () => trackShare('linkedin', serviceId));
    });
    
    document.querySelectorAll('a[href*="wa.me"]').forEach(link => {
        link.addEventListener('click', () => trackShare('whatsapp', serviceId));
    });
    
    document.querySelectorAll('a[href^="mailto:"]').forEach(link => {
        link.addEventListener('click', () => trackShare('email', serviceId));
    });
});
</script>
@endpush
