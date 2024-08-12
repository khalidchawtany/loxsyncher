<?php

namespace App\Console\Commands;

use App\Imports\GenericCollectionImport;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UnifyProductNamesById extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lox:unify_product_names {--no-debug} {--revert}';

    private $OP_CODE = 'SU202308';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unify names of products according to product ids grabed from a xls file';

    private $debug = true;

    private $revert = false;

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
        $this->debug = !$this->option('no-debug');
        $this->revert = $this->option('revert');
        Auth::loginUsingId(1);

        if ($this->revert) {
            $this->revert();

            return;
        }

        $siteProductInfoArray = $this->getSiteProductInfo();

        $siteProductInfoArray->each(function ($productInfoRow) {
            $this->applyProductModifications($productInfoRow);
        });
    }

    private function revert()
    {
        $products = Product::whereRaw('note like "' . $this->OP_CODE . '%"')->get();
        $products->each(function ($product) {
            $this->revertProduct($product);
        });
    }

    private function revertProduct($product)
    {
        $prevProductInfo = $product->extra[$this->OP_CODE];
        if (
            isset($prevProductInfo['create'])
            && $prevProductInfo['create'] == null
        ) {

            if ($this->debug) {
                dump('Dropping: ', $product->toArray());

                return;
            }

            $product->delete();

            return;
        }

        // this product was not modified by us
        if (!isset($prevProductInfo['update'])) {
            return;
        }

        $prevProductInfo = $prevProductInfo['update'];

        $product->kurdish_name = $prevProductInfo['kurdish_name'];
        // $product->name = $prevProductInfo['name'];
        $product->customs_name = $prevProductInfo['customs_name'];
        $product->arabic_name = $prevProductInfo['arabic_name'];
        $product->alternative_names = $prevProductInfo['alternative_names'];
        $product->disabled = $prevProductInfo['disabled'];
        $product->note = $prevProductInfo['note'];

        $product->extra = json_encode(array_except($product->extra->toArray(), $this->OP_CODE));

        if ($this->debug) {
            dump('Reverting: ', $product->toArray());

            return;
        }
        $product->save();
    }

    private function createNonExistingProduct($productInfoRow)
    {
        $productInfo = [
            'kurdish_name' => $productInfoRow['KurdishName'],
            // 'name' => $productInfoRow['EnglishName'],
            'customs_name' => $productInfoRow['CustomsName'],
            'arabic_name' => $productInfoRow['ArabicName'],
            'alternative_names' => $productInfoRow['AlternativeName'],
            'disabled' => $productInfoRow['Block'] == 'yes',
            'note' => $this->OP_CODE,
            'extra' => json_encode([$this->OP_CODE => ['create' => null]]),
            'user_id' => 1,
        ];

        if ($this->debug) {
            dump('Creating: ', $productInfo);

            return;
        }

        Product::create($productInfo);
    }

    private function storeUpdateInExtraField($product)
    {

        $prevProductInfo = [
            'kurdish_name' => $product->kurdish_name,
            // 'name' => $product->name,
            'customs_name' => $product->customs_name,
            'arabic_name' => $product->arabic_name,
            'alternative_names' => $product->alternative_names,
            'note' => $product->note,
            'disabled' => $product->disabled,
        ];

        if (empty($product->extra)) {
            $product->extra($prevProductInfo);
        } else {
            $product->extra = json_encode(array_merge($product->extra->toArray(), [
                $this->OP_CODE => [
                    'update' => $prevProductInfo,
                ],
            ]));
        }
    }

    private function updateExistingProduct($productInfoRow, $product)
    {
        if (Str::startsWith($product->note, $this->OP_CODE)) {
            return;
        }

        $this->storeUpdateInExtraField($product);

        $product->kurdish_name = $productInfoRow['KurdishName'];
        // $product->name = $productInfoRow['EnglishName'];
        $product->customs_name = $productInfoRow['CustomsName'];
        $product->arabic_name = $productInfoRow['ArabicName'];
        $product->alternative_names = $productInfoRow['AlternativeName'];

        if ($productInfoRow['Block'] == 'yes') {
            $product->disabled = 1;
        }

        $product->note = $this->OP_CODE . $product->note;

        if ($this->debug) {
            dump('Updatting: ', $product->toArray());

            return;
        }
        $product->save();
    }

    private function applyProductModifications($productInfoRow)
    {
        $productId = $productInfoRow['ProductId'];

        if (empty($productId)) {
            $this->createNonExistingProduct($productInfoRow);

            return;
        }

        $product = Product::find($productId);
        if (empty($product)) {
            $this->createNonExistingProduct($productInfoRow);

            return;
        }

        $this->updateExistingProduct($productInfoRow, $product);
    }

    private function getSiteProductInfo()
    {
        $siteProductColIndex = $this->getSiteProductColIndex();
        $file = 'productInfo.xlsx';
        $collection = Excel::toCollection(new GenericCollectionImport(), $file);
        $productInfoRowsWithHeader = $collection[0];
        $productInfoRows = $productInfoRowsWithHeader->slice(1);
        $siteProductInfoArray = $productInfoRows->map(function ($productInfoRow) use ($siteProductColIndex) {
            return [
                'ProductId' => $productInfoRow[$siteProductColIndex],
                'KurdishName' => $productInfoRow[4],
                'EnglishName' => $productInfoRow[5],
                'CustomsName' => $productInfoRow[6],
                'ArabicName' => $productInfoRow[7],
                'AlternativeName' => $productInfoRow[8],
                'Block' => $productInfoRow[9],
            ];
        });

        return $siteProductInfoArray;
    }

    private function getSiteProductColIndex()
    {

        $siteProductColIndexes = [
            'Parwezxan' => 0,
            'Kele' => 1,
            'Pshta' => 2,
        ];

        $siteName = strtok(config('app.site_name'), ' ');

        $siteProductColIndex = $siteProductColIndexes[$siteName];

        dump('siteName: ' . $siteName);
        dump('siteProductColIndex: ' . $siteProductColIndex);

        if (!$this->confirm('Do you wish to continue?')) {
            dd('Aborted!');
        }

        return $siteProductColIndex;
    }
}
