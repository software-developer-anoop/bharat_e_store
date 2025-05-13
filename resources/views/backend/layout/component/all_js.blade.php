 </div>
    <!-- END MAIN CONTAINER -->

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    
    <script src="{{asset('assets/backend/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/backend/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('assets/backend/assets/js/app.js')}}"></script>
    <script>
        $(document).ready(function() {
            App.init();
        });
    </script>
    <script src="{{asset('assets/backend/assets/js/custom.js')}}"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{asset('assets/backend/plugins/apex/apexcharts.min.js')}}"></script>
    <script src="{{asset('assets/backend/assets/js/dashboard/dash_1.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/notification/snackbar/snackbar.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/table/datatable/datatables.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/select2/select2.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/dropify/dropify.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/blockui/jquery.blockUI.min.js')}}"></script>
    <script src="{{asset('assets/backend/plugins/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('assets/backend/assets/js/users/account-settings.js')}}"></script>
    <script src="{{asset('assets/backend/common.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
         $('#alter_pagination').DataTable({
             "pagingType": "full_numbers",
             "oLanguage": {
                 "oPaginate": {
                     "sFirst": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                     "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>',
                     "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
                     "sLast": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>'
                 },
                 "sInfo": "Showing page _PAGE_ of _PAGES_",
                 "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                 "sSearchPlaceholder": "Search...",
                 "sLengthMenu": "Results :  _MENU_",
             },
             "stripeClasses": [],
             "lengthMenu": [10, 20, 50, 100],
             "pageLength": 10
         });
     });

     function getSlug(val) {
         $.ajax({
             url: "{{route('admin.getSlug')}}",
             type: 'POST',
             data: {
                 'keyword': val,
                 _token: '{{csrf_token()}}'
             },
             cache: false,
             success: function(response) {
                 $('#page_slug').val(response);
                 $('#project_slug').val(response);
                 $('#service_slug').val(response);
             }
         });
     }

     function changeStatus(val, id, table) {
         $.ajax({
             url: "{{route('admin.changeStatus')}}",
             type: 'POST',
             data: {
                 'status': val,
                 'id': id,
                 'table': table,
                 _token: '{{csrf_token()}}'
             },
             dataType: 'json',
             cache: false,
             success: function(response) {
                 if (response.status) {
                     Snackbar.show({
                         text: response.msg,
                         pos: 'top-right',
                         actionTextColor: '#fff',
                         backgroundColor: '#8dbf42'
                     });
                     window.location.reload();
                 } else {
                     Snackbar.show({
                         text: response.msg,
                         pos: 'top-right',
                         actionTextColor: '#fff',
                         backgroundColor: '#e7515a'
                     });
                 }
             }
         });
     }

     function deleteItem(id, table) {
         Swal.fire({
             title: 'Are you sure?',
             text: "You won't be able to revert this!",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonText: 'Delete',
             cancelButtonText: 'Cancel',
             padding: '2em'
         }).then((result) => {
             if (result.isConfirmed) {
                 $.ajax({
                     url: "{{ route('admin.deleteItem') }}",
                     type: 'POST',
                     data: {
                         id: id,
                         table: table,
                         _token: '{{ csrf_token() }}'
                     },
                     dataType: 'json',
                     cache: false,
                     beforeSend: function() {
                         Swal.fire({
                             title: 'Processing...',
                             text: 'Please wait.',
                             allowOutsideClick: false,
                             showConfirmButton: false,
                             didOpen: () => {
                                 Swal.showLoading();
                             }
                         });
                     },
                     success: function(response) {
                         Swal.close(); // Close the loader
                         Snackbar.show({
                             text: response.msg,
                             pos: 'top-right',
                             actionTextColor: '#fff',
                             backgroundColor: response.status ? '#8dbf42' : '#e7515a'
                         });

                         if (response.status) {
                             $('#del_'+id).remove();
                         }
                     },
                     error: function() {
                         Swal.close();
                         Snackbar.show({
                             text: 'Something went wrong.',
                             pos: 'top-right',
                             actionTextColor: '#fff',
                             backgroundColor: '#e7515a'
                         });
                     }
                 });
             }
         });
     }

     document.querySelectorAll('.editor').forEach(editorElement => {
         ClassicEditor
             .create(editorElement, {
                 toolbar: [
                     'heading', '|',
                     'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                     'blockQuote', 'undo', 'redo'
                 ]
             })
             .then(editor => {
                 editor.ui.view.editable.element.style.height = '300px';
             })
             .catch(error => {
                 console.error(error);
             });
     });

    [...document.getElementsByClassName('datepicker')].forEach(el => {
      flatpickr(el, {
        maxDate: "today" // prevents selecting any date after today
      });
    });

    @if(isset($data->country) && isset($data->state))
    getStates(@json($data->country),@json($data->state));
    @endif
    function getStates(country_id,state=null) {
         $.ajax({
             url: "{{route('admin.getStates')}}",
             type: 'POST',
             data: {
                 'country_id': country_id,
                 'state': state,
                 _token: '{{csrf_token()}}'
             },
             cache: false,
             dataType: 'json',
             success: function(response) {
                 $('#state_list').html(response.html);
             }
         });
     }

    @if(isset($data->category_id) && isset($data->subcategory_id))
    getSubcategory(@json($data->category_id), @json($data->subcategory_id));
    @endif

    function getSubcategory(category_id, subcategory_id = null) {
        $.ajax({
            url: "{{ route('admin.getSubcategory') }}",
            type: 'POST',
            data: {
                category_id: category_id,
                subcategory_id: subcategory_id,
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',
            success: function(response) {
                $('#subcategory_list').html(response.html);
            }
        });
    }

   function manageInventory(condition, product_id) {
    $.ajax({
        url: "{{ route('admin.manageInventory') }}",
        type: 'POST',
        data: {
            condition: condition,
            product_id: product_id,
            _token: '{{ csrf_token() }}'
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            Snackbar.show({
                text: response.msg,
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: response.status ? '#8dbf42' : '#e7515a'
            });
        }
    });
}

// Attach event listeners to all increment and decrement buttons
document.querySelectorAll('.quantity-wrapper').forEach(wrapper => {
    const product_id = wrapper.getAttribute('data-product-id');
    const input = wrapper.querySelector('.quantity');
    const incrementBtn = wrapper.querySelector('.increment');
    const decrementBtn = wrapper.querySelector('.decrement');

    incrementBtn.addEventListener('click', function () {
        let current = parseInt(input.value) || 0;
        current += 1;
        input.value = current;
        manageInventory('increment', product_id);
    });

    decrementBtn.addEventListener('click', function () {
        let current = parseInt(input.value) || 1;
        if (current > 1) {
            current -= 1;
            input.value = current;
            manageInventory('decrement', product_id);
        }
    });
});

function setIsTrending(product_id){
var checked = $('#check_' + product_id).prop('checked');
$.ajax({
        url: "{{ route('admin.setIsTrending') }}",
        type: 'POST',
        data: {
            checked: checked,
            product_id: product_id,
            _token: '{{ csrf_token() }}'
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            Snackbar.show({
                text: response.msg,
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: response.status ? '#8dbf42' : '#e7515a'
            });
        }
    });
}

function broadCastNotification(id) {
    $.ajax({
        url: "{{route('admin.push-notification')}}",
        type: "POST",
        data: { 
            id: id,
            _token: '{{ csrf_token() }}'
        },
        async: true,
        crossDomain: true,
        dataType: "json",
        success: function (obj) {
            if (obj.status) {
                var nid = obj.id;
                var nstart = obj.start;
                var nlimit = obj.limit;
                setTimeout(function () {
                    sendNotifications(nid);
                }, 300);
            } else {
               // window.location.href = "{{route('admin.notification-list')}}";
            }
        },
        error: function (xhr, status, error) {
            console.error("Notification send failed:", error);
        }
    });
}

</script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->