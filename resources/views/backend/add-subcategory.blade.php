
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
          <form method="post" action="{{route('admin.save-subcategory')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <input type="hidden" name="old_subcategory_image" value="{{$data->subcategory_image??''}}">
            <div class="row">
                <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="category">Category</label>
                  <select name="category" id="category" class="form-control select2" required>
                    <option value="">Select Category</option>
                    @if(!empty($category))
                    @foreach($category as $key=>$value)
                    <option value="{{$value->id}}" {{!empty($data->category) && ($data->category==$value->id)?'selected':''}}>{{$value->category_name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="subcategory_name">Subcategory Name</label>
                  <input id="subcategory_name" type="text" name="subcategory_name" placeholder="Subcategory Name" class="form-control" required value="{{$data->subcategory_name??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="subcategory_image">Subcategory Image</label>
                  <input id="subcategory_image" type="file" name="subcategory_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($data->subcategory_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$data->subcategory_image)}}" height="70" width="100">
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