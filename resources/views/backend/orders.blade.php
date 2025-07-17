@extends('backend.layout.master')

@section('content')
<div class="layout-px-spacing">
  <div class="page-header">
    <div class="page-title">
      <h3>{{ $page_name ?? '' }}</h3>
    </div>
  </div>

  <div class="row" id="cancel-row">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
      <div class="widget-content widget-content-area br-6">
        <div class="table-responsive mb-4 mt-4">
          <table id="alter_pagination" class="table table-hover" style="width:100%">
            <thead>
              <tr>
                <th class="text-center">Order ID</th>
                <th class="text-center">Customer ID / Name</th>
                <th class="text-center">Address /<br> Pincode</th>
                <th class="text-center">Payment Mode /<br> Amount /<br> Coupon</th>
                <th class="text-center">Payment Status /<br> Order Status</th>
                <th class="text-center">Order On</th>
                @if(isset($key) && $key == "total")
                <th class="text-center">Change Order Status</th>
                @endif
                <th class="text-center">View History</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($data) && count($data))
                @foreach($data as $row)
                <tr id="del_{{ $row->id }}">
                  <td class="text-center">{{ $row->order_id ?? 'N/A' }}</td>
                  <td class="text-center">{{ $row->customer_id ?? 'N/A' }} /<br> {{ $row->customer_name ?? 'N/A' }}</td>
                  <td class="text-center">{{ $row->address ?? 'N/A' }} /<br> {{ $row->pincode ?? 'N/A' }}</td>
                  <td class="text-center">{{ ucfirst($row->payment_mode ?? 'N/A') }} /<br> {{ $row->amount ?? 'N/A' }} /<br> {{ $row->coupon_title ?? 'N/A' }}</td>
                  <td class="text-center">{{ ucfirst($row->status ?? 'N/A') }} /<br> {{ ucfirst($row->order_status ?? 'N/A') }}</td>
                  <td class="text-center">{{ $row->order_created_at ?? 'N/A' }}</td>
                  @if(isset($key) && $key == "total")
                  <td class="text-center">
                    <select class="form-control select2" onchange="changeOrderStatus(this.value, {{ $row->order_tbl_id }}, {{ $row->order_customer_id }})">
                      <option value="">Change Status</option>
                      <option value="shipped" {{ ($row->order_status ?? '') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                      <option value="delivered" {{ ($row->order_status ?? '') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                      <option value="cancelled" {{ ($row->order_status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                  </td>
                  @endif
                  <td class="text-center">
                    <a href="{{ route('admin.orderHistory', $row->id) }}" class="bs-tooltip" data-toggle="tooltip" data-placement="top" title="View Order History">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-activity">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </a>
                  </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="8" class="text-center">No orders found.</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
