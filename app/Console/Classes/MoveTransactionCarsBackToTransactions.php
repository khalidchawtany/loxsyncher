<?php

namespace App\Console\Classes;

use DB;

class MoveTransactionCarsBackToTransactions
{
    public static function migrate()
    {
        DB::statement('
                    UPDATE transactions AS transactions1
                    SET transactions1.truck_id =
                        (
                                SELECT
                                transaction_cars2.car_id
                                FROM
                                (
                                        SELECT
                                            transaction_cars.car_id,
                                            transaction_cars.transaction_id
                                        FROM
                                            transaction_cars
                                ) transaction_cars2
                                WHERE
                                transaction_cars2.transaction_id = transactions1.id
                                limit 1
                        )
                        ');

        DB::statement('
                    UPDATE transactions AS transactions1
                    SET transactions1.amount =
                        (
                                SELECT
                                transaction_cars2.amount
                                FROM
                                (
                                        SELECT
                                            transaction_cars.amount,
                                            transaction_cars.transaction_id
                                        FROM
                                            transaction_cars
                                ) transaction_cars2
                                WHERE
                                transaction_cars2.transaction_id = transactions1.id
                                limit 1
                        )
                        ');

        DB::statement('
                    UPDATE transactions AS transactions1
                    SET transactions1.unit =
                        (
                                SELECT
                                transaction_cars2.unit
                                FROM
                                (
                                        SELECT
                                            transaction_cars.unit,
                                            transaction_cars.transaction_id
                                        FROM
                                            transaction_cars
                                ) transaction_cars2
                                WHERE
                                transaction_cars2.transaction_id = transactions1.id
                                limit 1
                        )
                        ');

        return 'DONE!';
    }
}
