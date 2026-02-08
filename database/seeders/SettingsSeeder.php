<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [

            // ── General ─────────────────────────────────────
            ['group' => 'general',     'name' => 'site_name',          'label' => 'نام سایت',                  'type' => 'text',     'value' => 'شرکت XYZ'],
            ['group' => 'general',     'name' => 'site_slogan',         'label' => 'شعار سایت',                 'type' => 'text',     'value' => 'نوآوری در خدمت شما'],
            ['group' => 'general',     'name' => 'company_address',     'label' => 'آدرس شرکت',                 'type' => 'textarea', 'value' => 'تهران، خیابان انقلاب، پلاک ۱۲۳'],
            ['group' => 'general',     'name' => 'company_phone',       'label' => 'تلفن',                      'type' => 'text',     'value' => '021-12345678'],
            ['group' => 'general',     'name' => 'company_email',       'label' => 'ایمیل',                     'type' => 'text',     'value' => 'info@company.com'],

            // ── Branding ────────────────────────────────────
            ['group' => 'branding',    'name' => 'logo_light',          'label' => 'لوگو روشن',                 'type' => 'image',    'value' => null],
            ['group' => 'branding',    'name' => 'logo_dark',           'label' => 'لوگو تیره',                 'type' => 'image',    'value' => null],
            ['group' => 'branding',    'name' => 'favicon',             'label' => 'فاویکون',                   'type' => 'image',    'value' => null],
            ['group' => 'branding',    'name' => 'primary_color',       'label' => 'رنگ اصلی',                  'type' => 'text',     'value' => '#0ea5e9'],

            // ── SEO ─────────────────────────────────────────
            ['group' => 'seo',         'name' => 'seo_default_title',   'label' => 'عنوان پیش‌فرض',             'type' => 'text',     'value' => 'شرکت XYZ | ارائه‌دهنده راه‌حل‌های نوآورانه'],
            ['group' => 'seo',         'name' => 'seo_default_description', 'label' => 'توضیحات پیش‌فرض',       'type' => 'textarea', 'value' => ''],
            ['group' => 'seo',         'name' => 'google_analytics_id', 'label' => 'شناسه گوگل آنالیتیکس',     'type' => 'text',     'value' => 'G-XXXXXXXXXX'],
            ['group' => 'seo',         'name' => 'google_site_verification', 'label' => 'کد تأیید گوگل',      'type' => 'text',     'value' => ''],

            // ── Maintenance ─────────────────────────────────
            ['group' => 'maintenance', 'name' => 'maintenance_mode',    'label' => 'فعال کردن حالت نگهداری',   'type' => 'toggle',   'value' => '0'],
            ['group' => 'maintenance', 'name' => 'maintenance_message', 'label' => 'پیام حالت نگهداری',        'type' => 'textarea', 'value' => 'سایت در حال به‌روزرسانی است. به‌زودی برمی‌گردیم.'],

            // ── Footer ──────────────────────────────────────
            ['group' => 'footer',      'name' => 'footer_copyright',    'label' => 'متن کپی‌رایت فوتر',        'type' => 'text',     'value' => '© ۱۴۰۴ شرکت XYZ - تمامی حقوق محفوظ است'],
            ['group' => 'footer',      'name' => 'footer_text',         'label' => 'متن اضافی فوتر',           'type' => 'textarea', 'value' => ''],

            // ── Contact ─────────────────────────────────────
            ['group' => 'contact',     'name' => 'contact_email',       'label' => 'ایمیل تماس',                'type' => 'text',     'value' => 'info@company.com'],
            ['group' => 'contact',     'name' => 'contact_phone',       'label' => 'تلفن تماس',                 'type' => 'text',     'value' => '021-12345678'],
            ['group' => 'contact',     'name' => 'contact_whatsapp',    'label' => 'واتساپ',                    'type' => 'text',     'value' => '+989123456789'],

            // ── Social ──────────────────────────────────────
            ['group' => 'social',      'name' => 'instagram',           'label' => 'اینستاگرام',                'type' => 'text',     'value' => 'https://instagram.com/company'],
            ['group' => 'social',      'name' => 'linkedin',            'label' => 'لینکدین',                   'type' => 'text',     'value' => 'https://linkedin.com/company'],

            // ── Legal ───────────────────────────────────────
            ['group' => 'legal',       'name' => 'privacy_policy_link', 'label' => 'لینک حریم خصوصی',           'type' => 'text',     'value' => '/privacy'],
            ['group' => 'legal',       'name' => 'terms_link',          'label' => 'لینک شرایط استفاده',        'type' => 'text',     'value' => '/terms'],
            ['group' => 'legal',       'name' => 'cookie_banner_enabled','label' => 'نمایش بنر کوکی',           'type' => 'toggle',   'value' => '1'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['name' => $setting['name'], 'group' => $setting['group']],
                $setting
            );
        }
    }
}
