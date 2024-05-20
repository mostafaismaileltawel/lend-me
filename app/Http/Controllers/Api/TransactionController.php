<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function get_all_transactoin()
    {
        $user = Auth::user()->phone_number;
        $transaction = Transaction::where('from_phone_number', $user)->Orwhere('to_phone_number', $user)->orderBy('send_time', 'desc')->get();
        $numberof_confirm = Transaction::where(function ($query) use ($user) {
            $query->where('from_phone_number', $user)
                ->where('status', 'confirmed');
        })->orWhere(function ($query) use ($user) {

            $query->where('to_phone_number', $user)
                ->where('status', 'confirmed');
        })->count();
        $numberof_refused = Transaction::where(function ($query) use ($user) {
            $query->where('from_phone_number', $user)
                ->where('status', 'refused');
        })->orWhere(function ($query) use ($user) {

            $query->where('to_phone_number', $user)
                ->where('status', 'refused');
        })->count();
        return response()->json(['transactions' => $transaction, 'count of confirm' => $numberof_confirm, 'count of refuse' => $numberof_refused], 200);
    }
    public function get_transactoin_last_month()
    {
        $date = Carbon::today()->subDays(30)->format('Y-m-d');
        $user = Auth::user()->phone_number;
        $transaction = Transaction::where(function ($query) use ($user,$date) {
            $query->where('from_phone_number', $user)
                ->where('send_time', '>=',$date);
        })->orWhere(function ($query) use ($user,$date) {

            $query->where('to_phone_number',$user)
                ->where('send_time','>=',$date);
        })->orderBy('send_time','desc')->get();

        $numberof_confirm = Transaction::where(function ($query) use ($user,$date) {
            $query->where('from_phone_number', $user)->where('send_time','>=',$date)
                ->where('status', 'confirmed');
        })->orWhere(function ($query) use ($user,$date) {

            $query->where('to_phone_number', $user)->where('send_time','>=',$date)
                ->where('status', 'confirmed');
        })->count();
        $numberof_refused = Transaction::where(function ($query) use ($user,$date) {
            $query->where('from_phone_number', $user)->where('send_time','>=',$date)
                ->where('status', 'refused');
        })->orWhere(function ($query) use ($user,$date) {

            $query->where('to_phone_number', $user)->where('send_time','>=',$date)
                ->where('status', 'refused');
        })->count();


        return response()->json(['transactions' => $transaction, 'count of confirm' => $numberof_confirm, 'count of refuse' => $numberof_refused]);
    }
    public function delete_transactoin($id)
    {
        try {
            $transaction = Transaction::where('id', $id);

            $transaction->delete();
            return response()->json(['messages' => 'transaction deleted']);
        } catch (\Throwable $th) {
            return response()->json(['messages' => 'transaction not deleted']);
        }
    }
    public function delete_all_transactoin()
    {
        try {
            $user = Auth::user()->phone_number;
            $transaction = Transaction::where('from_phone_number', $user)->Orwhere('to_phone_number', $user)->orderBy('send_time', 'desc');
            $transaction->delete();
            return response()->json(['messages' => ' all transactions deleted']);} catch (\Throwable $th) {
            return response()->json(['messages' => ' all transaction not deleted']);
        }
    }
}
