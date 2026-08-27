<?php
/**
 * SewaSathi - Application Constants & Helpers
 */

if (!defined('APP_NAME')) define('APP_NAME', 'SewaSathi');
if (!defined('APP_NAME_NEPALI')) define('APP_NAME_NEPALI', 'सेवासाथी');
if (!defined('APP_TAGLINE')) define('APP_TAGLINE', 'Nepal\'s Trusted Home Service Booking Platform');
if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', 'रू ');
if (!defined('CURRENCY_CODE')) define('CURRENCY_CODE', 'NPR');
if (!defined('EMERGENCY_HOTLINE')) define('EMERGENCY_HOTLINE', '1660-01-73927');
if (!defined('SUPPORT_EMAIL')) define('SUPPORT_EMAIL', 'support@sewasathi.com');

// Status configuration
if (!defined('STATUS_CONFIG')) {
    define('STATUS_CONFIG', [
        'pending' => [
            'label' => 'Pending Confirmation',
            'nepali_label' => 'पुष्टिको पर्खाइमा',
            'badge_class' => 'bg-amber-50 text-amber-700 border-amber-200 ring-amber-600/20',
            'dot_class' => 'bg-amber-500',
            'step' => 1
        ],
        'accepted' => [
            'label' => 'Provider Assigned & Accepted',
            'nepali_label' => 'कारीगर स्वीकृत',
            'badge_class' => 'bg-blue-50 text-blue-700 border-blue-200 ring-blue-600/20',
            'dot_class' => 'bg-blue-500',
            'step' => 2
        ],
        'in_progress' => [
            'label' => 'Service In Progress',
            'nepali_label' => 'काम भइरहेको',
            'badge_class' => 'bg-indigo-50 text-indigo-700 border-indigo-200 ring-indigo-600/20',
            'dot_class' => 'bg-indigo-500',
            'step' => 3
        ],
        'completed' => [
            'label' => 'Work Completed',
            'nepali_label' => 'सम्पन्न भयो',
            'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200 ring-emerald-600/20',
            'dot_class' => 'bg-emerald-500',
            'step' => 4
        ],
        'cancelled' => [
            'label' => 'Booking Cancelled',
            'nepali_label' => 'रद्द गरिएको',
            'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200 ring-rose-600/20',
            'dot_class' => 'bg-rose-500',
            'step' => 0
        ]
    ]);
}

// Helper to format currency
function formatNpr(float|int $amount): string {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

// Pre-defined Nepali Municipalities & Wards for cascading selection
function getNepaliLocations(): array {
    return [
        'Bagmati' => [
            'Kathmandu' => [
                'Kathmandu Metropolitan City' => [
                    'type' => 'Metropolitan City',
                    'wards' => range(1, 32),
                    'landmarks' => ['Baluwatar', 'Lazimpat', 'New Baneshwor', 'Chabahil', 'Kalanki', 'Maharajgunj', 'Koteshwor', 'Thamel', 'Asan', 'Putalisadak', 'Sinamangal', 'Gaushala']
                ],
                'Kirtipur Municipality' => [
                    'type' => 'Municipality',
                    'wards' => range(1, 10),
                    'landmarks' => ['Naya Bazaar', 'TU Gate', 'Panga', 'Chobhar', 'Tyanglaphat']
                ],
                'Budhanilkantha Municipality' => [
                    'type' => 'Municipality',
                    'wards' => range(1, 13),
                    'landmarks' => ['Hattigauda', 'Golfutar', 'Mandikhatar', 'Chunikhel', 'Narayanthan']
                ],
                'Tokha Municipality' => [
                    'type' => 'Municipality',
                    'wards' => range(1, 11),
                    'landmarks' => ['Grande Hospital Area', 'Dhapasi', 'Baniyatar', 'Tokha Saraswati']
                ]
            ],
            'Lalitpur' => [
                'Lalitpur Metropolitan City' => [
                    'type' => 'Metropolitan City',
                    'wards' => range(1, 29),
                    'landmarks' => ['Pulchowk', 'Jhamsikhel', 'Kupondole', 'Sanepa', 'Jawalakhel', 'Kumaripati', 'Lagankhel', 'Mangalbazar', 'Ekantakuna', 'Dhapakhel', 'Balkumari']
                ],
                'Mahalaxmi Municipality' => [
                    'type' => 'Municipality',
                    'wards' => range(1, 10),
                    'landmarks' => ['Imadol', 'Tikathali', 'Lubhu', 'Siddhipur']
                ]
            ],
            'Bhaktapur' => [
                'Bhaktapur Municipality' => [
                    'type' => 'Municipality',
                    'wards' => range(1, 10),
                    'landmarks' => ['Durbar Square Area', 'Byasi', 'Kamalbinayak', 'Suryabinayak Gate', 'Jagati']
                ],
                'Madhyapur Thimi Municipality' => [
                    'type' => 'Municipality',
                    'wards' => range(1, 9),
                    'landmarks' => ['Sanothimi CTEVT', 'Lokanthali', 'Gatthaghar', 'Kaushaltar', 'Balkot']
                ]
            ],
            'Chitwan' => [
                'Bharatpur Metropolitan City' => [
                    'type' => 'Metropolitan City',
                    'wards' => range(1, 29),
                    'landmarks' => ['Narayangarh Lion Chowk', 'Chaubiskothi', 'Bypass Road', 'Rampur', 'Parsa']
                ]
            ]
        ],
        'Gandaki' => [
            'Kaski' => [
                'Pokhara Metropolitan City' => [
                    'type' => 'Metropolitan City',
                    'wards' => range(1, 33),
                    'landmarks' => ['Lakeside', 'Mahendrapool', 'Prithvi Chowk', 'Biruta', 'Bagar', 'Chipledhunga']
                ]
            ]
        ],
        'Koshi' => [
            'Morang' => [
                'Biratnagar Metropolitan City' => [
                    'type' => 'Metropolitan City',
                    'wards' => range(1, 19),
                    'landmarks' => ['Traffic Chowk', 'Main Road', 'Bargachhi', 'Rani Gate']
                ]
            ],
            'Sunsari' => [
                'Dharan Sub-Metropolitan City' => [
                    'type' => 'Sub-Metropolitan City',
                    'wards' => range(1, 20),
                    'landmarks' => ['Bhanu Chowk', 'BPKIHS Area', 'Chhata Chowk', 'Putali Line']
                ]
            ]
        ],
        'Lumbini' => [
            'Rupandehi' => [
                'Butwal Sub-Metropolitan City' => [
                    'type' => 'Sub-Metropolitan City',
                    'wards' => range(1, 19),
                    'landmarks' => ['Traffic Chowk', 'Milan Chowk', 'Golpark', 'Kalikanagar']
                ]
            ]
        ]
    ];
}
