@extends('layouts.app')

@push('page_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Search User</div>

                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Select User</label>
                        <select id="user" name="user" class="form-control">
                            <option value="">Select User</option>
                        </select>
                    </div>
                    <div>
                        <center id="spinner" class='d-none'><i class="fa fa-spinner fa-spin text-center" style="font-size: 22px;"></i></center>
                        <div id="send-money-form"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script type="text/javascript">
    var usersListUrl = "{{ route('list-users') }}";
    var getUserDetail = "{{ url('user-details') }}";
</script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    $("#user").select2({
        tags: false,
        multiple: false,
        minimumInputLength: 2,
        minimumResultsForSearch: 10,
        ajax: {
            url: usersListUrl,
            dataType: "json",
            type: "GET",
            data: function (params) {

                var queryParameters = {
                    term: params.term
                }
                return queryParameters;
            }
        }
    });

    $('#user').on("change", function(e) { 
        var value = $(this).val();
        if(value){
            var actionUrl = getUserDetail+'/'+value;
            $.ajax({
                url: actionUrl,
                type : "GET",
                beforeSend: function() {
                    $('#send-money-form').html();
                    $('#spinner').removeClass('d-none');
                },
                success: function(response){
                    $('#spinner').addClass('d-none');                    
                    $('#send-money-form').append(response);
                },
                error: function(jqXhr){
                    $('#spinner').addClass('d-none');
                    $('#send-money-form').append('Someting went wrong');
                }
            })
        }   
    });

    $('body').on('click', '.submit-button', function(e){
        var form      = document.getElementById('transfer-money-form');
        var formData  = new FormData(form);
        formData.append('_token',$('meta[name="csrf-token"]').attr('content'));

        var button    = e.target.innerHTML;
        var URL       = e.target.form.getAttribute("action");
        var method    = e.target.form.getAttribute("method");
        e.target.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Please wait';
        e.target.disabled = true;
        $('.help-block').remove();
        $.ajax({
            url: URL,
            type : method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                e.target.innerHTML = button;
                e.target.disabled  = false;

                if(response.status == 'success'){
                    alert('Money send successfully');
                    location.reload();
                }else{
                    alert(response.message);
                }
            },
            error: function (jqXHR) {
                e.target.innerHTML = button;
                e.target.disabled  = false;
                if (jqXHR.status == 422) {
                    $.each(jqXHR.responseJSON.errors, function (index, value) {
                        $("#amount").after(
                        '<span class="help-block inline-error text-red-600"><strong>'+value+'</strong></span>'
                        );
                    });
                }else{
                    alert(jqXHR.responseJSON.message);
                }
            }
        })
    });

</script>
@endpush
