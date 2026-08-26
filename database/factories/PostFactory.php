<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(6);

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Post::uniqueSlug($title),
            'excerpt' => fake()->paragraph(),
            'content' => collect(fake()->paragraphs(5))
                ->map(fn (string $p) => "<p>{$p}</p>")
                ->implode(''),
            'status' => PostStatus::DRAFT,
            'read_time' => fake()->numberBetween(2, 10),
            'views_count' => fake()->numberBetween(0, 500),
            'likes_count' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::PUBLISHED,
            'published_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ]);
    }

    public function pendingReview(): static
    {
        return $this->state([
            'status' => PostStatus::PENDING_REVIEW,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::ARCHIVED,
            'published_at' => fake()->dateTimeBetween('-6 months', '-2 months'),
        ]);
    }
}
