<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Class Logs</title>
        <link rel="stylesheet" href="css/mobile.css" media="screen and (max-width: 480px)">
        <link rel="stylesheet" href="css/desktop.css" media="screen and (min-width: 900px)">
        <link rel="stylesheet" href="css/narrowDesktop.css" media="screen and (min-width: 481px) and (max-width: 899px)">
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/dataScripts.js"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Chewy&family=Coming+Soon&family=Molle:ital@1&family=Titan+One&display=swap');
        </style> 
    </head>
    <body>
        <?php
        $name=$_REQUEST['groupName'];
        $table=$_REQUEST['category'];
        ?>
        <form name="details">
            <input type="hidden" name="name" value='<?=$name?>'>
            <input type="hidden" name="table" value="<?=$table?>">
        </form>
        <div class="main">
            <div class="flexContainer singleColFlex marginTop">
                <h1 id="groupName"><?=$name?></h1>
                <form method="get" action="" name="dateFilter">
                    <input type="hidden" name="groupName" value='<?=$name?>'>
                    <input type="hidden" name="category" value="<?=$table?>">
                    <fieldset name="filters">
                        <legend>Filter by date</legend>
                        <label>Start date:  
                            <input type="datetime-local" name="startDate">
                        </label>
                        <label>End date:   
                            <input type="datetime-local" name="endDate">
                        </label>
                        <div>
                            <button type="submit" name="applyFilters" class="aquaBack">Apply filters</button>
                            <button type="button" name="clearFilters" class="aquaBack">Clear filters</button>
                        </div>
                    </fieldset>
                </form>
                <div>
                    <a class="button greenBack" id="linkAdd">Add Entry</a>
                    <a class="button bluePurpleBack" id="linkModify">Modify entry</a>
                    <a class="button redBack" id="linkRemove">Remove entry</a>
                </div>
                <form name="actionForm" class="flexContainer singleColFlex" action='#' method='get'></form>
                <table id="lessonDetails">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Hours</th>
                            <th>Content</th>
                            <th>Attendance</th>
                            <th>Student Notes</th>
                            <th>Future Notes</th>
                            <?php
                            if($table=='private_students'){
                                ?>
                                <th id="pay_rate">Earnings</th>
                                <th>Pay Date</th>
                                <?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include "connect.php";
                        $connection=connect("localhost", "root", "", "class_logs");
                        $cols='class_date, hours_taught, content, attendance, student_notes, future_notes';
                        if($table=='private_students'){
                            $cols=$cols.', pay_rate, pay_date';
                        }
                        $sql="SELECT $cols from $table WHERE name='$name'";
                        if(isset($_REQUEST['startDate']) && $_REQUEST['startDate']!=""){
                            $start_date=$_REQUEST['startDate'];
                            $sql=$sql." AND class_date > '$start_date'";
                        }
                        if(isset($_REQUEST['endDate']) && $_REQUEST['endDate']!=""){
                            $end_date=$_REQUEST['endDate'];
                            $sql=$sql." AND class_date < '$end_date'";
                        }
                        $sql=$sql." ORDER BY class_date";  
                        $query=$connection->stmt_init();
                        $query->prepare($sql);
                        try{
                            $query->execute();
                            if($table=='private_students'){
                                $query->bind_result($date, $hours, $content, $attendance, $s_notes, $f_notes, $pay_rate, $pay_date);
                            } else {
                                $query->bind_result($date, $hours, $content, $attendance, $s_notes, $f_notes);
                            }
                            $unpaid=0;
                            while($query->fetch()){
                                if($date > date("Y-m-d H:i:s")){
                                    echo "<tr style='color:gray; background-color: #e5e5e5'>";
                                } else {
                                    echo "<tr>";
                                }
                                ?>
                                    <td class="lessonDate"><?=$date?></td>
                                    <td class="hours"><?=$hours?></td>
                                    <td class="content"><?=htmlspecialchars($content)?></td>
                                    <td class="attendance"><?=htmlspecialchars($attendance)?></td>
                                    <td class="sNotes"><?=htmlspecialchars($s_notes)?></td>
                                    <td class="fNotes"><?=htmlspecialchars($f_notes)?></td>
                                    <?php
                                    if($table=='private_students'){
                                        ?>
                                        <td class="payRate"><?=$pay_rate*$hours?></td>
                                        <?php
                                        if($pay_date=="0000-00-00"||$pay_date==""){
                                            $unpaid+=1;
                                            ?>
                                            <td class="payDate">Not yet paid</td>
                                            <?php
                                        }else{
                                            ?>
                                            <td class="payDate"><?=$pay_date?></td>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tr>
                                <?php
                            }
                            $query->close();
                        } catch (mysqli_sql_exception $e){
                            die("ERROR: ".$e->get_message());
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total Hours</td>
                            <?php
                            $query1=$connection->stmt_init();
                            $sql="SELECT SUM(hours_taught) FROM $table WHERE name='$name'";
                            $query1->prepare($sql);
                            try{
                                $query1->execute();
                                $query1->bind_result($total_hours);
                                $query1->fetch();
                                ?>
                                <td><?=$total_hours?></td>
                                <?php
                            }catch(msqli_sql_exception $e){
                                die("Error: ".$e->get_message());
                            }
                            $query1->close();
                            echo "<td>Total Lessons</td>";
                            $query2=$connection->stmt_init();
                            $sql="SELECT COUNT(class_date) FROM $table WHERE name='$name'";
                            $query2->prepare($sql);
                            try{
                                $query2->execute();
                                $query2->bind_result($total_lessons);
                                $query2->fetch();
                                echo "<td>$total_lessons</td>";
                            }catch(mysqli_sql_exception $e){
                                die("Error: ".$e->get_message());
                            }
                            $query2->close();
                            if($table=="private_students"){
                                echo "<td>Unpaid lessons</td>";
                                echo "<td>$unpaid</td>";
                                echo "<td>Amount owed</td>";
                                $query3=$connection->stmt_init();
                                $sql="SELECT SUM(pay_rate*hours_taught) 
                                FROM private_students 
                                WHERE name='$name' 
                                AND (pay_date IS NULL OR pay_date='0000-00-00')";
                                $query3->prepare($sql);
                                try{
                                    $query3->execute();
                                    $query3->bind_result($amount_owed);
                                    $query3->fetch();
                                    echo "<td>€".round($amount_owed*100)/100 ."</td>";
                                } catch(mysqli_sql_exception $e){
                                    die($e->get_message());
                                }
                                $query3->close();
                                $connection->close();
                            } else {
                                echo '<td colspan="2"> </td>';
                            }
                            ?>
                        </tr>
                    </tfoot>
                </table>
                <table id="mobileSummary">
                    <thead>
                        <tr>
                            <th>Total Hours</th>
                            <th>Total Lessons</th>
                            <?php
                            if($table=="private_students"){
                                echo "<th>Unpaid Lessons</th>";
                                echo "<th>Amount owed</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?=$total_hours?></td>
                            <td><?=$total_lessons?></td>
                            <?php
                            if($table=="private_students"){
                                echo "<td>$unpaid</td>";
                                echo "<td>€".round($amount_owed*100)/100 ."</td>";
                            }
                            ?>
                        </tr>
                    </tbody>
                </table>
                <a class="fontButton wide90" href="index.php">Back</a>
            </div>
        </div>
    </body>
    <?php
    include 'footer.html';
    ?>
</html>