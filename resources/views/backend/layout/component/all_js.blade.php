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
             "pageLength": 10,
             "order": [[0, "desc"]] // optional
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

    @if(isset($data->country) && isset($data->state) && isset($data->city))
    getCities(@json($data->country),@json($data->state),@json($data->city));
    @endif
    function getCities(country_id,state_id,city=null) {
         $.ajax({
             url: "{{route('admin.getCities')}}",
             type: 'POST',
             data: {
                 'country_id': country_id,
                 'state_id': state_id,
                 'city': city,
                 _token: '{{csrf_token()}}'
             },
             cache: false,
             dataType: 'json',
             success: function(response) {
                 $('#city_list').html(response.html);
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

function setIsTrendingHotDeal(product_id,key){
var checked = $('#'+key+'_' + product_id).prop('checked');

$.ajax({
        url: "{{ route('admin.setIsTrendingHotDeal') }}",
        type: 'POST',
        data: {
            checked: checked,
            product_id: product_id,
            key:key,
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

function broadCastNotification(id,start,limit) {
    $.ajax({
        url: "{{route('admin.push-notification')}}",
        type: "POST",
        data: { 
            id: id,
            start:start,
            limit:limit,
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
                    broadCastNotification(nid, nstart, nlimit);
                }, 300);
            } else {
               window.location.href = "{{route('admin.notification-list')}}";
            }
        },
        error: function (xhr, status, error) {
            console.error("Notification send failed:", error);
        }
    });
}

function viewAssigned(id) {
    if (id == "0") {
        window.location.href = "{{ route('admin.assign-menu') }}";
    } else {
        window.location.href = "{{ url('admin/assign-menu') }}?id=" + id;
    }
}

$(document).ready(function () {
    const admin = $("#admin").val();

    // Handle Select All checkbox
    $("#readcheckAll").off('change').on('change', function () {
        if (admin == "0") {
            toastr.error("Please Select An Admin");
            $(this).prop("checked", false);
            return;
        }

        $(".read-checkbox").prop('checked', $(this).prop('checked'));
        updateAssignedMenus();
    });

    // Handle individual checkbox changes
    $(".read-checkbox").off('change').on('change', function () {
        if (admin == "0") {
            toastr.error("Please Select An Admin");
            $(this).prop("checked", false);
            return;
        }

        if (!$(this).prop("checked")) {
            $("#readcheckAll").prop("checked", false);
        }

        updateAssignedMenus();
    });

    // Function to send AJAX update
    function updateAssignedMenus() {
        const menus = $(".read-checkbox:checked").map(function () {
            return $(this).val();
        }).get();

        $.ajax({
            url: "{{ route('admin.assignmenu') }}",
            type: "POST",
            data: {
                admin: admin,
                menus: menus,
                _token: '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function (response) {
                Snackbar.show({
                    text: response.message,
                    pos: 'top-right',
                    actionTextColor: '#fff',
                    backgroundColor: response.status ? '#8dbf42' : '#e7515a'
                });
            }
        });
    }
});

 function del_faq(a) {
    $('#faq_' + a).remove(); 
    $('#bt_' + a).remove(); 
  }



$(document).ready(function () {
    var maxFieldLimit = 10; // Input fields increment limitation
    var addMoreButton = $('.add_button'); // Add button selector
    var fieldWrapper = $('.input_field_wrapper'); // Input field wrapper
    var x = $('.faq_block').length || 1; // Initial field count

    var getFieldHTML = function () {
        return `
            <div class="faq_block">
                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-form-label">Question</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="faq_question[]" placeholder="Question">
                    </div>
                </div>
                <div class="form-group row mb-3">
                    <label class="col-sm-2 col-form-label">Answer</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="faq_answer[]" placeholder="Answer">
                    </div>
                </div>
                <a href="javascript:void(0);" class="remove_button btn btn-danger btn-sm" title="Remove field">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="feather feather-activity">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </a>
                <hr>
            </div>`;
    };

    addMoreButton.on('click', function () {
        if (x < maxFieldLimit) {
            x++;
            fieldWrapper.append(getFieldHTML());
        }
    });

    fieldWrapper.on('click', '.remove_button', function (e) {
        e.preventDefault();
        $(this).closest('.faq_block').remove();
        x--;
    });
});


function changeOrderStatus(val, id,customer_id,el) {
    if (val === 'delivered' || val === 'cancelled') {
        $(el).prop('disabled', true);
    }
         $.ajax({
             url: "{{route('admin.changeOrderStatus')}}",
             type: 'POST',
             data: {
                 'status': val,
                 'id': id,
                 'customer_id': customer_id,
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
                     
                     //window.location.reload();
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

     function deleteImage(image,product_id) {
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
                     url: "{{ route('admin.deleteImage') }}",
                     type: 'POST',
                     data: {
                         image: image,
                         product_id: product_id,
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
                             $('#img_'+product_id).remove();
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
</script>

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->