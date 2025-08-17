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
          <form method="post" action="{{route('admin.save-product')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            <div class="row">
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="category">Category</label>
                  <select name="category" id="category" class="form-control select2" onchange="return getSubcategory(this.value,{{$data->subcategory_id??null}})" required>
                    <option value="">Select Category</option>
                    @if(!empty($categories))
                    @foreach($categories as $key=>$value)
                    <option value="{{$value->id}}" {{!empty($data->category_id) && ($data->category_id==$value->id)?'selected':''}}>{{$value->category_name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="subcategory">Subcategory</label>
                  <select name="subcategory" id="subcategory_list" class="form-control select2" >
                  </select>
                </div>
              </div>
              <div class="col-lg-6 col-12">
                <div class="form-group">
                  <label for="product_name">Product Name</label>
                  <input id="product_name" type="text" name="product_name" placeholder="Product Name" class="form-control ucwords" required value="{{$data->product_name??''}}" autocomplete="off">
                </div>
              </div>
              <div class="col-lg-12 col-12">
                <div class="form-group">
                  <label for="product_description">Product Description</label>
                  <textarea id="product_description" name="product_description" placeholder="Product Description" class="form-control editor">{{$data->product_description??''}}</textarea>
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_cost_price">Product Cost Price</label>
                  <input id="product_cost_price" type="text" name="product_cost_price" placeholder="Product Cost Price" class="form-control numbersWithZeroOnlyInput" required value="{{$data->product_cost_price??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_selling_price">Product Selling Price</label>
                  <input id="product_selling_price" type="text" name="product_selling_price" placeholder="Product Selling Price" class="form-control numbersWithZeroOnlyInput" required value="{{$data->product_selling_price??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_quantity">Product Quantity</label>
                  <input id="product_quantity" type="text" name="product_quantity" placeholder="Product Quantity" class="form-control numbersWithZeroOnlyInput" required value="{{$data->product_quantity??''}}">
                </div>
              </div>
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="product_rating">Product Rating</label>
                  <input id="product_rating" type="text" name="product_rating" placeholder="Product Rating" class="form-control"  value="{{$data->product_rating??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_size">Product Size</label>
                  <input id="product_size" type="text" name="product_size" placeholder="Product Size" class="form-control" required value="{{$data->product_size??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_colors">Product Colors (E.g. Color - Code)</label>
                  <input id="product_colors" type="text" name="product_colors" placeholder="Product Colors" class="form-control" required value="{{$data->product_colors??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_availability">Product Availability</label>
                  <select name="product_availability" id="product_availability" class="form-control select2" required>
                    <option value="">Select Availability</option>
                    <option value="In Stock" {{!empty($data->product_availability) && ($data->product_availability=="In Stock")?'selected':''}}>In Stock</option>
                    <option value="Out Of Stock" {{!empty($data->product_availability) && ($data->product_availability=="Out Of Stock")?'selected':''}}>Out Of Stock</option>
                  </select>
                </div>
              </div>
              <div class="input_field_wrapper col-sm-12 mb-3">
                @php $faqIndex = 1; @endphp
                @if (!empty($faqs) && count($faqs) > 0)
                @foreach ($faqs as $faq)
                <div id="faq_{{ $faqIndex }}">
                  <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Question</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" placeholder="Question" value="{{ $faq->question }}" name="faq_question[]">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Answer</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" placeholder="Answer" value="{{ $faq->answer }}" name="faq_answer[]">
                    </div>
                  </div>
                  @if ($faqIndex == 1)
                  <a href="javascript:void(0);" class="add_button btn btn-success btn-sm" title="Add field">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                      viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="feather feather-activity">
                      <line x1="12" y1="5" x2="12" y2="19"></line>
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                  </a>
                  @else
                  <a href="javascript:void(0);" class="btn btn-danger btn-sm" title="Remove field"
                    onclick="del_faq({{ $faqIndex }})" id="bt_{{ $faqIndex }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                      viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="feather feather-activity">
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                  </a>
                  @endif
                </div>
                @php $faqIndex++; @endphp
                @endforeach
                @else
                <div id="faq_1">
                  <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Question</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" placeholder="Question" name="faq_question[]">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Answer</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" placeholder="Answer" name="faq_answer[]">
                    </div>
                  </div>
                  <a href="javascript:void(0);" class="add_button btn btn-success btn-sm" title="Add field">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                      viewBox="0 0 24 24" fill="none" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="feather feather-activity">
                      <line x1="12" y1="5" x2="12" y2="19"></line>
                      <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                  </a>
                </div>
                @endif
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_off">Product Off</label>
                  <input id="product_off" type="text" name="product_off" placeholder="Product Off" class="form-control"  value="{{$data->product_off??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <label for="check">Is Trending</label>
                <select name="is_trending" id="is_trending" class="form-control select2" required>
                  <option value="">Select Trending</option>
                  <option value="yes" {{!empty($data->is_trending) && ($data->is_trending=="yes")?'selected':''}}>Yes</option>
                  <option value="no" {{!empty($data->is_trending) && ($data->is_trending=="no")?'selected':''}}>No</option>
                </select>
              </div>
              <div class="col-lg-4 col-12">
                <label for="check">Is Hot Deal</label>
                <select name="is_hot_deal" id="is_hot_deal" class="form-control select2" required>
                  <option value="">Select hot_deal</option>
                  <option value="yes" {{!empty($data->is_hot_deal) && ($data->is_hot_deal=="yes")?'selected':''}}>Yes</option>
                  <option value="no" {{!empty($data->is_hot_deal) && ($data->is_hot_deal=="no")?'selected':''}}>No</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="product_image">Product Image</label>
                  <input id="product_image" type="file" name="product_image[]" accept="image/jpeg, image/png" class="form-control" multiple>
                </div>
              </div>
              @php
              $images = !empty($data->product_image) ? json_decode($data->product_image, true) : [];
              @endphp
              @if (!empty($images))
<div class="col-sm-12 mt-2 d-flex flex-wrap gap-2">
    @foreach ($images as $image)
        <div class="position-relative d-inline-block">
            <!-- Trash Icon -->
            <a href="javascript:void(0);" 
   class="position-absolute top-0 end-0 m-1 text-danger" 
   onclick="return deleteImage('{{ $image['image'] }}', {{ $data->id ?? '0' }})">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" 
                     fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                     style="background:#fff; border-radius:50%; padding:2px;">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    <line x1="10" x2="10" y1="11" y2="17" />
                    <line x1="14" x2="14" y1="11" y2="17" />
                </svg>
            </a>

            <!-- Image -->
            <a href="{{ asset('uploads/' . $image['image']) }}" target="_blank">
                <img src="{{ asset('uploads/' . $image['image']) }}" height="70" width="100" alt="Logo" 
                     class="border rounded" id="img_{{$data->id ??''}}">
            </a>
        </div>
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