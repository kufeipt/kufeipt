<?php
require "../include/bittorrent.php";
dbconn();
//require_once(get_langfile_path());
loggedinorreturn();
if (get_user_class() >= UC_STAFFLEADER || $CURUSER["id"] == '15766') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete1'])) {
            $idToDelete = (int)$_POST['delete1']; // 获取要删除的记录的ID
            sql_query("DELETE FROM `user_auth` WHERE `qq` = $idToDelete");
        }
        if (isset($_POST['delete'])) {
            $idToDelete = (int)$_POST['delete']; // 获取要删除的记录的ID
            sql_query("DELETE FROM `user_auth` WHERE `id` = $idToDelete");
        } elseif (isset($_POST['qq'])) {
            $qq = $_POST['qq'];
            $uid = $_POST['uid'];
            $auth_count = (int)$_POST['auth_count']; // 获取授权数
            if (empty($auth_count)){
                bark('必须填写授权数!');
            }
            $now = now(); // 获取当前时间
            $sql = sql_query("INSERT INTO `user_auth` (`id`, `qq`, `userid`, `mac`, `cpu`, `userkey` , `auth_str`, `created_at`) VALUES (NULL, '$qq', '$uid', '', '', NULL, '$auth_count' ,'$now')");
        }
        nexus_redirect($_SERVER['REQUEST_URI']);
    }

    $sql = sql_query("SELECT * FROM `user_auth` WHERE `userkey` IS NOT NULL");
    $sql1 = sql_query("SELECT * FROM `user_auth` WHERE `userkey` IS NULL");

    stdhead('Authorization Management');
    begin_main_frame();

    echo "<h1 style='text-align: center'>授权管理</h1>";
    echo "<table border='1' width='100%' cellspacing='0' cellpadding='9'>
            <tr>
                <td class='colhead'><b>user</b></td>
                <td class='colhead'><b>QQ</b></td>
                <td class='colhead'><b>授权数</b></td>
                <td class='colhead'><b>添加时间</b></td>
                <td class='colhead'><b>操作</b></td>
            </tr>";
    while ($arr1 = mysql_fetch_array($sql1)) {
        echo "<tr class='rowfollow'>
                <td>".get_username($arr1['userid'])."</td>
                <td>{$arr1['qq']}</td>
                <td>{$arr1['auth_str']}</td>
                <td>{$arr1['created_at']}</td>
                <td>
                    <form method='post' class='layui-form' lay-filter='deleteForm'>
                        <input type='hidden' name='delete1' value='{$arr1['qq']}' />
                        <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>全部删除</button>
                    </form>
                </td>
            </tr>";
    }
    echo "</table>";
    // 显示已有的记录
    echo "<h1 style='text-align: center'>授权信息</h1>";
    echo "<table border='1' width='100%' cellspacing='0' cellpadding='9'>
            <tr>
                <td class='colhead'><b>user</b></td>
                <td class='colhead'><b>QQ</b></td>
                <td class='colhead'><b>MAC</b></td>
                <td class='colhead'><b>添加时间</b></td>
                <td class='colhead'><b>操作</b></td>
            </tr>";

    while ($arr = mysql_fetch_array($sql)) {
        echo "<tr class='rowfollow'>
                <td>".get_username($arr['userid'])."</td>
                <td>{$arr['qq']}</td>
                <td>{$arr['mac']}</td>
                <td>{$arr['created_at']}</td>
                <td>
                    <form method='post' class='layui-form' lay-filter='deleteForm'>
                        <input type='hidden' name='delete' value='{$arr['id']}' />
                        <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>删除</button>
                    </form>
                </td>
            </tr>";
    }

    echo "</table>";

    // 添加新 QQ 用户的表单
    echo "<h2>添加新用户</h2>";
    echo "<form method='post'>
            <label for='UID'>UID：</label>
            <input type='text' name='uid' required>
            <label for='qq'>QQ：</label>
            <input type='text' name='qq' required>
            <label for='auth_count'>授权数：</label>
            <input type='text' name='auth_count' required>
            <button type='submit'>添加</button>
          </form>";

    end_main_frame();
    stdfoot();
} else {
    stderr("Error", "Access denied.");
}
?>
<script src="../vendor/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form;
        var layer = layui.layer;

        // 监听确认删除按钮
        form.on('submit(confirmDelete)', function(data){
            layer.confirm('确认删除此记录吗？', function(index){
                layer.close(index);
                // 执行删除操作
                data.form.submit();
            });
        });
    });
</script>
