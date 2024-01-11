@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Brand</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{route('brands.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
                    <form action="" method="post" name="brandform" id="brandform">
					<div class="container-fluid">
						<div class="card">
							<div class="card-body">								
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Name</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Name">	
                                            <p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="email">Slug</label>
											<input type="text" name="slug" id="slug" class="form-control" placeholder="Slug">	
                                            <p></p>
										</div>
									</div>
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="email">Status</label>
											<select name="status" id="status" class="form-control">
                                                <option value="1">Active</option>
                                                <option value="0">Block</option>
                                            </select>
                                            <p></p>	
										</div>
									</div>									
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button class="btn btn-primary">Create</button>
							<a href="{{route('brands.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
						</div>
					</div>
                    </form>
					<!-- /.card -->
				</section>
				<!-- /.content -->
@endsection

@section('customJs')
<script>
    $("#brandform").submit(function(){
        event.preventDefault();
        var element = $(this);
        $('button[type="submit"]').prop('disabled', true);
        $.ajax({
            url: "{{route('brands.store')}}",
            data: element.serializeArray(),
            type: 'POST',
            dataType: 'json',
            success: function(response){
                $('button[type="submit"]').prop('disabled', false);
                if(response.status == true){
                    $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    $("#status").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    window.location.href = "{{route('brands.index')}}";
                }else{
                    if(response.errors.name){
                        $("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(response.errors.name);
                    }else{
                        $("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(response.errors.slug){
                        $("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(response.errors.slug);
                    }else{
                        $("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }

                    if(response.errors.status){
                        $("#status").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(response.errors.status);
                    }else{
                        $("#status").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                }
            }
        });
    });


    $("#name").change(function(){
        var element = $(this);
        $.ajax({
            url: '{{route("get.slug")}}',
            data: {'title': element.val()},
            type: 'GET',
            dataType: 'json',
            success: function(response)
            {
                $("#slug").val(response.slug);
            }
        });
    });
</script>
@endsection