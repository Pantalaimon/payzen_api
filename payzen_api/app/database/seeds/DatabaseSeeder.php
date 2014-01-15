<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
		$this->call('ChargesTableSeeder');
		$this->call('AvalaiblemethodsTableSeeder');
		$this->call('ContextsTableSeeder');
		$this->call('MessagesTableSeeder');
		$this->call('UsedmethodsTableSeeder');
		$this->call('CurrenciesTableSeeder');
	}

}