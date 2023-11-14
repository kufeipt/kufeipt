<?php
require "../include/bittorrent.php";
dbconn(true);

loggedinorreturn(true);
stdhead($lang_index['head_home']);
//begin_main_frame();
//KPS("-",889,1)
if (get_user_class() >= UC_STAFFLEADER || $CURUSER["id"] == '5' || $CURUSER["id"] == '13403' || $CURUSER["id"] == '14084' || $CURUSER["id"] == '14460') {
    if (get_user_class() >= UC_STAFFLEADER || $CURUSER["id"] == '5' || $CURUSER["id"] == '13403' || $CURUSER["id"] == '14084' || $CURUSER["id"] == '14460') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['delete'])) {
                $idToDelete = (int)$_POST['delete']; // 获取要删除的记录的ID
                KPS("+",100000,$idToDelete);
            }
        }
    }
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
            <h1 style='text-align: center'>保种组任务量完成情况</h1>
    <table border='1' width='100%' cellspacing='0' cellpadding='9'>
            <tr>
                <td class='colhead'><b>用户名</b></td>
                <td class='colhead'><b>用户ID</b></td>
                <td class='colhead'><b>任务量</b></td>
                <td class='colhead'><b>完成量</b></td>
                <td class='colhead'><b>操作</b></td>
            </tr>
<?php
$sql = "select * from userzmpt where class = '保种组'";
$result = sql_query($sql);
while($row2 = mysqli_fetch_assoc($result)) {
    $sql2 = "select * from peers where userid=" . $row2['id'];
    $result1 = sql_query($sql2);
    $name = $row2['username'];
    $a = 0;
    if (mysqli_num_rows($result1) > 0) {
        // 输出数据
        while($row = mysqli_fetch_assoc($result1)) {
            $sql3 = "select * from torrents where id=" .$row["torrent"];
            $result2 = sql_query($sql3);
            $row1 = mysqli_fetch_assoc($result2);
            $a= $a + $row1['size'];
           // echo '账号：' . 'wanda' . "| 种子ID：" . $row["torrent"] . '种子大小' . $row1["size"] .'</br>';
           }
    }else{
        echo "0 结果";
    }
    if($a < 1024){
        echo "<tr style='font-size:15px'><th><a href='https://zmpt.cc/nexusphp/users/".$row2['id']."'>".$row2['username']."</a></th><th>".$row2['id']."</th><th>".$row2['renwu'] .' TB'."</th><th>".round($a , 3) . ' B'."</th><th>
            <form method='post' class='layui-form' lay-filter='deleteForm'>
            <input type='hidden' name='delete' value='{$arr['id']}' />
            <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>发工资</button>
            </form></th>
        </tr>";
    }elseif($a > 1024 && $a < 1024 * 1024){
        echo "<tr style='font-size:15px'><th><a href='https://zmpt.cc/nexusphp/users/".$row2['id']."'>".$row2['username']."</a></th><th>".$row2['id']."</th><th>".$row2['renwu'].' TB'."</th><th>".round($a / 1024 , 3) . ' KB'."</th><th>
            <form method='post' class='layui-form' lay-filter='deleteForm'>
            <input type='hidden' name='delete' value='{$arr['id']}' />
            <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>发工资</button>
            </form></th>
        </tr>";
    }elseif($a > 1024 * 1024 && $a < 1024 * 1024 * 1024){
        echo '用户:' . $name . ' 保种量:' . round($a / 1024 / 1024 , 3) . ' MB';
        echo "<tr style='font-size:15px'><th><a href='https://zmpt.cc/nexusphp/users/".$row2['id']."'>".$row2['username']."</a></th><th>".$row2['id']."</th><th>".$row2['renwu'].' TB'."</th><th>".round($a / 1024 / 1024 , 3) . 'MB'."</th><th>
            <form method='post' class='layui-form' lay-filter='deleteForm'>
            <input type='hidden' name='delete' value='{$arr['id']}' />
            <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>发工资</button>
            </form></th>
        </tr>";
    }elseif($a > 1024 * 1024 * 1024 && $a < 1024 * 1024 * 1024 * 1024){
        echo "<tr style='font-size:15px'><th><a href='https://zmpt.cc/nexusphp/users/".$row2['id']."'>".$row2['username']."</a></th><th>".$row2['id']."</th><th>".$row2['renwu'].' TB'."</th><th>".round($a / 1024 / 1024 /1024 , 3) . ' GB'."</th><th>
            <form method='post' class='layui-form' lay-filter='deleteForm'>
            <input type='hidden' name='delete' value='{$arr['id']}' />
            <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>发工资</button>
            </form></th>
        </tr>";
    }elseif($a > 1024 * 1024 * 1024 * 1024){
        echo "<tr style='font-size:15px'><th><a href='https://zmpt.cc/nexusphp/users/".$row2['id']."'>".$row2['username']."</a></th><th>".$row2['id']."</th><th>".$row2['renwu'].' TB'."</th><th>".round($a / 1024 / 1024 /1024 /1024 , 3) . ' TB'."</th><th>
            <form method='post' class='layui-form' lay-filter='deleteForm'>
            <input type='hidden' name='delete' value='{$row2['id']}' />
            <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>发工资</button>
            </form></th>
        </tr>";
    }
}
}else{
    echo "你没有操作权限！";
}
end_main_frame();
stdfoot();
?>
<script src="../vendor/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form;
        var layer = layui.layer;

        // 监听确认删除按钮
        form.on('submit(confirmDelete)', function(data){
            layer.confirm('确认要发放工资吗？', function(index){
                layer.close(index);
                // 执行删除操作
                data.form.submit();
            });
        });
    });
</script>