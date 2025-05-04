@include('backend.layout.component.meta')
@include('backend.layout.component.all_css')
@include('backend.layout.component.navbar')
@include('backend.layout.component.header')
@include('backend.layout.component.sidebar')
<div id="content" class="main-content">
@yield('content')
</div>
@include('backend.layout.component.all_js')
@include('backend.layout.component.footer')
