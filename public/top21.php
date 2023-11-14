<?php
$loclhost = "127.0.0.1:3306";
$dbuser = "zmpt_cc";
$dbpass = "2986641";
$dbname = "zmpt_cc";

$conn = mysqli_connect($loclhost, $dbuser, $dbpass, $dbname);
$sql = "select count(owner) as times,owner from torrents where added > '2022-10-01 00:00:00' and seeders >= 3 group by owner order by times desc";
$res=mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html>
    <head>
        <title>织梦PT有效发种排行！</title>
    </head>
    <style>
        #header {
            display:block; 
            text-align: 
            center;
            color: 
        }
    </style>
    <body>
        <div id="header">
        <center>
        <table border='1'>
            <caption><b>有效发种排行</b><br/></caption>
            <tr><th>用户ID</th><th>有效发种数</th></tr>
<?php
while($myrow=mysqli_fetch_assoc($res)){
    echo "<tr><th>".$myrow['owner']."</th><th>".$myrow['times']."</th></tr>";
}
mysqli_close($conn);
?>
        </table>
        </center>
        </div>
    </body>
</html>