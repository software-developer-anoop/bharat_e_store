@extends('backend.layout.master')
@section('content')
<div class="layout-px-spacing">
  <div class="account-settings-container layout-top-spacing">
    <div class="account-content">
      <div class="scrollspy-example" data-spy="scroll" data-target="#account-settings-scroll" data-offset="-100">
        <div class="row">
          <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
            <form id="general-info" class="section general-info" action="{{route('admin.save-profile')}}" method="post" enctype="multipart/form-data">
                @csrf
              <input type="hidden" name="old_profile_image" value="{{$user->profile_image??''}}">
              <div class="info">
                <h6 class="">General Information</h6>
                <div class="row">
                  <div class="col-lg-11 mx-auto">
                    <div class="row">
                      <div class="col-xl-2 col-lg-12 col-md-4">
                        <div class="upload mt-4 pr-md-4">
                          <input 
                                type="file" 
                                name="profile_image" 
                                id="input-file-max-fs" 
                                accept="image/jpeg, image/png" 
                                class="dropify" 
                                data-default-file="{{ $user->profile_image ? asset('uploads/' . $user->profile_image) : asset('assets/backend/assets/img/200x200.jpg') }}" 
                                data-max-file-size="2M" 
                            />
                          <p class="mt-2"><i class="flaticon-cloud-upload mr-1"></i> Upload Picture</p>
                        </div>
                      </div>
                      <div class="col-xl-10 col-lg-12 col-md-8 mt-md-0 mt-4">
                        <div class="form">
                          <div class="row">
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control mb-4" id="name" name="name" placeholder="Name" value="{{$user->name??''}}" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="user_name">Username</label>
                                <input type="text" placeholder="Username" class="form-control mb-4" name="user_name" id="user_name" value="{{$user->user_name??''}}" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control mb-4" id="phone" name="phone" placeholder="Phone" value="{{$user->phone??''}}" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control mb-4" id="email" name="email" placeholder="Email" value="{{$user->email??''}}" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control mb-4" id="password" name="password" placeholder="Password" value="" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="dob">Date Of Birth</label>
                                <input type="text" class="form-control mb-4 datepicker flatpickr-input flatpickr" max="{{date('Y-m-d')}}" id="dob" name="dob" value="{{$user->dob??''}}" autocomplete="off">
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="country">Country</label>
                                <select name="country" id="country" class="form-control select2">
                                <option value="">Select Country</option>
                                @if(!empty($countries))
                                @foreach($countries as $key=>$value)
                                <option value="{{$value->country_name}}" {{($user->country) && ($user->country==$value->country_name)?'selected':''}}>{{$value->country_name}}</option>
                                @endforeach
                                @endif
                                </select>
                              </div>
                            </div>
                            <div class="col-sm-6">
                              <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control mb-4" id="address" name="address" placeholder="Address" value="{{$user->address??''}}" autocomplete="off">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <button id="submit" type="submit" class="btn btn-dark float-right">Save Changes</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection