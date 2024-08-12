<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrintController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if (!$request->filled('urls')) {
            return ezReturnErrorMessage('URL not set.');
        }

        $urls = $request->urls;
        $paper = optional($request)->paper;

        if (!$paper) {
            $paper = 'A4';
        }

        $args = optional($request)->args;

        $cmd = printThrough($urls, $paper, $args);

        return ezReturnSuccessMessage('Print command sent.', $cmd);
    }
}
