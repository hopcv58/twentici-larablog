<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $datetime = $this->faker->dateTimeThisYear();
        return [
            'author_id' => function () {
                return User::all()->random();
            },
            'category_id' => function () {
                return Category::all()->random();
            },
            'title' => $this->faker->sentence(rand(8, 12)),
            'excerpt' => $this->faker->text(rand(250, 300)),
            'body' => $this->faker->paragraphs(rand(10, 15), true),
            'slug' => $this->faker->unique()->slug,
            'image' => rand(0, 5) > 3 ? 'Post_Image_' . rand(1, 5) . '.jpg' : null,
            'view_count' => rand(1, 10) * 10,
            'created_at' => $datetime,
            'updated_at' => $datetime,
            'published_at' => rand(0, 4) == 0 ? $this->faker->dateTimeBetween('now', '+3 months') : (rand(0, 4) == 0 ? null : $datetime)
        ];
    }
}
