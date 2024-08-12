<?php

namespace App\Console\Commands;

use App\Models\ReceivedSample;
use Illuminate\Console\Command;
use App\Models\ProductCheckType;
use App\Models\Batch;

class MoveOutReceivedAtAndByToReceivedSamples extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MoveOutReceivedAtAndByToReceivedSamples';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy received_by and received_at to received_samples table';

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
        $productCheckTypes = ProductCheckType::query()
            ->select(['product_check_type.product_id', 'check_types.category'])
            ->join('check_types', 'check_types.id', 'product_check_type.check_type_id')
            ->get();

        $productLabs = [];

        foreach ($productCheckTypes as $productCheckType) {
            $productId = $productCheckType['product_id'];
            $category = $productCheckType['category'];
            // is array has category continue
            if (isset($productLabs[$productId]) && in_array($category, $productLabs[$productId])) {
                continue;
            }
            $productLabs[$productId][] = $category;
        }

        $labs = [
            'Chemistry',
            'Microbiology',
            'Physical',
            'Construction',
            'Fuel',
        ];
        $batches = Batch::query()
            ->select(['id', 'received_on', 'received_by', 'batch_product_id'])
            ->whereNotNull('received_on')
            ->get();

        $batches->chunk(1000)->each(function ($batches) use (&$labs, &$productLabs) {
            $data = [];
            $batches->each(function ($batch) use (&$labs, &$data, &$productLabs) {
                foreach ($labs as $lab) {
                    if (!in_array($lab, $productLabs[$batch->batch_product_id])) {
                        continue;
                    }

                    $data[] = [
                        'lab' => $lab,
                        'received_on' => $batch->received_on,
                        'received_by' => $batch->received_by,
                        'batch_id' => $batch->id,
                    ];
                }
            });

            ReceivedSample::insert($data);
        });
        $this->info('The command was successful!');
    }
}
