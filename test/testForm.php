<html>
    <body>
        <form name="addEntry" action="testServer.php" method="get">
            <div class="flexContainer formFlex">
                <label>Class date 
                    <input type="datetime-local" name="class_date" required="">
                </label>
                <label>Hours taught 
                    <input type="number" name="hours_taught" required="">
                </label>
                <label>Content 
                    <textarea name="content" maxlength="90"></textarea>
                </label>
                <label>Attendance 
                    <input type="text" name="attendance" maxlength="50">
                </label>
                <label>Student notes 
                    <textarea name="sNotes" maxlength="90"></textarea>
                </label>
                <label>Future notes 
                    <textarea name="fNotes" maxlength="90"></textarea>
                </label>
            </div>
            <button class="greenBack" type="submit" name="add">ADD</button>
            <button class="greenBack" type="button" name="cancel">CANCEL</button>
            <input type="hidden" name="table" value="private_students">
            <input type="hidden" name="name" value="Private Test Student 1">
        </form>
    </body>
</html>