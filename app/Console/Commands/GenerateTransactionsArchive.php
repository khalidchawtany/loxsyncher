<?php

namespace App\Console\Commands;

use App\Services\TransactionArchiveGenerator;
use Illuminate\Console\Command;

class GenerateTransactionsArchive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:transactions_archive {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates transaction archive for a given $data';

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
        $date = $this->argument('date');
        if (empty($date)) {
            $date = now()->format('Y-m-d');
        }

        (new TransactionArchiveGenerator($date))->generate();
    }
}
