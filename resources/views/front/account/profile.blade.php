@extends('front.layout.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">My Account</a></li>
                    <li class="breadcrumb-item">Settings</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-12">
                    @include('front\account\common\message')
                </div>
                <div class="col-md-3">
                    @include('front.account.common.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                        </div>                        
                        <form action="" name="profileForm" id="profileForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3">               
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value="{{$user->name}}" placeholder="Enter Your Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">            
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" value="{{$user->email}}" placeholder="Enter Your Email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">                                    
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" value="{{$user->phone}}" placeholder="Enter Your Phone" class="form-control">
                                    <p></p>
                                </div>                         

                                <div class="d-flex">
                                    <button type="submit" class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>

                    <div class="card mt-5">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                        </div>                        
                        <form action="" name="addressForm" id="addressForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">               
                                    <label for="name">First Name</label>
                                    <input type="text" name="first_name" id="first_name" value="{{(!empty($customerAddress->first_name)) ? $customerAddress->first_name : ''}}" placeholder="Enter Your First Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-3">               
                                    <label for="name">Last Name</label>
                                    <input type="text" name="last_name" id="last_name" value="{{(!empty($customerAddress->last_name)) ? $customerAddress->last_name : ''}}" placeholder="Enter Your Last Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-3">            
                                    <label for="email">Email</label>
                                    <input type="text" name="email" id="email" value="{{(!empty($customerAddress->email)) ? $customerAddress->email : ''}}" placeholder="Enter Your Email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="col-md-6 mb-3">                                    
                                    <label for="phone">Mobile</label>
                                    <input type="text" name="mobile" id="mobile" value="{{ !empty($customerAddress->mobile) ? $customerAddress->mobile : ''}}" placeholder="Enter Your Mobile" class="form-control">
                                    <p></p>
                                </div> 
                                <div class="mb-3">                                    
                                    <label for="country">Country</label>
                                    <select class="form-control" name="country" id="country">
                                        <option value="">Select</option>
                                        @foreach($countries as $country)
                                            <option {{(!empty($customerAddress->country_id) && $customerAddress->country_id == $country->id) ? 'selected' : ''}} value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                    <p></p>
                                </div>
                                
                                <div class="mb-3">                                    
                                    <label for="address">Address</label>
                                    <textarea name="address" id="address" cols="30" rows="10" class="form-control">{{(!empty($customerAddress->address)) ? $customerAddress->address : ''}}</textarea>
                                    <p></p>
                                </div> 

                                <div class="col-md-6 mb-3">                                    
                                    <label for="apartment">Apartment</label>
                                    <input type="text" name="apartment" id="apartment" value="{{!empty($customerAddress->apartment) ? $customerAddress->apartment : ''}}" placeholder="Enter Your Apartment" class="form-control">
                                    <p></p>
                                </div>
                                
                                <div class="col-md-6 mb-3">                                    
                                    <label for="city">City</label>
                                    <input type="text" name="city" id="city" value="{{!empty($customerAddress->city) ? $customerAddress->city : ''}}" placeholder="Enter Your City" class="form-control">
                                    <p></p>
                                </div>

                                <div class="col-md-6 mb-3">                                    
                                    <label for="state">State</label>
                                    <input type="text" name="state" id="state" value="{{!empty($customerAddress->state) ? $customerAddress->state : ''}}" placeholder="Enter Your State" class="form-control">
                                    <p></p>
                                </div>

                                <div class="col-md-6 mb-3">                                    
                                    <label for="zip">Zip</label>
                                    <input type="text" name="zip" id="zip" value="{{!empty($customerAddress->zip) ? $customerAddress->zip : ''}}" placeholder="Enter Your Zip" class="form-control">
                                    <p></p>
                                </div>

                                <div class="d-flex">
                                    <button type="submit" class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('customJs')
<script>
    $("#profileForm").submit(function(event){
        event.preventDefault();
        $.ajax({
            url: '{{route("account.updateProfile")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response)
            {
                if(response.status == true){
                    window.location.href="{{route('account.profile')}}";
                }else{
                    var errors = response.errors;
                    if(errors.name){
                        $('#profileForm #name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
                    }else{
                        $('#profileForm #name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                    if(errors.email){
                        $("#profileForm #email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email);
                    }else{
                        $("#profileForm #email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                    if(errors.phone){
                        $('#profileForm #phone').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.phone);
                    }else{
                        $('#profileForm #phone').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                }
                
            }
        });
    });

    $("#addressForm").submit(function(event){
        event.preventDefault();
        $.ajax({
            url: '{{route("account.updateAddress")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response)
            {
                if(response.status == true){
                    window.location.href="{{route('account.profile')}}";
                }else{
                    var errors = response.errors;
                    if(errors.first_name){
                        $('#addressForm #first_name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
                    }else{
                        $('#addressForm #first_name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                    if(errors.last_name){
                        $('#addressForm #last_name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
                    }else{
                        $('#addressForm #last_name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                    if(errors.email){
                        $("#addressForm #email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email);
                    }else{
                        $("#addressForm #email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                    if(errors.mobile){
                        $('#addressForm #mobile').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.mobile);
                    }else{
                        $('#addressForm #mobile').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.country){
                        $('#addressForm #country').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.country);
                    }else{
                        $('#addressForm #country').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.address){
                        $('#addressForm #address').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.address);
                    }else{
                        $('#addressForm #address').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.city){
                        $('#addressForm #city').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.city);
                    }else{
                        $('#addressForm #city').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.state){
                        $('#addressForm #state').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.state);
                    }else{
                        $('#addressForm #state').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(errors.zip){
                        $('#addressForm #zip').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.zip);
                    }else{
                        $('#addressForm #zip').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }zip          }
                
            }
        });
    });
</script>
@endsection