<?php
require "../include/bittorrent.php";
dbconn();

function auth($userkey,$key){
    if ($userkey == $key) {
        $data = array(
        'code' => $userkey,
        );
        $json = json_encode($data);
        header('Content-Type: application/json');
        echo $json;
    }else{
        $data = array(
        'code' => base64_encode('Binding mismatch')
        );
        $json = json_encode($data);
        header('Content-Type: application/json');
        echo $json;
    }
}

function auth_sql($qq,$mac,$cpu){
    $sql = sql_query("SELECT * FROM `user_auth` WHERE `qq` = '$qq' AND `mac` = '$mac' AND `cpu` = '$cpu'");
    $row1 = mysql_fetch_array($sql);
    $count = mysql_num_rows($sql);
    return array('count' => $count, 'row' => $row1);
}

function auth_count($qq){
    $sql1 = sql_query("SELECT * FROM `user_auth` WHERE `qq` = '$qq' and `auth_str` IS NOT NULL LIMIT 1");
    $row = mysql_fetch_array($sql1);
    return $row;
}
$qq = base64_decode($_GET['qq']);
$cpu = base64_decode($_GET['cpu']);
$mac = base64_decode($_GET['mac']);
// 检查qq值是否已存在
$sql_check_qq = sql_query("SELECT COUNT(*) as count FROM `user_auth` WHERE qq=".$qq);
$row_check_qq = mysql_fetch_array($sql_check_qq);
$qq_count = $row_check_qq['count'];
if (!empty($qq) && !empty($cpu) && !empty($mac)) {
    if ($qq_count > 0) {
        // 检查重复次数是否超过5次
        $row = auth_count($qq);
        $sql_check_repeats = sql_query("SELECT COUNT(*) as count FROM `user_auth` WHERE qq='$qq' AND `auth_str` IS NULL");
        $row_check_repeats = mysql_fetch_array($sql_check_repeats);
        $count_auth = $row_check_repeats['count'];
        if ($count_auth < $row['auth_str'] ){
            $result = auth_sql($qq, $mac, $cpu);
            $count = $result['count'];
            $row1 = $result['row'];
            $key= base64_encode($cpu.$mac);
            if ($count === 1){
                auth($row1['userkey'],$key);
                return;
            }else{
                //新增认证
                $now = now(); // 获取当前时间
                $row = auth_count($qq);
                $uid =$row['userid']; 
                $sql = sql_query("INSERT INTO `user_auth` (`id`, `userid`, `qq`, `mac`, `cpu`, `userkey` ,`created_at`) VALUES (NULL, '$uid', '$qq', '$mac', '$cpu', '$key', '$now')");
                $result = auth_sql($qq, $mac, $cpu);
                $row1 = $result['row'];
                if ($sql) {
                    auth($row1['userkey'],$key);
                    return;
                } else {
                    //添加授权失败
                    $data = array(
                    'code' => base64_encode('Update operation not executed.')
                    );
                    $json = json_encode($data);
                    header('Content-Type: application/json');
                    echo $json;
                    return;
                }
            }
        }elseif ($count_auth === $row['auth_str']) {
            $result = auth_sql($qq, $mac, $cpu);
            $row1 = $result['row'];
            $key= base64_encode($cpu.$mac);
            auth($row1['userkey'],$key);
            return;
        }else{
            //授权上限
            $data = array(
            'code' => base64_encode('Authorization limit reached')
            );
            $json = json_encode($data);
            header('Content-Type: application/json');
            echo $json;
            return;
        }
    }else{
        //不存在用户
        $data = array(
        'code' => base64_encode('user does not exist')
        );
        $json = json_encode($data);
        header('Content-Type: application/json');
        echo $json;
        return;
    }
}else{
    //非法请求
$data = array(
'code' => base64_encode('Data cannot be empty')
);
$json = json_encode($data);
header('Content-Type: application/json');
echo $json;
return;
}

?>