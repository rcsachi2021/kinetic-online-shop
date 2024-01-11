@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Edit Product</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{route('products.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
                    <form action="" name="productForm" id="productForm">
					<!-- Default box -->
					<div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-body">								
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="title">Title</label>
                                                    <input type="text" name="title" id="title" value="{{$product->title}}" class="form-control" placeholder="Title">	
                                                    <p class="error"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="slug">Slug</label>
                                                    <input type="text" readonly name="slug" id="slug" value="{{$product->slug}}" class="form-control" placeholder="Slug">	
                                                    <p class="error"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="description">Short Description</label>
                                                    <textarea name="short_description" id="short_description" cols="30" rows="10" class="summernote" placeholder="Short Description">{{$product->short_description}}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="description">Description</label>
                                                    <textarea name="description" id="description" cols="30" rows="10" class="summernote" placeholder="Description">{{$product->description}}</textarea>
                                                </div>
                                            </div>  
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="description">Shipping and Returns</label>
                                                    <textarea name="shipping_returns" id="shipping_returns" cols="30" rows="10" class="summernote" placeholder="shipping Returns">{{$product->shipping_returns}}</textarea>
                                                </div>
                                            </div>                                                                                      
                                        </div>
                                    </div>	                                                                      
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h2 class="h4 mb-3">Media</h2>								
                                        <div id="image" class="dropzone dz-clickable">
                                            <div class="dz-message needsclick">    
                                                <br>Drop files here or click to upload.<br><br>                                            
                                            </div>
                                        </div>
                                    </div>	                                                                      
                                </div>
                                <div class="row" id="product-gallery">
                                    @if($productImages->isNotEmpty())
                                        @foreach($productImages as $image)
                                        <div class="col-md-3" id="row-image-{{$image->id}}"><div class="card">
                                        <input type="hidden" name="image_array[]" value="{{$image->id}}">
                                        <img src="{{asset('uploads/product/'.$image->name)}}" height="200" class="card-img-top" alt="...">
                                        <div class="card-body">                                
                                            <a href="javascript:void(0);" onclick="deleteImage({{$image->id}})" class="btn btn-danger">Delete</a>
                                        </div>
                                        </div></div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h2 class="h4 mb-3">Pricing</h2>								
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="price">Price</label>
                                                    <input type="text" name="price" id="price" value="{{$product->price}}" class="form-control" placeholder="Price">	
                                                    <p class="error"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="compare_price">Compare at Price</label>
                                                    <input type="text" name="compare_price" id="compare_price" value="{{$product->compare_price}}" class="form-control" placeholder="Compare Price">
                                                    <p class="text-muted mt-3">
                                                        To show a reduced price, move the product’s original price into Compare at price. Enter a lower value into Price.
                                                    </p>	
                                                </div>
                                            </div>                                            
                                        </div>
                                    </div>	                                                                      
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h2 class="h4 mb-3">Inventory</h2>								
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="sku">SKU (Stock Keeping Unit)</label>
                                                    <input type="text" name="sku" id="sku" value="{{$product->sku}}" class="form-control" placeholder="sku">	
                                                    <p class="error"></p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="barcode">Barcode</label>
                                                    <input type="text" name="barcode" id="barcode" value="{{$product->barcode}}" class="form-control" placeholder="Barcode">	
                                                </div>
                                            </div>                                               
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="hidden" name="track_qty" value="No">
                                                        <input class="custom-control-input" type="checkbox" id="track_qty" name="track_qty" {{ ($product->track_qty == "Yes") ? 'checked' : ''  }} value="Yes" checked>
                                                        <label for="track_qty" class="custom-control-label">Track Quantity</label>
                                                        <p class="error"></p>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <input type="number" min="0" name="qty" id="qty" value="{{$product->qty}}" class="form-control" placeholder="Qty">	
                                                    <p class="error"></p>
                                                </div>
                                            </div>                                         
                                        </div>
                                    </div>	                                                                      
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">	
                                        <h2 class="h4 mb-3">Related products</h2>
                                        <div class="mb-3">
                                            <select multiple name="related_products[]" id="related_products" class="form-control related-product w-100">
                                               @if(!empty($relatedProducts))
                                                @foreach($relatedProducts as $relatedProduct)
                                                    <option selected value="{{$relatedProduct->id}}">{{$relatedProduct->title}}</option>
                                                @endforeach
                                               @endif
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">	
                                        <h2 class="h4 mb-3">Product status</h2>
                                        <div class="mb-3">
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" {{($product->status == 1)?'selected':''}}>Active</option>
                                                <option value="0" {{($product->status == 0)?'selected':''}}>Block</option>
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="card">
                                    <div class="card-body">	
                                        <h2 class="h4  mb-3">Product category</h2>
                                        <div class="mb-3">
                                            <label for="category">Category</label>
                                            <select name="category" id="category" class="form-control">
                                                <option value="">Select a category</option>
                                                @if ($categories->isNotEmpty())
                                                    @foreach($categories as $category)
                                                    <option value="{{$category->id}}" {{ ($product->category_id == $category->id) ? 'selected' : '' }}>{{$category->name}}</option>
                                                    @endforeach
                                                @endif  
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category">Sub category</label>
                                            <select name="sub_category" id="sub_category" class="form-control">
                                                <option value="">Select a sub category</option>
                                                @if($subCategories->isNotEmpty())
                                                @foreach($subCategories as $subCategory)
                                                <option value="{{$subCategory->id}}" {{($product->sub_category_id == $subCategory->id) ? 'selected' : ''}}>{{$subCategory->name}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="card mb-3">
                                    <div class="card-body">	
                                        <h2 class="h4 mb-3">Product brand</h2>
                                        <div class="mb-3">
                                            <select name="brand" id="brand" class="form-control">
                                                    <option value="">Select a brand</option>
                                                    @if ($brands->isNotEmpty())
                                                    @foreach($brands as $brand)
                                                        <option value="{{$brand->id}}" {{ ($product->brand_id == $brand->id) ? 'selected' : '' }}>{{$brand->name}}</option>
                                                    @endforeach
                                                @endif  
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="card mb-3">
                                    <div class="card-body">	
                                        <h2 class="h4 mb-3">Featured product</h2>
                                        <div class="mb-3">
                                            <select name="is_featured" id="is_featured" class="form-control">
                                                <option value="No" {{($product->is_featured == 'No') ? 'selected' : ''}}>No</option>
                                                <option value="Yes" {{($product->is_featured == 'Yes') ? 'selected' : ''}}>Yes</option>                                                
                                            </select>
                                            <p class="error"></p>
                                        </div>
                                    </div>
                                </div>                                                                 
                            </div>
                        </div>
						
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Update</button>
							<a href="{{route('products.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
						</div>
					</div>
                    </form>
					<!-- /.card -->
				</section>
				<!-- /.content -->
@endsection

@section('customJs')
    <script>
        $(document).ready(function(){

            $('.related-product').select2({
                ajax: {
                    url: '{{ route("products.getProducts") }}',
                    dataType: 'json',
                    tags: true,
                    multiple: true,
                    minimumInputLength: 3,
                    processResults: function (data) {
                        return {
                            results: data.tags
                        };
                    }
                }
            }); 

            $('.summernote').summernote({
                height: '250px'
            });

            $('#title').change(function(){
                var element = $(this);
                $.ajax({
                    url: "{{route('get.slug')}}",
                    data: {'title': element.val()},
                    type: 'GET',
                    dataType: 'json',
                    success: function(response){
                        $('#slug').val(response.slug);
                    }
                });
            });

            $("#category").change(function(){
                var category_id = $(this).val();
                $.ajax({
                    url: "{{ route('product-subcategories.index') }}",
                    data: {category_id: category_id},
                    type: 'GET',
                    dataType: 'json',
                    success: function(response)
                    {
                        $("#sub_category").find('option').not(':first').remove();
                        $.each(response.subcategories, function(key, item){
                            $("#sub_category").append(`<option value='${item.id}'>${item.name}</option>`);
                        });
                        //console.log(response);
                    }
                });
            });

            $("#productForm").submit(function(event){
                event.preventDefault();
                $('button[type="submit"]').prop('disabled', true);
                var formData = $(this).serializeArray();

                $.ajax({
                    url: '{{ route("products.update",$product->id) }}',
                    data: formData,
                    type: 'put',
                    dataType: 'json',
                    success: function(response){
                        $('button[type="submit"]').prop('disabled', false);
                        if(response.status == true)
                        {
                            window.location.href = "{{route('products.index')}}";
                        }else{
                            $('input[type="text"],input[type="number"],select').removeClass('is-invalid');
                            $('.error').removeClass('invalid-feedback').html('');
                            $.each(response.errors, function(key,item){
                                $(`#${key}`).addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(item)
                            });
                        }
                    },
                    error:{

                    }                    
                });
            });

            
        });

        Dropzone.autoDiscover = false;    
            const dropzone = $("#image").dropzone({ 
            
            url:  "{{ route('product-images.save', $product->id) }}",
            maxFiles: 10,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/gif/webp",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }, success: function(file, response){
                var html = `<div class="col-md-3" id="row-image-${response.image_id}"><div class="card">
                            <input type="hidden" name="image_array[]" value="${response.image_id}">
                            <img src="${response.image_path}" height="200" class="card-img-top" alt="...">
                            <div class="card-body">                                
                                <a href="javascript:void(0);" onclick="deleteImage(${response.image_id})" class="btn btn-danger">Delete</a>
                            </div>
                            </div></div>`;
                            $("#product-gallery").append(html);
               },
               complete: function(file){
                    this.removeFile(file);
               }

               
            });

            function deleteImage(id)
            {
               // $("#row-image-"+id).remove();
               if(confirm('Do you want to delete this image?')){
                var url =  "{{route('product-images.delete', 'ID')}}";
                var newUrl = url.replace('ID',id);
                    $.ajax({
                        url: newUrl,
                        type: 'delete',
                        dataType: 'json',
                        success: function(response){
                            if(response.status == true)
                            {
                                $("#row-image-"+id).remove();
                            }
                        }
                    });
               }
            }
    </script>
@endsection