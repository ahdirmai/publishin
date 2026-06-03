<?php
namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder {
    public function run(): void {
        $plans = [
            [
                'name' => 'Starter', 'slug' => 'starter',
                'price_monthly' => 99000, 'price_yearly' => 990000,
                'max_social_accounts' => 3, 'max_scheduled_posts' => 30,
                'max_team_members' => 1, 'max_clients' => 1,
                'has_ai_features' => false, 'has_white_label' => false, 'has_api_access' => false,
            ],
            [
                'name' => 'Pro', 'slug' => 'pro',
                'price_monthly' => 149000, 'price_yearly' => 1490000,
                'max_social_accounts' => 10, 'max_scheduled_posts' => 0,
                'max_team_members' => 3, 'max_clients' => 1,
                'has_ai_features' => true, 'has_white_label' => true, 'has_api_access' => false,
            ],
            [
                'name' => 'Agency', 'slug' => 'agency',
                'price_monthly' => 299000, 'price_yearly' => 2990000,
                'max_social_accounts' => 0, 'max_scheduled_posts' => 0,
                'max_team_members' => 0, 'max_clients' => 0,
                'has_ai_features' => true, 'has_white_label' => true, 'has_api_access' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
