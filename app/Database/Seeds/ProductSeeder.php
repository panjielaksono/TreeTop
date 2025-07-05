<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama' => 'Dumbell',
                'harga' => 55000,
                'jumlah' => 17,
                'berat' => 2500,
                'foto' => '1748393241_f434fd3b266450c54ff1.jpg',
                'created_at' => '2025-05-20 13:52:41',
                'updated_at' => '2025-05-28 00:47:21',
            ],
            [
                'nama' => 'Tumblr',
                'harga' => 35000,
                'jumlah' => 20,
                'berat' => 400,
                'foto' => '1747749416_02dad0aa91dee77f5140.jpeg',
                'created_at' => '2025-05-20 13:56:56',
                'updated_at' => null,
            ],
            [
                'nama' => 'Steawberry Whey',
                'harga' => 90000,
                'jumlah' => 11,
                'berat' => 1000,
                'foto' => '1747749638_95665e6417a020efc34a.png',
                'created_at' => '2025-05-20 14:00:38',
                'updated_at' => null,
            ],
            [
                'nama' => 'Triple Pro Whey 2270 g',
                'harga' => 120000,
                'jumlah' => 25,
                'berat' => 2270,
                'foto' => '1747749662_4e971f22d84e3b2b3a6c.jpg',
                'created_at' => '2025-05-20 14:01:02',
                'updated_at' => '2025-05-20 14:02:04',
            ],
            [
                'nama' => 'HULKFIT 15 LB Plate',
                'harga' => 249000,
                'jumlah' => 12,
                'berat' => 6800,
                'foto' => '1748331966_044a4e319a898688871f.webp',
                'created_at' => '2025-05-27 07:46:06',
                'updated_at' => '2025-05-27 07:46:26',
            ],
            [
                'nama' => 'Tomahawk',
                'harga' => 12000000000000, // 😱 pastikan ini tidak melebihi batas BIGINT
                'jumlah' => 12,
                'berat' => 1200,
                'foto' => '1748996738_b16ffbc08a36d708e8ab.jpg',
                'created_at' => '2025-06-04 00:25:38',
                'updated_at' => null,
            ],
        ];

        foreach ($data as $item) {
            $this->db->table('product')->insert($item);
        }
    }
}
