<!DOCTYPE html>
<html>
	<head>
	   <title>User Dashboard</title>        
       <meta name="viewport"content="width=device-width,height=device-height, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no,user-scalable=0">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <link rel="icon" href="favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
			<style>
				html,body{
					margin:0px;
					padding:0px;
					width:100%;
					height:100%;
                    box-sizing:border-box;
				}
                *{
                    box-sizing: border-box;
                }*:after{
                    clear:both;
                    display: block;
                    content: " ";
                }
                .title{
                    color:white;
                    text-shadow: 1px 1px 5px gray;
                    font-size: 28px;
                    font-family: Candara;
                }
				#map {
					margin: 0px auto;
					padding: 0px;
				}
                td{
                    text-align:center;
                }
                table,.mapbox{
                    box-shadow: 1px 1px 10px 1px rgba(200,200,200,0.5);
                }
                .heading{
                    float: left;
                    border:1px solid orange;
                    background-color: #FFFFFF;
                    color: white;
                    font-size: 24px;
                    padding: 2px 20px;
                    font-family: Segoe Ui Light;
                    color: black;
                    margin: 50px 0px 30px 0px;
                    border-radius: 5px;
                }

        @media (max-width: 992px) {
                .container-fluid{
                    padding:0px 20px; 
                }
                .titlebar{
                    margin:0px;
                }
                .imgblock{
                    float:none;
                }

        }
			</style>
    </head>
    <body style="background-color: #F1F4F5">
    <div class="container-fluid">
        <div class="row" style="margin-top:40px">
            <?php $con = mysqli_connect("127.0.0.1", "root", "", "test");
                $sql = "SELECT * FROM busproject";

                $result = mysqli_query($con,$sql);
                while($row = mysqli_fetch_assoc($result)){
                    $buses[]=$row;
            }?>
            <div class="col-md-8 col-md-offset-2"><div class="row"><span class="heading">List of Buses</span></div></div>
            <div  id="maptable" class="col-md-8 col-md-offset-2">
                <div class="row">
                <table class="table table-striped table-responsive">
                    <tr style="background-color: #3D3D3D;color:white;">
                        <td>SL.No</td>
                        <td>Marker</td>
                        <td>Vacant Seat</td>
                        <td>Trip Time</td>
                        <td>Speed</td>
                        <td>Place</td>
                    </tr>
                    <?php
                    $colour=['red','green','orange','pink','purple','yellow'];
                    foreach($buses as $bus){?>
                    <tr>
                        <td><?php echo $bus['id'];?></td>
                        <td><div style="width:20px;height:20px;margin:0px auto;background-color:<?php echo $colour[$bus['id']-1];?>;border-radius:20px;"> </div></td>
                        <td><?php echo $bus['vacanseat'];?></td>
                        <td><?php echo $bus['exptime'];?> hours</td>
                        <td><?php echo $bus['speed'];?> kmph</td>
                        <td><?php echo $bus['place'];?></td>
                    </tr>
                    <?php }?>
                </table>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>
        $(function() {
            getMapTable = function() {
            $.post('user-busreader.php', {type:'hi'}, function(data)
            { 
                $('#maptable').html(data);
                setTimeout(function() {
                            getMapTable();
                 }, 1000);
            });
            }
            getMapTable();
            }
        );
        </script>
        <?php $colour=['red','green','orange','pink','purple','yellow'];
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

        $map_json=json_encode($maparr,JSON_HEX_TAG);
        //print_r($map_json);?>
    	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDsaLEUOaPgik36fDLVG9QY7dsY_AOVetw"></script>
            <div class="row">
                <div class="col-md-8 col-md-offset-2"><div class="row"><span class="heading">Live Tracking</span></div></div>
                <div class="col-md-8 col-md-offset-2 mapbox" style="background-color:white;padding:2px;height:400px;">
        		      <div id="map" style="width:100%;height:100%;margin-bottom:100px"></div>
                </div>
            </div>
        </div>

  <script type="text/javascript">
    var locations = <?php echo $map_json;?>;

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 14,
      center: new google.maps.LatLng(<?php echo $avlat;?>, <?php echo $avlng;?>),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var infowindow = new google.maps.InfoWindow();
    var i,j;
    marker=[];
    function add_marker(lat,lng,colour,info){
    	marker[info] = new google.maps.Marker({
            position: new google.maps.LatLng(lat,lng),			    
            icon: {
          	url: 'http://maps.google.com/mapfiles/ms/icons/' + colour + '-dot.png',
      			labelOrigin: new google.maps.Point(15, 10)
        	},
            map: map
        });
        minfo=marker[info];
      google.maps.event.addListener(marker[info], 'mouseover', (function(minfo, j) {
        return function() {
          infowindow.setContent(info);
          infowindow.open(map, marker[info]);
        }
      })(marker[info], j));
    }

    var bounds = new google.maps.LatLngBounds();
    for (i = 0; i < locations.length; i++){
   		add_marker(locations[i][1],locations[i][2],locations[i][3],locations[i][0]);
        bounds.extend(marker[locations[i][0]].position);
    }
    map.fitBounds(bounds);
    
    moveMarker=function(){
                $.ajax({
                      type: 'POST',
                      url: 'busreader.php',
                      data: { 'type' : 'json' },
                      dataType: 'json',
                      success: function(newlocations) {
                            if(JSON.stringify(window.locations)!=JSON.stringify(newlocations)){
                                for (j = 0; j < newlocations.length; j++){
                                    marker[newlocations[j][0]].setPosition( new google.maps.LatLng(newlocations[j][1], newlocations[j][2]));
                                }
                               var bounds = new google.maps.LatLngBounds();
                                for (j = 0; j < newlocations.length; j++){
                                bounds.extend(marker[newlocations[j][0]].position);
                                }
                                window.map.fitBounds(bounds);
                                window.locations=newlocations;
                            }
                            setTimeout('moveMarker()',1000);
                      },
                      error: function() {
                        //alert('Error loading map');
                      }
                    });

            //map.panTo( new google.maps.LatLng( 0, 0 ) );
    }
    moveMarker();


    //var listener = google.maps.event.addListener(map, "idle", function () {
    //map.setZoom(14);
    //google.maps.event.removeListener(listener);
    //});
  </script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</body>
</html>
