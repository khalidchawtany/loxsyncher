<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class ResetDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ResetDB';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets DB for new site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $preservedUserIDS = '(1,33,36,37, 39, 40,41, 82, 90, 91, 93, 94, 95)';

        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        DB::statement('update app_settings set user_id = 1;');
        DB::statement('update destinations set user_id = 1;');
        DB::statement('update countries set user_id = 1;');
        DB::statement('update check_types set user_id = 1;');
        DB::statement('update departments set user_id = 1;');
        DB::statement('update permissions_descriptions set user_id = 1;');
        DB::statement('update products set user_id = 1;');
        DB::statement('update categories set user_id = 1;');

        DB::statement('truncate  activity_log');
        DB::statement('truncate  sensor_logs');
        DB::statement('truncate  sensors');

        DB::statement('truncate  incineration_fees');
        DB::statement('truncate  incineration_payments');
        DB::statement('truncate  incineration_batches');
        DB::statement('truncate  incinerations');

        DB::statement('truncate  balance_transactions');
        DB::statement('truncate  balances');

        DB::statement('truncate  checks');
        DB::statement('truncate  batches');
        // DB::statement('truncate  transaction_trucks');
        DB::statement('truncate  vehicles');
        DB::statement('truncate  payments');
        DB::statement('truncate  inspections');
        DB::statement('truncate  transactions');
        DB::statement('truncate  inspectors');

        DB::statement('truncate  releases');

        DB::statement('truncate  trucks');
        DB::statement('truncate  certificates');
        DB::statement('truncate  media');
        DB::statement('truncate  merchants');
        DB::statement('truncate  swabs');
        DB::statement('truncate  people');
        DB::statement('truncate  offices');
        DB::statement('truncate  specifications');
        DB::statement('truncate  permission_requests');
        DB::statement('truncate  change_requests');

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        DB::statement('delete from  department_user where user_id not in ' . $preservedUserIDS);
        DB::statement('delete from  model_has_roles where model_id not in ' . $preservedUserIDS);
        DB::statement('delete from  model_has_permissions where model_id not in ' . $preservedUserIDS);
        DB::statement('delete from  users where id not in ' . $preservedUserIDS);

        DB::statement('delete from  permissions
    where permissions.id not in (
      select permission_id as id from model_has_permissions
      UNION
      select permission_id as id from role_has_permissions
    )');

        DB::statement('delete from  roles
    where roles.id not in (
      select role_id as id from model_has_roles
      UNION
      select role_id as id from role_has_permissions
    )');

        dump('DONE');
    }
}
