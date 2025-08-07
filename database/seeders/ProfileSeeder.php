<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereDoesntHave('profile')->get();

        foreach ($users as $user) {
            Profile::create([
                'name' => $user->name,
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('Created profiles for ' . $users->count() . ' users');
    }
}
