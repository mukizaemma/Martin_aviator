<?php

namespace App\View\Composers;

use App\Models\About;
use App\Models\Facility;
use App\Models\Setting;
use Illuminate\View\View;

class FrontLayoutComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();

        $setting = $data['setting'] ?? null;
        if ($setting === null) {
            $setting = Setting::first();
        }
        if ($setting === null) {
            $setting = (object) [
                'title' => '',
                'keywords' => '',
                'company' => config('app.name', ''),
                'address' => '',
                'phone' => '',
                'phone1' => '',
                'phone2' => '',
                'email' => '',
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'youtube' => '',
                'linkedin' => '',
                'reserveUrl' => '',
                'logo' => '',
                'usd_to_rwf_rate' => 1300,
                'facilities_hero_image' => '',
                'facilities_intro' => '',
                'dining_hero_image' => '',
                'dining_intro' => '',
                'flexible_stay_bg_image' => '',
                'flexible_stay_heading' => '',
                'flexible_stay_subheading' => '',
                'flexible_stay_card1_title' => '',
                'flexible_stay_card1_text' => '',
                'flexible_stay_card1_icon' => '',
                'flexible_stay_card2_title' => '',
                'flexible_stay_card2_text' => '',
                'flexible_stay_card2_icon' => '',
                'flexible_stay_card3_title' => '',
                'flexible_stay_card3_text' => '',
                'flexible_stay_card3_icon' => '',
                'url_booking' => '',
                'url_expedia' => '',
                'url_tripadvisor' => '',
                'url_google_business' => '',
                'google_map_embed' => '',
            ];
        }
        $view->with('setting', $setting);

        $about = $data['about'] ?? null;
        if ($about === null) {
            $about = About::first();
        }
        if ($about === null) {
            $about = (object) [
                'title' => '',
                'mission' => '',
                'vision' => '',
                'background' => '',
                'welcome' => '',
                'values' => '',
                'chooseUs' => '',
                'specialities' => '',
                'calculumn' => '',
                'startYear' => '',
                'students' => '',
                'graduates' => '',
                'aboutImage' => '',
                'middleImage' => '',
                'chooseusImage' => '',
                'terms' => '',
            ];
        }
        $view->with('about', $about);

        if (! array_key_exists('facilities', $data) || $data['facilities'] === null) {
            $view->with('facilities', Facility::orderBy('created_at', 'asc')->paginate(6));
        }
    }
}
