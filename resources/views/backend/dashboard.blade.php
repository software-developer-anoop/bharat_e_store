@extends('backend.layout.master')
@section('content')
<div class="layout-px-spacing">
  <div class="row layout-top-spacing">
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
      <div class="widget widget-card-four">
        <div class="widget-content">
          <div class="w-content">
            <div class="w-info">
              <h6 class="value">{{$currency}} {{$stats->total_amount??''}}</h6>
              <p class="">Total Orders</p>
            </div>
            <div class="">
              <div class="w-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
              </div>
            </div>
          </div>
          <div class="progress">
            @php
            $maxValue = 10000000; // define max value
            $currentValue = $stats->total_amount ?? 0;
            $percentage = $maxValue > 0 ? min(100, ($currentValue / $maxValue) * 100) : 0;
            @endphp
            <div class="progress-bar bg-gradient-secondary" 
              role="progressbar" 
              style="width: {{ $percentage }}%" 
              aria-valuenow="{{ $currentValue }}" 
              aria-valuemin="0" 
              aria-valuemax="{{ $maxValue }}">
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
      <div class="widget widget-card-four">
        <div class="widget-content">
          <div class="w-content">
            <div class="w-info">
              <h6 class="value">{{$currency}} {{$stats->pending_amount??''}}</h6>
              <p class="">Pending Orders</p>
            </div>
            <div class="">
              <div class="w-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
              </div>
            </div>
          </div>
          <div class="progress">
            @php
            $maxValue = 10000000;
            $pendingValue = $stats->pending_amount ?? 0;
            $pendingPercentage = $maxValue > 0 ? min(100, ($pendingValue / $maxValue) * 100) : 0;
            @endphp
            <div class="progress-bar bg-gradient-secondary" 
              role="progressbar" 
              style="width: {{ $pendingPercentage }}%" 
              aria-valuenow="{{ $pendingValue }}" 
              aria-valuemin="0" 
              aria-valuemax="{{ $maxValue }}"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
      <div class="widget widget-card-four">
        <div class="widget-content">
          <div class="w-content">
            <div class="w-info">
              <h6 class="value">{{$currency}} {{$stats->delivered_amount??''}}</h6>
              <p class="">Delivered Orders</p>
            </div>
            <div class="">
              <div class="w-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                  <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
              </div>
            </div>
          </div>
          <div class="progress">
            @php
            $maxValue = 1000000;
            $deliveredValue = $stats->delivered_amount ?? 0;
            $deliveredPercentage = $maxValue > 0 ? min(100, ($deliveredValue / $maxValue) * 100) : 0;
            @endphp
            <div class="progress-bar bg-gradient-secondary" 
              role="progressbar" 
              style="width: {{ $deliveredPercentage }}%" 
              aria-valuenow="{{ $deliveredValue }}" 
              aria-valuemin="0" 
              aria-valuemax="{{ $maxValue }}"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection