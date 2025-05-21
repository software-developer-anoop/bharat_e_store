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
          <table id="" class="table table-hover" style="width:100%">
            <div class="row">
              <div class="col-lg-3 col-12">
                <div class="form-group">
                  <label for="admin">User</label>
                  <select name="user" id="admin" class="form-control select2" onchange="return viewAssigned(this.value)" required>
                    <option value="">Select User</option>
                    @if(!empty($users))
                    @foreach($users as $key=>$value)
                    <option value="{{$value->id}}" {{!empty($id) && ($id==$value->id)?'selected':''}}>{{$value->name}}</option>
                    @endforeach
                    @endif
                  </select>
                </div>
              </div>
            </div>
            <thead>
              <tr>
                <th class="text-center">Id</th>
                <th class="text-center">Menu Name</th>
                <th class="text-center">
                Assign Menu 
                <input type="checkbox" id="readcheckAll" name="readcheckAll" 
                    {{ count($assigned_menus) == count($menus) ? 'checked' : '' }}>
                </th>
              </tr>
            </thead>
            <tbody>
                @foreach($menus as $id => $menu)
                <tr>
                    <td class="text-center">{{ $id }}</td>
                    <td class="text-center">{{ $menu }}</td>
                    <td class="text-center">
                        <input 
                            type="checkbox" 
                            value="{{ $id }}" 
                            class="read-checkbox readmenu" 
                            data-name="{{ $menu }}"
                            {{ in_array($id, $assigned_menus) ? 'checked' : '' }}>
                    </td>
                </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection