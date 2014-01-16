<?php

class CurrenciesTableSeeder extends Seeder {

    public function run() {
        // Uncomment the below to wipe the table clean before populating
        DB::table('currencies')->truncate();

        $currencies = array(
            [
                "alpha3" => "eur",
                "numeric" => "978",
                "multiplicator" => 100
            ]
        );

        // Uncomment the below to run the seeder
        DB::table('currencies')->insert($currencies);
    }
}
