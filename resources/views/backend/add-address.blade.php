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
          <form method="post" action="{{route('admin.save-address')}}">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="customer_id">Customer</label>
                  <select name="customer_id" id="customer_id" class="form-control select2" required>
                    <option value="">Select Customer</option>
                    @if(!empty($customers))
                    @foreach($customers as $key=>$value)
                    <option value="{{$value->id}}" {{!empty($data->customer_id) && ($data->customer_id==$value->id)?'selected':''}}>{{$value->customer_name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input id="name" type="text" name="name" placeholder="Name" class="form-control ucwords" required value="{{$data->name??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="customer_email">Email</label>
                  <input id="customer_email" type="text" name="email" placeholder="Email" class="form-control" required value="{{$data->email??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input id="phone" type="text" name="phone" placeholder="Phone" class="form-control" required value="{{$data->phone??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="country_id">Country</label>
                  <select name="country" id="country_id" class="form-control select2" onchange="return getStates(this.value)" required>
                    <option value="">Select Country</option>
                    @if(!empty($countries))
                    @foreach($countries as $key=>$value)
                    <option value="{{$value->id}}" {{!empty($data->country) && ($data->country==$value->id)?'selected':''}}>{{$value->country_name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="state_list">State</label>
                  <select name="state" id="state_list" class="form-control select2" 
                  onchange="return getCities(@json($data->country), this.value)">
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="city_list">City Name</label>
                  <select name="city" id="city_list" class="form-control select2">
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="address">Address</label>
                  <input id="address" type="text" name="address" placeholder="Address" class="form-control" required value="{{$data->address??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="pincode">Pincode</label>
                  <input id="pincode" type="text" name="pincode" placeholder="Pincode" class="form-control" required value="{{$data->pincode??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="address_type">Address Type</label>
                  <select name="address_type" id="address_type" class="form-control select2" required>
                    <option value="">Select Type</option>
                    <option value="Home" {{!empty($data->address_type) && ($data->address_type=="Home")?'selected':''}}>Home</option>
                    <option value="Work" {{!empty($data->address_type) && ($data->address_type=="Work")?'selected':''}}>Work</option>
                  </select>
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