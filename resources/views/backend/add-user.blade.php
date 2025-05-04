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
          <form method="post" action="{{route('admin.save-user')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <input type="hidden" name="old_profile_image" value="{{$data->profile_image??''}}">
            <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control restrictedInput ucwords" id="name" name="name" placeholder="Name" value="{{$data->name??''}}" required autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="user_name">Username</label>
                  <input type="text" placeholder="Username" class="form-control" name="user_name" id="user_name" value="{{$data->user_name??''}}" required autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control emailInput" id="email" name="email" placeholder="Email" value="{{$data->email??''}}" required autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="phone">Phone</label>
                  <input type="tel" class="form-control mb-4" id="phone" name="phone" placeholder="Phone" value="{{$data->phone??''}}" autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="" autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="dob">Date Of Birth</label>
                  <input type="text" class="form-control datepicker flatpickr-input flatpickr" max="{{date('Y-m-d')}}" id="dob" name="dob" required value="{{$data->dob??''}}" autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="gender">Gender</label>
                  <select name="gender" id="gender" class="form-control select2" required>
                    <option value="">Select Gender</option>
                    <option value="Male" {{!empty($data->gender) && ($data->gender=="Male")?'selected':''}}>Male</option>
                    <option value="Female" {{!empty($data->gender) && ($data->gender=="Female")?'selected':''}}>Female</option>
                    <option value="Others" {{!empty($data->gender) && ($data->gender=="Others")?'selected':''}}>Others</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="country">Country</label>
                  <select name="country" id="country" class="form-control select2" required>
                    <option value="">Select Country</option>
                    @if(!empty($countries))
                    @foreach($countries as $key=>$value)
                    <option value="{{$value->country_name}}" {{!empty($data->country) && ($data->country==$value->country_name)?'selected':''}}>{{$value->country_name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="address">Address</label>
                  <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{$data->address??''}}" required autocomplete="off">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="role">Role</label>
                  <select name="role" id="role" class="form-control select2" required>
                    <option value="">Select Role</option>
                    @if(!empty($roles))
                    @foreach($roles as $key=>$value)
                    <option value="{{$value->role_name}}" {{!empty($data->role) && ($data->role==$value->role_name)?'selected':''}}>{{$value->role_name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="profile_image">Profile Image</label>
                  <input id="profile_image" type="file" name="profile_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($data->profile_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$data->profile_image)}}" height="70" width="100">
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