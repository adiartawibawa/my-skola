<?php

namespace Database\Factories;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => null,
            'parent_id' => null,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'content' => fake()->paragraph(),
            'status' => CommentStatus::APPROVED,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => CommentStatus::PENDING]);
    }

    public function byUser(User $user): static
    {
        return $this->state([
            'user_id' => $user->id,
            'guest_name' => null,
            'guest_email' => null,
        ]);
    }
}
