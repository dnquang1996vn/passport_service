<?php

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
        // $this->call(UserSeeder::class);
        $user = factory(\App\User::class)->create([
            'name' => 'admin',
            'email' => 'ssoAdminManager@admin.com',
            'password' => bcrypt('password'),
        ]);
    }
}
