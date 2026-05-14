<?php

namespace App\Http\Controllers;

use App\Models\SiteAnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DirectPayPlaceholderController extends Controller
{
    public function __invoke(Request $request): View
    {
        SiteAnalyticsEvent::create([
            'event_key' => 'direct_pay_page_view',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        return view('frontend.pay-dpo-placeholder');
    }
}
