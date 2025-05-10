@extends('backend.layout.master')
@section('content')
<div class="layout-px-spacing">
  <div class="page-header">
    <div class="page-title">
      <h3>{{$page_name??''}}</h3>
    </div>
  </div>
  <div class="row" id="cancel-row">
    <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
      <div class="widget-content widget-content-area br-6">
        <div class="table-responsive mb-4 mt-4">
          <table id="alter_pagination" class="table table-hover" style="width:100%">
            <thead>
              <tr>
                <th class="text-center">Subcategory / <br>Category</th>
                <th class="text-center">Subcategory Image</th>
                <th class="text-center">Status</th>
                <th class="text-center">Created At / <br> Updated At</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($data))
              @foreach($data as $key => $value)
              <tr id="del_{{$value->id}}">
                <td class="text-center">{{$value->subcategory_name??''}} / <br>{{$value->category_name??''}}</td>
                @if($value->subcategory_image)
                <td class="text-center"><img src="{{$value->subcategory_image?asset('uploads/'.$value->subcategory_image):''}}" width="100"></td>
                @else
                <td class="text-center">N/A</td>
                @endif
                <td class="text-center">
                    <a href="javascript:void(0)" 
                       onclick="return changeStatus('{{ $value->status == 'Active' ? 'Inactive' : 'Active' }}', {{ $value->id }}, 'subcategories')" 
                       title="Change To {{ $value->status == 'Active' ? 'Inactive' : 'Active' }}" >
                        {{ $value->status ?? 'N/A' }}
                    </a>
                </td>
                <td class="text-center">{{$value->created_at??'N/A'}} / <br>  {{$value->updated_at??'N/A'}}</td>
                <td class="text-center">
                  <a href="{{route('admin.edit-subcategory',$value->id)}}" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"></path><polygon points="18 2 22 6 12 16 8 16 8 12 18 2"></polygon>
                      <line x1="15" y1="9" x2="9" y2="15"></line>
                      <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                  </a>
                  <a href="javascript:void(0)" onclick="return deleteItem({{$value->id}},'subcategories')" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path><line x1="18" y1="9" x2="12" y2="15"></line><line x1="12" y1="9" x2="18" y2="15"></line></svg>
                  </a>
                </td>
              </tr>
              @endforeach
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection