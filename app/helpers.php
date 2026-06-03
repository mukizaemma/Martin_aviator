<?php

use App\Models\PageHeader;

if (! function_exists('page_header_url')) {
    function page_header_url(string $slug): ?string
    {
        return PageHeader::urlFor($slug);
    }
}
