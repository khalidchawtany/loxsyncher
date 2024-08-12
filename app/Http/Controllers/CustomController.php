<?php

namespace App\Http\Controllers;

use App\Exceptions\AjaxException;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class CustomController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_custom', ['only' => ['index', 'list']]);
        $this->middleware('permission:receive_transaction', [
            'only' => ['receiveTransaction', 'showReceiveTransactionDialog'],
        ]);
    }

    public function index()
    {
        return view('customs.index');
    }

    public function list()
    {
        $customs = JQueryBuilder::for(Transaction::class)
            ->join('products', 'products.id', 'transactions.product_id')
            ->allowedFilters('id', 'porducts.kurdish_name', 'date_time', 'received_on')
            ->whereNotNull('transactions.received_on')
            ->selectRaw('
          transactions.id,
          transactions.date_time,
          transactions.received_on,
          products.kurdish_name as kurdish_name
          ')
            ->jsonJPaginate();

        return $customs;
    }

    public function showReceiveTransactionDialog()
    {
        return view('customs.receive_transaction_dialog');
    }

    public function receiveTransaction(Request $request)
    {
        $qrcode = $request->qrcode;

        $qrcodeParts = explode('.', $qrcode);

        $transactionId = $qrcodeParts[0];

        $transactionCode = $qrcodeParts[1];

        $transaction = Transaction::findOrFail($transactionId);

        throw_if($transaction->code != $transactionCode, new AjaxException('Transaction QrCode is faulty!'));

        throw_if(!empty($transaction->received_on), new AjaxException('Transaction already received!'));

        $transaction->received_on = now();

        $transaction->save();

        return ezReturnSuccessMessage(
            'Transaction Received',
            [
                'transaction_id' => $transactionId,
                'transaction_date' => \Carbon\Carbon::parse($transaction->date_time)->format('d/m/Y'),
                'product_name' => $transaction->product->kurdish_name,
            ]
        );
    }
}
