<?php

namespace App\Console\Commands;

use App\Category;
use App\Imports\GenericCollectionImport;
use App\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UnifyProductCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lox:unify_product_categories {--no-debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unify product categories according to product ids grabed from a xls file';

    private $debug = true;

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

        Auth::loginUsingId(1);

        $productCategoryRows = $this->getSiteProductInfoFromExcelFile();

        $productCategoryRows->each(function ($productCategoryRow) {
            $this->applyCategoryModifications($productCategoryRow);
        });
    }

    private function applyCategoryModifications($productCategoryRow)
    {
        $productId = $productCategoryRow['ProductId'];
        if (empty($productId)) {
            $this->warn('Product Id is empty: ' . $productCategoryRow);

            return;
        }

        $product = Product::find($productId);
        if (empty($product)) {
            $this->warn('Product not found #: ' . $productId);

            return;
        }

        $categoryName = $productCategoryRow['CategoryName'];

        if (str_contains($categoryName, '،')) {
            $this->warn('CategoryName contains comma: ' . $categoryName);

            return;
        }

        if (empty($categoryName)) {
            $this->warn('CategoryName is empty: row with productId#' . $productId);

            return;
        }

        $category = Category::where('name', $categoryName)->first();
        if (empty($category)) {
            $this->info('Creating category: ' . $categoryName);
            $category = $this->createNonExistingCategory($productCategoryRow);
        }

        $prevCatId = $product->category_id;
        $product->category_id = $category->id;

        if ($prevCatId == $product->category_id) {
            $this->warn('Skipping as same category_id');

            return;
        }

        if (!$this->debug) {
            $product->save();
        }

        $this->info("Product#{$product->id}: category_id changed from $prevCatId to {$product->category_id}");
    }

    private function createNonExistingCategory($productCategoryRow)
    {
        $category = new Category;
        $category->name = $productCategoryRow['CategoryName'];

        if (!$this->debug) {
            $category->save();
        }

        return $category;
    }

    private function getSiteProductInfoFromExcelFile()
    {
        $siteProductIdColIndex = $this->getSiteProductColIndex();

        $file = 'categoryInfo.xlsx';

        $collection = Excel::toCollection(new GenericCollectionImport(), $file);

        $productCategoryRowsWithHeader = $collection[0];

        $productCategotyRows = $productCategoryRowsWithHeader->slice(1);

        $productCategoryRows = $productCategotyRows->map(function ($productCategoryRow) use ($siteProductIdColIndex) {
            return [
                'ProductId' => $productCategoryRow[$siteProductIdColIndex],
                'CategoryName' => $productCategoryRow[5],
            ];
        });

        return $productCategoryRows;
    }

    private function getSiteProductColIndex()
    {

        $siteProductColIndexes = [
            'Parwezxan' => 0,
            'Kele' => 1,
            'Pshta' => 2,
        ];

        $siteName = strtok(config('app.site_name'), ' ');

        $siteProductIdColIndex = $siteProductColIndexes[$siteName];

        $this->info('');
        $this->info('Setting product categories for ' . $siteName);
        $this->info('siteName: ' . $siteName);
        $this->info('siteProductIdColIndex: ' . $siteProductIdColIndex);

        if (!$this->confirm('Do you wish to continue?')) {
            dd('Aborted!');
        }

        return $siteProductIdColIndex;
    }
}
