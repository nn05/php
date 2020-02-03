<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "root", "root", "cv");

if (isset($_POST["import"])) {
    $fileName = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $cvsdata['uId'] = $column[0];
            $cvsdata['firstName'] = $column[1];
            $cvsdata['lastName'] = $column[2];
            $cvsdata['birthDay'] = $column[3];
            $cvsdata['dateChange'] = $column[4];
            $cvsdata['description'] = $column[5];
            $sqlSelect = "SELECT uId, dateChange FROM `cv` WHERE uId = '". $cvsdata['uId'] ."' ";
            $sqlSelectToDelete = "SELECT * FROM `cv` WHERE uId != '". $cvsdata['uId'] ."' ";
            $res = mysqli_query($conn, $sqlSelect);
            $resd = mysqli_query($conn, $sqlSelectToDelete);
            $rowd = mysqli_fetch_array($resd);
            $row = mysqli_fetch_array($res);
            echo mysqli_num_rows($res);
            if(mysqli_num_rows($res) < 1){
                $sqlInsert = "INSERT INTO cv (uId,firstName,lastName,birthDay,dateChange,description)
               values ('" . $cvsdata['uId'] . "','" . $cvsdata['firstName'] . "','" . $cvsdata['lastName'] . "','" . $cvsdata['birthDay'] . "','" . $cvsdata['dateChange'] . "','". $cvsdata['description'] ."')";
                $result = mysqli_query($conn, $sqlInsert); 
                if (! empty($result)) {
                    $type = "success";
                    $message = "info added";
                } else {
                    $type = "error";
                    $message = "error while adding info to new table";
                }
            }elseif(mysqli_num_rows($res) > 0){
                $sqlUpdate = "UPDATE cv SET firstName = '". $cvsdata['firstName'] ."', lastName = '". $cvsdata['lastName'] ."', birthDay = '". $cvsdata['birthDay'] ."', dateChange = '". $cvsdata['dateChange'] ."', description = '". $cvsdata['description'] ."' WHERE dateChange LIKE '". $row['dateChange'] . "'";
                $sqlDelete = "DELETE FROM `cv` WHERE uId != '". $cvsdata['uId'] ."'";
                $result1 = mysqli_query($conn, $sqlUpdate);
                $resultd = mysqli_query($conn, $sqlDelete);
                if (! empty($result1)) {
                    $type = "success";
                    $message = "info has been updated";
                } else{
                    $type = "error";
                    $message = "error while updating info";
                }
            }else{
                echo "ssss";
            }
        }
    }            
}
?>
<!DOCTYPE html>
<html>

<head>
<script src="jquery-3.2.1.min.js"></script>
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<script type="text/javascript">
$(document).ready(function() {
    $("#frmCSVImport").on("submit", function () {

	    $("#response").attr("class", "");
        $("#response").html("");
        var fileType = ".csv";
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");
        if (!regex.test($("#file").val().toLowerCase())) {
        	    $("#response").addClass("error");
        	    $("#response").addClass("alert alert-warning");
            $("#response").html("Invalid File. Upload : <b>" + fileType + "</b> Files.");
            return false;
        }
        return true;
    });
});
</script>
</head>

<body>
    <div class="container">
        <h2>Import CSV file into Mysql using PHP</h2>
        
        <div id="response" class="alert alert-success <?php if(!empty($type)) { echo $type . " display-block"; } ?>">
            <?php if(!empty($message)) { echo $message; } ?>
        </div>
        <div class="row">
            <form class="form-group d-flex w-100" action="" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data">
                    <label class="col control-label">Choose CSV File</label> 
                    <input class="col "type="file" name="file" id="file" accept=".csv">
                    <button class="col" type="submit" id="submit" name="import" class="btn-submit">Import</button>
            </form>
        </div>
               <?php
            $sqlSelect = "SELECT * FROM cv";
            $result = mysqli_query($conn, $sqlSelect);
            
            if (mysqli_num_rows($result) > 0) {
                ?>
            <table class="table">
            <thead class="thead-dark" >
                <tr>
                    <th class="col">uId</th>
                    <th class="col">First Name</th>
                    <th class="col">Last Name</th>
                    <th class="col">birth Day</th>
                    <th class="col">date Change</th>
                    <th class="col">description</th>

                </tr>
            </thead>
<?php
                
                while ($row = mysqli_fetch_array($result)) {
                    ?>
                    
                <tbody>
                <tr>
                    <td><?php  echo $row['uId']; ?></td>
                    <td><?php  echo $row['firstName']; ?></td>
                    <td><?php  echo $row['lastName']; ?></td>
                    <td><?php  echo $row['birthDay']; ?></td>
                    <td><?php  echo $row['dateChange']; ?></td>
                    <td><?php  echo $row['description']; ?></td>
                </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php } ?>
    </div>

</body>

</html>