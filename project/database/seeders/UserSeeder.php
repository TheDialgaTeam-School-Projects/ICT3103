<?php

namespace Database\Seeders;

use App\Repository\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param UserRepositoryInterface $userRepository
     * @return void
     */
    public function run(UserRepositoryInterface $userRepository)
    {
        $users = [];

        $users[] = [
            'username' => 'jianmingyong',
            'hashed_password' => '$2y$10$guILWksdshtFV13S2CT6/useh3AoxOcB.makekCrw07a/ZVvcBgH2',
            'first_name' => 'Jian Ming',
            'last_name' => 'Yong',
            'date_of_birth' => Carbon::create(1998, 2, 24),
            'authy_id' => 258114310,
        ];

        $userRepository->createBulkUsers($users);
    }
}
