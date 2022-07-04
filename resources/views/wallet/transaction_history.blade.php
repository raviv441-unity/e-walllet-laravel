@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Transaction History</div>

                <div class="card-body">                    
                    @forelse($transactionHistories as $history)
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label><b>Transaction id:</b> {{ $history->transaction_id}}</label><br>
                                    <label><b>From:</b> {{ $history->sender_id == Auth::user()->id ? $history->receiver->name : $history->sender->name }}</label><br>
                                    <label><b>Amount:</b> {{ number_format($history->amount,2) }}</label><br>
                                    <label><b>Type:</b>{{ $history->sender_id == Auth::user()->id ? 'Debit':'Credit'}}</label><br>
                                    <label><b>At:</b> {{ date('d/m/Y H:i:s',strtotime($history->created_at)) }} </label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="col-lg-12">
                           No Transaction Found
                        </div>
                    @endforelse   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

