<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Support\ServiceProvider;

class JsonJPaginatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerMacro();
    }

    protected function registerMacro()
    {
        $macro = function (bool $hideTotalPageCount = null, int $maxResults = null, int $defaultSize = null) {

            if ($hideTotalPageCount === null) {
                $hideTotalPageCount = request()->has('hide_total_page_count')
                    && request('hide_total_page_count') === 'true';
            }

            if ($hideTotalPageCount) {
                config(['json-api-paginate.use_simple_pagination' => true]);
            }

            $p = $this->jsonPaginate($maxResults, $defaultSize);

            $res = [
                'current_page' => $p->currentPage(),
                'rows' => $p->items(),
                'first_page_url' => $p->url(1),
                'from' => $p->firstItem(1),
                'next_page_url' => $p->nextPageUrl(),
                'path' => $p->path(),
                'per_page' => $p->perPage(),
                'prev_page_url' => $p->previousPageUrl(),
                'to' => $p->lastItem(),
                'paginator' => $p,
            ];

            if ($hideTotalPageCount) {
                $res['total'] = ($p->currentPage() + 200) * $p->perPage(); // attach a fake total to response so that users can navigate using jeasyui
            }

            if (!$hideTotalPageCount) {
                $res = array_merge($res, [
                    'last_page' => $p->lastPage(1),
                    'last_page_url' => $p->url($p->lastPage()),
                    'total' => $p->total(),
                ]);
            }

            return $res;
        };

        EloquentBuilder::macro('jsonJPaginate', $macro);
        BaseBuilder::macro('jsonJPaginate', $macro);
        BelongsToMany::macro('jsonJPaginate', $macro);
        HasManyThrough::macro('jsonJPaginate', $macro);
    }
}
