<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum image file size (bytes)
    |--------------------------------------------------------------------------
    | Uploads over this size are resized and compressed. Smaller files are
    | stored unchanged.
    */
    'max_image_bytes' => (int) env('MEDIA_MAX_IMAGE_KB', 700) * 1024,

    /*
    |--------------------------------------------------------------------------
    | Folder → optimization preset (for batch compress command)
    |--------------------------------------------------------------------------
    */
    'folder_presets' => [
        'slides' => 'slide',
        'gallery' => 'gallery',
        'rooms' => 'room',
        'facilities' => 'facility',
        'services' => 'service',
        'partners' => 'partner',
        'pages' => 'page',
        'dining' => 'dining',
        'dining-gallery' => 'gallery',
        'menu-categories' => 'dining',
    ],
];
