<?php

$id = $_POST["id"];
$con = mysqli_connect("localhost:3306", "numciaco_user", "pass@2019", "numciaco_database");

$sql = "SELECT * FROM busproject";

$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($result)){
    $buses[]=$row;
}

echo json_encode($buses);
?>