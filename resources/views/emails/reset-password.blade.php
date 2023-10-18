<!DOCTYPE html>
<html lang="en">

<head>
    <title>Web Carbon</title>
</head>

<body>
    {{-- Generate email to reset password with style --}}
    <div style="background-color: #f5f5f5; padding: 20px;">
        <div style="background-color: #fff; padding: 20px; border-radius: 5px;">
            <h1 style="text-align: center;">Web Carbon</h1>
            <p style="text-align: center;">You are receiving this email because we received a password reset request for
                your account.</p>
            <p style="text-align: center;">Click the button below to reset your password:</p>
            <div style="text-align: center;">
                <a href="{{ $url }}"
                    style="background-color: #007bff; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Reset
                    Password</a>
            </div>
            <p style="text-align: center;">If you did not request a password reset, no further action is required.</p>
        </div>
</body>

</html>
