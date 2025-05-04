
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
          <form method="post" action="{{route('admin.save-coupon')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <div class="row">
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="coupon_title">Coupon Title</label>
                  <input id="coupon_title" type="text" name="coupon_title" placeholder="Coupon Title" class="form-control" required value="{{$data->coupon_title??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="coupon_code">Coupon Code</label>
                  <input id="coupon_code" type="text" name="coupon_code" placeholder="Coupon Code" class="form-control" required value="{{$data->coupon_code??''}}">
                </div>
              </div>              
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="coupon_description">Coupon Description</label>
                  <textarea id="coupon_description" name="coupon_description" placeholder="Coupon Description" class="form-control editor">{{$data->coupon_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="coupon_type">Coupon Type</label>
                  <select name="coupon_type" id="coupon_type" class="form-control select2" required>
                    <option value="">Select Type</option>
                    <option value="Fixed" {{!empty($data->coupon_type) && ($data->coupon_type=="Fixed")?'selected':''}}>Fixed</option>
                    <option value="Percent" {{!empty($data->coupon_type) && ($data->coupon_type=="Percent")?'selected':''}}>Percent</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="coupon_value">Coupon Value</label>
                  <input id="coupon_value" type="text" name="coupon_value" placeholder="Coupon Value" class="form-control" required value="{{$data->coupon_value??''}}">
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