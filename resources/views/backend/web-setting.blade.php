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
          <form method="post" action="{{route('admin.save-web-setting')}}" enctype="multipart/form-data">
          	@csrf
          	<input type="hidden" name="id" value="{{$web->id??''}}">
          	<input type="hidden" name="old_logo" value="{{$web->logo??''}}">
          	<input type="hidden" name="old_logo_webp" value="{{$web->logo_webp??''}}">
          	<input type="hidden" name="old_favicon" value="{{$web->favicon??''}}">
            <input type="hidden" name="old_banner" value="{{$web->banner??''}}">
            <div class="row">
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="site_name">Site Name</label>
                  <input id="site_name" type="text" name="site_name" placeholder="Site Name" class="form-control restrictedInput ucwords" autocomplete="off" required value="{{$web->site_name??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="mobile_number">Mobile Number</label>
                  <input id="mobile_number" type="tel" name="mobile_number" placeholder="Mobile Number" class="form-control numbersWithZeroOnlyInput" maxlength="10" minlength="10" autocomplete="off" required value="{{$web->mobile_number??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="email">Email</label>
                  <input id="email" type="email" name="email" placeholder="Email" class="form-control emailInput" autocomplete="off" required value="{{$web->email??''}}">
                </div>
              </div>
              <!-- <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="facebook_link">Facebook Link</label>
                  <input id="facebook_link" type="text" name="facebook_link" placeholder="Facebook Link" class="form-control" required value="{{$web->facebook_link??''}}">
                </div>
              </div> -->
              <!-- <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="twitter_link">Twitter Link</label>
                  <input id="twitter_link" type="text" name="twitter_link" placeholder="Twitter Link" class="form-control" required value="{{$web->twitter_link??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="instagram_link">Instagram Link</label>
                  <input id="instagram_link" type="text" name="instagram_link" placeholder="Instagram Link" class="form-control" required value="{{$web->instagram_link??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="linkedin_link">Linkedin Link</label>
                  <input id="linkedin_link" type="text" name="linkedin_link" placeholder="Linkedin Link" class="form-control" required value="{{$web->linkedin_link??''}}">
                </div>
              </div> -->
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="address">Full Address</label>
                  <input id="address" type="text" name="address" placeholder="Full Address" class="form-control" required value="{{$web->address??''}}">
                </div>
              </div>
              <!-- <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_title">Meta Title</label>
                  <textarea id="meta_title" name="meta_title" placeholder="Meta Title" class="form-control" required>{{$web->meta_title??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_description">Meta Description</label>
                  <textarea id="meta_description" name="meta_description" placeholder="Meta Description" class="form-control" required>{{$web->meta_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_keyword">Meta Keywords</label>
                  <textarea id="meta_keyword" name="meta_keyword" placeholder="Meta Keywords" class="form-control" required>{{$web->meta_keyword??''}}</textarea>
                </div>
              </div> -->
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="logo">Logo</label>
                  <input id="logo" type="file" name="logo" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($web->logo))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$web->logo)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="favicon">Favicon</label>
                  <input id="favicon" type="file" name="favicon" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($web->favicon))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$web->favicon)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="banner">Banner</label>
                  <input id="banner" type="file" name="banner[]" accept="image/jpeg, image/png" class="form-control" multiple>
                </div>
              </div>
              @php
                $images = !empty($web->banner) ? json_decode($web->banner, true) : [];
              @endphp

              @if (!empty($images))
                    <div class="col-sm-12 mt-2">
                        @foreach ($images as $image)
                            <a href="{{ asset('uploads/' . $image['image']) }}" target="_blank">
                                <img src="{{ asset('uploads/' . $image['image']) }}" height="70px" width="100px" alt="Logo">
                            </a>
                        @endforeach
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