<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact Email</title>
</head>
<body style="font-family: Arial,Helvetica,'Sakkal Majalla'; font-size: 16px;">
<p>
    Name:{{$mailData['name']}}
</p>
<p>
    Email:{{$mailData['email']}}
</p>
<p>
    Subject:{{$mailData['subject']}}
</p>
<p>
    Message:{{$mailData['message']}}
</p>


</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Contact Us</h1>
<p>We'd love to hear from you! Fill out the form below and we'll get back to you as soon soon as possible.</p>
<form action="#" method="post">
    <div class="form-group">
        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required>
    </div>
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="5" required></textarea>
    </div>
    <button type="submit">Send Message</button>
</form>
</body>
</html>
