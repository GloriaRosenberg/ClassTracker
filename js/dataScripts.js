window.onload = function(){
    const linkForm = document.addLink;
    console.log(linkForm.sendLink);
    linkForm.sendLink.onclick=function(){
        if(linkForm.linkDescription != "" && linkForm.linkURL != ""){
            ajaxAddLink();
        } else {
            alert("You must fill in a description and url");
        }
    }
    const filterToggle = document.getElementById("filterToggle");
    let filterOpen = false;
    filterToggle.onclick = function(){
        if(!filterOpen){
            document.getElementById("filterCollapsible").style.display = "unset";
            document.getElementById("linksCollapsible").style.display = "none";
            document.getElementById("collapsibleLabels").style.borderBottom = "3px solid var(--paleBlue)";
            filterOpen = true;
            linksOpen = false;
        } else if (filterOpen) {
            document.getElementById("filterCollapsible").style.display = "none";
            filterOpen = false; 
        }
    }
    const linksToggle = document.getElementById("linksToggle");
    let linksOpen = false;
    linksToggle.onclick = ()=>{
        if(!linksOpen){
            document.getElementById("linksCollapsible").style.display = "unset";
            document.getElementById("filterCollapsible").style.display = "none";
            document.getElementById("collapsibleLabels").style.borderBottom = "3px solid var(--red)";
            filterOpen = false; 
            linksOpen = true;
        } else if (linksOpen) {
            document.getElementById("linksCollapsible").style.display = "none";
            linksOpen = false; 
        }
    }
    let filters = document.dateFilter;
    filters.clearFilters.onclick=function(){
        filters.startDate.value = "";
        filters.endDate.value = "";
        filters.submit();
    }
    filters.thisMonth.onclick=function(){
        let firstOfMonth = new Date();
        firstOfMonth.setDate(1);
        firstOfMonth.setHours(2);
        firstOfMonth.setMinutes(0);
        let startString = firstOfMonth.toISOString();
        filters.startDate.value = startString.substring(0, startString.length -8);
        let lastOfMonth = new Date();
        lastOfMonth.setMonth(lastOfMonth.getMonth() + 1);
        lastOfMonth.setDate(0);
        lastOfMonth.setHours(2);
        lastOfMonth.setMinutes(0);
        let endString = lastOfMonth.toISOString();
        filters.endDate.value = endString.substring(0, endString.length -8);
        console.log(startString+" and "+endString);
        filters.submit();
    }
    document.getElementById("linkAdd").onclick = addForm;
    let modButtons = document.getElementsByClassName("linkModify");
    let delButtons = document.getElementsByClassName("linkRemove");
    for(let i=0; i<modButtons.length; i++){
        modButtons[i].addEventListener("click", enableEditing);
        delButtons[i].addEventListener("click", function(event){
            if(confirm("Delete class log for " + event.target.parentElement
                .parentElement.children[0].textContent + "?")){
                ajaxRemove(event);
            }
        });
    }
    document.getElementById("deleteGroup").onclick = function(){
        if(confirm("Are you sure you want to delete all data for the class " + document.details.name.value
            + "? This action cannot be undone.")){
                deleteGroup();
                setTimeout(function() {
                    window.location.replace("classLogs.php");
                }, 1000);
            }
    }
}
function enableEditing(event){
    console.log(event.target);
    let fields = event.target.parentElement.parentElement.children;
    let n = 0;
    if(document.details.name.value=="all")
        n = 1;
    let oldDate = fields[n].textContent;
    fields[n].innerHTML = "<input type='datetime-local' name='class_date' style='width:fit-content' value='"
    +fields[n].textContent+"'>";
    fields[n+1].innerHTML = "<input type='number' name='hours_taught' min='0' style='width:50px' value='"
    +fields[n+1].textContent+"'>";
    for(let i=n+2; i<n+6; i++){ 
        let txt = fields[i].textContent;
        for(let j=0; j<fields[i].children.length; j++){
            console.log(fields[i].children[j].tagName);
            if(fields[i].children[j].tagName == "A"){
                console.log("link found");
                let url = fields[i].children[j].href;
                txt = txt.replace("link", url);
            }
        }
        fields[i].innerHTML = "<textarea name='"+fields[i].className+"' style='width:100px'>"
        +txt+"</textarea>";
    }
    if(document.details.table.value=="private_students"){
        fields[n+6].innerHTML = "<input type='number' name='hours_taught' min='0' style='width:50px' value='"
        +fields[n+6].textContent+"'>";
        let payDate = fields[n+7].textContent;
        let html = "<label>Paid? <input type='checkbox' name='paid' class='payCheck'";
        if(payDate != "Not yet paid"){
            html += "checked";
        }
        html += "><input type='date' name='class_date' style='width:fit-content'";
        if(payDate != "Not yet paid"){
            html += "value='"+payDate+"'";
        } else {
            html += "disabled";
        }
        html += ">";
        fields[n+7].innerHTML = html;
        let boxes = document.getElementsByClassName("payCheck");
        for(let i=0; i<boxes.length; i++){
            boxes[i].addEventListener("click", function(){
                if(boxes[i].checked){
                    boxes[i].nextSibling.disabled = false;
                } else {
                    boxes[i].nextSibling.disabled = true;
                    boxes[i].nextSibling.value = null;
                }
            });
        }
    }
    
    let buttonMod = document.createElement("button");
    buttonMod.className = "bluePurpleBack";
    buttonMod.name="modify";
    buttonMod.textContent = "MODIFY";
    buttonMod.onclick = function(event){
        if(confirm("Confirm changes?")){
            ajaxModify(event, oldDate);
        }
    };
    let buttonCan = document.createElement("button");
    buttonCan.className = "bluePurpleback";
    buttonCan.name="cancel";
    buttonCan.textContent = "CANCEL";
    buttonCan.onclick = function(){
        // Improve this later
        location.reload();
    }
    fields[fields.length-1].innerHTML = "";
    fields[fields.length-1].appendChild(buttonMod);
    fields[fields.length-1].appendChild(buttonCan);
}
function ajaxModify(event, oldDate){
    console.log(oldDate);
    let name;
    let table = document.details.table.value;
    let tr = event.target.parentElement.parentElement;
    let n = 0;
    if(document.details.name.value=="all"){
        n = 1;
        name = tr.children[0].textContent;
    } else {
        name = document.details.name.value
    }
    encodedName = name.replaceAll("&", "%26").replaceAll("+", "%2B");
    let class_date = tr.children[n].children[0].value;
    let hours_taught = tr.children[n+1].children[0].value;
    let content = tr.children[n+2].children[0].value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let attendance = tr.children[n+3].children[0].value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let student_notes = tr.children[n+4].children[0].value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let future_notes = tr.children[n+5].children[0].value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let pay_rate = null;
    let pay_date = null;
    let error = null;
    console.log("modifyEntryServer.php?class_date="+class_date
        +"&name="+encodedName+"&content="+content+"&attendance="+attendance
        +"&student_notes="+student_notes+"&future_notes="+future_notes
        +"&hours_taught="+hours_taught+"&pay_rate="+pay_rate+"&pay_date="+pay_date
        +"&table="+table+"&old_date="+oldDate);
    if(document.details.table.value=="private_students"){
        pay_rate = tr.children[n+6].children[0].value;
        let ch
        pay_date = tr.children[n+7].children[0].children[1].value;
        if(pay_rate == ""){
            error = "pay rate is required";
        }
        if(tr.children[n+7].children[0].children[0].checked && pay_date == ""){
            error = "if class has been paid you must specify the date";
        }
    }
    if(class_date == "" || hours_taught == ""){
        error = "class date and hours are required";
    }
    if(error == null){
        let request = new XMLHttpRequest();
        request.open("GET", "modifyEntryServer.php?class_date="+class_date
            +"&name="+encodedName+"&content="+content+"&attendance="+attendance
            +"&student_notes="+student_notes+"&future_notes="+future_notes
            +"&hours_taught="+hours_taught+"&pay_rate="+pay_rate+"&pay_date="+pay_date
            +"&table="+table+"&old_date="+oldDate, true);
        request.setRequestHeader("Content-Type", "application/json");
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                let response = JSON.parse(request.responseText);
                console.log(response);
                if(response.success){
                    alert(response.success);
                    location.reload();
                } else if(response.error){
                    alert(response.error);
                } else {
                    alert("Something went wrong.");
                }
            }
        }
        request.send(null);
    } else {
        alert(error);
    }
}
function deleteGroup(){
    let group = document.details.name.value;
    let table = document.details.table.value;

    let request = new XMLHttpRequest();
    request.open("GET", "deleteGroupServer.php?table="+table+"&group="+group, true);
    request.setRequestHeader("Content-Type", "application/json");
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let response = JSON.parse(request.responseText);
            if(response.error){
                alert(response.error);
            } else if(response.success){
                alert(response.success);
            }
        }
    }
    request.send(null);
}
function fillGroupSelector(){
    let groupSelector = document.actionForm.group_name;
    console.log(document.actionForm);
    let category = document.details.table.value;

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
function addForm(){
    let html = "<div class='flexContainer formFlex'>";
    if(document.details.name.value === "all"){
        html += "<label>Group Name <select name='group_name'></select></label>";
    }
    html += "<label>Class date <input type='datetime-local' name='class_date'></label>"
    + "<label>Hours taught <input type='number' name='hours_taught' min='0'></label>"
    + "<label>Content <textarea name='content'></textarea></label>"
    + "<label>Attendance <input type='text' name='attendance'></label>"
    + "<label>Student notes <textarea name='student_notes'></textarea></label>"
    + "<label>Future notes <textarea name='future_notes'></textarea></label>";
    if(document.details.table.value=="private_students"){
        html += "<label>Hourly Rate <input type='number' name='pay_rate' ";
        if(document.details.name.value == "Jamie & Jayden"){
            html += "value='12' ";
        } else if(document.details.name.value == "Tiago & Iker"){
            html += "value='15' ";
        } else if(document.details.name.value == "Farouk"){
            html += "value='12' ";
        }
        html += "min='0'></label>"
        + "<label>Paid? <input type='checkbox' name='paid'>"
        + "<input type='date' name='pay_date' disabled></label>";
    }
    html += "</div><div><button class='greenBack' type='button' name='add' id='addEntry'>ADD</button>"
    + "<button class='greenBack' type='button' name='cancel'>CANCEL</button></div>";
    document.actionForm.innerHTML = html;
    if(document.details.name.value === "all"){
        fillGroupSelector();
    }
    if(document.details.table.value == "private_students"){
        document.actionForm.paid.onchange = checkPaid;
    }
    document.actionForm.cancel.onclick = function(){
        document.actionForm.innerHTML = "";
    }
    document.actionForm.add.onclick = verify;
}
function checkPaid(){
    if(document.actionForm.paid.checked){
        document.actionForm.pay_date.disabled = false;
    } else {
        document.actionForm.pay_date.disabled = true;
    }
}
function verify(){
    let valid = true;
    let dateEntered = document.actionForm.class_date.value;
    if(dateEntered == ""){
        alert("You must enter a class date");
        valid = false;
    }
    if(document.actionForm.hours_taught.value == ""){
        alert("Please enter hours taught");
        valid = false;
    }
    if(document.details.table.value == "private_students" && document.actionForm.pay_rate.value == ""){
        alert("Enter the hourly rate for this class");
        valid = false;
    }
    if(document.details.table.value == "private_students" && document.actionForm.paid.checked){
        if(document.actionForm.pay_date.value == ""){
            alert("If the lesson has been paid, please enter the payment date");
            valid = false;
        }
    }
    let dates = document.getElementsByClassName("lessonDate");
    for(let i=0; i<dates.length; i++){
        if(Date.parse(dates[i].textContent) == Date.parse(dateEntered)){
            if(!confirm("A lesson record has already been created at this date and time. "
                + "Continuing will replace the existing record. Confirm?")){
                    valid = false;
                }
        }
    }
    if(valid){
        ajaxAdd();
    }
}
function ajaxAdd(){
    let form = document.actionForm;
    let table = document.details.table.value;
    let class_date = form.class_date.value;
    let name = document.details.name.value;
    if(name === "all"){
        name = form.group_name.value;
    }
    encodedName = name.replaceAll("&", "%26").replaceAll("+", "%2B");
    let hours_taught = form.hours_taught.value;
    let content = form.content.value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let attendance = form.attendance.value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let student_notes = form.student_notes.value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let future_notes = form.future_notes.value.replaceAll("&", "%26").replaceAll("+", "%2B");
    let pay_rate = null;
    let pay_date = null;
    if(table == "private_students"){
        pay_rate = form.pay_rate.value;
        if(form.paid.checked){
            pay_date = form.pay_date.value;
        }
    }

    let request = new XMLHttpRequest();
    request.open("GET", "addEntryServer.php?class_date="+class_date
    +"&name="+encodedName+"&content="+content+"&attendance="+attendance
    +"&student_notes="+student_notes+"&future_notes="+future_notes
    +"&hours_taught="+hours_taught+"&pay_rate="+pay_rate+"&pay_date="+pay_date
    +"&table="+table, true);
    request.setRequestHeader("Content-Type", "application/json");
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            console.log(request.responseText);
            var response = JSON.parse(request.responseText);
            if(response.error){
                alert(response.error);
            } else if(response.success) {
                alert("Class record for " + name + " on " 
                + class_date + " added successfully");
                location.reload();
            }
        }
    }
    request.send(null);
}
function getEntries(event){
    console.log(event.target);
    let div = document.actionForm;
    let table = document.details.table.value;
    let name = document.details.name.value;
    let encodedName = name.replace("&", "%26");
    let request = new XMLHttpRequest();
    request.open("GET", "getEntriesServer.php?table="+table+"&name="+encodedName, true);
    request.setRequestHeader("Content-Type", "application/json");
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            var response = JSON.parse(request.responseText);
            if(response.error){
                alert(response.error);
            } else {
                let html = "<select id='selector' name='selector'>";
                for(var row in response){
                    html += "<option value='"+response[row].class_date+"'>"
                    + response[row].class_date + "</option>";
                }
                html += "</select><div id='specifics'></div>";
                div.innerHTML = html;
                let selector = document.getElementById("selector");
                
                // selector.onchange = fillForm;
                let isOpen = false;

                // Detect when the dropdown opens
                selector.addEventListener('mousedown', () => {
                    isOpen = true;
                });

                // Fires when value changes
                selector.addEventListener('change', () => {
                    isOpen = false;
                    handleSelection(selector.value);
                });

                // Fires when user clicks without changing value
                selector.addEventListener('click', () => {
                    if (isOpen) {
                    // The second click closes the dropdown, meaning an option was selected
                    handleSelection(selector.value);
                    isOpen = false;
                    }
                });

                function handleSelection(value) {
                    console.log('Selected option:', value);
                    if(event.target.id == "linkModify"){
                        fillForm("modify");
                    }else if(event.target.id == "linkRemove"){
                        fillForm("delete");
                    }
                }
            }
        }
    }
    request.send(null);
}
function ajaxRemove(event){
    let name;
    let n=0;
    if(document.details.name.value=="all"){
        n=1;
        name = event.target.parentElement.parentElement.children[0].textContent;
    } else {
        name = document.details.name.value
    }
    encodedName = name.replaceAll("&", "%26").replaceAll("+", "%2B");
    let table = document.details.table.value;
    let class_date = event.target.parentElement.parentElement.children[n].textContent;
    console.log("deleteServer.php?class_date="+class_date
         +"&name="+encodedName+"&table="+table);
    let request = new XMLHttpRequest();
    request.open("GET", "deleteServer.php?class_date="+class_date
        +"&name="+encodedName+"&table="+table,true);
    request.setRequestHeader("Content-Type", "application/json");
    request.onreadystatechange=function(){
        if(request.readyState == 4 && request.status == 200){
            console.log(request.responseText);
            let response = JSON.parse(request.responseText);
            if(response.error){
                alert(response.error);
            } else if(response.success){
                alert(response.success);
                location.reload();            
            }
        }
    }
    request.send(null);
}
function ajaxAddLink(){
    const description = document.addLink.linkDescription.value.replaceAll("&", "%26").replaceAll("+", "%2B");
    const url = document.addLink.linkURL.value.replaceAll("&", "%26").replaceAll("+", "%2B");
    const group_name = document.details.name.value.replaceAll("&", "%26").replaceAll("+", "%2B");

    const request = new XMLHttpRequest();
    request.open("GET", 'addLinkServer.php?description='+description+"&url="+url+"&group_name="+group_name, true);
    request.setRequestHeader("Content-Type", "application/json");
    request.onreadystatechange=function(){
        if(request.readyState == 4 && request.status == 200){
            console.log(request.responseText);
            let response = JSON.parse(request.responseText);
            if(response.error){
                alert(response.error);
            } else if(response.success){
                alert(response.success);
                location.reload();            
            }
        }
    }
    request.send(null);
}
function verifyPaid(event){
    let valid = true;
    if(document.details.table.value == "private_students" && document.actionForm.paid.checked){
        if(document.actionForm.pay_date.value == ""){
            alert("If the lesson has been paid, please enter the payment date");
            valid = false;
        }
    }
    if(valid){
        ajaxModify();
    }
}