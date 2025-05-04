
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
          <form method="post" action="{{route('admin.save-city')}}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$data->id??''}}">
            
            <div class="row">
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="country">Country</label>
                  <select name="country" id="country" class="form-control select2" onchange="return getStates(this.value)" required>
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
                  <label for="state">State</label>
                  <select name="state" id="state_list" class="form-control select2" >
                  </select>
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="city_name">City Name</label>
                  <input id="city_name" type="text" name="city_name" placeholder="City Name" class="form-control" required value="{{$data->city_name??''}}">
                </div>
              </div>
              <div class="col-lg-4 col-12">
                <div class="form-group">
                  <label for="locality">Locality</label>
                  <input id="locality" type="text" name="locality" placeholder="Locality" class="form-control" required value="{{$data->locality??''}}">
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