<?php
namespace Database\Factories;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialAccountFactory extends Factory {
    protected $model = SocialAccount::class;

    public function definition(): array {
        $platform = $this->faker->randomElement(['instagram', 'facebook', 'tiktok', 'twitter', 'youtube']);
        return [
            'user_id' => User::factory(),
            'platform' => $platform,
            'platform_user_id' => $this->faker->numerify('##########'),
            'username' => '@' . $this->faker->userName(),
            'display_name' => $this->faker->name(),
            'access_token' => 'fake_token_' . $this->faker->sha256(),
            'token_expires_at' => now()->addMonths(3),
            'follower_count' => $this->faker->numberBetween(100, 100000),
            'is_active' => true,
        ];
    }

    public function platform(string $platform): static {
        return $this->state(['platform' => $platform]);
    }

    public function expiring(): static {
        return $this->state(['token_expires_at' => now()->addDays(3)]);
    }
}
