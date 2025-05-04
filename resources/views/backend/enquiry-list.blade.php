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
                <th class="text-center">Name</th>
                <th class="text-center">Email</th>
                <th class="text-center">Message</th>
                <th class="text-center">Created At</th>
                <th class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($data))
              @foreach($data as $key => $value)
              <tr id="del_{{$value->id}}">
                <td class="text-center">{{ ($value->first_name ?? '') . ' ' . ($value->last_name ?? '') }}</td>
                <td class="text-center">{{$value->email??'N/A'}}</td>
                <td class="text-center">{{$value->message??'N/A'}}</td>
                <td class="text-center">{{$value->add_date??'N/A'}}</td>
                <td class="text-center">
                  <a href="javascript:void(0)" onclick="return deleteItem({{$value->id}},'enquiry_list')" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">
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