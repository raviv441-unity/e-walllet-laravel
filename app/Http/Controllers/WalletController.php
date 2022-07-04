<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TransferMoneyRequest;
use App\Models\User;
use App\Models\TransactionHistory;
use Auth, DB;

class WalletController extends Controller
{
    /**
     * Display a listing of the users for transfer money.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendMoney()
    {
        return view('wallet.send_money');
    }

    /**
     * Display a listing of the users for transfer money.
     *
     * @return \Illuminate\Http\Response
     */
    public function transactionHistory()
    {
        $transactionHistories = TransactionHistory::with('sender','receiver')->where(function($query){
            $query->where('sender_id',Auth::user()->id)->orWhere('receiver_id',Auth::user()->id);
        })->orderBy('id','desc')->get();
        return view('wallet.transaction_history', compact('transactionHistories'));
    }


    /**
     * Display a listing of the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response JSON
     */
    public function listUsers(Request $request)
    {
        $query = User::selectRaw('`id`, CONCAT(`name`," (",`email`,")") as text');

        if ($request->term) {
            $query->where('name', 'like', '%' . $request->term . '%');
            $query->where('email', 'like', '%' . $request->term . '%');
        }

        $users     = $query->orderBy('name', 'ASC')->get();
        $data['results'] = $users;

        return $data;
    }

    /**
     * Display a listing of the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response JSON
     */
    public function userDetail($id)
    {
        $user = User::select('id','name','email')->whereId($id)->firstOrFail();
        return view('wallet.send_money_form', compact('user'))->render();
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function transferMoney(TransferMoneyRequest $request, $id)
    {   
        DB::beginTransaction();
        try{    
            $id = base64_decode($id);
            $user = Auth::user();
            $receiver = User::whereId($id)->firstOrFail();

            if($receiver){

                TransactionHistory::create([
                    'sender_id'   => $user->id,
                    'receiver_id' => $id,
                    'amount'      => $request->amount
                ]);

                $userRemainingWalletAmount = ($user->wallet_amount - $request->amount);
                $receiverRemainingWalletAmount = ($receiver->wallet_amount + $request->amount);

                $user->wallet_amount = $userRemainingWalletAmount;
                $user->save();

                $receiver->wallet_amount = $receiverRemainingWalletAmount;
                $receiver->save();

                DB::commit();

                return response()->json(['status' => 'success', 'message' => 'Money sent successfully']);
            }else{

                return response()->json(['status' => 'error', 'message' => 'Something went wrong']);
            }

        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['status' => 'error', 'message' => $e->getMessage(),'line'=> $e->getLine()]);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
