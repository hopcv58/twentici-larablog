<?php
namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $post = Post::published()->get()->random();

        $hours = rand(0, 24);
        $datetime = $post->published_at->modify("+{$hours} hours");
        return [
            'post_id' => $post->id,
            'author_name' => $this->faker->name,
            'author_email' => $this->faker->email,
            'author_url' => rand(0, 5) > 2 ? $this->faker->url : null,
            'body' => $this->faker->paragraphs(rand(1, 5), true),
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ];
    }
}
