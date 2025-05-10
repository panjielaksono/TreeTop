<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TransactionDetailSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 20; $i++) {
            $data = [
                'transaction_id' => $faker->numberBetween(1, 10),
                'product_id' => $faker->numberBetween(1, 10),
                'jumlah' => $faker->numberBetween(1, 5),
                'diskon' => $faker->randomFloat(2, 0, 50), // 50% max discount
                'subtotal_harga' => $faker->randomFloat(2, 10000, 50000),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ];

            $this->db->table('transaction_detail')->insert($data);
        }
    }
}
