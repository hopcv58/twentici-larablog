<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * IMPORTANT!
         * The database seed is written to handle the task centralized
         * It should use:
         * php artisan db:seed
         * -> You can not run the seeds separately, it could cause errors!
         */

        // Truncate all tables, except migrations
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            if ($table->{'Tables_in_' . env('DB_DATABASE')} !=='migrations')
                DB::table($table->{'Tables_in_' . env('DB_DATABASE')})->truncate();
        }

        User::factory()->count(5)->create();
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);

        $this->call(CategoriesTableSeeder::class);
        Category::factory()->count(10)->create();

        Post::factory()->count(100)->create();

        Tag::factory()->count(20)->create();
        $this->call(PostTagTableSeeder::class);

        Comment::factory()->count(250)->create();
    }
}
