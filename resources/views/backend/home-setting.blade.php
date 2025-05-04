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
          <form method="post" action="{{route('admin.save-home-setting')}}" enctype="multipart/form-data">
          	@csrf
          	<input type="hidden" name="id" value="{{$home->id??''}}">
          	<input type="hidden" name="old_main_image" value="{{$home->main_image??''}}">
          	<input type="hidden" name="old_main_image_webp" value="{{$home->main_image_webp??''}}">
            <input type="hidden" name="old_second_image" value="{{$home->second_image??''}}">
            <input type="hidden" name="old_second_image_webp" value="{{$home->second_image_webp??''}}">
            <input type="hidden" name="old_third_image" value="{{$home->third_image??''}}">
            <input type="hidden" name="old_third_image_webp" value="{{$home->third_image_webp??''}}">
            <input type="hidden" name="old_fourth_image" value="{{$home->fourth_image??''}}">
            <input type="hidden" name="old_fourth_image_webp" value="{{$home->fourth_image_webp??''}}">
            <input type="hidden" name="old_about_image" value="{{$home->about_image??''}}">
            <input type="hidden" name="old_about_image_webp" value="{{$home->about_image_webp??''}}">
            <input type="hidden" name="old_expertise_image" value="{{$home->expertise_image??''}}">
            <input type="hidden" name="old_expertise_image_webp" value="{{$home->expertise_image_webp??''}}">
            <input type="hidden" name="old_footer_image" value="{{$home->footer_image??''}}">
            <input type="hidden" name="old_footer_image_webp" value="{{$home->footer_image_webp??''}}">
            <div class="row">
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="main_heading">Main Heading</label>
                  <input id="main_heading" type="text" name="main_heading" placeholder="Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="main_description">Main Description</label>
                  <input id="main_description" type="text" name="main_description" placeholder="Main Description" class="form-control" autocomplete="off" required value="{{$home->main_description??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="about_main_heading">About Main Heading</label>
                  <input id="about_main_heading" type="text" name="about_main_heading" placeholder="About Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->about_main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="about_sub_heading">About Sub Heading</label>
                  <input id="about_sub_heading" type="text" name="about_sub_heading" placeholder="About Sub Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->about_sub_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="about_description">About Description</label>
                  <input id="about_description" type="text" name="about_description" placeholder="About Description" class="form-control" autocomplete="off" required value="{{$home->about_description??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="services_main_heading">Service Main Heading</label>
                  <input id="services_main_heading" type="text" name="services_main_heading" placeholder="Service Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->services_main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="services_sub_heading">Service Sub Heading</label>
                  <input id="services_sub_heading" type="text" name="services_sub_heading" placeholder="Service Sub Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->services_sub_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="services_description">Service Description</label>
                  <input id="services_description" type="text" name="services_description" placeholder="Service Description" class="form-control" autocomplete="off" required value="{{$home->services_description??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="work_main_heading">Work Main Heading</label>
                  <input id="work_main_heading" type="text" name="work_main_heading" placeholder="Work Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->work_main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="work_sub_heading">Work Sub Heading</label>
                  <input id="work_sub_heading" type="text" name="work_sub_heading" placeholder="Work Sub Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->work_sub_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="work_description">Work Description</label>
                  <input id="work_description" type="text" name="work_description" placeholder="Work Description" class="form-control" autocomplete="off" required value="{{$home->work_description??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="expertise_main_heading">Expertise Main Heading</label>
                  <input id="expertise_main_heading" type="text" name="expertise_main_heading" placeholder="Expertise Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->expertise_main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="expertise_sub_heading">Expertise Sub Heading</label>
                  <input id="expertise_sub_heading" type="text" name="expertise_sub_heading" placeholder="Expertise Sub Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->expertise_sub_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="expertise_description">Expertise Description</label>
                  <input id="expertise_description" type="text" name="expertise_description" placeholder="Expertise Description" class="form-control" autocomplete="off" required value="{{$home->expertise_description??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="achievement_main_heading">Achievement Main Heading</label>
                  <input id="achievement_main_heading" type="text" name="achievement_main_heading" placeholder="Achievement Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->achievement_main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="achievement_sub_heading">Achievement Sub Heading</label>
                  <input id="achievement_sub_heading" type="text" name="achievement_sub_heading" placeholder="Achievement Sub Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->achievement_sub_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="achievement_description">Achievement Description</label>
                  <input id="achievement_description" type="text" name="achievement_description" placeholder="Achievement Description" class="form-control" autocomplete="off" required value="{{$home->achievement_description??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="testimonial_main_heading">Testimonial Main Heading</label>
                  <input id="testimonial_main_heading" type="text" name="testimonial_main_heading" placeholder="Testimonial Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->testimonial_main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="testimonial_sub_heading">Testimonial Sub Heading</label>
                  <input id="testimonial_sub_heading" type="text" name="testimonial_sub_heading" placeholder="Testimonial Sub Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->testimonial_sub_heading??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="blog_main_heading">Blog Main Heading</label>
                  <input id="blog_main_heading" type="text" name="blog_main_heading" placeholder="Blog Main Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->blog_main_heading??''}}">
                </div>
              </div>
              <div class="col-lg-8 col-12">
                <div class="form-group">
                  <label for="blog_sub_heading">Blog Sub Heading</label>
                  <input id="blog_sub_heading" type="text" name="blog_sub_heading" placeholder="Blog Sub Heading" class="form-control ucwords" autocomplete="off" required value="{{$home->blog_sub_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_title">Meta Title</label>
                  <textarea id="meta_title" name="meta_title" placeholder="Meta Title" class="form-control" required>{{$home->meta_title??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_description">Meta Description</label>
                  <textarea id="meta_description" name="meta_description" placeholder="Meta Description" class="form-control" required>{{$home->meta_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_keyword">Meta Keywords</label>
                  <textarea id="meta_keyword" name="meta_keyword" placeholder="Meta Keywords" class="form-control" required>{{$home->meta_keyword??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-6 col-12">
                <div class="form-group">
                  <label for="about_us_yt_link">About Us Youtube Link</label>
                  <input id="about_us_yt_link" type="text" name="about_us_yt_link" placeholder="About Us Youtube Link" class="form-control" autocomplete="off" required value="{{$home->about_us_yt_link??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="footer_description">Footer Description</label>
                  <input id="footer_description" type="text" name="footer_description" placeholder="Footer Description" class="form-control" autocomplete="off" required value="{{$home->footer_description??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="main_image">Main Image</label>
                  <input id="main_image" type="file" name="main_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($home->main_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$home->main_image)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="second_image">Second Image</label>
                  <input id="second_image" type="file" name="second_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($home->second_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$home->second_image)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="third_image">Third Image</label>
                  <input id="third_image" type="file" name="third_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($home->third_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$home->third_image)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="fourth_image">Fourth Image</label>
                  <input id="fourth_image" type="file" name="fourth_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($home->fourth_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$home->fourth_image)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="about_image">About Image</label>
                  <input id="about_image" type="file" name="about_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($home->about_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$home->about_image)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="expertise_image">Expertise Image</label>
                  <input id="expertise_image" type="file" name="expertise_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($home->expertise_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$home->expertise_image)}}" height="70" width="70">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="footer_image">Footer Image</label>
                  <input id="footer_image" type="file" name="footer_image" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($home->footer_image))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$home->footer_image)}}" height="70" width="70">
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