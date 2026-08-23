<html lang="en">
    <head>
        <title>Class Tracker</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/mobile.css" media="screen and (max-width: 480px)">
        <link rel="stylesheet" href="css/desktop.css" media="screen and (min-width: 900px)">
        <link rel="stylesheet" href="css/narrowDesktop.css" media="screen and (min-width: 481px) and (max-width: 899px)">
        <link rel="stylesheet" href="css/styles.css">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Chewy&family=Coming+Soon&family=Molle:ital@1&family=Titan+One&display=swap');
        </style>
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
                        groupSelector.innerHTML = "<option value='' selected hidden>--Choose a class--</option>";
                        if(data.error){
                            console.log(data.error);
                        } else if(data.noData){
                            console.log(data.noData);
                        } else {
                            for(var row in data){
                                let groupName=data[row].name;
                                let option = document.createElement("option");
                                option.value = groupName;
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
        <div class="main">
            <div class="flexContainer singleColFlex marginTop">
                <h2 class="">Welcome to</h2>
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
                <h2 class="subtitle">A tool for teachers</h2>
                <form name="chooseClient" action="#" method="GET">
                    <label>Select category:
                        <br>
                        <select name="clientGroup">
                            <option value="ega_classes">EGA Classes</option>
                            <option value="linguaviaje">Linguaviaje</option>
                            <option value="private_students">Private Students</option>
                            <option value="amazing_talker">Amazing Talker</option>
                        </select>
                    </label>
                </form>
                <form name="chooseGroup" action="classData.php" method="get">
                    <label>Select class:
                        <br>
                        <select name="groupName">
                            <option value="" selected hidden>--Choose a class--</option>
                            
                        </select>
                    </label>
                    <input type="hidden" name="category">
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