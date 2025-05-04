$(document).ready(function() {
    $(".numbersOnlyInput").on("keydown", function(event) {
        // Allow: backspace, delete, tab, escape, enter, and numbers
        if (
            event.key === "Backspace" ||
            event.key === "Delete" ||
            event.key === "Tab" ||
            event.key === "Escape" ||
            event.key === "Enter" ||
            (event.key > "0" && event.key <= "9")
        ) {
            // Allow the key press
        } else {
            // Prevent the key press
            event.preventDefault();
        }
    });
});

$(document).ready(function() {
    $(".numbersWithZeroOnlyInput").on("keydown", function(event) {
        // Allow: backspace, delete, tab, escape, enter, and numbers
        if (
            event.key === "Backspace" ||
            event.key === "Delete" ||
            event.key === "Tab" ||
            event.key === "Escape" ||
            event.key === "Enter" ||
            (event.key >= "0" && event.key <= "9")
        ) {
            // Allow the key press
        } else {
            // Prevent the key press
            event.preventDefault();
        }
    });
});

$(document).ready(function() {
    $(".restrictedInput").on("keypress", function(event) {
        var keyCode = event.which;
        var inputChar = String.fromCharCode(keyCode);

        // Allow spaces and letters (a-zA-Z)
        if (!/[a-zA-Z\s]/.test(inputChar)) {
            event.preventDefault();
        }
    });
});

$(document).ready(function() {
    $('.emailInput').on('input', function() {
        var inputValue = $(this).val();
        var emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/;

        if (!emailPattern.test(inputValue)) {
            $(this).css('border-color', 'red');
        } else {
            $(this).css('border-color', ''); // Reset border color if valid
        }
    });
});

$('.notzero').keyup(function(e) {
    if ($(this).val().match(/^0/)) {
        $(this).val('');
        return false;
    }
});

jQuery(document).ready(function($) {
    'use strict';
    $('.numbersOnly').keyup(function() {
        this.value = this.value.replace(/[^0-9]+$/g, '');
    });
    $('.lettersOnly').keyup(function() {
        this.value = this.value.replace(/[^a-zA-Z\s]+$/g, '');
    });
    $('.ucwords').keyup(function() {
        var vall = this.value.replace(/[^A-Za-z0-9 ]+$/g, '');
        this.value = capitalizeFirstLetters(vall);
    });
    $('.alphanum').keyup(function() {
        this.value = this.value.replace(/[^0-9a-zA-Z\s]+$/g, '');
    });
    $('.uppercase').keyup(function() {
        this.value = this.value.replace(/[^A-Za-z0-9 ]+$/g, '').toUpperCase();
    });
    $('.lowercase').keyup(function() {
        this.value = this.value.replace(/[^A-Za-z0-9 ]+$/g, '').toLowerCase();
    });
    $('.emailOnly').keyup(function() {
        this.value = this.value.replace(/[^@_a-zA-Z0-9\.]/g, '');
    });

    $('.address').keyup(function() {
        this.value = this.value.replace(/[^0-9a-zA-Z.\s]+$/g, '');
    });

});
/*end function script*/
function capitalizeFirstLetters(str) {
    return str.toLowerCase().replace(/^\w|\s\w/g, function(letter) {
        return letter.toUpperCase();
    })
}

$(document).ready(function() {
    $('#datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        orientation: "bottom",
        todayHighlight: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        startDate: '+0d',
    });
    $('#datepicker1').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        orientation: "bottom",
        todayHighlight: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        startDate: '+0d',
    });
    $('#datepicker2').datepicker({
        dateFormat: 'yyyy-mm-dd',
        autoclose: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        todayHighlight: true,
        endDate: '+0d',
        orientation: "bottom",
    });

});

var ss = $(".select2").select2({
    tags: true,
});

function validate_from() {
    var max = $("#to_date").val();
    $("#from_date").attr("max", max);
}

function validate_to() {
    var min = $("#from_date").val();
    $("#to_date").attr("min", min);
    $("#to_date").val(min);
}

function remove(id, table) {
    Swal.fire({
        title: "Are you sure?",
        text: "It Will Delete the Row!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteRecord(id, table)
        }
    });
}


// toastr.options.timeOut = 500;