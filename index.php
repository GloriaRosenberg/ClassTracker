<html lang="en">
    <head>
        <title>Class Tracker</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/mobile.css" media="screen and (max-width: 480px)">
        <link rel="stylesheet" href="css/desktop.css" media="screen and (min-width: 900px)">
        <link rel="stylesheet" href="css/narrowDesktop.css" media="screen and (min-width: 481px) and (max-width: 899px)">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <?php
        include 'header.html';
        ?>
        <div class="main">
            <div class="flexContainer vertical gap10" id="mainContainer">
                <h2 class="bluePurple subtitle">Welcome to</h2>
                <h1 class="flexContainer title">
                    <span class="red">C</span>
                    <span class="paleBlue">l</span>
                    <span class="green">a</span>
                    <span class="yellow">s</span>
                    <span class="blue">s</span>
                    <span class="palePurple">T</span>
                    <span class="pink">r</span>
                    <span class="bluePurple">a</span>
                    <span class="red">c</span>
                    <span class="paleBlue">k</span>
                    <span class="yellow">e</span>
                    <span class="blue">r</span>
                </h1>
                <h2 class="subtitle blue">A tool for teachers</h2>
                <div class="flexContainer" id="bigButtonsContainer">
                    <a href="calendar.php" class="bigButton blueBack dotted">My calendar</a>
                    <a href="classLogs.php" class="bigButton yellowBack dotted">My class logs</a>
                    <a href="finances.php?thisMonth=" class="bigButton greenBack dotted">My financial stats</a>
                </div>
            </div>
        </div>
    </body>
    <?php
    include 'footer.html';
    ?>
</html>