<?php

use Illuminate\Database\Seeder;
use App\Entity\Manufacturer;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manufacturers=[['name'=>'正園'],['name'=>'御饌坊'],['name'=>'彩鶴']];
        foreach ($manufacturers as $item)
            Manufacturer::create($item);
    }
}
