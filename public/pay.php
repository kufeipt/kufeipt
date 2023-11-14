<?php
require "../include/bittorrent.php";
dbconn(true);
stdhead("有效发种排行");
//require_once(get_langfile_path());
loggedinorreturn(true);
$id =  $_POST['id'];
$time =  $_POST['time'];
$passkey = $_POST['passkey'];
if ($id == "") {
    echo "<script>alert('请填入ID!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
}elseif($time == ""){
    echo "<script>alert('请填入到期时间!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
}else {
    if($passkey=="2986641"){
        if(sql_query("Update users set vip_added='yes',vip_until='".$time."',class='10' where id='".$id."'"){  
            echo "<script>alert('VIP时长设置成功!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }else{  
            echo "<script>alert('未成功!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
        }
        mysql_close($conn);
    }else{
        echo "<script>alert('passkey错误!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
    }
}
end_main_frame();
stdfoot();
?>