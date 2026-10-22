<!DOCTYPE HTML>
<html>
    <head>
        <title>My Class Logs | Class Tracker</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <link rel="icon" type="image/x-icon" href="img/favicon.ico">
        <link rel="stylesheet" href="css/mobile.css" media="screen and (max-width: 480px)">
        <link rel="stylesheet" href="css/desktop.css" media="screen and (min-width: 900px)">
        <link rel="stylesheet" href="css/narrowDesktop.css" media="screen and (min-width: 481px) and (max-width: 899px)">
        <link rel="stylesheet" href="css/styles.css">
        <script>
            window.onload=function(){
                ajaxJSON();
                document.chooseClient.clientGroup.onchange = ajaxJSON;
                document.chooseGroup.onsubmit=function(){
                    if(document.chooseGroup.groupName.value==""){
                        alert("Please select a group to continue.");
                        return false;
                    }
                }
                document.chooseGroup.addGroup.onclick = addGroup;
            }
            function ajaxJSON(){
                let groupSelector = document.chooseGroup.groupName;
                let category = document.chooseClient.clientGroup.value;
                document.chooseGroup.category.value = category;

                let httpRequest = new XMLHttpRequest();
                httpRequest.open("GET", "groupsServer.php?clientGroup="+category, true);
                httpRequest.setRequestHeader("Content-Type", "application/json");
                httpRequest.onreadystatechange = function(){
                    if(httpRequest.readyState == 4 && httpRequest.status == 200){
                        var data = JSON.parse(httpRequest.responseText);
                        groupSelector.innerHTML = "<option value='' selected hidden>--Choose a class--</option>"
                        + "<option value='all'>All classes in this category</option>";
                        if(data.error){
                            console.log(data.error);
                        } else if(data.noData){
                            console.log(data.noData);
                        } else {
                            let groupCookie = document.chooseGroup.groupCookie.value;
                            for(var row in data){
                                
                                let groupName=data[row].name;
                                let option = document.createElement("option");
                                option.value = groupName;
                                if(groupCookie != null && groupCookie == groupName){
                                    option.selected = true;
                                }
                                option.appendChild(document.createTextNode(groupName));
                                groupSelector.appendChild(option);
                            }
                        }

                    }
                }
                httpRequest.send(null);
            }
            function addGroup(){
                let or = document.getElementById("or");
                let addButton = document.chooseGroup.addGroup;
                let label = document.createElement("label");
                let input = document.createElement("input");
                input.type = "text";
                input.name = "newGroup";
                label.appendChild(document.createTextNode("Class / group name: "));
                label.appendChild(input);
                addButton.after(label);
                let div = document.createElement("div");
                let confirm = document.createElement("button");
                confirm.type = "button";
                confirm.name = "confirm";
                confirm.className = "paleBlueBack";
                confirm.appendChild(document.createTextNode("Confirm"));
                div.appendChild(confirm);
                let cancel = document.createElement("button");
                cancel.type = "button";
                cancel.name = "cancel";
                cancel.className = "paleBlueBack";
                cancel.appendChild(document.createTextNode("Cancel"));
                div.appendChild(cancel);
                label.after(div);
                addButton.style.display = "none";
                or.style.display = "none";
                confirm.onclick = function(){
                    let newName = input.value;
                    if(newName == ""){
                        alert("Please enter the name of the new student or group");
                    } else {
                    let option = document.createElement("option");
                    option.value = newName;
                    option.innerHTML = newName;
                    document.chooseGroup.groupName.appendChild(option);
                    option.selected = true;
                    console.log(option);
                    }
                }
                cancel.onclick = function(){
                    label.style.display = "none";
                    div.style.display = "none";
                    addButton.style.display = "unset";
                    or.style.display = "unset";
                }
            }
        </script>
    </head>
    <body>
        <?php
        include 'header.html';
        ?>
        <div class="main">
            <div class="flexContainer vertical gap10" id="mainContainer">
                <h2 class="subtitle blue">My Class Logs</h2>
                <form name="chooseClient" action="#" method="GET">
                    <label>Select category:
                        <br>
                        <select name="clientGroup">
                            <option value="linguaviaje" 
                            <?php
                            if(isset($_COOKIE['category']) && $_COOKIE['category'] == 'linguaviaje'){
                                echo "selected";
                            }
                            ?>
                            >Linguaviaje</option>
                            <option value="private_students" 
                            <?php
                            if(isset($_COOKIE['category']) && $_COOKIE['category'] == 'private_students'){
                                echo "selected";
                            }
                            ?>
                            >Private Students</option>
                            <option value="amazing_talker" 
                            <?php
                            if(isset($_COOKIE['category']) && $_COOKIE['category'] == 'amazing_talker'){
                                echo "selected";
                            }
                            ?>
                            >Amazing Talker</option>
                            <option value="ega_classes" 
                            <?php
                            if(isset($_COOKIE['category']) && $_COOKIE['category'] == 'ega_classes'){
                                echo "selected";
                            }
                            ?>
                            >EGA Classes</option>
                        </select>
                    </label>
                </form>
                <form name="chooseGroup" action="classData.php" method="get">
                    <label>Select class:
                        <br>
                        <select name="groupName">
                        </select>
                    </label>
                    <input type="hidden" name="category">
                    <input type="hidden" name="groupCookie" 
                    <?php
                    if(isset($_COOKIE['groupName'])){
                        echo "value='".$_COOKIE['groupName']."'";
                    } else {
                        echo "value='null'";
                    }
                    ?>
                    >
                    <p id="or">or</p>
                    <button type="button" class="paleBlueBack" name="addGroup">Add a class</button>
                    <button type="submit" name="go" value="go">GO!</button>
                </form>
            </div>
        </div>
    </body>
    <?php
    include 'footer.html';
    ?>
</html>