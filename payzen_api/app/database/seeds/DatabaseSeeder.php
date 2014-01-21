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
		$this->call('AvailablemethodsTableSeeder');
		$this->call('ContextsTableSeeder');
		$this->call('CurrenciesTableSeeder');
		$this->call('TransactionsTableSeeder');
	}

}