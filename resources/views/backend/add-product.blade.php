
@extends('backend.layout.master')
@section('content')
<div class="container">
  <div class="row layout-top-spacing">
    <div id="basic" class="col-lg-12 layout-spacing">
      <div class="statbox widget box box-shadow">
        <div class="widget-header">
          <div class="row">
            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
              <h4>{{$page_name??''}}</h4>
            </div>
          </div>
        </div>
        <div class="widget-content widget-content-area">
          <form method="post" action="{{route('admin.save-product')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <input type="hidden" name="old_product_image" value="{{$data->product_image??''}}">
            <input type="hidden" name="old_product_image_webp" value="{{$data->product_image_webp??''}}">
            <div class="row">
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="category">Category</label>
                  <select name="category" id="category" class="form-control select2" onchange="return getSubcategory(this.value,{{$data->subcategory_id}})" required>
                    <option value="">Select Category</option>
                    @if(!empty($categories))
                    @foreach($categories as $key=>$value)
                    <option value="{{$value->id}}" {{!empty($data->category_id) && ($data->category_id==$value->id)?'selected':''}}>{{$value->category_name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="subcategory">Subcategory</label>
                  <select name="subcategory" id="subcategory_list" class="form-control select2" >
                  </select>
                </div>
              </div>
              <div class="col-lg-6 col-12">
                <div class="form-group">
                  <label for="product_name">Product Name</label>
                  <input id="product_name" type="text" name="product_name" placeholder="Product Name" class="form-control ucwords" required value="{{$data->product_name??''}}" autocomplete="off">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="product_description">Product Description</label>
                  <textarea id="product_description" name="product_description" placeholder="Product Description" class="form-control editor">{{$data->product_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_cost_price">Product Cost Price</label>
                  <input id="product_cost_price" type="text" name="product_cost_price" placeholder="Product Cost Price" class="form-control numbersWithZeroOnlyInput" required value="{{$data->product_cost_price??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_selling_price">Product Selling Price</label>
                  <input id="product_selling_price" type="text" name="product_selling_price" placeholder="Product Selling Price" class="form-control numbersWithZeroOnlyInput" required value="{{$data->product_selling_price??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_quantity">Product Quantity</label>
                  <input id="product_quantity" type="text" name="product_quantity" placeholder="Product Quantity" class="form-control numbersWithZeroOnlyInput" required value="{{$data->product_quantity??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_rating">Product Rating</label>
                  <input id="product_rating" type="text" name="product_rating" placeholder="Product Rating" class="form-control"  value="{{$data->product_rating??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_size">Product Size</label>
                  <input id="product_size" type="text" name="product_size" placeholder="Product Size" class="form-control" required value="{{$data->product_size??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_colors">Product Colors</label>
                  <input id="product_colors" type="text" name="product_colors" placeholder="Product Colors" class="form-control" required value="{{$data->product_colors??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_availability">Product Availability</label>
                  <input id="product_availability" type="text" name="product_availability" placeholder="Product Availability" class="form-control"  value="{{$data->product_availability??''}}">
                </div>
              </div>
              <!-- <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="meta_title">Meta Title</label>
                  <textarea id="meta_title" name="meta_title" placeholder="Meta Title" class="form-control" required>{{$data->meta_title??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_description">Meta Description</label>
                  <textarea id="meta_description" name="meta_description" placeholder="Meta Description" class="form-control" required>{{$data->meta_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_keyword">Meta Keywords</label>
                  <textarea id="meta_keyword" name="meta_keyword" placeholder="Meta Keywords" class="form-control" required>{{$data->meta_keyword??''}}</textarea>
                </div>
              </div> -->
              <!-- <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_schema">Meta Schema</label>
                  <textarea id="meta_schema" type="text" name="meta_schema" placeholder="Meta Schema" class="form-control">{{$data->meta_schema??''}}</textarea>
                </div>
              </div> -->
              </div>
              <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_image">Product Image</label>
                  <input id="product_image" type="file" name="product_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($data->product_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$data->product_image)}}" height="70" width="100">
              </div>
              @endif
            </div>
            <input type="submit" name="txt" class="mt-4 btn btn-primary">
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection