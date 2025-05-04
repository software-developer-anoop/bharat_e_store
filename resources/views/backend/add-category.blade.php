
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
          <form method="post" action="{{route('admin.save-category')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <input type="hidden" name="old_category_image" value="{{$data->category_image??''}}">
            <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="category_name">Category Name</label>
                  <input id="category_name" type="text" name="category_name" placeholder="Category Name" class="form-control" required value="{{$data->category_name??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="category_image">Category Image</label>
                  <input id="category_image" type="file" name="category_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($data->category_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$data->category_image)}}" height="70" width="100">
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