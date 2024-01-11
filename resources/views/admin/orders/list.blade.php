@extends('admin.layouts.app')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Orders</h1>
							</div>
							<div class="col-sm-6 text-right">
							</div>
						</div>
					</div>
					@include('admin.messages')
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
						<div class="card">
							<div class="card-header">
								<a href="{{route('orders.index')}}" class="btn btn-sm btn-default">Reset</a>
								<div class="card-tools">
								<form action="" method="get">
									<div class="input-group input-group" style="width: 250px;">
										<input type="text" name="keyword" value="{{Request::get('keyword')}}" class="form-control float-right" placeholder="Search">
					
										<div class="input-group-append">
										  <button type="submit" class="btn btn-default">
											<i class="fas fa-search"></i>
										  </button>
										</div>
									  </div>
									  </form>
								</div>
								
							</div>
							<div class="card-body table-responsive p-0">								
								<table class="table table-hover text-nowrap">
									<thead>
										<tr>
											<th>Orders #</th>											
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Phone</th>
											<th>Status</th>
                                            <th>Total</th>
                                            <th>Date Purchased</th>
										</tr>
									</thead>
									<tbody>
										@if($orders->isNotEmpty())
										@foreach($orders as $order)
										<tr>
											<td><a href="{{route('orders.detail',$order->id)}}">{{$order->id}}</a></td>
											<td>{{$order->name}}</td>
                                            <td>{{$order->email}}</td>
                                            <td>{{$order->mobile}}</td>
                                            <td>
												@if($order->status == 'pending')
												<span class="badge bg-danger">Pending</span>
												@elseif($order->status == 'shipped')
												<span class="badge bg-info">Shipped</span>
												@else
												<span class="badge bg-success">Delivered</span>
												@endif
											</td>
											<td>${{number_format($order->grand_total,2)}}</td>
                                            <td>{{\Carbon\Carbon::parse($order->created_at)->format('M d, Y')}}</td>																				
										</tr>
										@endforeach
										@else
										<tr>
											<td colspan="7">No orders!</td>
										</tr>
										@endif
										
									</tbody>
								</table>										
							</div>
							<div class="card-footer clearfix">
								<!-- <ul class="pagination pagination m-0 float-right">
								  <li class="page-item"><a class="page-link" href="#">«</a></li>
								  <li class="page-item"><a class="page-link" href="#">1</a></li>
								  <li class="page-item"><a class="page-link" href="#">2</a></li>
								  <li class="page-item"><a class="page-link" href="#">3</a></li>
								  <li class="page-item"><a class="page-link" href="#">»</a></li>
								</ul> -->
								{{$orders->links()}}
							</div>
						</div>
					</div>
					<!-- /.card -->
				</section>
				<!-- /.content -->
@endsection
@section('customJs')
<script>
   function deletecategory(id)
   {
	if(confirm('Are you sure want to delete category?')){
		var url = "{{route('categories.delete', 'ID')}}";
		var newUrl = url.replace('ID',id);
			$.ajax({
				url: newUrl,
				type: 'delete',
				dataType: 'json',
				success: function(response)
				{
					if(response)
					{
						window.location.href = "{{route('categories.index')}}";
					}
				}
			});
	}	
   }
</script>
@endsection