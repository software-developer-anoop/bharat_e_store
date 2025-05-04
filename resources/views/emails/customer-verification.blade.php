<!DOCTYPE html>
<html>
<head>
    <title>Customer Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT3zWl5yFfZC8xIW+zJw6k5g" crossorigin="anonymous">
</head>
<body>
    <h1>Hello {{ $data['name'] }}</h1>
    <p>Thank you for registering. Please verify your email by clicking the following link:</p>
    <h2 class="btn btn-primary">
    <a href="{{ url('/verify-email') }}?id={{ $data['id'] }}&status={{ $data['status'] }}" style="color: white; text-decoration: none;">
        Click to verify your account
    </a>
    </h2>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4+o+zY1U2n5D1Q09/0q7ClQ+AZf4W4fA5f5Zl3E+PZ2r0YI14BDDEIxhN" crossorigin="anonymous"></script>
</body>
</html>
