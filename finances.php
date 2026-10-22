<!DOCTYPE HTML>
<html>
    <head>
        <title>My Financial Stats | Class Tracker</title>
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/mobile.css" media="screen and (max-width: 480px)">
        <link rel="stylesheet" href="css/desktop.css" media="screen and (min-width: 900px)">
        <link rel="stylesheet" href="css/narrowDesktop.css" media="screen and (min-width: 481px) and (max-width: 899px)">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <?php
        include 'header.html';
        include 'connect.php';
        $tables=array("ega_classes", "linguaviaje", "private_students", "amazing_talker");
        foreach($tables as $table){
            $query=$connection->stmt_init();
            $today=date("Y-m-d 00:00:00");
            $sql="SELECT SUM(hours_taught) FROM $table WHERE class_date < '$today'";
            if(isset($_REQUEST['thisMonth'])){
                $firstDay=date("Y-m-01 00:00:00");
                $lastDay=date("Y-m-d 00:00:00");
                $sql=$sql." AND class_date > '$firstDay'";
            } else if(isset($_REQUEST['thisYear'])){
                if (date('n') < 9){
                    $prev=date('Y')-1;
                    $firstDay=date("$prev-09-01 00:00:00");
                } else {
                    $firstDay=date("Y-09-01 00:00:00");
                }
                $sql=$sql." AND class_date > '$firstDay'";
            } else if(isset($_REQUEST['thisWeek'])){
                $subtract=date('w');
                $startOfWeek=date_create();
                date_sub($startOfWeek,date_interval_create_from_date_string("$subtract days"));
                $day=date_format($startOfWeek, "Y-m-d 00:00:00");
                $sql=$sql." AND class_date> '$day'";
            }
            $query->prepare($sql);
            try{
                $query->execute();
                $query->bind_result($totalHours);
                $query->fetch();
                if($totalHours > 0){
                    $hourTotals[$table]=$totalHours;
                } else {
                    $hourTotals[$table]=0;
                }
                $query->close();
            } catch (mysqli_sql_exception $e) {
                die("Error: ".$e->getMessage());
            }
        }
        $egaIncome=$hourTotals['ega_classes']*13;
        $lvIncome=$hourTotals['linguaviaje']*8;
        $atIncome=$hourTotals['amazing_talker']*12.5;

        $query=$connection->stmt_init();
        $sql="SELECT SUM(hours_taught*pay_rate) FROM private_students";
        if(isset($_REQUEST['thisMonth'])){
            $firstDay=date("Y-m-01 00:00:00");
            $lastDay=date("Y-m-t 00:00:00");
            $sql=$sql." WHERE class_date > '$firstDay' AND class_date <'$lastDay'";
        } else if(isset($_REQUEST['thisYear'])){
            if (date('n') < 9){
                $prev=date('Y')-1;
                $firstDay=date("$prev-09-01 00:00:00");
            } else {
                $firstDay=date("Y-09-01 00:00:00");
            }
            $sql=$sql." WHERE class_date > '$firstDay'";
        } else if(isset($_REQUEST['thisWeek'])){
            $subtract=date('w');
            $startOfWeek=date_create();
            date_sub($startOfWeek,date_interval_create_from_date_string("$subtract days"));
            $day=date_format($startOfWeek, "Y-m-d 00:00:00");
            $sql=$sql." WHERE class_date > '$day'";
        }
        $query->prepare($sql);
        try{
            $query->execute();
            $query->bind_result($privIncome);
            $query->fetch();
            $query->close();
        } catch (mysqli_sql_exception $e){
            die ("Error: ".$e->getMessage());
        }
        $totalIncome = $egaIncome + $lvIncome + $atIncome + $privIncome;
        
        $egaAngle=0;
        $lvAngle=0;
        $privAngle=0;
        $atAngle=0;
        if($totalIncome != 0){
            $egaAngle=round($egaIncome/$totalIncome*360);
            $lvAngle=round($lvIncome/$totalIncome*360);
            $privAngle=round($privIncome/$totalIncome*360);
            $atAngle=round($atIncome/$totalIncome*360);
        }
                

        ?>
        <div class="main">
            <div class="flexContainer vertical gap20" id="mainContainer">
                <h1 class="pink">My Financial Stats</h1>
                <form name="timePeriod" class="flexContainer wrap" method="GET" action="">
                    <div class="flexContainer">
                        <button type="submit" name="thisMonth" class="greyButton"
                        <?php
                        if(isset($_REQUEST['thisMonth'])){
                            echo "style='background-color: rgb(185,185,185);'";
                        }
                        ?>
                        >This Month</button>
                        <button type="submit" name="thisWeek" class="greyButton"
                        <?php
                        if(isset($_REQUEST['thisWeek'])){
                            echo "style='background-color: rgb(185,185,185);'";
                        }
                        ?>
                        >This Week</button>
                    </div>
                    <div class="flexContainer">
                        <button type="submit" name="thisYear" class="greyButton"
                        <?php
                        if(isset($_REQUEST['thisYear'])){
                            echo "style='background-color: rgb(185,185,185);'";
                        }
                        ?>
                        >This Academic Year</button>
                        <button type="submit" name="clear" class="greyButton"
                        <?php
                        if(isset($_REQUEST['clear']) || !(isset($_REQUEST['thisMonth']) 
                        || isset($_REQUEST['thisWeek']) || isset($_REQUEST['thisYear']))){
                            echo "style='background-color: rgb(185,185,185);'";
                        }
                        ?>
                        >All Time</button>
                    </div>
                </form>
                <div id="financialSummary">
                    <h3><strong>Total hours: </strong>
                        <?php
                        $allHours=0;
                        foreach($hourTotals as $total){
                            $allHours+=$total;
                        }
                        echo number_format($allHours, 2);
                        ?>
                    </h3>
                    <h3><strong>Total income: </strong>
                        <?php
                        echo "€".number_format($totalIncome, 2);
                        ?>
                    </h3>
                </div>
                <div class="flexContainer gap20">
                    <div id="pie" style="background-image: conic-gradient(#a968ff 0deg, #a968ff <?=$egaAngle?>deg, #009444 <?=$egaAngle?>deg, #009444 <?=$egaAngle+$lvAngle?>deg,
                    #0296f6 <?=$egaAngle+$lvAngle?>deg, #0296f6 <?=$egaAngle+$lvAngle+$privAngle?>deg, #e84643 <?=$egaAngle+$lvAngle+$privAngle?>deg, #e84643 360deg)"></div>
                    <div id="legendDiv">
                        <div>
                            <div class="legend purpleBack"></div>EGA - 
                            <?php
                            echo round($egaAngle/3.6)."%";
                            // echo " (€".number_format($egaIncome, 2).", ".number_format($hourTotals["ega_classes"],2)." hours)";
                            ?>
                            </div>
                        <div>
                            <div class="legend greenBack"></div>Linguaviaje - 
                            <?php
                            echo round($lvAngle/3.6)."%";
                            // echo " (€".number_format($lvIncome, 2).", ".number_format($hourTotals["linguaviaje"],2)." hours)";
                            ?>
                        </div>
                        <div>
                            <div class="legend blueBack"></div>Private Students - 
                            <?php
                            echo round($privAngle/3.6)."%";
                            // echo " (€".number_format($privIncome, 2).", ".number_format($hourTotals["private_students"],2)." hours)";
                            ?> 
                        </div>
                        <div>
                            <div class="legend redBack"></div>Amazing Talker - 
                            <?php
                            echo round($atAngle/3.6)."%";
                            // echo " (€".number_format($atIncome, 2).", ".number_format($hourTotals["amazing_talker"],2)." hours)";
                            ?>
                        </div>
                    </div>
                </div>
                <div class="flexContainer gap20">
                    <div class="flexContainer gap20">
                        <div class="tile purpleBack dotted">
                            <h3>EGA Classes</h3>
                            <p>Total hours: 
                                <?php
                                echo number_format($hourTotals["ega_classes"],2);
                                ?>
                            </p>
                            <p>Income: 
                                <?php
                                echo "€".number_format($egaIncome, 2);
                                ?>
                            </p>
                        </div>
                        <div class="tile greenBack dotted">
                            <h3>Linguaviaje</h3>
                            <p>Total hours:
                            <?php
                                echo number_format($hourTotals["linguaviaje"],2);
                            ?>
                            </p>
                            <p>Income: 
                                <?php
                                echo "€".number_format($lvIncome, 2);
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="flexContainer gap20">
                        <div class="tile blueBack dotted">
                            <h3>Private Students</h3>
                            <p>Total hours:
                                <?php
                                echo number_format($hourTotals["private_students"],2);
                                ?>
                            </p>
                            <p>Income: 
                                <?php
                                echo "€".number_format($privIncome, 2);
                                ?>
                            </p>
                        </div>
                        <div class="tile redBack dotted">
                            <h3>Amazing Talker</h3>
                            <p>Total hours:
                                <?php
                                echo number_format($hourTotals["amazing_talker"],2);
                                ?>
                            </p>
                            <p>Income:  
                                <?php
                                echo "€".number_format($atIncome, 2);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include 'footer.html';
        ?>
    </body>
</html>