//Draw menu:
      //Import menu items
      var menuItems = null;
      function importItem(fileLoc) {
        var varName = null;
        $.ajax({
          'async': false,
          'global': false,
          'url': fileLoc,
          'dataType': "json",
          'success': function(data) {
            varName = data;
          }});
        return varName;
      };
      
      function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    setInterval(function () {
     
        minutes = parseInt(timer / 60, 10)
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (timer > 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html(display.textContent).animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").text(display.textContent).animate({'opacity': 1}, 250); }, 700);;});
        }

        if (timer < 60){
          $('#time').animate({'opacity': 0.9}, 0, function(){
          $(this).html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 500);
          setTimeout(function() { $("#time").html("<font color='red'>"+display.textContent+"</font").animate({'opacity': 1}, 250); }, 700);;});
        }

        if (--timer < 0) {
            timer = duration;
            window.location.href = "utility/logout.ut.php"
        }
    }, 1000);
};

      //Loop trough Menu left side
      //jumpupfolderstruct: ha almappában van, és feljebb kell ugrani.
      function drawMenuItemsLeft(activeName,menuItems,jumpupFolderStruct=1){
        for (i = 0; i < menuItems.menu.left.length; i++) {
        //draw item on top side
         //console.log(menuItems.menu.left[i]);
         if (menuItems.menu.left[i].name == activeName){
          $('.navbarUl').append('<li class="nav-item active imported"><a class="nav-link" href="'+(".").repeat(jumpupFolderStruct)+menuItems.menu.left[i].href+'"><i class="'+menuItems.menu.left[i].icon+'"></i></a></li>');
         }else{
          $('.navbarUl').append('<li class="nav-item imported"><a class="nav-link" href="'+(".").repeat(jumpupFolderStruct)+menuItems.menu.left[i].href+'"><i class="'+menuItems.menu.left[i].icon+'"></i></a></li>');
         }
         }};
        function drawMenuItemsRight(activeName,menuItems,jumpupFolderStruct=1){
            console.log("drawMenuItemsRight called.")
            if (menuItems){
                for (i = 0; i < menuItems.menu.right.length; i++) {
                    //draw item on top side
                     console.log(menuItems.menu.right[i]);
                     if (menuItems.menu.right[i].name == activeName){
                      $('.menuRight').append('<a class="nav-link my-2 my-sm-0 active" href="'+(".").repeat(jumpupFolderStruct)+menuItems.menu.right[i].href+'"><i class="'+menuItems.menu.right[i].icon+'"></i></a>');
                     }else{
                      $('.menuRight').append('<a class="nav-link my-2 my-sm-0" href="'+(".").repeat(jumpupFolderStruct)+menuItems.menu.right[i].href+'"><i class="'+menuItems.menu.right[i].icon+'"></i></a>');
                     } 
            }}else{
                console.log("menItems not defined!")
            }
            
             };

    function drawIndexTable(menuItems,jumpupFolderStruct=1){

        console.log("Drawing Main Page table");
        if (menuItems){
            for (i = 0; i < menuItems.indexTable.length; i++) {
                //console.log(i,menuItems.indexTable[i])
                if(i%2==0){
                    $('.mainRow'+Math.round((i+1)/2)).append(`<div class="col-6 col-sm-2"><a class="nav-link ab" href="${(".").repeat(jumpupFolderStruct)+menuItems.indexTable[i].href}"><i class="${menuItems.indexTable[i].icon}"></i><br><h5>${menuItems.indexTable[i].displayName}</h5></a></div>`);
                    //$('.mainRow'+Math.round(i/2)).append('<div class="col-6 col-sm-2"><a class="nav-link ab" href="./takeout.php"><i class="fas fa-upload fa-3x"></i><br><h5>Debug</h5></a></div>');
                }else{
                    $('.mainRow'+Math.round((i+1)/2)).append(`<div class="col-6 col-sm-2 offset-md-1"><a class="nav-link ab" href="${(".").repeat(jumpupFolderStruct)+menuItems.indexTable[i].href}"><i class="${menuItems.indexTable[i].icon}"></i><br><h5>${menuItems.indexTable[i].displayName}</h5></a></div>`);
                    //$('.mainRow'+Math.round(i/2)).append('<div class="col-6 col-sm-2"><a class="nav-link ab" href="./takeout.php"><i class="fas fa-upload fa-3x"></i><br><h5>Debug</h5></a></div>');
                }
                 
            }
        }
    }
    console.log("_initMenu loaded.")