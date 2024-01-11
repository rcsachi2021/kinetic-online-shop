@extends('front.layout.app')
@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item">{{$page->name}}</li>
                </ol>
            </div>
        </div>
    </section>
    @if($page->slug === 'contact-us')
    <section class=" section-10">
        <div class="container">
            <div class="section-title mt-5 ">
               <h1 class="my-3">{{$page->name}}</h1>
            </div>   
        </div>
    </section>

    <section>
        <div class="container">    
            <div class="row">
                @include('front.account.common.message')
            </div>      
            <div class="row">
              {!!$page->content!!}

                <div class="col-md-6">
                    <form class="shake" role="form" method="post" id="contactForm" name="contactForm">
                        <div class="mb-3">
                            <label class="mb-2" for="name">Name</label>
                            <input class="form-control" id="name" type="text" name="name">
                            <p></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="mb-2" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email">
                            <p class="help-block with-errors"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="mb-2">Subject</label>
                            <input class="form-control" id="subject" type="text" name="subject">
                            <p class="help-block with-errors"></p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="mb-2">Message</label>
                            <textarea class="form-control" rows="3" id="message" name="message"></textarea>
                            <p class="help-block with-errors"></p>
                        </div>
                      
                        <div class="form-submit">
                            <button class="btn btn-dark" type="submit" id="form-submit"><i class="material-icons mdi mdi-message-outline"></i> Send Message</button>
                            <div id="msgSubmit" class="h3 text-center hidden"></div>
                            <div class="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @else
    <section class=" section-10">
        <div class="container">
            <h1 class="my-3">{{$page->name}}</h1>
            {!!$page->content!!}
        </div>
    </section>
    @endif
</main>
@endsection

@section('customJs')
<script>
$('#contactForm').submit(function(event){
    $('button[type="submit"]').attr('disabled', true)
    event.preventDefault();
    $.ajax({
        type: 'post',
        url: '{{ route("front.sendContactEmail") }}',
        data: $(this).serializeArray(),
        dataType: 'json',
        success: function(response){
            if(response.status == true){
                window.location.href = '{{route("front.page", "contact-us")}}'
            }else{
                var errors = response.errors
                if(errors.name){
                    $("#name").addClass('is_invalid').siblings('p').addClass('invalid-feedback').html(errors.name).show()
                }else{
                    $("#name").removeClass('is_invalid').siblings('p').removeClass('invalid-feedback').html('')
                }
                if(errors.email){
                    $("#email").addClass('is_invalid').siblings('p').addClass('invalid-feedback').html(errors.email).show()
                }else{
                    $("#email").removeClass('is_invalid').siblings('p').removeClass('invalid-feedback').html('')
                }

                if(errors.subject){
                    $("#subject").addClass('is_invalid').siblings('p').addClass('invalid-feedback').html(errors.subject).show()
                }else{
                    $("#subject").removeClass('is_invalid').siblings('p').removeClass('invalid-feedback').html('')
                }
                
            }
        }
    })
})

</script>


@endsection