<?php
require "../include/bittorrent.php";
dbconn(true);
stdhead("银行");
//require_once(get_langfile_path());
loggedinorreturn(true);
?>
<style type="text/css">
    #back_content {
        width: 80%;
        display: flex;
        gap: 15px;
    }

    #back_content button,
    #back_content div,
    #back_content input,
    #back_content ul,
    #back_content h5,
    #back_content li {
        margin: 0;
        box-sizing: border-box;

    }

    #back_content button {
        background: rgb(254 177 71);
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
        flex:1;
    }

    #back_content input {
        background: hsl(0, 0%, 93%);
        border-radius: 5px;
        padding: 10px 5px;
        border: 1px solid rgb(254 177 71);
        width:200px;
    }

    #back_left {
        flex: 1;
        display: flex;
        gap: 15px;
        flex-direction: column;
    }
    #back_left>div{
        display: flex;
        gap: 15px;
    }
    #back_left>div>div{
        flex:1;
    }
    #savings_account,
    #withdraw_money {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 10px;
    }
    #savings_account .layui-form,
    #withdraw_money .layui-form {
        width: 100%;
    }
    #back_right {
        width: 30%;

    }

    #back_left,
    #back_right {
        background-color: rgba(210, 210, 210, 0.25);
        box-shadow: 1px 1px 6px rgba(0, 0, 0, 0.3);
        border-radius: 3px;
        box-sizing: border-box;
        padding: 0 12px 12px 12px;
        padding: 20px;
    }
    div#savings_account{
        margin:0 0 15px 0;
    }
    #back_left .layui-form {
        display:flex;
        gap:10px;
    }


    #no_back_info {
        box-shadow: 1px 3px 8px rgb(254 177 71);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    #back_content h5 {
        font-size: 16px;
        color: black;
        position: relative;
        padding-left: 10px;
        margin-bottom: 10px;
        text-align: left;
    }
    #back_content li {
        text-align: left;
    }
    #back_content h5::after {
        content: "";
        width: 6px;
        height: 100%;
        left: 0;
        background-color: rgb(254 177 71);
        position: absolute;
        top: 0;
    }
    #no_back_info{
        min-height:89px;
    }
    #bank-info-btn {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    #bank-info-btn {
        flex: unset !important;
        width: 300px;
    }
    .info {
        flex: unset !important;
        width: 50%;
    }
</style>
<body>
<script src="../vendor/layui/layui.js"></script>
<div id="back_content">
    <div id="back_left">
        <!-- 我的信息 -->
        <div id="my_info">
            <?php
            $uid = sqlesc($CURUSER['id']);
            $bonus = $_POST['bonus'];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                switch ($_POST['action']) {
                    case "kaihu":
                        sql_query("INSERT INTO `user_bank_account` (`uid`, `bouns`, `rate`, `interest` , `uninterest` ) VALUES (" . $uid . ", 0.0, 0.0018, 0.0, 0.0)");
                        echo "<script type=\"text/javascript\">layer.msg(\"开户成功\",{icon:1, offset: \"auto\", time:5000})</script>";
                        break;
                    case "cunqian":
                        if (!$bonus) {
                            echo "<script type=\"text/javascript\">layer.msg(\"魔力输入不能为空\",{icon:1, offset: \"auto\", time:5000})</script>";
                            nexus_redirect($_SERVER['REQUEST_URI']);
                        } else if (!is_numeric($bonus)) {
                            echo "<script type=\"text/javascript\">layer.msg(\"魔力输入只能为数字\",{icon:1, offset: \"auto\", time:5000})</script>";
                            nexus_redirect($_SERVER['REQUEST_URI']);
                        } else if ($bonus < 0.1) {
                            echo "<script type=\"text/javascript\">layer.msg(\"魔力输入必须不小于0.1\",{icon:1, offset: \"auto\", time:5000})</script>";
                            nexus_redirect($_SERVER['REQUEST_URI']);
                        }
                        $is_enough = judge_user_bonus("-", $bonus, $uid);
                        if (!$is_enough) {
                            echo "<script type=\"text/javascript\">layer.msg(\"你的站点魔力不足\",{icon:1, offset: \"auto\", time:5000})</script>";
                        } else {
                            mysql_query("BEGIN");
                            try {
                                $rs1 = sql_query("UPDATE user_bank_account set `bouns` = `bouns` + " . sqlesc($bonus) . " WHERE `uid` = " . $uid) or sqlerr(__FILE__, __LINE__);
                                $rs2 = sql_query("INSERT INTO user_bank_account_log (`uid` ,`bouns` ,`type` ) VALUES (" . $uid . "," . sqlesc($bonus) . ",1)") or sqlerr(__FILE__, __LINE__);
                                $rs3 = sql_query("UPDATE users SET seedbonus = seedbonus - " . sqlesc($bonus) . " WHERE id = ".$uid) or sqlerr(__FILE__, __LINE__);
                                if ($rs1 && $rs2 && $rs3) {
                                    mysql_query("COMMIT");
                                    echo "<script>layer.msg(\"存入成功\",{icon:1, offset: \"auto\", time:5000})</script>";
                                    sleep(2);
                                } else {
                                    mysql_query("ROLLBACK");
                                    echo "<script type=\"text/javascript\">layer.msg(\"存入失败，请联系管理\",{icon:1, offset: \"auto\", time:5000})</script>";

                                }
                            } catch (Exception $e) {
                                mysql_query("ROLLBACK");
                                echo "<script type=\"text/javascript\">layer.msg(\"存入失败，请联系管理\",{icon:1, offset: \"auto\", time:5000})</script>";

                            }
                        }
                        break;
                    case "quqian":
                        if (!$bonus) {
                            echo "<script type=\"text/javascript\">layer.msg(\"魔力输入不能为空\",{icon:1, offset: \"auto\", time:5000})</script>";

                            nexus_redirect($_SERVER['REQUEST_URI']);
                        } else if (!is_numeric($bonus)) {
                            echo "<script type=\"text/javascript\">layer.msg(\"魔力输入只能为数字\",{icon:1, offset: \"auto\", time:5000})</script>";

                            nexus_redirect($_SERVER['REQUEST_URI']);
                        } else if ($bonus < 0.1) {
                            echo "<script type=\"text/javascript\">layer.msg(\"魔力输入必须不小于0.1\",{icon:1, offset: \"auto\", time:5000})</script>";

                            nexus_redirect($_SERVER['REQUEST_URI']);
                        }
                        $is_enough = judge_user_bonus("+", $bonus, $uid);
                        if (!$is_enough) {
                            echo "<script type=\"text/javascript\">layer.msg(\"你的存款余额不足\",{icon:1, offset: \"auto\", time:5000})</script>";

                        } else {
                            mysql_query("BEGIN");
                            try {
                                $rs1 = sql_query("UPDATE user_bank_account set `bouns` = `bouns` - " . sqlesc($bonus) . " WHERE `uid` = " . $uid);
                                $rs2 = sql_query("INSERT INTO user_bank_account_log (`uid` ,`bouns` ,`type` ) VALUES (" . $uid . "," . sqlesc($bonus) . ",2)");
                                $rs3 = sql_query("UPDATE users SET seedbonus = seedbonus + " . sqlesc($bonus) . " WHERE id = ".$uid);
                                if ($rs1 && $rs2 && $rs3) {
                                    mysql_query("COMMIT");
                                    echo "<script type=\"text/javascript\">layer.msg(\"取出成功\",{icon:1, offset: \"auto\", time:5000})</script>";

                                } else {
                                    mysql_query("ROLLBACK");
                                    echo "<script type=\"text/javascript\">layer.msg(\"取出失败，请联系管理\",{icon:1, offset: \"auto\", time:5000})</script>";

                                }
                            } catch (Exception $e) {
                                mysql_query("ROLLBACK");
                                echo "<script type=\"text/javascript\">layer.msg(\"取出失败，请联系管理\",{icon:1, offset: \"auto\", time:5000})</script>";

                            }
                        }
                        break;
                    default:
                        break;
                }

                nexus_redirect($_SERVER['REQUEST_URI']);
            }
            //未开户逻辑
            $userBankAccountQuery = sql_query("SELECT * FROM user_bank_account WHERE uid = " . $CURUSER['id']) or sqlerr(__FILE__, __LINE__);
            $userBankAccount = mysql_fetch_array($userBankAccountQuery);
            if (!$userBankAccount) {
                print("<div id=\"no_back_info\">还没有账户！<form method='post' class='layui-form' lay-filter='deleteForm'> <input type='hidden' name='action' value='kaihu' /><button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete'>开户</button></form></div>");
            } else {
                $bankStr = "<div class=\"info\"><h5>账户信息</h5>";
                $bankStr .= "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"70%\" style=\"margin-left:4%\">";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">用户ID</td><td>" . $CURUSER['id'] . "</td></tr>";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">用户名</td><td>" . get_username($CURUSER['id']) . "</td></tr>";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">银行账户ID</td><td>" . $userBankAccount['id'] . "</td></tr>";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">活期存款</td><td>" . $userBankAccount['bouns'] . "</td></tr>";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">活期利息</td><td>" . $userBankAccount['uninterest'] . "</td></tr>";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">开户时间</td><td>" . $userBankAccount['created_at'] . "</td></tr>";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">最后更新时间</td><td>" . $userBankAccount['updated_at'] . "</td></tr>";
                $bankStr .= "<tr><td align=\"center\" class=\"colhead\">结息说明</td><td>月息5%每月1日结息</td></tr>";
                $bankStr .= "</table>";
                $bankStr .= "</div>";
                print($bankStr);
            }
            ?>
            <div id="bank-info-btn">
                <!-- 存款按钮 -->
                <div id="savings_account" style="text-align:auto">
                    <form method='post' class='layui-form' lay-filter='deleteForm'>
                        <input type="number"  lay-verify="number" name="bonus" value="0.0" onblur="if(value< 0)value=0" class="layui-input num"/>
                        <input type='hidden' name='action' value='cunqian' />
                        <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='cunkuan'>存款</button>
                    </form>
                </div>
                <!-- 取款按钮 -->
                <div id="withdraw_money">
                    <form method='post' class='layui-form' lay-filter='deleteForm'>
                        <input type="number" name="bonus" oninput = "value=value.replace(/[^\d]/g,'')">
                        <input type='hidden' name='action' value='quqian' />
                        <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='qukuan'>取款</button>
                    </form>
                </div>
            </div>
        </div>
        <div>
            <!-- 存款流水信息 -->
            <div>
                <h5>
                    存款流水
                </h5>
                <ul>
                    <?php
                    $sql_query = sql_query("select * from user_bank_account_log where type = 1 and uid = " . sqlesc($CURUSER['id']) . " order by created_at desc limit 10");
                    $flow_in_str = "";
                    while($res = mysql_fetch_assoc($sql_query)) {
                        $flow_in_str .= "<li>".get_username($res['uid']) . "存入 " . $res['bouns'] ." 魔力</li>";
                    }
                    print($flow_in_str);
                    ?>
                </ul>
            </div>
            <!-- 存款流水信息 -->
            <div>
                <h5>
                    取款流水
                </h5>
                <ul>
                    <?php
                    $sql_query = sql_query("select * from user_bank_account_log where type = 2 and uid = " . sqlesc($CURUSER['id']) . " order by created_at desc limit 10");
                    $flow_out_str = "";
                    while($res = mysql_fetch_assoc($sql_query)) {
                        $flow_out_str .= "<li>".get_username($res['uid']) . "取出 " . $res['bouns'] ." 魔力</li>";
                    }
                    print($flow_out_str);
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="back_right">
        <!-- 右侧银行信息 -->
        <div>
            <h5>
                储蓄排行
            </h5>
            <table border="1" cellspacing="0" cellpadding="5" align="center" width="80%">
                <tr><td align="center" class="colhead">用户名</td>
                    <td align="center" class="colhead">魔力值</td></tr>
                <?php
                //按本金金额排序查询
                $sql_query = sql_query("select * from user_bank_account order by bouns desc limit 10");
                $top_str = "";
                while($res = mysql_fetch_assoc($sql_query)) {
                    $top_str .= "<tr><td class=\"colfollow\">".get_username($res['uid']) . "</td><td class=\"colfollow\"> " . $res['bouns'] . "</td></tr>";
                }
                print($top_str);
                ?>
            </table>
        </div>
    </div>

</div>
</body>

</html>


<script>
    layui.use(['form', 'layer'], function () {
        var form = layui.form;
        var layer = layui.layer;
        form.on('submit(confirmDelete)', function (data) {
            layer.confirm('确认开户吗？', function (index) {
                layer.close(index);
                data.form.submit();
            });
        });
        form.on('submit(cunkuan)', function (data) {
            layer.confirm('确认存款吗？', function (index) {
                layer.close(index);
                data.form.submit();
            });
        });
        form.on('submit(qukuan)', function (data) {
            layer.confirm('确认取款吗？', function (index) {
                layer.close(index);
                data.form.submit();
            });
        });
    });
</script>


<?php


end_main_frame();
stdfoot();
?>
