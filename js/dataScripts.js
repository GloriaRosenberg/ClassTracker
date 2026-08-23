window.onload = function(){
    let filters = document.dateFilter;
    filters.clearFilters.onclick=function(){
        filters.startDate.value = "";
        filters.endDate.value = "";
        filters.submit();
    }
    document.getElementById("linkAdd").onclick = addForm;
    document.getElementById("linkModify").addEventListener("click", getEntries);
    document.getElementById("linkRemove").onclick = getEntries;
}
function addForm(){
    let html = "<div class='flexContainer formFlex'>"
    + "<label>Class date <input type='datetime-local' name='class_date'></label>"
    + "<label>Hours taught <input type='number' name='hours_taught' min='0'></label>"
    + "<label>Content <textarea name='content' maxlength='90'></textarea></label>"
    + "<label>Attendance <input type='text' name='attendance' maxlength='50'></label>"
    + "<label>Student notes <textarea name='student_notes' maxlength='90'></textarea></label>"
    + "<label>Future notes <textarea name='future_notes' maxlength='90'></textarea></label>";
    if(document.getElementById("pay_rate")!=null){
        html += "<label>Hourly Rate <input type='number' name='pay_rate' min='0'></label>"
        + "<label>Paid? <input type='checkbox' name='paid'>"
        + "<input type='date' name='pay_date' disabled></label>";
    }
    html += "</div><div><button class='greenBack' type='button' name='add' id='addEntry'>ADD</button>"
    + "<button class='greenBack' type='button' name='cancel'>CANCEL</button></div>";
    document.actionForm.innerHTML = html;
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
function verify(event){
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
        ajaxAdd(event);
    }
}
function ajaxAdd(event){
    console.log(event.target);
    let form = document.actionForm;
    let table = document.details.table.value;
    let class_date;
    if(event.target.id == "addEntry"){
        class_date = form.class_date.value;
    } else if (event.target.id == "modifyEntry"){
        class_date = document.getElementById("selector").value;
    }
    let name = document.details.name.value;
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
                if(event.target.id == "addEntry")
                    alert("Class record for " + name + " on " 
                + class_date + " added successfully");
                else if(event.target.id == "modifyEntry")
                    alert("Class record for " + name + " on " 
                        + class_date + " modified successfully");
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
function ajaxRemove(){
    let class_date = document.getElementById("selector").value;
    let name = document.details.name.value;
    encodedName = name.replace("&", "%26");
    let table = document.details.table.value;

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
                alert("Class record for " + name + " on " 
                    + class_date + " deleted successfully");
                location.reload();            
            }
        }
    }
    request.send(null);
}
function fillForm(mode){
    document.getElementById("specifics").innerHTML="";
    let dates = document.getElementsByClassName("lessonDate");
    let hours = document.getElementsByClassName("hours");
    let contents = document.getElementsByClassName("content");
    let attendances = document.getElementsByClassName("attendance");
    let sNotes = document.getElementsByClassName("sNotes");
    let fNotes = document.getElementsByClassName("fNotes");
    let payDates = document.getElementsByClassName("payDate");
    let payRates = document.getElementsByClassName("payRate");

    let this_date = document.getElementById("selector").value;
    let index = 0;
    for(let i=0; i<dates.length; i++){
        if(dates[i].textContent == this_date){
            index = i;
            break;
        }
    }
    if(mode == "modify"){
        let div1 = document.createElement("div");
        div1.className = "flexContainer";
        div1.className = "formFlex";
        let html = "<h3>Class on "+this_date.substring(0,10)+" at "
        + this_date.substring(11,16)+"</h3>"
        + "<label>Hours taught <input type='number' name='hours_taught' min='0' value ='"
        + hours[index].textContent+"'></label>"
        + "<label>Content <textarea name='content' maxlength='90'>"
        + contents[index].textContent+"</textarea></label>"
        + "<label>Attendance <input type='text' name='attendance' maxlength='50' value ='"
        + attendances[index].textContent+"'></label>"
        + "<label>Student notes <textarea name='student_notes' maxlength='90'>"
        + sNotes[index].textContent+"</textarea></label>"
        + "<label>Future notes <textarea name='future_notes' maxlength='90'>"
        + fNotes[index].textContent+"</textarea></label>";
        if(document.details.table.value=="private_students"){
            html += "<label>Hourly Rate <input type='number' name='pay_rate' min='0' value = '"
            + payRates[index].textContent/hours[index].textContent+"'></label>"
            + "<label>Paid? <input type='checkbox' name='paid'";
            if(payDates[index].textContent!="Not yet paid"){
                html += " checked>"
                + "<input type='date' name='pay_date' value='"
                + payDates[index].textContent+"'></label>";
            } else {
                html += "><input type='date' name='pay_date' disabled>";
            }
        }
        div1.innerHTML = html;
        let div2 = document.createElement("div");
        let html2 = "<button class='bluePurpleBack' type='button' name='modify' id='modifyEntry'>MODIFY</button>"
        + "<button class='bluePurpleBack' type='button' name='cancel'>CANCEL</button>";
        div2.innerHTML = html2;
        document.getElementById("specifics").appendChild(div1);
        document.getElementById("specifics").appendChild(div2);
        if(document.details.table.value == "private_students"){
            document.actionForm.paid.onchange = checkPaid;
            document.actionForm.modify.onclick = verifyPaid;
        } else {
            document.actionForm.modify.onclick = ajaxAdd;
        }
        document.actionForm.cancel.onclick = function(){
            document.actionForm.innerHTML = "";
        }
    } else if (mode == "delete"){
        let html = null;
        if(window.innerWidth > 480){
            html = "<tr><td>Date & Time</td><td>Hours</td><td>Content</td>"
            +"<td>Attendance</td><td>Student Notes</td><td>Future Notes</td></tr>"
            +"<tr><td>"+dates[index].textContent+"</td><td>"+hours[index].textContent+"</td><td>"+contents[index].textContent+"</td>"
            + "<td>"+attendances[index].textContent+"</td><td>"+sNotes[index].textContent+"</td><td>"+fNotes[index].textContent+"</td></tr>";
        } else {
            html = "<tr><td>Date & Time</td><td>Hours</td><td>Content</td></tr>"
            + "<tr><td>"+dates[index].textContent+"</td><td>"+hours[index].textContent+"</td><td>"+contents[index].textContent+"</td></tr>"
            + "<tr><td>Attendance</td><td>Student Notes</td><td>Future Notes</td></tr>"
            + "<tr><td>"+attendances[index].textContent+"</td><td>"+sNotes[index].textContent+"</td><td>"+fNotes[index].textContent+"</td></tr>"; 
        }
        let tab = document.createElement("table");
        tab.className = "preview";
        tab.innerHTML = html;
        document.getElementById("specifics").appendChild(tab);
        let div = document.createElement("div");
        let del = document.createElement("button");
        del.type = "button";
        del.onclick = function(){
            if(confirm("Delete this entry?")){
                ajaxRemove();
            }
        };
        del.appendChild(document.createTextNode("DELETE"));
        del.className = "redBack";
        div.appendChild(del);
        let cancel = document.createElement("button");
        cancel.className = "redBack";
        cancel.type = "button";
        cancel.appendChild(document.createTextNode("Cancel"));
        cancel.onclick = function(){
            document.actionForm.innerHTML = "";
        }
        div.appendChild(cancel);
        document.getElementById("specifics").appendChild(div);
    }
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
        ajaxAdd(event);
    }
}