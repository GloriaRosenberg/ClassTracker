<!DOCTYPE HTML>
<html>
    <head>
        <title>My Calendar | Class Tracker</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
        <link rel="stylesheet" href="css/mobile.css" media="screen and (max-width: 480px)">
        <link rel="stylesheet" href="css/desktop.css" media="screen and (min-width: 900px)">
        <link rel="stylesheet" href="css/narrowDesktop.css" media="screen and (min-width: 481px) and (max-width: 899px)">
        <link rel="stylesheet" href="css/styles.css">
    <body>
        <?php
        include 'header.html';
        ?>
        <div class="main">
            <div class="flexContainer vertical gap10" id="mainContainer">
                <!-- <h1 class="subtitle">Calendar will appear here</h1> -->
                <div id="calendar">
                <iframe src="https://calendar.google.com/calendar/embed?height=600&wkst=1&ctz=Europe%2FMadrid&showPrint=0&src=Z2xvcmJlcmcwNUBnbWFpbC5jb20&src=Y2JvbGg2aTczcTNlZnAyNWVicm1lZWF2aDltbjg3ZXFAaW1wb3J0LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23039be5&color=%234285f4" style="border:solid 1px #777" width="800" height="600" frameborder="0" scrolling="no"></iframe>
                </div>
            </div>
        </div>
        <?php
        include 'footer.html';
        ?>
    </body>
</html>