
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
          <form method="post" action="{{route('admin.save-cms-page')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <input type="hidden" name="old_bg_image_jpg" value="{{$data->bg_image_jpg??''}}">
            <input type="hidden" name="old_bg_image_webp" value="{{$data->bg_image_webp??''}}">
            <div class="row">
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="page_name">Page Name</label>
                  <select name="page_name" id="page_name" class="form-control select2" required onchange="return getSlug(this.value)">
                  <option value="">Select Page Name</option>
                  <option value="About Us" <?=!empty($data->page_name) && ($data->page_name == "About Us") ? "selected" : ""?>>About Us</option>
                  <option value="Contact Us" <?=!empty($data->page_name) && ($data->page_name == "Contact Us") ? "selected" : ""?>>Contact Us</option>
                  <option value="Team" <?=!empty($data->page_name) && ($data->page_name == "Team") ? "selected" : ""?>>Team</option>
                  <option value="Services" <?=!empty($data->page_name) && ($data->page_name == "Services") ? "selected" : ""?>>Services</option>
                  <option value="Portfolio" <?=!empty($data->page_name) && ($data->page_name == "Portfolio") ? "selected" : ""?>>Portfolio</option>
                </select>
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="page_slug">Page Slug</label>
                  <input id="page_slug" type="text" name="page_slug" placeholder="Page Slug" readonly class="form-control" required value="{{$data->page_slug??''}}">
                </div>
              </div>
              <div class="col-lg-6 col-12">
                <div class="form-group">
                  <label for="h1_heading">H1 Heading</label>
                  <input id="h1_heading" type="text" name="h1_heading" placeholder="H1 Heading" class="form-control" required value="{{$data->h1_heading??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="short_description">Short Description</label>
                  <input id="short_description" type="text" name="short_description" placeholder="Short Description" class="form-control" required value="{{$data->short_description??''}}">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="first_description">First Description</label>
                  <textarea id="first_description" name="first_description" placeholder="First Description" class="form-control editor">{{$data->first_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="second_description">Second Description</label>
                  <textarea id="second_description" name="second_description" placeholder="Second Description" class="form-control editor">{{$data->second_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_title">Meta Title</label>
                  <textarea id="meta_title" name="meta_title" placeholder="Meta Title" class="form-control" required>{{$data->meta_title??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_description">Meta Description</label>
                  <textarea id="meta_description" name="meta_description" placeholder="Meta Description" class="form-control" required>{{$data->meta_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_keyword">Meta Keywords</label>
                  <textarea id="meta_keyword" name="meta_keyword" placeholder="Meta Keywords" class="form-control" required>{{$data->meta_keyword??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="meta_schema">Meta Schema</label>
                  <textarea id="meta_schema" type="text" name="meta_schema" placeholder="Meta Schema" class="form-control">{{$data->meta_schema??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="bg_image_jpg">Background Image</label>
                  <input id="bg_image_jpg" type="file" name="bg_image_jpg" accept="image/jpeg, image/png" class="form-control">
                </div>
              </div>
              @if(!empty($data->bg_image_jpg))
              <div class="col-lg-2 col-12 mt-4">
                <img src="{{asset('uploads/'.$data->bg_image_jpg)}}" height="70" width="100">
              </div>
              @endif
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="bg_image_alt">Background Image Alt</label>
                  <input id="bg_image_alt" type="text" name="bg_image_alt" placeholder="Background Image Alt" class="form-control" required value="{{$data->bg_image_alt??''}}">
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