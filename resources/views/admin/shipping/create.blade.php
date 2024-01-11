@extends('admin.layouts.app')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Shipping Management</h1>
							</div>
							<div class="col-sm-6 text-right">
								<!-- <a href="{{route('categories.index')}}" class="btn btn-primary">Back</a> -->
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        @include('admin.messages')
                        <form action="" method="post" name="shipping-form" id="shipping-form">
                            <div class="card">
                                <div class="card-body">								
                                    <div class="row">
                                        <div class="col-md-4">                                            
                                                <select name="country" id="country" class="form-control">
                                                    <option value="">Select a Country</option>
                                                    @if($countries->isNotEmpty())
                                                        @foreach($countries as $country)
                                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                                        @endforeach
                                                        <option value="rest_of_world">Rest of the world</option>
                                                    @endif
                                                </select>	
                                                <p></p>                                            
                                        </div>
                                        <div class="col-md-4">                                            
                                                
                                                <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount">	
                                                <p></p>                                   
                                        </div>
                                        <div class="col-md-4">
                                           
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            
                                        </div>
                                        
                                        							
                                    </div>
                                </div>							
                            </div>
                        
                           
                        </form>
                        <div class="card">
                                <div class="card-body">								
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-striped">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Delete</th>
                                                </tr>
                                                @if($shippingCharges->isNotEmpty())
                                                @foreach($shippingCharges as $shippingCharge)
                                                <tr>
                                                    <th>{{$shippingCharge->id}}</th>
                                                    <th>{{($shippingCharge->country_id == 'rest_of_world') ? 'Rest of the world' : $shippingCharge->name}}</th>
                                                    <th>${{$shippingCharge->amount}}</th>
                                                    <th>
                                                        <a href="{{route('shipping.edit',$shippingCharge->id)}}" class="btn btn-primary btn-sm">Edit</a>
                                                    <a href="javascript:void(0);" onclick="deleteShipping('{{$shippingCharge->id}}')" class="btn btn-danger btn-sm">Delete</a>
                                                </th>
                                                </tr>
                                                @endforeach
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                    </div>
					</div>
					<!-- /.card -->
                    
				</section>
				<!-- /.content -->
@endsection
@section('customJs')
<script>
    $("#shipping-form").submit(function(event){
        event.preventDefault();
        $('button[type="submit"]').prop('disabled', true);
        var element = $(this);
        $.ajax({
            url:"{{ Route('shipping.store') }}",
            data: element.serializeArray(),
            type: 'POST',
            dataType: 'json',
            success: function(response){
                $("button[type='submit']").prop('disabled', false);
                if(response.status === true)
                {
                    window.location.href = '{{route("shipping.create")}}';
                    $("#country").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');    
                    $("#amount").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');                  
                }else{
                    var errors = response.errors;
                    if(errors.country)
                        {
                        $("#country").addClass('is-invalid')
                        .siblings('p')
                        .addClass('invalid-feedback')
                        .html(errors.country);                        
                    }else{
                        $("#country").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');
                        }
                    if(errors.amount)
                    {
                        $("#amount").addClass('is-invalid')
                        .siblings('p')
                        .addClass('invalid-feedback')
                        .html(errors.amount);
                    }else{
                        $("#amount").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback')
                        .html('');
                    }
                        
                    }                
            },
            error: function(jqXHR, exception){
                console.log("Something went wrong");
            }
        });
    });

    function deleteShipping(id)
    {
        if(confirm('Are you sure do you want to delete this record?')){
            var url = "{{route('shipping.delete','id')}}";
            var newUrl = url.replace('id', id);
            $.ajax({
                url: newUrl,
                type: 'get',
                dataType: 'json',
                success: function(response){
                    if(response.status == true){
                        window.location.href = "{{route('shipping.create')}}";
                    }
                }
            });
        }
    }
    
</script>
@endsection