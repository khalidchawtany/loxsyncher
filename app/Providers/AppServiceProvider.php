<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Console\CliDumper;
use Illuminate\Foundation\Http\HtmlDumper;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::macro('defaultSelectAll', function () {
            if (is_null($this->getQuery()->columns)) {
                $this->select($this->getQuery()->from . '.*');
            }

            return $this;
        });

        Builder::macro('addSubSelect', function ($column, $query) {
            $this->defaultSelectAll();

            return $this->selectSub($query->limit(1)->getQuery(), $column);
        });

        HtmlDumper::dontIncludeSource();

        CliDumper::dontIncludeSource();

        Validator::excludeUnvalidatedArrayKeys();

        Model::shouldBeStrict();
        Model::preventSilentlyDiscardingAttributes(false);
        Model::preventAccessingMissingAttributes(false);

        // Model::unguard();

        // Relation::enforceMorphMap([
        Relation::morphMap([
            'user' => User::class,
        ]);

        Date::use(CarbonImmutable::class);

    }
}
