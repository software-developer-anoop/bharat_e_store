
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
          <form method="post" action="{{route('admin.save-customer')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <!-- <input type="hidden" name="old_member_image" value="{{$data->member_image??''}}">
            <input type="hidden" name="old_member_image_webp" value="{{$data->member_image_webp??''}}"> -->
            <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="customer_name">Name</label>
                  <input id="customer_name" type="text" name="customer_name" placeholder="Customer Name" class="form-control ucwords" required value="{{$data->customer_name??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="customer_email">Email</label>
                  <input id="customer_email" type="text" name="customer_email" placeholder="Email" class="form-control" required value="{{$data->customer_email??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="customer_phone">Phone</label>
                  <input id="customer_phone" type="text" name="customer_phone" placeholder="Phone" class="form-control" required value="{{$data->customer_phone??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="customer_address">Address</label>
                  <input id="customer_address" type="text" name="customer_address" placeholder="Address" class="form-control" required value="{{$data->customer_address??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="customer_gender">Gender</label>
                  <select name="customer_gender" id="customer_gender" class="form-control select2" required>
                    <option value="">Select Gender</option>
                    <option value="Male" {{!empty($data->customer_gender) && ($data->customer_gender=="Male")?'selected':''}}>Male</option>
                    <option value="Female" {{!empty($data->customer_gender) && ($data->customer_gender=="Female")?'selected':''}}>Female</option>
                    <option value="Others" {{!empty($data->customer_gender) && ($data->customer_gender=="Others")?'selected':''}}>Others</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="referral_code">Referral Code</label>
                  <input type="text" class="form-control" id="referral_code" name="referral_code" placeholder="Referral Code" value="{{$data->referral_code??''}}"  autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="referrer_code">Referrer Code</label>
                  <input type="text" class="form-control" id="referrer_code" name="referrer_code" placeholder="Referral Code" value="{{$data->referrer_code??''}}"  autocomplete="off">
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