<?php
session_start();
    include("header.php");
    include("../translation.php");?>
    <html>
<?php 
if (in_array("admin", $_SESSION["groups"])) {?>
    <script src="../utility/_initMenu.js" crossorigin="anonymous"></script>

    
<script> $( document ).ready(function() {
              menuItems = importItem("../utility/menuitems.json");
              drawMenuItemsLeft("forms",menuItems,2);
              drawMenuItemsRight('forms',menuItems,2);
            });</script>
    
        
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">
    <img src="../utility/logo2.png" height="50">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto navbarUl">
    </ul>
    <ul class="navbar-nav navbarPhP">
      <li>
        <a class="nav-link disabled timelock" href="#"><span id="time"> 10:00 </span><?php echo ' '.$_SESSION['UserUserName'];?>
        </a>
      </li>
    </ul>
    <form method='post' class="form-inline my-2 my-lg-0" action=../utility/userLogging.php>
      <button class="btn btn-danger my-2 my-sm-0" name='logout-submit' type="submit">Kijelentkezés</button>
      <script type="text/javascript">
        window.onload = function () {
          display = document.querySelector('#time');
          var timeUpLoc="../utility/userLogging.php?logout-submit=y"
          startTimer(display, timeUpLoc);
        };
      </script>
    </form>
    <a class="nav-link my-2 my-sm-0" href="./help.php">
      <i class="fas fa-question-circle fa-lg"></i>
    </a>
  </div>
</nav>

<div class="container">
    <h5 class="text text-danger">Nem végleges. Az oldal hibásan működhet!</h5>
  <div class="row">
    <div class="col-4">
      <h6>Elemek</h6>
      <div id="tools">
      <span draggable="false" ondragstart="drag(event)" class="mail toolIcon clickableIcon" name="E-mail"><i class="fas fa-at fa-2x"></i></span> <!-- Email cim -->
      <span draggable="false" ondragstart="drag(event)" class="date toolIcon clickableIcon" name="Dátum"><i class="fas fa-calendar-alt fa-2x"></i></span> <!-- Datum -->
      <span draggable="false" ondragstart="drag(event)" class="date_time toolIcon clickableIcon" name="Idő"><i class="fas fa-clock fa-2x"></i></span> <!-- Ido -->
      <span draggable="false" ondragstart="drag(event)" class="heading toolIcon clickableIcon" name="Szakaszcím"><i class="fas fa-heading fa-2x"></i></span> <!-- Szakaszcim -->
      <span draggable="false" ondragstart="drag(event)" class="paragraph toolIcon clickableIcon" name="Szakasz bekezdés"><i class="fas fa-paragraph fa-2x"></i></span> <!-- Szakasz bekezdes -->
      <span draggable="false" ondragstart="drag(event)" class="tinytext toolIcon clickableIcon"><i class="fas fa-grip-lines fa-2x"></i></span> <!-- Rovid szoveg -->
      <span draggable="false" ondragstart="drag(event)" class="longtext toolIcon clickableIcon" name="Hosszú szöveg"><i class="fas fa-align-justify fa-2x"></i></span> <!-- Hosszu szoveg -->
      <span draggable="false" ondragstart="drag(event)" class="radio toolIcon clickableIcon" name="Feleletválasztós"><i class="fas fa-circle fa-2x"></i></span> <!-- Feleletvalasztos -->
      <span draggable="false" ondragstart="drag(event)" class="checkbox toolIcon clickableIcon" name="Jelölőnégyzet"><i class="far fa-check-square fa-2x"></i></span> <!-- Jelolonegyzet -->
      <span draggable="false" ondragstart="drag(event)" class="dropdown toolIcon clickableIcon" name="Legördülő lista"><i class="fas fa-arrow-circle-down fa-2x"></i></span> <!-- Legordulo lista -->
      <span draggable="false" ondragstart="drag(event)" class="scale toolIcon clickableIcon" name="Lineáris skála"><i class="fas fa-sort-numeric-up fa-2x"></i></span> <!-- Linearis skala -->
    </div>
        <!-- <div class="drag-zone" ondrop="drop(event)" ondragover="allowDrop(event)">
            <span class="drop-zone__prompt">Húzd ide a hozzáadandó típust</span>
        </div> -->
        <input type='text' class='form-control' id='formTitle' placeholder='Kérdőív címe'></input>

        <button class="btn btn-primary" onclick="saveForm()">Mentés</button>
        <button class="btn btn-danger" onclick="deleteForm()">Törlés</button>
        </br>
        <label for="cars">Form állapota:</label>
<select id="formState" name="formState">
  <option value="e">Szerkesztés alatt</option>
  <option value="1">Fogad válaszokat</option>
  <option value="0">Nem fogad válaszokat</option>
</select>
    </br>
Csak nem szerkesztés alatt levő form esetén:
<select id="accessRestrict" name="accessRestrict">
  <option value="private">Privát</option>
  <option value="public">Publikus</option>
</select>

    </div>
    <div class="col-8">
      <h6>Form - ID:<?php echo $_GET['formId'] ?></h6>
    <div class="editorZone">

    </div>


    </div>
  </div>
</div>

<?php
}else{
    echo "<h3 id='titleBar'>Ehhez a tartalomhoz nincs hozzáférésed.</h3>";
} 
?>

<style>
    .drag-zone{
        border: 3px solid #ccc;
        min-height: 100px;
        padding: 10px;
        border-style: dotted;
    }

    .formElement{
        border: 1px solid #ccc;
        padding: 10px;
        margin: 10px;
        display: inline-block;
    }

    .formElement:hover{
        border: 2px solid aqua;
        padding: 10px;
        margin: 10px;
        display: inline-block;
        background-color: #eee;
    }

    .modifyButton{
        margin-left: 20px;
    }

    .formElementText{
        margin-left: 20px;
    }

    .editorZone{
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .formElementEditorField{
        border: 1px solid #ccc;
        padding: 10px;
        margin-top: -10px;
        display: inline-block;

    }
</style>

<script>
    i=1;
    $( document ).ready(function() {
        //Load form from server
        $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: {mode:"getForm",id:<?php echo $_GET['formId'] ?>},
            success: function(data){
                //if data is 404, redirect to index.php
                if(data==404){
                    window.location.href = "index.php?invalidID";
                }
                var form = JSON.parse(data);
                var formElements = JSON.parse(form.Data);
                console.log(formElements);
                var formName = form.Name;
                //Set form Name
                document.getElementsByTagName("input")[0].value = formName;

                //Set access restrict
                form.AccessRestrict = JSON.parse(form.AccessRestrict);
                console.log("accessRestrict:"+form.AccessRestrict[0]);
                document.getElementById("accessRestrict").value = form.AccessRestrict[0];

                //Set form state
                console.log("form Status:"+form.Status);
                document.getElementById("formState").value = form.Status;

                //Load form elements
                for(var j=0;j<formElements.length;j++){
                    var element = formElements[j];
                    var elementName = element.name;
                    var elementId = element.id;
                    var elementSettings = element.settings;
                    var originalElement = document.querySelectorAll('span[name="'+elementName+'"]')[0];
                    console.log(originalElement,elementName);
                    var clonedElement = originalElement.cloneNode(true);
                    clonedElement.removeAttribute("draggable");
                    clonedElement.id = elementId;
                    clonedElement.classList.add("formElementIcon");
                    document.getElementsByClassName("editorZone")[0].appendChild(clonedElement);
                    //Add delete button
                    var button = document.createElement("button");
                    button.innerHTML = "X";
                    button.classList.add("btn");
                    button.classList.add("btn-danger");
                    button.classList.add("btn-sm");
                    button.classList.add("modifyButton");
                    button.onclick = function() {
                        this.parentNode.parentNode.removeChild(this.parentNode);
                        //Delete span with same id
                        var span = document.getElementById(this.parentNode.id+"-settings");
                        if(span!=null)
                            span.parentNode.removeChild(span);
                    };

                    clonedElement.appendChild(button);

                    //Fire a click event on the cloned element
                    clonedElement.click();


                    //Add settings, where possible
                    console.log("Id: "+elementId+" Name: "+elementName+" Settings: "+elementSettings);
                    if(elementName=="Szakaszcím"){
                        console.log("Ez egy szakaszcím");
                        //Change value on document.getElementById(id+"-settings")
                        var input = document.createElement("input");
                        input.type = "text";
                        input.classList.add("form-control");
                        input.placeholder = "Szakaszcím";
                        input.value = elementSettings;
                        

                        //Get span with same id
                        var span = document.getElementById(elementId+"-settings");
                        console.log(span);
                        span.innerHTML = "";
                        //Set span innerHTML to input
                        span.appendChild(input);
                        console.log(span);

                    }
                    if(elementName=="Szakasz bekezdés"){
                        console.log("Ez egy szakasz bekezdés");
                        // var textarea = document.createElement("textarea");
                        // textarea.classList.add("form-control");
                        // textarea.placeholder = "Szakasz bekezdés";
                        // textarea.value = elementSettings;
                        // document.getElementById(elementId).click();
                        
                        // // //Get span with same id
                        // var span = document.getElementById(elementId+"-settings");
                        // console.log(span);
                        // span.innerHTML = "";
                        // span.appendChild(input);
                        // console.log(span);
                    }
                    if(elementName=="Lineáris skála"){
                        var min = elementSettings.split("-")[0];
                        var max = elementSettings.split("-")[1];
                        var inputMin = document.createElement("input");
                        inputMin.type = "number";
                        inputMin.classList.add("form-control");
                        inputMin.placeholder = "min";
                        inputMin.value = min;
                        var inputMax = document.createElement("input");
                        inputMax.type = "number";
                        inputMax.classList.add("form-control");
                        inputMax.placeholder = "max";
                        inputMax.value = max;
                        //Get span with same id
                        var span = document.getElementById(elementId+"-settings");
                        console.log(span);
                        span.innerHTML = "";


                        span.appendChild(inputMin);
                        span.appendChild(inputMax);
                    }
                i=parseInt(elementId)+1;
                }
            }})
        });

    
    function allowDrop(ev) {
      ev.preventDefault();
    }

    function drag(ev) {
      ev.dataTransfer.effectAllowed = 'copy';
      ev.dataTransfer.setData("text", ev.target.className);
    }

    //WHen a toolIcon is clicked, detect it and fire a drop event
    $(document).on("click",".clickableIcon",function() {
        //Add a div to the editorzone
        var div=document.createElement("div");
        //When the div is clicked, create a span with the settings
        div.onclick = function() {
            console.log("div clicked");
            //Append a span below the clicked div, if there is no span with same id
            if(document.getElementById($(this).children(".toolIcon").attr("id")+"-settings")!=null){
                console.log("span already exists");
                return;
            }

            var span = document.createElement("span");
            span.id=$(this).children(".toolIcon").attr("id")+"-settings";
            span.innerHTML = loadSettingOptions($(this).children(".toolIcon").attr("name"),$(this).children(".toolIcon").attr("id"));
            span.classList.add("formElementEditorField");
            //Append span to div
            div.appendChild(span);



        };
        
        div.classList.add("formElement");
        //Copy the clicked toolIcon to the div
        var originalElement = document.getElementsByClassName($(this).attr("class"))[0];
        var clonedElement = originalElement.cloneNode(true);
        clonedElement.removeAttribute("draggable");
        clonedElement.id = i++;
        //Prevent click event on clonedElement
        clonedElement.onclick = null;
        clonedElement.classList.remove("clickableIcon");
        div.appendChild(clonedElement);

        //Add text to the div based on the toolIcon name
        var span = document.createElement("span");
        span.innerHTML = $(this).attr("name");
        span.classList.add("formElementText");
        div.appendChild(span);

        //Add a button that deletes the div
        var button = document.createElement("button");
        button.innerHTML = "X";
        button.classList.add("btn");
        button.classList.add("btn-danger");
        button.classList.add("btn-sm");
        button.classList.add("modifyButton");
        button.onclick = function() {

            this.parentNode.parentNode.removeChild(this.parentNode);
        };
        div.appendChild(button);



        document.getElementsByClassName("editorZone")[0].appendChild(div);
    });

    function drop(ev) {
    //   ev.preventDefault();
    //   var data = ev.dataTransfer.getData("text");
    //   var originalElement = document.getElementsByClassName(data)[0];
    //   var clonedElement = originalElement.cloneNode(true);
    //   clonedElement.removeAttribute("draggable");
    //   clonedElement.id = i;
    //   clonedElement.classList.add("formElementIcon");
    //   document.getElementsByClassName("editorZone")[0].appendChild(clonedElement);

    //     //Create span with text inside it
    //     var span = document.createElement("span");
    //     span.innerHTML = originalElement.getAttribute("name");
    //     span.classList.add("formElementText");
    //     clonedElement.appendChild(span);


    //   var button = document.createElement("button");
    //     button.innerHTML = "X";
    //     button.classList.add("btn");
    //     button.classList.add("btn-danger");
    //     button.classList.add("btn-sm");
    //     button.classList.add("modifyButton");
    //     button.onclick = function() {
    //         this.parentNode.parentNode.removeChild(this.parentNode);
    //         //Delete span with same id
    //         var span = document.getElementById(this.parentNode.id+"-settings");
    //         if(span!=null)
    //             span.parentNode.removeChild(span);
    //     };

    //     //Add edit button
    //     var editButton = document.createElement("button");
    //     editButton.innerHTML = "Szerkesztés";
    //     editButton.classList.add("btn");
    //     editButton.classList.add("btn-primary");
    //     editButton.classList.add("modifyButton");
    //     editButton.onclick = function() {
    //         //TODO
    //     };

    //     // //Add move up button
    //     // var moveUpButton = document.createElement("button");
    //     // moveUpButton.innerHTML = "Fel";
    //     // moveUpButton.classList.add("btn");
    //     // moveUpButton.classList.add("btn-primary");
    //     // moveUpButton.classList.add("modifyButton");
    //     // moveUpButton.onclick = function() {
    //     //     //Move clonedElement up in the list
    //     //     var parent = this.parentNode;
    //     //     var previous = parent.previousSibling;
    //     //     if(previous!=null){
    //     //         parent.parentNode.insertBefore(parent,previous);
    //     //         //Move the spans with the same id too
    //     //         var span = document.getElementById(this.parentNode.id+"-settings");
    //     //         if(span!=null){
    //     //             span.parentNode.insertBefore(span,previous);
    //     //         }

    //     //     }

    //     // };

    //     // //Add move down button
    //     // var moveDownButton = document.createElement("button");
    //     // moveDownButton.innerHTML = "Le";
    //     // moveDownButton.classList.add("btn");
    //     // moveDownButton.classList.add("btn-primary");
    //     // moveDownButton.classList.add("modifyButton");
    //     // moveDownButton.onclick = function() {
    //     //     //Move clonedElement down in the list
    //     //     var parent = this.parentNode;
    //     //     var next = parent.nextSibling;
    //     //     if(next!=null){
    //     //         parent.parentNode.insertBefore(next,parent);
    //     //     }
    //     //     //Move the spans with the same id too
    //     //     var span = document.getElementById(this.parentNode.id+"-settings");
    //     //     if(span!=null){
    //     //         var nextSpan = span.nextSibling;
    //     //         if(nextSpan!=null){
    //     //             span.parentNode.insertBefore(nextSpan,span);
    //     //         }
    //     //     }
    //     // };

    //     clonedElement.appendChild(editButton);
    //     clonedElement.appendChild(button);
    //     clonedElement.appendChild(moveUpButton);
    //     clonedElement.appendChild(moveDownButton);
    //   //ev.target.appendChild(clonedElement);

    //     i++;
    }

    $(document).on("click",".formElementIcon",function() {
        //get id of element
        var id = $(this).attr("id");
        var name = $(this).attr("name");
        //if there is already a span with id
        if(document.getElementById(id+"-settings")!=null){
            return;
        }
        var span = document.createElement("span");
        
        span.id=id+"-settings";
        span.innerHTML = loadSettingOptions(name,span.id);
        span.classList.add("formElementEditorField");
        $(this).after(span);

    });


    //Load Form inputs to span. Return html
    function loadSettingOptions(name,id){
        if(name=="E-mail"){
            return "<p class='text text-secondary fw-light'>E-mail cím.</br><span class='text text-danger'>Nem tartozik hozzá beállítás.</span></p>";
        }else if(name=="Dátum"){
            return "<p class='text text-secondary fw-light'>Év.Hónap.Nap</br><span class='text text-danger'>Nem tartozik hozzá beállítás.</span></p>";
        }else if(name=="Idő"){
            return "<p class='text text-secondary fw-light'>Óra.Perc</br><span class='text text-danger'>Nem tartozik hozzá beállítás.</span></p>";
        }else if(name=="Szakaszcím"){
            return "<input type='text' class='form-control' placeholder='Szakaszcím'></input>";
        }else if(name=="Szakasz bekezdés"){
            return "<textarea class='form-control' placeholder='Szakasz bekezdés'></textarea>";
        }else if(name=="Hosszú szöveg"){
            return "<p class='text text-secondary fw-light'>Hosszú szöveg</br><span class='text text-danger'>Nem tartozik hozzá beállítás.</span></p>";
        }else if(name=="Feleletválasztós"){
            return "TODO";
        }else if(name=="Jelölőnégyzet"){
            return "TODO";
        }else if(name=="Legördülő lista"){
            return "TODO";
        }else if(name=="Lineáris skála"){
            return "<input type='number' class='form-control' placeholder='min'></input>-<input type='number' class='form-control' placeholder=max></input>";
        }

    }


    // //Every 10 seconds, save the form
    // setInterval(function(){
    //     //Add growing spinner to the first h6
    //     document.getElementById("time").innerHTML="<div class='spinner-grow text-success' role='status'></div>"
    //     saveForm();
    // }, 10000);


    function saveForm(){
        //Get all elements
        var elements = document.getElementsByClassName("formElementIcon");
        var formName = document.getElementsByTagName("input")[0].value;
        var formElements = [];
        for(var i=0;i<elements.length;i++){
            var element = elements[i];
            var elementName = element.getAttribute("name");
            var elementId = element.getAttribute("id");
            var elementSettings = document.getElementById(elementId+"-settings");
            var elementSettingsText = "";
            if(elementSettings!=null){
                elementSettingsText = elementSettings.innerHTML;
                if(elementName=="Szakaszcím"){
                    elementSettingsText = elementSettings.getElementsByTagName("input")[0].value;
                }else if(elementName=="Szakasz bekezdés"){
                    elementSettingsText = elementSettings.getElementsByTagName("textarea")[0].value;
                }else if(elementName=="Lineáris skála"){
                    elementSettingsText = elementSettings.getElementsByTagName("input")[0].value+"-"+elementSettings.getElementsByTagName("input")[1].value;
                }else{
                    elementSettingsText = "";
                }
            }
            var formElement = {
                "name":elementName,
                "id":elementId,
                "settings":elementSettingsText
            };
            formElements.push(formElement);
        }
        var form = {
            "name":formName,
            "elements":formElements
        };
        var formJson = JSON.stringify(form);
        console.log(JSON.parse(formJson));
        
        var formState = document.getElementById("formState").value;
        var accessRestrict = document.getElementById("accessRestrict").value;
        //Send form to server
         $.ajax({
             type: "POST",
             url: "../formManager.php",
             data: {form:formJson,formState:formState,accessRestrict:accessRestrict, mode:"save",id:<?php echo $_GET['formId'] ?>},
             success: function(data){
                 console.log(data);

                 //If data is 200, append a span with a message after time
                    if(data==200){
                        $('h5').append("<span class='saveStatus text text-success'>Mentve</span>");
                    }else{
                        $('h5').append("<span class='saveStatus text text-danger'>Hiba</span>");
                    }

                    //remove message after 2 seconds
                    setTimeout(function(){
                         $('.saveStatus').remove();
                    }, 2000);


             }
         });
    }

    function deleteForm(){
        //Send request to server
         $.ajax({
             type: "POST",
             url: "../formManager.php",
             data: {mode:"deleteForm",id:<?php echo $_GET['formId'] ?>},
             success: function(data){
                 //console.log(data);
                 window.location.href = "index.php";
             }
         });
    }
</script> 