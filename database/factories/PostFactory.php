<?php
namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory {
    protected $model = Post::class;

    public function definition(): array {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(6),
            'status' => 'draft',
            'scheduled_at' => null,
        ];
    }

    public function scheduled(): static {
        return $this->state([
            'status' => 'scheduled',
            'scheduled_at' => now()->addDays(rand(1, 30)),
        ]);
    }

    public function published(): static {
        return $this->state([
            'status' => 'published',
            'scheduled_at' => now()->subDays(rand(1, 30)),
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function draft(): static {
        return $this->state(['status' => 'draft']);
    }
}
