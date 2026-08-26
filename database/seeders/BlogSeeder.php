<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Enums\RoleEnum;
use App\Models\Capability;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(CapabilitySeeder::class);

        $writeCapability = Capability::where('key', 'blog.write')->firstOrFail();
        $editorCapability = Capability::where('key', 'blog.editor')->firstOrFail();

        // Editor blog (guru senior yang bertugas mereview)
        $editor = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'editor.blog@example.com',
            'role' => RoleEnum::TEACHER,
        ]);
        $editor->capabilities()->syncWithoutDetaching([
            $editorCapability->id,
            $writeCapability->id,
        ]);

        // Penulis: campuran guru & siswa, semua dapat capability blog.write
        $authors = User::factory()->count(4)->create(['role' => RoleEnum::TEACHER])
            ->concat(User::factory()->count(4)->create(['role' => RoleEnum::STUDENT]));

        $authors->each(
            fn (User $author) => $author->capabilities()->syncWithoutDetaching([$writeCapability->id])
        );

        $categories = Category::factory()->count(5)->create();
        $tags = Tag::factory()->count(12)->create();

        $allAuthors = $authors->push($editor);

        $allAuthors->each(function (User $author) use ($categories, $tags) {
            // Post published
            Post::factory()->count(3)->published()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
            ])->each(
                fn (Post $post) => $post->tags()->attach($tags->random(rand(1, 3))->pluck('id'))
            );

            // Post masih draft
            Post::factory()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
                'status' => PostStatus::DRAFT,
            ]);

            // Post menunggu review editor
            Post::factory()->pendingReview()->create([
                'user_id' => $author->id,
                'category_id' => $categories->random()->id,
            ]);
        });

        // Komentar (top-level + reply) untuk post yang sudah published
        Post::published()->get()->each(function (Post $post) {
            Comment::factory()->count(rand(0, 4))->create(['post_id' => $post->id])
                ->each(function (Comment $comment) use ($post) {
                    if (fake()->boolean(40)) {
                        Comment::factory()->create([
                            'post_id' => $post->id,
                            'parent_id' => $comment->id,
                        ]);
                    }
                });

            // Beberapa komentar masih menunggu moderasi
            Comment::factory()->pending()->count(rand(0, 2))->create(['post_id' => $post->id]);
        });

        // Likes acak dari user yang ada
        $allUsers = User::all();

        Post::published()->get()->each(function (Post $post) use ($allUsers) {
            $likers = $allUsers->random(min(rand(0, 10), $allUsers->count()));

            $likers->each(fn (User $user) => PostLike::firstOrCreate([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]));

            $post->update(['likes_count' => $post->likes()->count()]);
        });
    }
}
