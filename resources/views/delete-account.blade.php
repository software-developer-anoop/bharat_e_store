<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Delete Account</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  <body class="bg-light py-5">
    <div class="container">
      <div class="card shadow-sm mx-auto" style="max-width: 600px;">
        <div class="card-body">
          <h1 class="text-danger text-center mb-4">Delete Account</h1>
          <p class="text-muted">
            We're sorry to see you go. Deleting your account is a permanent action and cannot be undone. All your data, including profile information, posts, and other related data, will be permanently erased.
          </p>
          <p class="text-danger fw-bold">
            Please note: This action is irreversible.
          </p>
          <form action="{{ route('delete.account') }}" method="post">
            @csrf
            <!-- Country Dropdown -->
            <div class="mb-3">
              <label for="country" class="form-label">Please Select Country</label>
              <select class="form-select" id="country" name="country" onchange="showFields(this.value)">
                <option value="">Select Option</option>
                @if(!empty($countries))
                @foreach($countries as $country)
                <option value="{{ $country->country_name }}">{{ $country->country_name }}</option>
                @endforeach
                @endif
              </select>
            </div>
            <!-- Email Input -->
            <div class="mb-3 d-none" id="email-group">
              <label for="confirm-email" class="form-label">Enter your email address to confirm:</label>
              <input type="email" id="confirm-email" name="email" class="form-control" placeholder="Email Address">
            </div>
            <!-- Mobile Number Input -->
            <div class="mb-3 d-none" id="mobile-group">
              <label for="confirm-mobile" class="form-label">Enter your mobile number to confirm:</label>
              <input type="tel" id="confirm-mobile" name="mobile" class="form-control" placeholder="Mobile Number" pattern="[0-9]{10}">
              <div class="form-text">Please enter your 10-digit mobile number.</div>
            </div>
            <button type="submit" class="btn btn-danger" onclick="return confirmation()">
            Delete Account
            </button>
            <!-- Delete Button -->
            <div class="d-grid">
              <script>
                function confirmation() {
                  return confirm('Are you sure you want to delete your account?');
                }
              </script>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- jQuery (must be before Bootstrap if using Bootstrap JS that relies on it) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      function showFields(val) {
        if (val === "India") {
          $('#mobile-group').removeClass('d-none');
          $('#confirm-mobile').prop('required', true);
      
          $('#email-group').addClass('d-none');
          $('#confirm-email').prop('required', false);
        } else if (val !== "") {
          $('#email-group').removeClass('d-none');
          $('#confirm-email').prop('required', true);
      
          $('#mobile-group').addClass('d-none');
          $('#confirm-mobile').prop('required', false);
        } else {
          // Hide both if no option selected
          $('#email-group, #mobile-group').addClass('d-none');
          $('#confirm-email, #confirm-mobile').prop('required', false);
        }
      }
      
      
      function confirmation() {
      return confirm('Are you sure you want to delete your account?');
      }
      
    </script>
    @if (session('error'))
    <script>
      alert("{{ session('error') }}");
    </script>
    @endif
    @if (session('success'))
    <script>
      alert("{{ session('success') }}");
    </script>
    @endif
  </body>
</html>