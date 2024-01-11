@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Edit Sub Category</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="subcategory.html" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
                    <form action="" name="subCategoryForm" id="subCategoryForm">
					<div class="container-fluid">
						<div class="card">
							<div class="card-body">								
								<div class="row">
                                    <div class="col-md-12">
										<div class="mb-3">
											<label for="name">Category</label>
											<select name="category_id" id="category_id" class="form-control">
                                                <option value="">Select a category</option>
												@if($categories->isNotEmpty())
                                                @foreach($categories as $category)
                                                <option value="{{$category->id}}" {{ ($subCategory->category_id == $category->id) ? 'selected' : '' }}>{{$category->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
											<p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Name</label>
											<input type="text" name="name" id="name" value="{{$subCategory->name}}" class="form-control" placeholder="Name">	
											<p></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="email">Slug</label>
											<input type="text" name="slug" id="slug" value="{{$subCategory->slug}}" class="form-control" placeholder="Slug">	
											<p></p>
										</div>
									</div>	
                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="email">Status</label>
											<select name="status" id="status" class="form-control">
                                                <option value="1" {{ ($subCategory->status == 1) ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ ($subCategory->status == 0) ? 'selected' : '' }}>Block</option>
                                            </select>	
											<p></p>
										</div>
									</div>  
									<div class="col-md-6">
									<div class="mb-3">
                                                <label for="status">Show on home</label>
                                                <select name="showhome" id="showhome" class="form-control">
                                                    <option value="Yes" {{ ($subCategory->showhome == 'Yes') ? 'selected': ''}}>Yes</option>
                                                    <option value="No" {{ ($subCategory->showhome == 'No') ? 'selected' : ''}}>No</option>
                                                </select>
                                    </div>
									</div>
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Update</button>
							<a href="{{route('sub-categories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
						</div>
					</div>
                    </form>
					<!-- /.card -->
				</section>
				<!-- /.content -->
@endsection
@section('customJs')
<script>

    $("#subCategoryForm").submit(function(event){
        event.preventDefault();
		$('button[type="submit"]').prop('disabled', true);
        var element = $(this);
        $.ajax({
            url: "{{route('sub-categories.update', $subCategory->id)}}",
            data: element.serializeArray(),
            type: 'put',
            dataType: 'json',
            success: function(response)
            {
				$('button[type="submit"]').prop('disabled', false);
				if(response.status == true)
				{					
					
					$("#category_id").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
					$("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
					$("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
					$("#status").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
					window.location.href = "{{route('sub-categories.index')}}";
				}
				else{
					errors = response.errors;
					if(errors.category_id)
					{
						$("#category_id").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(response.category_id);
					}else{
						$("#category_id").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
					}
					if(errors.name){
						$("#name").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(response.errors.name);
					}else{
						$("#name").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
					}
					if(errors.slug){
						$("#slug").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.slug);
					}else{
						$("#slug").removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
					}
					if(errors.status)
					{
						$("#status").addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.status);
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
            url: "{{route('get.slug')}}",
            type: 'GET',
            dataType: 'json',
            data: {'title': element.val()},
            success: function(response)
            {
                if(response.status == true){
                    $("#slug").val(response.slug);
                }                
            }
        });
    });
</script>
@endsection