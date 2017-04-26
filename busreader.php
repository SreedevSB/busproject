<?php

$con = mysqli_connect("127.0.0.1", "root", "", "test");

$sql = "SELECT * FROM busproject";

$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($result)){
    $buses[]=$row;
}
if($_GET['type']=='json'){
		$colour=['red','green','orange'];
        $maparr=array();
        $i=0;
        $avlat=0;$avlng=0;
        foreach( $buses as $bus){
            $maparr[$i][0]=$bus['id'];
            $maparr[$i][1]=$bus['latitude'];
            $maparr[$i][2]=$bus['longitude'];
            $maparr[$i][3]=$colour[$bus['id']-1];
            $i=$i+1;
            $avlat=$bus['latitude']+$avlat;
            $avlng=$bus['longitude']+$avlng;
        }
        $avlat=$avlat/$i;
        $avlng=$avlng/$i;
	echo json_encode($maparr);
}else{?>
	<div class="col-md-8 col-md-offset-2">
                <div class="row">
                <table class="table table-striped table-responsive">
                    <tr style="background-color: #3D3D3D;color:white;">
                        <td>SL.No</td>
                        <td>Marker</td>
                        <td>Vacant Seat</td>
                        <td>Exp. Time</td>
                        <td>Speed</td>
                        <td>Place</td>
                        <td>Total Fare</td>
                    </tr>
                    <?php
                    $colour=['red','green','orange'];
                    foreach($buses as $bus){?>
                    <tr>
                        <td><?php echo $bus['id'];?></td>
                        <td><div style="width:20px;height:20px;margin:0px auto;background-color:<?php echo $colour[$bus['id']-1];?>;border-radius:20px;"> </div></td>
                        <td><?php echo $bus['vacanseat'];?></td>
                        <td><?php echo $bus['exptime'];?> hours</td>
                        <td><?php echo $bus['speed'];?> kmph</td>
                        <td><?php echo $bus['place'];?></td>
                        <td>Rs. <?php echo $bus['Totalfare'];?></td>
                    </tr>
                    <?php }?>
                </table>
                </div>
            </div>
<?php }
?>