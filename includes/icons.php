<?php
/**
 * SewaSathi - Professional SVG Icon Library
 * 100% Emoji-Free, Pure Crisp Vector Icons with Tailwind Class Customization
 */

function icon(string $name, string $class = 'w-5 h-5', string $extra = ''): string {
    $icons = [
        'wrench' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>',

        'zap' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polygon></svg>',

        'hammer' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 12-8.5 8.5c-.83.83-2.17.83-3 0 0 0 0 0 0 0a2.12 2.12 0 0 1 0-3L12 9"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m17.64 15 4.07-4.06a2.5 2.5 0 0 0 0-3.54l-5.11-5.11a2.5 2.5 0 0 0-3.54 0L9 6.36"></path></svg>',

        'sparkles' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"></path></svg>',

        'paint-brush' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 11-8-8-8.6 8.6a2 2 0 0 0 0 2.8l5.2 5.2c.8.8 2 .8 2.8 0L19 11Z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 2 5 5"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 13h15"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 20a2 2 0 1 1-4 0c0-1.6 1.7-2.4 2-4 .3 1.6 2 2.4 2 4Z"></path></svg>',

        'tv' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="15" x="2" y="7" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect><polyline points="17 2 12 7 7 2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polyline></svg>',

        'axe' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14 12-8.5 8.5a2.12 2.12 0 1 1-3-3L11 9"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13 9 7l4-4 6 6h3a8 8 0 0 1-7 7v-3z"></path></svg>',

        'thermometer' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"></path></svg>',

        'check-circle' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',

        'verified' => '<svg class="' . $class . '" ' . $extra . ' viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0112 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 013.498 1.307 4.491 4.491 0 011.307 3.497A4.49 4.49 0 0121.75 12a4.49 4.49 0 01-1.549 3.397 4.491 4.491 0 01-1.307 3.497 4.491 4.491 0 01-3.497 1.307A4.49 4.49 0 0112 21.75a4.49 4.49 0 01-3.397-1.549 4.49 4.49 0 01-3.498-1.306 4.491 4.491 0 01-1.307-3.498A4.49 4.49 0 012.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 011.307-3.497 4.49 4.49 0 013.497-1.307zm7.007 6.387a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>',

        'star' => '<svg class="' . $class . '" ' . $extra . ' fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>',

        'star-empty' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>',

        'map-pin' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',

        'calendar' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect width="18" height="18" x="3" y="4" rx="2" ry="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect><line x1="16" x2="16" y1="2" y2="6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line><line x1="8" x2="8" y1="2" y2="6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line><line x1="3" x2="21" y1="10" y2="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line></svg>',

        'clock' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle><polyline points="12 6 12 12 16 14" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polyline></svg>',

        'phone' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>',

        'shield-check' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',

        'user' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>',

        'search' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>',

        'arrow-right' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>',

        'chevron-down' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>',

        'filter' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>',

        'credit-card' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect width="20" height="14" x="2" y="5" rx="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect><line x1="2" x2="22" y1="10" y2="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line></svg>',

        'wallet' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7V4a1 1 0 00-1-1H5a2 2 0 000 4h15a1 1 0 011 1v4h-3a2 2 0 000 4h3a1 1 0 001-1v-2a1 1 0 00-1-1"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5v14a2 2 0 002 2h15a1 1 0 001-1v-4"></path></svg>',

        'logout' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>',

        'dashboard' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect width="7" height="9" x="3" y="3" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect><rect width="7" height="5" x="14" y="3" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect><rect width="7" height="9" x="14" y="12" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect><rect width="7" height="5" x="3" y="16" rx="1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect></svg>',

        'menu' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>',

        'bell' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.73 21a2 2 0 01-3.46 0"></path></svg>',

        'file-text' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polyline><line x1="16" x2="8" y1="13" y2="13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line><line x1="16" x2="8" y1="17" y2="17" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line><polyline points="10 9 9 9 8 9" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polyline></svg>',

        'award' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polyline></svg>',

        'truck' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect width="16" height="12" x="1" y="3" rx="2" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></rect><polygon points="16 8 20 8 23 11 23 15 16 15 16 8" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polygon><circle cx="5.5" cy="18.5" r="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle><circle cx="18.5" cy="18.5" r="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle></svg>',

        'x' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',

        'refresh' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>',

        'users' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',

        'alert-circle' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></circle><line x1="12" x2="12" y1="8" y2="12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line><line x1="12" x2="12.01" y1="16" y2="16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></line></svg>',
        
        'badge-check' => '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>'
    ];

    return $icons[$name] ?? '<svg class="' . $class . '" ' . $extra . ' fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle></svg>';
}

// Render 5-star rating breakdown helper
function renderStarRating(float $rating, string $starClass = 'w-4 h-4 text-amber-400'): string {
    $fullStars = floor($rating);
    $html = '<div class="flex items-center gap-0.5" title="' . number_format($rating, 1) . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $fullStars) {
            $html .= icon('star', $starClass);
        } else {
            $html .= icon('star-empty', 'w-4 h-4 text-slate-300');
        }
    }
    $html .= '</div>';
    return $html;
}
