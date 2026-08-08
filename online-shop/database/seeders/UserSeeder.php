<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed admin, sellers and customers.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@shopease.test'],
            [
                'name' => 'System Admin',
                'email' => 'admin@shopease.test',
                'password' => Hash::make('password'),
                'phone' => '01700000001',
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
                'address' => 'Head Office, Dhaka',
            ]
        );

        $sellers = [
            ['name' => 'Tech Zone', 'email' => 'seller1@shopease.test', 'phone' => '01700000011', 'address' => 'Dhanmondi, Dhaka'],
            ['name' => 'Fashion Hub', 'email' => 'seller2@shopease.test', 'phone' => '01700000012', 'address' => 'Uttara, Dhaka'],
            ['name' => 'Home Mart', 'email' => 'seller3@shopease.test', 'phone' => '01700000013', 'address' => 'Gulshan, Dhaka'],
        ];

        foreach ($sellers as $seller) {
            User::updateOrCreate(
                ['email' => $seller['email']],
                array_merge($seller, [
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_SELLER,
                    'status' => User::STATUS_ACTIVE,
                ])
            );
        }

        $customers = [
            ['name' => 'Alice Rahman', 'email' => 'customer1@shopease.test', 'phone' => '01800000001', 'address' => 'Mirpur, Dhaka'],
            ['name' => 'Bob Ahmed', 'email' => 'customer2@shopease.test', 'phone' => '01800000002', 'address' => 'Banani, Dhaka'],
            ['name' => 'Carol Islam', 'email' => 'customer3@shopease.test', 'phone' => '01800000003', 'address' => 'Motijheel, Dhaka'],
            ['name' => 'David Khan', 'email' => 'customer4@shopease.test', 'phone' => '01800000004', 'address' => 'Khilgaon, Dhaka'],
            ['name' => 'Eva Chowdhury', 'email' => 'customer5@shopease.test', 'phone' => '01800000005', 'address' => 'Bashundhara, Dhaka'],
            ['name' => 'Frank Hoque', 'email' => 'customer6@shopease.test', 'phone' => '01800000006', 'address' => 'Rampura, Dhaka'],
            ['name' => 'Grace Das', 'email' => 'customer7@shopease.test', 'phone' => '01800000007', 'address' => 'Mohammadpur, Dhaka'],
            ['name' => 'Hasan Sheikh', 'email' => 'customer8@shopease.test', 'phone' => '01800000008', 'address' => 'Lalmatia, Dhaka'],
            ['name' => 'Ivy Begum', 'email' => 'customer9@shopease.test', 'phone' => '01800000009', 'address' => 'Shyamoli, Dhaka'],
            ['name' => 'James Paul', 'email' => 'customer10@shopease.test', 'phone' => '01800000010', 'address' => 'Cantonment, Dhaka'],
        ];

        foreach ($customers as $customer) {
            User::updateOrCreate(
                ['email' => $customer['email']],
                array_merge($customer, [
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_CUSTOMER,
                    'status' => User::STATUS_ACTIVE,
                ])
            );
        }

        // A blocked example customer for demo purposes
        User::firstOrCreate(
            ['email' => 'blocked@shopease.test'],
            [
                'name' => 'Blocked User',
                'password' => Hash::make('password'),
                'phone' => '01800000099',
                'role' => User::ROLE_CUSTOMER,
                'status' => User::STATUS_BLOCKED,
            ]
        );
    }
}
