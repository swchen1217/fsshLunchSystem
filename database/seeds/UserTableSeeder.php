<?php

use Illuminate\Database\Seeder;
use App\Entity\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(['account'=>'810001','password'=>bcrypt('test'),'class'=>'101','number'=>'1','name'=>'Tester','email'=>'swchen1217@gmail.com']);
        User::create(['account'=>'810461','password'=>bcrypt('daboyu'),'class'=>'113','number'=>'30','name'=>'Dabo','email'=>'borishuang813@gmail.com']);
    }
}
