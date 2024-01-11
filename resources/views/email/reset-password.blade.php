<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">

    <div class="card-body table-responsive p-3">
        
    <h2 class="card-title">You have requested to change password</h2>
    <p>Hello {{$mailData['user']->name}},</p>
    <p class="card-text">Please click the link given below to reset password</p>
    <p class="card-text"><a href="{{route('front.resetPassword', $mailData['token'])}}">Click Here</a></p>
   </div>
   <p>Thank You</p>
   Admin
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>