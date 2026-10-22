<?php
setcookie('category', $_REQUEST['category'], time() + 3600);
setcookie('groupName', $_REQUEST['groupName'], time() + 3600);
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
        <title><?=$_REQUEST['groupName']?> - Class Records | Class Tracker</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/mobile.css" media="screen and (max-width: 480px)">
        <link rel="stylesheet" href="css/desktop.css" media="screen and (min-width: 900px)">
        <link rel="stylesheet" href="css/narrowDesktop.css" media="screen and (min-width: 481px) and (max-width: 899px)">
        <link rel="stylesheet" href="css/styles.css">
        <script src="js/dataScripts.js"></script>
    </head>
    <body>
        <?php
        include 'header.html';
        $name=$_REQUEST['groupName'];
        $table=$_REQUEST['category'];
        $groupNames=array("ega_classes"=>"EGA", "linguaviaje"=>"Linguaviaje",
        "private_students"=>"Private", "amazing_talker"=>"Amazing Talker");
        ?>
        <form name="details">
            <input type="hidden" name="name" value='<?=$name?>'>
            <input type="hidden" name="table" value="<?=$table?>">
        </form>
        <div class="main">
            <div class="flexContainer vertical gap10" id="mainContainer">
                <h1 id="groupName">
                    <?php
                    if($name=='all'){
                        echo "All ".$groupNames[$table]." Classes";
                    } else {
                        echo $name;
                    }
                    ?>
                </h1>
                <div id="collapsibleContainer">    
                    <div id="collapsibleLabels">
                        <div class="paleBlueBack" id="filterToggle">Filter by date</div>
                        <div class="redBack" id="linksToggle">Links</div>
                    </div>
                    <div id="filterCollapsible" class="collapsible paleBlueBack">
                        <form method="get" action="" name="dateFilter" class="flexContainer vertical gap10">
                            <input type="hidden" name="groupName" value='<?=$name?>'>
                            <input type="hidden" name="category" value="<?=$table?>">
                            <label>Start date:  
                                <input type="datetime-local" name="startDate"
                                <?php
                                if(isset($_REQUEST['startDate'])){
                                    echo "value='".$_REQUEST['startDate']."'";
                                }
                                ?>
                                >
                            </label>
                            <label>End date:   
                                <input type="datetime-local" name="endDate"
                                <?php
                                if(isset($_REQUEST['endDate'])){
                                    echo "value='".$_REQUEST['endDate']."'";
                                }
                                ?>
                                >
                            </label>
                            <div>
                                <button type="submit" name="applyFilters" class="aquaBack smallButton">Apply</button>
                                <button type="button" name="clearFilters" class="aquaBack smallButton">Reset</button>
                                <button type="button" name="thisMonth" class="specialButton smallButton yellowBack">This Month</button>
                            </div>
                        </form>
                    </div>
                    <div id="linksCollapsible" class="collapsible redBack">
                        <?php
                        include "connect.php";
                        $stmt=$connection->stmt_init();
                        $sql="SELECT description, url FROM links WHERE group_name=? ORDER BY description desc";
                        $stmt->prepare($sql);
                        $stmt->bind_param('s', $name);
                        try{
                            $stmt->execute();
                            $stmt->bind_result($desc, $url);
                            while($stmt->fetch()){
                                echo "<p>".$desc.": <a href='".$url."' target='_BLANK'>link</a></p>";
                            }
                        }catch(mysqli_sql_exception $e){
                            echo $e->getMessage();
                        }
                        ?>
                        <form name="addLink">
                            <fieldset>
                                <legend>Add a link</legend>
                                <label>Description: 
                                    <input type="text" name="linkDescription">
                                </label>
                                <label>URL: 
                                    <input type="text" name="linkURL">
                                </label>
                                <button class="whiteBack" type="button" name="sendLink">ADD</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div>
                    <a class="button greenBack" id="linkAdd">Add Entry</a>
                </div>
                <form name="actionForm" class="flexContainer vertical" action='#' method='get'></form>
                
                <table id="lessonDetails">
                    <thead>
                        <tr>
                            <?php
                            if($name=="all"){
                                echo "<th>Name</th>";
                            }
                            ?>
                            <th>Date & Time</th>
                            <th>Hours</th>
                            <th>Content</th>
                            <th>Attendance</th>
                            <th>Student Notes</th>
                            <th>Future Notes</th>
                            <?php
                            if($table=='private_students'){
                                ?>
                                <th id="pay_rate">Pay Rate</th>
                                <th>Pay Date</th>
                                <?php
                            }
                            ?>
                            <th id="buttonsHeader">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $cols='name, class_date, hours_taught, content, attendance, student_notes, future_notes';
                        if($table=='private_students'){
                            $cols=$cols.', pay_rate, pay_date';
                        }
                        $sql="SELECT $cols from $table WHERE 1";
                        if($name!='all'){
                            $sql=$sql." AND name='$name'";
                        }
                        if(isset($_REQUEST['startDate']) && $_REQUEST['startDate']!=""){
                            $start_date=$_REQUEST['startDate'];
                            $sql=$sql." AND class_date > '$start_date'";
                        }
                        if(isset($_REQUEST['endDate']) && $_REQUEST['endDate']!=""){
                            $end_date=$_REQUEST['endDate'];
                            $sql=$sql." AND class_date < '$end_date'";
                        }
                        $sql=$sql." ORDER BY class_date DESC";  
                        $query=$connection->stmt_init();
                        $query->prepare($sql);
                        try{
                            $query->execute();
                            if($table=='private_students'){
                                $query->bind_result($student_name, $date, $hours, $content, $attendance, $s_notes, $f_notes, $pay_rate, $pay_date);
                            } else {
                                $query->bind_result($student_name, $date, $hours, $content, $attendance, $s_notes, $f_notes);
                            }
                            $unpaid=0;
                            while($query->fetch()){
                                date_default_timezone_set("Europe/Madrid");
                                $now=date("Y-m-d H:i:s");
                                if($date > $now){
                                    echo "<tr style='color:gray; background-color: #e5e5e5'>";
                                } else {
                                    echo "<tr>";
                                }
                                if($name=='all'){
                                    echo "<td>".$student_name."</td>";
                                }
                                ?>
                                    <td class="lessonDate"><?=$date?></td>
                                    <td class="hours"><?=$hours?></td>
                                    <td class="content"><?php
                                    if($content != null){
                                        echo htmlspecialchars($content);
                                    }
                                    ?></td>
                                    <td class="attendance"><?php
                                    if($attendance != null){
                                        echo htmlspecialchars($attendance);
                                    }
                                    ?></td>
                                    <td class="sNotes"><?php
                                    if($s_notes != null){
                                        echo htmlspecialchars($s_notes);
                                    }
                                    ?></td>
                                    <td class="fNotes"><?php
                                    if($f_notes != null){
                                        $text=htmlspecialchars($f_notes);
                                        $regex="/https?:\/\/[^\s]+/";
                                        preg_match_all($regex, $text, $matches);
                                        foreach ($matches[0] as $link){
                                            $text=str_replace($link, "<a target='_blank' href='".$link."'>link</a>", $text);
                                        }
                                        echo $text;
                                    }
                                    ?></td>
                                    <?php
                                    if($table=='private_students'){
                                        ?>
                                        <td class="payRate"><?=$pay_rate?></td>
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
                                    <td class="actions">
                                        <a class="button bluePurpleBack linkModify">Modify entry</a>
                                        <a class="button redBack linkRemove">Remove entry</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            $query->close();
                        } catch (mysqli_sql_exception $e){
                            die("ERROR: ".$e->getMessage());
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total Hours</td>
                            <?php
                            $query1=$connection->stmt_init();
                            $sql="SELECT SUM(hours_taught) FROM $table WHERE 1";
                            if($name!='all'){
                                $sql=$sql." AND name='$name'";
                            }
                            if(isset($_REQUEST['startDate']) && $_REQUEST['startDate']!=""){
                                $start_date=$_REQUEST['startDate'];
                                $sql=$sql." AND class_date > '$start_date'";
                            }
                            if(isset($_REQUEST['endDate']) && $_REQUEST['endDate']!=""){
                                $end_date=$_REQUEST['endDate'];
                                $sql=$sql." AND class_date < '$end_date'";
                            }
                            $query1->prepare($sql);
                            try{
                                $query1->execute();
                                $query1->bind_result($total_hours);
                                $query1->fetch();
                                ?>
                                <td><?=$total_hours?></td>
                                <?php
                            }catch(mysqli_sql_exception $e){
                                die("Error: ".$e->getMessage());
                            }
                            $query1->close();
                            echo "<td>Total Lessons</td>";
                            $query2=$connection->stmt_init();
                            $sql="SELECT COUNT(class_date) FROM $table WHERE 1";
                            if($name!='all'){
                                $sql=$sql." AND name='$name'";
                            }
                            if(isset($_REQUEST['startDate']) && $_REQUEST['startDate']!=""){
                                $start_date=$_REQUEST['startDate'];
                                $sql=$sql." AND class_date > '$start_date'";
                            }
                            if(isset($_REQUEST['endDate']) && $_REQUEST['endDate']!=""){
                                $end_date=$_REQUEST['endDate'];
                                $sql=$sql." AND class_date < '$end_date'";
                            }
                            $query2->prepare($sql);
                            try{
                                $query2->execute();
                                $query2->bind_result($total_lessons);
                                $query2->fetch();
                                echo "<td>$total_lessons</td>";
                            }catch(mysqli_sql_exception $e){
                                die("Error: ".$e->getMessage());
                            }
                            $query2->close();
                            if($table=="private_students"){
                                echo "<td>Unpaid lessons</td>";
                                echo "<td>$unpaid</td>";
                                echo "<td>Amount owed</td>";
                                $query3=$connection->stmt_init();
                                $sql="SELECT SUM(pay_rate*hours_taught) 
                                FROM private_students 
                                WHERE (pay_date IS NULL)";
                                if($name!='all'){
                                    $sql=$sql." AND name='$name'";
                                }
                                if(isset($_REQUEST['startDate']) && $_REQUEST['startDate']!=""){
                                    $start_date=$_REQUEST['startDate'];
                                    $sql=$sql." AND class_date > '$start_date'";
                                }
                                if(isset($_REQUEST['endDate']) && $_REQUEST['endDate']!=""){
                                    $end_date=$_REQUEST['endDate'];
                                    $sql=$sql." AND class_date < '$end_date'";
                                }
                                $query3->prepare($sql);
                                try{
                                    $query3->execute();
                                    $query3->bind_result($amount_owed);
                                    $query3->fetch();
                                    echo "<td colspan='2'>€".round($amount_owed*100)/100 ."</td>";
                                } catch(mysqli_sql_exception $e){
                                    die($e->getMessage());
                                }
                                $query3->close();
                                $connection->close();
                            } else {
                                echo '<td colspan="3"> </td>';
                            }
                            if($name=="all"){
                                echo '<td></td>';
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
                <div style="padding: 10px">
                    <a class="fontButton" href="classLogs.php">Back</a>
                    <a class="fontButton redBack" href="#" id="deleteGroup"
                    <?php
                    if($name=="all")
                        echo "style='display:none'";
                    ?>>Delete Class</a>
                </div>
            </div>
        </div>
    </body>
    <?php
    include 'footer.html';
    ?>
</html>