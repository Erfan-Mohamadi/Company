<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\TranslationKey;
use Illuminate\Database\Seeder;

class LanguageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default Persian language (main language)
        $farsi = Language::firstOrCreate(
            ['name' => 'fa'],
            [
                'label' => 'فارسی',
                'is_rtl' => true,
            ]
        );

        $this->command->info('✅ Persian language created/updated');

        // Create English language
        $english = Language::firstOrCreate(
            ['name' => 'en'],
            [
                'label' => 'English',
                'is_rtl' => false,
            ]
        );

        $this->command->info('✅ English language created/updated');

        // Create Arabic language (optional)
        $arabic = Language::firstOrCreate(
            ['name' => 'ar'],
            [
                'label' => 'العربية',
                'is_rtl' => true,
            ]
        );

        $this->command->info('✅ Arabic language created/updated');

        // Create some sample translation keys
        $this->createSampleTranslations();

        $this->command->info('✅ Sample translations created');
        $this->command->info('🎉 Language database seeding completed successfully!');
    }

    /**
     * Create sample translation keys
     */
    private function createSampleTranslations(): void
    {
        $sampleKeys = [
            [
                'key' => 'welcome_message',
                'value' => [
                    'en' => 'Welcome',
                    'fa' => 'خوش آمدید',
                    'ar' => 'مرحبا',
                ],
                'group' => 'general',
                'message' => true,
            ],
            [
                'key' => 'goodbye',
                'value' => [
                    'en' => 'Goodbye',
                    'fa' => 'خداحافظ',
                    'ar' => 'وداعا',
                ],
                'group' => 'general',
                'message' => true,
            ],
            [
                'key' => 'hello',
                'value' => [
                    'en' => 'Hello',
                    'fa' => 'سلام',
                    'ar' => 'مرحبا',
                ],
                'group' => 'general',
                'message' => true,
            ],
            [
                'key' => 'save',
                'value' => [
                    'en' => 'Save',
                    'fa' => 'ذخیره',
                    'ar' => 'حفظ',
                ],
                'group' => 'buttons',
                'message' => true,
            ],
            [
                'key' => 'cancel',
                'value' => [
                    'en' => 'Cancel',
                    'fa' => 'لغو',
                    'ar' => 'إلغاء',
                ],
                'group' => 'buttons',
                'message' => true,
            ],
            [
                'key' => 'delete',
                'value' => [
                    'en' => 'Delete',
                    'fa' => 'حذف',
                    'ar' => 'حذف',
                ],
                'group' => 'buttons',
                'message' => true,
            ],
            [
                'key' => 'edit',
                'value' => [
                    'en' => 'Edit',
                    'fa' => 'ویرایش',
                    'ar' => 'تعديل',
                ],
                'group' => 'buttons',
                'message' => true,
            ],
            [
                'key' => 'email',
                'value' => [
                    'en' => 'Email',
                    'fa' => 'ایمیل',
                    'ar' => 'البريد الإلكتروني',
                ],
                'group' => 'auth',
                'message' => true,
            ],
            [
                'key' => 'password',
                'value' => [
                    'en' => 'Password',
                    'fa' => 'رمز عبور',
                    'ar' => 'كلمة المرور',
                ],
                'group' => 'auth',
                'message' => true,
            ],
            [
                'key' => 'login',
                'value' => [
                    'en' => 'Login',
                    'fa' => 'ورود',
                    'ar' => 'تسجيل الدخول',
                ],
                'group' => 'auth',
                'message' => true,
            ],
            [
                'key' => 'logout',
                'value' => [
                    'en' => 'Logout',
                    'fa' => 'خروج',
                    'ar' => 'تسجيل الخروج',
                ],
                'group' => 'auth',
                'message' => true,
            ],
            [
                'key' => 'register',
                'value' => [
                    'en' => 'Register',
                    'fa' => 'ثبت نام',
                    'ar' => 'التسجيل',
                ],
                'group' => 'auth',
                'message' => true,
            ],
            [
                'key' => 'success_message',
                'value' => [
                    'en' => 'Operation completed successfully!',
                    'fa' => 'عملیات با موفقیت انجام شد!',
                    'ar' => 'تمت العملية بنجاح!',
                ],
                'group' => 'messages',
                'message' => true,
            ],
            [
                'key' => 'error_message',
                'value' => [
                    'en' => 'An error occurred. Please try again.',
                    'fa' => 'خطایی رخ داد. لطفا دوباره تلاش کنید.',
                    'ar' => 'حدث خطأ. يرجى المحاولة مرة أخرى.',
                ],
                'group' => 'messages',
                'message' => true,
            ],
            [
                'key' => 'confirm_delete',
                'value' => [
                    'en' => 'Are you sure you want to delete this item?',
                    'fa' => 'آیا مطمئن هستید که می‌خواهید این مورد را حذف کنید؟',
                    'ar' => 'هل أنت متأكد أنك تريد حذف هذا العنصر؟',
                ],
                'group' => 'messages',
                'message' => true,
            ],
        ];

        foreach ($sampleKeys as $keyData) {
            TranslationKey::firstOrCreate(
                ['key' => $keyData['key']],
                $keyData
            );
        }
    }
}
