@extends('front.layout.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">Register</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            <div class="login-form">    
                <form action="" method="post" id="registerform" name="registerform">
                    <h4 class="modal-title">Register Now</h4>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name" id="name" name="name">
                        <p class="error"></p>
                    </div>
                    
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Email" id="email" name="email">
                        <p class="error"></p>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Phone" id="phone" name="phone">
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Password" id="password" name="password">
                        <p class="error"></p>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" placeholder="Confirm Password" id="password_confirmation" name="password_confirmation">
                    </div>
                    <div class="form-group small">
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div> 
                    <button type="submit" class="btn btn-dark btn-block btn-lg" value="Register">Register</button>
                </form>			
                <div class="text-center small">Already have an account? <a href="{{route('account.login')}}">Login Now</a></div>
            </div>
        </div>
    </section>
@endsection
@section('customJs')
    <script>
        $("#registerform").submit(function(event){
            event.preventDefault();
            $('button[type="submit"]').prop('disabled', true);
            $.ajax({
                url: "{{route('account.processRegister')}}",
                type: 'post',
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response){
                    $('button[type="submit"]').prop('disabled', false);
                    if(response.status == true)
                    {
                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        $("#password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');

                        window.location.href="{{route('account.login')}}";
                    }
                    if(response.status == false){
                        var errors = response.errors;
                        if(errors.name){
                            $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name);
                        }else{
                            $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        }

                        if(errors.email){
                            $("#email").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email);
                        }else{
                            $("#email").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        }

                        if(errors.password){
                            $("#password").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.password);
                        }else{
                            $("#password").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html(errors.password);
                        }
                    }
                },
                error: function(){
                    alert('Something went wrong please try again');
                }
            });
        });
    </script>
@endsection