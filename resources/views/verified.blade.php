<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
            <meta name="description" content="" />
            <meta name="author" content="" />
            <title>Exam Generator</title>
            <link rel="icon" type="image/x-icon" href="assets/logo.png" />
            <!-- Font Awesome icons (free version)-->
            <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css" rel="stylesheet" />
            <!-- Google fonts-->
            <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet" />
            <link href="https://fonts.googleapis.com/css?family=Catamaran:100,200,300,400,500,600,700,800,900" rel="stylesheet" />
            <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet" />
            <!-- Plugin CSS-->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/html5-device-mockups/3.2.1/dist/device-mockups.min.css" rel="stylesheet" />
            <!-- Core theme CSS (includes Bootstrap)-->
            <link href="assets/css/animation.css" rel="stylesheet" />
        </head>
        <SCRIPT type="text/javascript">
            function JavaBlink() {
               var blinks = document.getElementsByTagName('JavaBlink');
               for (var i = blinks.length - 1; i >= 0; i--) {
                  var s = blinks[i];
                  s.style.visibility = (s.style.visibility === 'visible') ? 'hidden' : 'visible';
               }
               window.setTimeout(JavaBlink, 900);
            }
            if (document.addEventListener) document.addEventListener("DOMContentLoaded", JavaBlink, false);
            else if (window.addEventListener) window.addEventListener("load", JavaBlink, false);
            else if (window.attachEvent) window.attachEvent("onload", JavaBlink);
            else window.onload = JavaBlink;
          </SCRIPT>
        <style>
            body{
                background-color: white;
                font-size:15px;
                font-family:Arial, Helvetica, sans-serif 
                /* background-image: url(assets/img/success.gif) */
            }
        </style>
        <body>
            <br> <br> <br> <br> <br>
            <center>
            <img src="{{asset('assets/img/success3.gif')}}" width="350" height="300">
            <JavaBlink><h1>Your Email is Verified</h1></JavaBlink>
            <br>
            <h3> Please Return to Login Page </h3>
            </center>
        </body>
    </html>