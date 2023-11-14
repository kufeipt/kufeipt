<?php

require "../include/bittorrent.php";
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
$bb = sql_query("select COUNT(*) as count from userzmpt where id =".$CURUSER['id']);
$bb = mysql_fetch_array($bb);
$isAdmin = get_user_class() < UC_ADMINISTRATOR;
if (empty($bb) || ($bb['count'] != 0 && !$isAdmin)) {
    permissiondenied();
}
$year=intval($_GET['year'] ?? 0);
if (!$year || $year < 2000)
$year=date('Y');
$month=intval($_GET['month'] ?? 0);
if (!$month || $month<=0 || $month>12)
$month=date('m');
$order=$_GET['order'] ?? '';
if (!in_array($order, array('username', 'torrent_size', 'torrent_count')))
	$order='username';
if ($order=='username')
	$order .=' ASC';
else $order .= ' DESC';
stdhead($lang_1['head_uploaders']);
begin_main_frame();
?>
<div style="width: 100%">
<?php
$year2 = substr($datefounded, 0, 4);
$yearfounded = ($year2 ? $year2 : 2007);
$yearnow=date("Y");

$timestart=strtotime($year."-".$month."-01 00:00:00");
$sqlstarttime=date("Y-m-d H:i:s", $timestart);
$timeend=strtotime("+1 month", $timestart);
$sqlendtime=date("Y-m-d H:i:s", $timeend);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['userid'])) {
        $uid = (int)$_POST['userid']; // 获取要删除的记录的ID
        $dt = sqlesc(date("Y-m-d H:i:s"));
        $sqlendtime = date("Y-m-d H:i:s", strtotime('+1 day', $timestart));
        $bonus = 100000;//魔力值
        $sql = sql_query("INSERT INTO `user_wages` (`id`, `userid`, `created_at`, `updated_at`, `bonus`) VALUES (NULL ,'$uid', '".$sqlendtime."', '$dt', $bonus)");
        KPS("+",$bonus,$uid);
        $owner =sqlesc($uid);
        $subject =sqlesc("发放$month月工资");
        $torrentUrl = sprintf('1.php?year=%s&month=%s', $year, $month);//链接改为实际地址
        $msg =sqlesc("您在$month月工资魔力:$bonus.\n[url=$torrentUrl]:查看详情[/url]");
        sql_query("INSERT INTO messages (sender, receiver, added, subject, msg) VALUES(0, $owner, $dt, $subject, $msg)") or sqlerr(__FILE__, __LINE__);
        writecomment($uid,"发放$month月工资魔力:$bonus.");
        nexus_redirect($_SERVER['REQUEST_URI']);
    }
}

print("<h1 align=\"center\">".$lang_1['text_uploaders']." - ".date("Y-m",$timestart)."</h1>");

$yearselection="<select name=\"year\">";
for($i=$yearfounded; $i<=$yearnow; $i++)
	$yearselection .= "<option value=\"".$i."\"".($i==$year ? " selected=\"selected\"" : "").">".$i."</option>";
$yearselection.="</select>";

$monthselection="<select name=\"month\">";
for($i=1; $i<=12; $i++)
	$monthselection .= "<option value=\"".$i."\"".($i==$month ? " selected=\"selected\"" : "").">".$i."</option>";
$monthselection.="</select>";

?>
<div>
<form method="get" action="?">
<span>
<?php echo $lang_1['text_select_month']?><?php echo $yearselection?>&nbsp;&nbsp;<?php echo $monthselection?>&nbsp;&nbsp;<input type="submit" value="<?php echo $lang_1['submit_go']?>" />
</span>
</form>
</div>

<?php
$numres = sql_query("SELECT COUNT(users.id) FROM users WHERE class >= ".UC_UPLOADER) or sqlerr(__FILE__, __LINE__);
$numrow = mysql_fetch_array($numres);
$num=$numrow[0];
if (!$num)
	print("<p align=\"center\">".$lang_1['text_no_uploaders_yet']."</p>");
else{
?>
<div style="margin-top: 8px">
<?php
	print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"97%\"><tr>");
	print("<td class=\"colhead\">".$lang_1['col_username']."</td>");
	print("<td class=\"colhead\">工作组</td>");
	print("<td class=\"colhead\">任务量</td>");
	print("<td class=\"colhead\">保种量</td>");
	print("<td class=\"colhead\">官种</td>");
	//print("<td class=\"colhead\">".$lang_1['col_torrents_size']."</td>");
	print("<td class=\"colhead\">".$lang_1['col_torrents_num']."</td>");
	print("<td class=\"colhead\">".$lang_1['col_last_upload_time']."</td>");
	print("<td class=\"colhead\">".$lang_1['col_last_upload']."</td>");
	print("<td class=\"colhead\">操作</td>");
	print("</tr>");
	$canGrantWages = false; // 假设初始没有权限
	$userId = $CURUSER['id'];
	if($CURUSER['id'] =='1'){
	    $canGrantWages = true; 
    	$res = sql_query("SELECT userzmpt.id AS userid, userzmpt.renwu AS renwu, userzmpt.class AS class,userzmpt.username AS username, COUNT(torrents.id) AS torrent_count, SUM(torrents.size) AS torrent_size FROM torrents LEFT JOIN userzmpt ON torrents.owner=userzmpt.id WHERE userzmpt.class IS NOT NULL AND torrents.added > ".sqlesc($sqlstarttime)." AND torrents.added < ".sqlesc($sqlendtime)." GROUP BY userid ORDER BY ".$order);
    
	}else{
	    $res = sql_query("SELECT userzmpt.id AS userid, userzmpt.renwu AS renwu, userzmpt.class AS class,userzmpt.username AS username, COUNT(torrents.id) AS torrent_count, SUM(torrents.size) AS torrent_size FROM torrents LEFT JOIN userzmpt ON torrents.owner=userzmpt.id WHERE userzmpt.class IS NOT NULL AND torrents.added > ".sqlesc($sqlstarttime)." AND torrents.added < ".sqlesc($sqlendtime)." AND userzmpt.id = $userId GROUP BY userid ORDER BY ".$order);
	}
	$hasupuserid=array();
	while($row = mysql_fetch_array($res))
	{  
	    
	    $Disabled = '';
	    $value = '发放';
    	$res1 = sql_query("SELECT COUNT(*) as count FROM `user_wages` WHERE userid = ".$row['userid']." AND created_at > ".sqlesc($sqlstarttime)." AND created_at < ".sqlesc($sqlendtime));
    	$b = mysql_fetch_array($res1);
    	if ($b['count'] > 0 ){
    	    $Disabled = ' disabled';
    	    $value = '已发';
    	}elseif($row['class'] != "保种组" && $row['torrent_count'] < 30){
    	    $Disabled = ' disabled';
    	    $value = '没有达标';
    	}elseif($row['class'] == "保种组" && $row['renwu'] > round($a / 1024 / 1024 /1024 /1024 , 0)){
    	    $Disabled = ' disabled';
    	    $value = '没有达标'; 
    	}
		$res2 = sql_query("SELECT torrents.id, torrents.name, torrents.added FROM torrents WHERE owner=".$row['userid']." ORDER BY id DESC LIMIT 1");
		$row2 = mysql_fetch_array($res2);
		$result3 = sql_query("select * from peers where userid=" . $row['userid']);
        $a = 0;
        if (mysqli_num_rows($result3) > 0) {
            // 输出数据
            while($row3 = mysqli_fetch_assoc($result3)) {
                $result4 = sql_query("select * from torrents where id=" .$row3["torrent"]);
                $row4 = mysqli_fetch_assoc($result4);
                $a= $a + $row4['size'];
               // echo '账号：' . 'wanda' . "| 种子ID：" . $row["torrent"] . '种子大小' . $row1["size"] .'</br>';
               }
        }
		print("<tr>");
		print("<td class=\"colfollow\">".get_username($row['userid'], false, true, true, false, false, true)."</td>");
		print("<td class=\"colfollow\">".$row['class']."</td>");
		if ($row['class'] == '保种组'){
		    print("<td class=\"colfollow\">".mksize($row['renwu']*1099511627776)."</td>");
		}else{
		    print("<td class=\"colfollow\">".$row['renwu']."个</td>");
		}
		
        print("<td class=\"colfollow\">".mksize(calculate_seed_bonus($row['userid'])['size'])."</td>");
        print("<td class=\"colfollow\">".mksize(calculate_seed_bonus($row['userid'])['official_size'])."</td>");
    	//print("<td class=\"colfollow\">".($row['torrent_size'] ? mksize($row['torrent_size']) : "0")."</td>");
		print("<td class=\"colfollow\">".$row['torrent_count']."</td>");
		print("<td class=\"colfollow\">".($row2['added'] ? gettime($row2['added']) : $lang_1['text_not_available'])."</td>");
		print("<td class=\"colfollow\">".($row2['name'] ? "<a href=\"details.php?id=".$row2['id']."\">".htmlspecialchars($row2['name'])."</a>" : $lang_1['text_not_available'])."</td>");
        if ($canGrantWages) {
            print("<td><form method='post' class='layui-form' lay-filter='deleteForm'>
                        <input type='hidden' name='userid' value='{$row['userid']}' />
                        <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete' $Disabled >$value</button>
                    </form></td>");
        }
		print("</tr>");
		$hasupuserid[]=$row['userid'];
		unset($row2);
	}
		if($CURUSER['id'] =='1'){
    	    $canGrantWages = true; 
        	$res3=sql_query("SELECT userzmpt.id AS userid,userzmpt.renwu AS renwu, userzmpt.class AS class,userzmpt.username AS username, 0 AS torrent_count, 0 AS torrent_size FROM userzmpt WHERE class IS NOT NULL".($hasupuserid ? " AND userzmpt.id NOT IN (".implode(",",$hasupuserid).")" : "")." ORDER BY username ASC") or sqlerr(__FILE__, __LINE__);
		}else{
        	$res3=sql_query("SELECT userzmpt.id AS userid, userzmpt.renwu AS renwu,userzmpt.class AS class,userzmpt.username AS username, 0 AS torrent_count, 0 AS torrent_size FROM userzmpt WHERE class IS NOT NULL AND userzmpt.id IN($userId) ORDER BY username ASC;") or sqlerr(__FILE__, __LINE__);
		}
    $count = 0;
	while($row = mysql_fetch_array($res3))
	{
	    $Disabled = '';
	    $value = '发放';
    	$res1 = sql_query("SELECT COUNT(*) as count FROM `user_wages` WHERE userid = ".$row['userid']." AND created_at > ".sqlesc($sqlstarttime)." AND created_at < ".sqlesc($sqlendtime));
    	$b = mysql_fetch_array($res1);
        if ($b['count'] > 0 ){
    	    $Disabled = ' disabled';
    	    $value = '已发';
    	}elseif($row['class'] != "保种组" && $row['torrent_count'] < 30){
    	    $Disabled = ' disabled';
    	    $value = '没有达标';
    	}elseif($row['class'] == "保种组" && $row['renwu'] > round($a / 1024 / 1024 /1024 /1024 , 0)){
    	    $Disabled = ' disabled';
    	    $value = '没有达标'; 
    	}
		$res2 = sql_query("SELECT torrents.id, torrents.name, torrents.added FROM torrents WHERE owner=".$row['userid']." ORDER BY id DESC LIMIT 1");
		$row2 = mysql_fetch_array($res2);
		$result3 = sql_query("select * from peers where userid=" . $row['userid']);
        $a = 0;
        if (mysqli_num_rows($result3) > 0) {
            // 输出数据
            while($row3 = mysqli_fetch_assoc($result3)) {
                $result4 = sql_query("select * from torrents where id=" .$row3["torrent"]);
                $row4 = mysqli_fetch_assoc($result4);
                $a= $a + $row4['size'];
               // echo '账号：' . 'wanda' . "| 种子ID：" . $row["torrent"] . '种子大小' . $row1["size"] .'</br>';
               }
        }
		print("<tr>");
		print("<td class=\"colfollow\">".get_username($row['userid'], false, true, true, false, false, true)."</td>");
		print("<td class=\"colfollow\">".$row['class']."</td>");
		if ($row['class'] == '保种组'){
		    print("<td class=\"colfollow\">".mksize($row['renwu']*1099511627776)."</td>");
		}else{
		    print("<td class=\"colfollow\">".$row['renwu']."个</td>");
		}
		
		/*if($a < 1024){
		    print("<td class=\"colfollow\">".round($a , 3) . ' B'."</td>");
        }elseif($a > 1024 && $a < 1024 * 1024){
            print("<td class=\"colfollow\">".round($a / 1024 , 3) . ' KB'."</td>");
        }elseif($a > 1024 * 1024 && $a < 1024 * 1024 * 1024){
            print("<td class=\"colfollow\">".round($a / 1024 / 1024 , 3) . 'MB'."</td>");
        }elseif($a > 1024 * 1024 * 1024 && $a < 1024 * 1024 * 1024 * 1024){
            print("<td class=\"colfollow\">".round($a / 1024 / 1024 /1024 , 3) . ' GB'."</td>");
        }elseif($a > 1024 * 1024 * 1024 * 1024){
            print("<td class=\"colfollow\">".round($a / 1024 / 1024 /1024 /1024 , 3) . ' TB'."</td>");
        }*/
        print("<td class=\"colfollow\">".mksize(calculate_seed_bonus($row['userid'])['size'])."</td>");
        print("<td class=\"colfollow\">".mksize(calculate_seed_bonus($row['userid'])['official_size'])."</td>");

    	
		//print("<td class=\"colfollow\">".($row['torrent_size'] ? mksize($row['torrent_size']) : "0")."</td>");
		print("<td class=\"colfollow\">".$row['torrent_count']."</td>");
		print("<td class=\"colfollow\">".($row2['added'] ? gettime($row2['added']) : $lang_1['text_not_available'])."</td>");
		print("<td class=\"colfollow\">".($row2['name'] ? "<a href=\"details.php?id=".$row2['id']."\">".htmlspecialchars($row2['name'])."</a>" : $lang_1['text_not_available'])."</td>");
        if ($canGrantWages) {
            print("<td><form method='post' class='layui-form' lay-filter='deleteForm'>
                        <input type='hidden' name='userid' value='{$row['userid']}' />
                        <button type='button' class='layui-btn layui-btn-danger' lay-submit lay-filter='confirmDelete' $Disabled >$value</button>
                    </form></td>");
        }
		print("</tr>");
		$count++;
		unset($row2);
	}
	print("</table>");
?>
</div>
<div style="margin-top: 8px; margin-bottom: 8px;">
<span id="order" onclick="dropmenu(this);"><span style="cursor: pointer;" class="big"><b><?php echo $lang_1['text_order_by']?></b></span>
<span id="orderlist" class="dropmenu" style="display: none"><ul>
<li><a href="?year=<?php echo $year?>&amp;month=<?php echo $month?>&amp;order=username"><?php echo $lang_1['text_username']?></a></li>
<li><a href="?year=<?php echo $year?>&amp;month=<?php echo $month?>&amp;order=torrent_size"><?php echo $lang_1['text_torrent_size']?></a></li>
<li><a href="?year=<?php echo $year?>&amp;month=<?php echo $month?>&amp;order=torrent_count"><?php echo $lang_1['text_torrent_num']?></a></li>
</ul>
</span>
</span>
</div>
<?php
}
?>
</div>
<?php
end_main_frame();
stdfoot();
?>
<script src="../vendor/layui/layui.js"></script>
<script>
    layui.use(['form', 'layer'], function(){
        var form = layui.form;
        var layer = layui.layer;
        form.on('submit(confirmDelete)', function(data){
            layer.confirm('确认要发放工资吗？', function(index){
                layer.close(index);
                data.form.submit();
            });
        });
    });
</script>