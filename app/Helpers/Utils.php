<?php

function ezReturnErrorMessage($msg)
{
    return ['isError' => true, 'msg' => $msg];
}

function ezReturnSuccessMessage($msg, $obj = null)
{
    $success = ['success' => $msg];

    if ($obj) {
        $success['obj'] = $obj;
    }

    return $success;
}

// Format the date to make it compatible with Mysql date format
function toMysqlDate($dateString)
{
    $date = DateTime::createFromFormat('d-m-Y', $dateString);

    return date_format($date, 'Y-m-d');
}

// Format the date
function toDMYDate($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d', $dateString);

    return date_format($date, 'd-m-Y');
}

// Format the time
function toDMYTime($dateString)
{
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $dateString);

    return date_format($date, 'H:i:s d-m-Y');
}

// Get a custom view else use the defaul
function getCustomView($view, $name = null)
{
    $name = strtolower($name);

    if (view()->exists("{$view}_{$name}")) {
        return "{$view}_{$name}";
    }

    return $view;
}

function isElectron()
{
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    return str_contains($_SERVER['HTTP_USER_AGENT'], 'Electron/');
}

function printThrough($urls, $paper = 'A4', $args = '')
{
    if (isElectron()) {
        return [
            'urls' => $urls,
            'paper' => $paper,
        ];
    }

    $printerName = \App\Models\AppSetting::get($paper . '_printer_name');

    $urls = collect($urls)
        ->map(function ($url) {
            /* return escapeshellarg($url); */
            return '"' . addcslashes($url, '\\"') . '"';
        })
        ->implode(' ');

    $printCommand = "start C:\printhtml\PrintHtml.exe  -t 0.05 -b 0.05 -l 0.15 -r 0.15 $args  -a  $paper  -p \"$printerName\"  $urls";
    //$printCommand = 'start C:\printhtml\PrintHtml.exe  -t 0.1 -b 0.1 -l 0.11 -r 0.11  -a ' . $paper . ' -p "' . $printerName . '" ' . $urls;

    pclose(popen($printCommand, 'r'));

    return $printCommand;
}

function getFilterRule($ruleKey)
{
    $rule = null;

    $filterRules = collect(json_decode(request('filterRules')));

    $filterRules->each(function ($filterRule) use (&$rule, $ruleKey) {
        if ($filterRule->field == $ruleKey && isset($filterRule->value)) {
            $rule = $filterRule->value;
        }
    });

    return $rule;
}

function getMediaUrl($media)
{
    return str_after($media->getUrl(), config('app.url'));
}

function prependNone($collection, $col_name = 'name', $title = 'NONE')
{
    $null_item = new \stdClass;
    $null_item->{$col_name} = $title;
    $collection->prepend($null_item);

    return $collection;
}

function prettyPrint($json)
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = null;
    $json_length = strlen($json);

    for ($i = 0; $i < $json_length; $i++) {
        $char = $json[$i];
        $new_line_level = null;
        $post = '';
        if ($ends_line_level !== null) {
            $new_line_level = $ends_line_level;
            $ends_line_level = null;
        }
        if ($in_escape) {
            $in_escape = false;
        } elseif ($char === '"') {
            $in_quotes = !$in_quotes;
        } elseif (!$in_quotes) {
            switch ($char) {
                case '}':
                case ']':
                    $level--;
                    $ends_line_level = null;
                    $new_line_level = $level;
                    break;

                case '{':
                case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = ' ';
                    break;

                case ' ':
                case "\t":
                case "\n":
                case "\r":
                    $char = '';
                    $ends_line_level = $new_line_level;
                    $new_line_level = null;
                    break;
            }
        } elseif ($char === '\\') {
            $in_escape = true;
        }
        if ($new_line_level !== null) {
            $result .= "\n" . str_repeat("\t", $new_line_level);
        }
        $result .= $char . $post;
    }

    return $result;
}

function userHasDepartment($department_id)
{
    return request()->user_departments->filter(function ($item) use ($department_id) {
        return $item == $department_id;
    })->count() > 0;
}

function statusToSymbol($status)
{
    if ($status == 'Passed') {
        return '✓';
    }

    if ($status == 'Failed') {
        /* return '✗'; */
        return '☓';
    }

    return '?';
}

function toChekbox($status, $truthValue)
{
    if ($status == $truthValue) {
        return '<span class="bold large">☑</span>';
    }

    return '<span class="bold large">☐</span>';
}

function statusToKurdish($status)
{
    if ($status == 'Passed') {
        return 'شیاوە';
    } elseif ($status == 'Failed') {
        return 'نەشیاوە';
    }

    return 'لە چاوەڕوانیدایە';
}

function colorifyStatus($status)
{
    if ($status == 'Passed') {
        return '<span style="">' . $status . '</span>';
    }

    if ($status == 'Failed') {
        /* return '✗'; */
        return '<span style="color:red;">' . $status . '</span>';
    }

    return '?';
}

function shouldShowTotal()
{
    return filter_var(request('show_total'), FILTER_VALIDATE_BOOLEAN);
}

function getPaginationOptions()
{
    return ['show_total' => shouldShowTotal()];
}

function attachFakeTotal($collection, $pages = 100)
{
    if (shouldShowTotal()) {
        return $collection;
    }

    if (!is_array($collection)) {
        $collection = $collection->toArray();
    }

    return array_merge($collection, getFakeTotalAttachement($pages));
}

function getFakeTotalAttachement($pages = 100)
{
    return ['total' => (request('page') + $pages) * request('rows')];
}

function combobox($options = null, $otherDataOptions = '')
{
    if ($options == null) {
        $options = "{value:'',text:'_______'},{value:'Passed', text:'Passed'},{value:'Failed', text:'Failed'}";
    }
    $res = '
    data-options="
      required:true,
      valueField:\'value\',
      textField:\'text\',
      panelHeight:\'auto\',
      limitToList:true,
      data:[';
    $res .= $options;
    $res .= ']';

    if (!empty($otherDataOptions)) {
        $res .= ",$otherDataOptions";
    }

    $res .= '"';

    return $res;
}

function onChange($sample, $operation, $options = null)
{
    $res = 'data-options="onChange: function(newVal, oldVal) { ' . $operation . '(\'' . $sample . '\'); }';
    if ($options != null) {
        $res = $res . ', ' . $options;
    }

    return $res . '"';
    //return 'data-options="onChange: function(newVal, oldVal) { calculate(this); }"';
}

function da($q)
{
    dd($q->toArray());
}

function dSql($query)
{
    $sql = $query->toSql();

    foreach ($query->getBindings() as $key => $binding) {
        $regex = is_numeric($key)
            ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
            : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

        if ($binding === null) {
            $binding = 'null';
        } elseif (!is_int($binding) && !is_float($binding)) {
            $binding = DB::connection()->getPdo()->quote($binding);
        }

        $sql = preg_replace($regex, $binding, $sql, 1);
    }

    dd($sql);
}

function lan($supposedLanguage = null)
{
    $requestedLanguage = isset(request()->lan) ? request()->lan : 'kur';

    if ($supposedLanguage == null) {
        return $requestedLanguage;
    }

    return $supposedLanguage == $requestedLanguage;
}

function runningInBrowser()
{
    return isset($_SERVER['HTTP_USER_AGENT']);
}

function user()
{
    return auth()->user();
}
