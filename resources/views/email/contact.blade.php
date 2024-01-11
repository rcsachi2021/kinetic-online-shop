<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contcat Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">

    <div class="card-body table-responsive p-3">
        
    <h5 class="card-title">You have received a contact mail</h5>
    <p class="card-text">Name: {{$mailData['name']}}</p>
    <p class="card-text">Email: {{$mailData['email']}}</p>
    <p class="card-text">Subject: {{$mailData['subject']}}</p>
    <p class="card-text">Message: {{$mailData['message']}}</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>