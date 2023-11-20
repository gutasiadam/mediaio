//Draw menu:
console.log("_initMenu file called, loading...");
// Begin by displaying spinner
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
      
function startTimer(display, timeUpLoc,minutes=10) {
  //Self correcting timer, for auto logout
  var start = Date.now()/1000;//in seconds
    var duration = start+(60*minutes+1); //Auto-logout after minutes
    var timer = duration, minutes, seconds;
    console.log("Timer start with" + duration);
  setInterval(function () {
    var secondsLeft=duration-Date.now()/1000;

    minutes = parseInt(secondsLeft / 60, 10)
    seconds = parseInt(secondsLeft % 60, 10);

    minutes = minutes < 10 ? "0" + minutes : minutes;
    seconds = seconds < 10 ? "0" + seconds : seconds;

    display.textContent = minutes + ":" + seconds;

    if (secondsLeft > 60) {
      $('#time').animate({ 'opacity': 0.75 }, 0, function () {
        $(this).html(display.textContent).animate({ 'opacity': 1 }, 500);
        setTimeout(function () { $("#time").text(display.textContent).animate({ 'opacity': 1 }, 250); }, 700);;
      });
    }

    if (secondsLeft < 60) {
      $('#time').animate({ 'opacity': 0.75 }, 0, function () {
        $(this).html("<font color='red'>" + display.textContent + "</font").animate({ 'opacity': 1 }, 500);
        setTimeout(function () { $("#time").html("<font color='red'>" + display.textContent + "</font").animate({ 'opacity': 1 }, 250); }, 700);;
      });
    }

    if (--secondsLeft < 0) {
      secondsLeft = 0;
      //window.location.href = hrefLoc; // Minden oldalon meg kell adni, hova ugorjon az oldal, ha lejárt a timer.
      window.location.href = timeUpLoc;
    }
  }, 1000);
};

      //Loop trough Menu left side
      //jumpupfolderstruct: ha almappában van, és feljebb kell ugrani.
      function drawMenuItemsLeft(activeName, menuItems, jumpupFolderStruct = 1) {
        for (let i = 0; i < menuItems.menu.left.length; i++) {
          if (menuItems.menu.left[i].hasOwnProperty('target') === false) {
            // Add target field if not defined
            menuItems.menu.left[i].target = "_self";
          }
      
          // Draw item on top side
          if (menuItems.menu.left[i].name == activeName) {
            $('.navbarUl').append('<li class="nav-item active imported"><a class="nav-link" target="' + menuItems.menu.left[i].target + '" href="' + ("../").repeat(jumpupFolderStruct - 1) + menuItems.menu.left[i].href + '"><i class="' + menuItems.menu.left[i].icon + '"></i></a></li>');
          } else {
            $('.navbarUl').append('<li class="nav-item imported"><a class="nav-link" target="' + menuItems.menu.left[i].target + '" href="' + ("../").repeat(jumpupFolderStruct - 1) + menuItems.menu.left[i].href + '"><i class="' + menuItems.menu.left[i].icon + '"></i></a></li>');
          }
        }
      }
      

      function drawMenuItemsRight(activeName, menuItems, jumpupFolderStruct = 1) {
        console.log("drawMenuItemsRight called.")
        if (menuItems && menuItems.menu && menuItems.menu.right) {
          for (let i = 0; i < menuItems.menu.right.length; i++) {
            if (menuItems.menu.right[i].hasOwnProperty('target') === false) {
              menuItems.menu.right[i].target = "_self";
            }
      
            // Draw item on top side
            console.log(menuItems.menu.right[i]);
            if (menuItems.menu.right[i].name == activeName) {
              $('.menuRight').append('<a class="nav-link my-2 my-sm-0 active" target="' + menuItems.menu.right[i].target + '" href="' + ("../").repeat(jumpupFolderStruct - 1) + menuItems.menu.right[i].href + '"><i class="' + menuItems.menu.right[i].icon + '"></i></a>');
            } else {
              $('.menuRight').append('<a class="nav-link my-2 my-sm-0" target="' + menuItems.menu.right[i].target + '" href="' + ("../").repeat(jumpupFolderStruct - 1) + menuItems.menu.right[i].href + '"><i class="' + menuItems.menu.right[i].icon + '"></i></a>');
            }
          }
        } else {
          console.log("menuItems or its properties are not defined!");
        }
      }
      

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