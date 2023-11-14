<?php
require "../include/bittorrent.php";
dbconn(true);

loggedinorreturn(true);
stdhead($lang_index['head_home']);
//begin_main_frame();
//KPS("-",889,1)
if (get_user_class() >= UC_STAFFLEADER || $CURUSER["id"] == '5' || $CURUSER["id"] == '13403' || $CURUSER["id"] == '14084' || $CURUSER["id"] == '14460'  || $CURUSER["id"] == '2' || $CURUSER["id"] == '15766') {
    if (get_user_class() >= UC_STAFFLEADER || $CURUSER["id"] == '5' || $CURUSER["id"] == '13403' || $CURUSER["id"] == '14084' || $CURUSER["id"] == '14460'  || $CURUSER["id"] == '2' || $CURUSER["id"] == '15766') {
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
    $sql = "select * from userzmpt where class = '转载组'";
    $result = sql_query($sql);
    while($row2 = mysqli_fetch_assoc($result)) {
        $sql2 = "select count(owner) as times,owner from torrents where added > '2023-08-01 00:00:00' and owner = ".$row2['id'];
        $result1 = sql_query($sql2);
        $myrow=mysqli_fetch_assoc($result1);
        $name = $row2['username'];
            echo "<tr style='font-size:15px'>
            <th><a href='https://zmpt.cc/nexusphp/users/".$row2['id']."'>".$row2['username']."</a></th>
            <th>".$row2['id']."</th>
            <th>".$row2['renwu']."</th>
            <th>".$myrow['times']."</th>
            <th>
                <form method='post' class='layui-form' lay-filter='deleteForm'>
                <input type='hidden' name='delete' value='{$arr['id']}' />
                <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>发工资</button>
                </form></th>
            </tr>";
    }
}else {
    echo "您没有访问权限！";
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