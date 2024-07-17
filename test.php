<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
  <title>PHP - Jquery Chosen Ajax Autocomplete Example - ItSolutionStuff.com</title>
  <link rel="stylesheet" href="bootstrap.min.css" />

 
  <script src="jquery.min.js"></script>
  <link rel="stylesheet" href="chosen.css" />
  <script src="chosen.jquery.min.js"></script>
  <script src="jquery-ui.min.js"></script>
</head>
<body>
 <?php
    
    $hostName = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "test";
 
    $mysqli = new mysqli($hostName, $username, $password, $dbname);
 
    $sql = "SELECT * FROM countries WHERE name LIKE '%".$_GET['name']."%'";
 
    $result = $mysqli->query($sql);
 
    $response = [];
    while($row = mysqli_fetch_assoc($result)){
       $response[] = array("id"=>$row['id'], "name"=>$row['name']);
    }
 
    echo json_encode($response);
 
?>
<div class="container">
  <div class="panel panel-default">
    <div class="panel-heading">PHP - Jquery Chosen Ajax Autocomplete Example - ItSolutionStuff.com</div>
    <div class="panel-body">
      <form>
        <select class="form-control select-box">
          <option>Select Option</option>
        </select>
      </form>
    </div>
  </div>
</div>
 
<script type="text/javascript">
  $(".select-box").chosen();
 
  $('.chosen-search input').autocomplete({
    source: function( request, response ) {
      $.ajax({
        url: "ajaxpro.php?name="+request.term,
        dataType: "json",
        success: function( data ) {
          $('.select-box').empty();
          response( $.map( data, function( item ) {
            $('.select-box').append('<option value="'+item.id+'">' + item.name + '</option>');
          }));
          $(".select-box").trigger("chosen:updated");
        }
      });
    }
  });
</script>
 
</body>

</html>
