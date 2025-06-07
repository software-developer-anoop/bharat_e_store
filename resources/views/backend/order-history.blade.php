@extends('backend.layout.master')
@section('content')
<div class="layout-px-spacing">
  <div class="page-header">
    <div class="page-title">
      <h3>{{$page_name??''}}</h3>
    </div>
  </div>
  <div class="row" id="cancel-row">
    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
      <div class="widget-content widget-content-area br-6">
        <div class="table-responsive mb-4 mt-4">
          <table id="alter_pagination" class="table table-hover" style="width:100%">
            <thead>
              <tr>
                <th class="text-center">Order ID</th>
                <th class="text-center">Product Name / <br> Product Color / <br> Product Size</th>
                <th class="text-center">Price / <br> Quantity</th>
                <th class="text-center">Image</th>                
              </tr>
            </thead>
            <tbody>
              @if(!empty($data))
              @foreach($data as $key => $value)
              <tr id="del_{{$value->id}}">
                <td class="text-center">{{$value->order_id??'N/A'}}</td>
                <td class="text-center">{{$value->product_name??'N/A'}} / <br> {{$value->product_color??'N/A'}} / <br> {{$value->product_size??'N/A'}}</td>
                <td class="text-center">{{$value->product_selling_price??'N/A'}} / <br> {{$value->quantity??'N/A'}}</td>
                <td class="text-center"><img src="{{$value->image??''}}" width="100"></td>
              </tr>
              @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection