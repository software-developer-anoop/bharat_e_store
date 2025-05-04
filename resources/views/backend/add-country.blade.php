
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
          <form method="post" action="{{route('admin.save-country')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <input type="hidden" name="old_flag_image" value="{{$data->flag_image??''}}">
            <input type="hidden" name="old_flag_image_webp" value="{{$data->flag_image_webp??''}}">
            <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="country_name">Country Name</label>
                  <input id="country_name" type="text" name="country_name" placeholder="Country Name" class="form-control" required value="{{$data->country_name??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="country_code">Country Code</label>
                  <input id="country_code" type="text" name="country_code" placeholder="Country Code" class="form-control" required value="{{$data->country_code??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="country_currency_symbol">Country Currency Symbol</label>
                  <input id="country_currency_symbol" type="text" name="country_currency_symbol" placeholder="Country Currency Symbol" class="form-control" required value="{{$data->country_currency_symbol??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="flag_image">Flag Image</label>
                  <input id="flag_image" type="file" name="flag_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($data->flag_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$data->flag_image)}}" height="70" width="100">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="flag_image_alt">Flag Image Alt</label>
                  <input id="flag_image_alt" type="text" name="flag_image_alt" placeholder="Flag Image Alt" class="form-control" required value="{{$data->flag_image_alt??''}}">
                </div>
              </div>
            </div>
            <input type="submit" name="txt" class="mt-4 btn btn-primary">
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection