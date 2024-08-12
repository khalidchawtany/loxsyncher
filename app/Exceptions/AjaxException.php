<?php

namespace App\Exceptions;

class AjaxException extends \Exception
{
    /**
    /**
     * Report the exception.
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // Don't report as we don't want this reported
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response()->json(ezReturnErrorMessage($this->getMessage()));
    }
}
