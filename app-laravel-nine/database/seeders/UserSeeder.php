<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->create();
        // factory(User::class, 10)->create();

        User::query()->updateOrCreate([
            'email'=> 'admin@mail.com'
        ], [
            'username'=> 'admin',
            'email'=> 'admin@mail.com',
            'password'=> 'admin123', // bcrypt('password'), // '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
    }
}
