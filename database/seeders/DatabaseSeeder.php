<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        User::factory()->times(1)->create([
            'name' => 'Fco',
            'surname' => 'Marcet Prieto',
            'email' => 'fcomarcet1@gmail.com',
            'password' => bcrypt("secret"),
            'role' => 'ROLE_USER',

        ]);
        User::factory()->times(1)->create([
            'name' => 'admin',
            'surname' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt("secret"),
            'role' => 'ROLE_ADMIN',

        ]);

        Category::factory()->times(5)->create();
        Post::factory()->times(10)->create();

    }
}
