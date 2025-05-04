
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
          <form method="post" action="{{route('admin.save-notification')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <input type="hidden" name="old_image" value="{{$data->image??''}}">
            <div class="row">
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="title">Title</label>
                  <input id="title" type="text" name="title" placeholder="Title" class="form-control" required value="{{$data->title??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea id="description" name="description" placeholder="Description" class="form-control" required>{{$data->description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="image">Image</label>
                  <input id="image" type="file" name="image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($data->image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$data->image)}}" height="70" width="100">
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