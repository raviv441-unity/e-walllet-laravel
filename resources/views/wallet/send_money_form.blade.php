<div class="jumbotron">
  <p>Name: {{ $user->name }}</p>
  <p>Email: {{ $user->email }}</p>  
  <form class="form" method="post" id="transfer-money-form" action="{{ route('transfer-money',base64_encode($user->id)) }}">    
    @csrf
    <div class="form-group">
      <label for="amount">Enter Amount:</label>
      <input type="number" name="amount" class="form-control" placeholder="Enter amount" id="amount">
    </div>
    <button type="button" class="btn btn-primary submit-button">Send</button>
  </form>
</div>