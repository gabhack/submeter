<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
	public function run()
	{
		\DB::table('users')->insert(
			array(
				'email' => 'admin@mail.com',
				'password' => \Hash::make('123456'),
				'name' => 'Administrador',
				'tipo' => 1
			)
		);

		\DB::table('users')->insert(
			array(
				'email' => 'cliente@mail.com',
				'password' => \Hash::make('123456'),
				'name' => 'Cliente1',
				'tipo' => 2
			)
		);

		\DB::table('perfils')->insert(
			array(
				'direccion' => 'Venezuela',
				'fijo' => 123,
				'movil' => 1234,
				'user_id' => 1
			)
		);

		\DB::table('perfils')->insert(
			array(
				'direccion' => 'Venezuela',
				'fijo' => 321,
				'movil' => 4321,
				'user_id' => 2
			)
		);

		\DB::table('counts')->insert(
			array(
				'count_label' => 'Contador1',
				'user_id' => 2
			)
		);


	}
}