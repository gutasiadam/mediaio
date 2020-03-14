<html>
<head>
  <script src="JTranslations.js"></script>
  <link rel="stylesheet" href="./main.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js">  </script>
  <script src="https://kit.fontawesome.com/2c66dc83e7.js" crossorigin="anonymous"></script>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $applicationTitleShort." Retrieve"; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<script>
function getVote(int) {
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      document.getElementById("poll").innerHTML=this.responseText;
    }
  }
  xmlhttp.open("GET","poll_vote.php?vote="+int,true);
  xmlhttp.send();
}
</script>
</head>
<body>

<div id="poll" >
<h5>Szavazás</h5>
<h3 style="border 2px black">{Szavazás témája}</h3>
<form>
  <div class="form-group">
    <label for="exampleInputPassword1">Feb. 3, hétfő</label>
    <input type="radio" name="vote" value="0" onclick="getVote(this.value)">
  </div>
  <div class="form-group">
    <label class="form-check-label" for="exampleCheck1">Feb. 4, kedd</label>
    <input type="radio" name="vote" value="1" onclick="getVote(this.value)">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Feb.5, szerda</label>
    <input type="radio" name="vote" value="2" onclick="getVote(this.value)">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Feb. 6, csütörtök</label>
    <input type="radio" name="vote" value="3" onclick="getVote(this.value)">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Feb. 7, péntek</label>
    <input type="radio" name="vote" value="4" onclick="getVote(this.value)">
  </div>
  <!-- <button type="submit" class="btn btn-primary">Submit</button> -->
</form>
</body>
</html>

<style>
#poll{
  width: 15%;
  text-align: center;
  margin: 0 auto; 
  border: 2px solid black;
}
</style>

