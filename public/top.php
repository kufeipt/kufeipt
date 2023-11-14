<?php
require "../include/bittorrent.php";
dbconn(true);
stdhead("有效发种排行");
//require_once(get_langfile_path());
loggedinorreturn(true);
$res = sql_query("select count(owner) as times, owner from torrents where `approval_status` = 1 and `added` > '2023-10-12 00:00:00' and `added` < '2023-10-22 23:59:59' and `seeders` > 0 and (`category` = 401 or `category` = 402 or `category` = 403 or `category` = 417 or `category` = 418 or `category` = 419 or `category` = 420 or `category` = 421 or `category` = 422) group by owner order by times desc");
//$res = sql_query("select COUNT(torrents.id) AS torrent_count");
$count = 1;
    print("<h1 align=\"center\">周年庆发种大赛排行</h1>");
    print("<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"center\" width=\"60%\"><tr>");
	print("<td align=\"center\" class=\"colhead\">排名</td>");
	print("<td align=\"center\" class=\"colhead\">用户名</td>");
	print("<td align=\"center\" class=\"colhead\">UID</td>");
	print("<td align=\"center\" class=\"colhead\">有效发种数</td></tr>");
while($count<=100 && $myrow=mysqli_fetch_assoc($res)){
    print "<tr>
    <td class=\"rowhead nowrap\" style=\"text-align:center;\">".$count."</td>
    <td class=\"rowhead nowrap\" style=\"text-align:center;\" href=userdetails.php?id=".$myrow['owner'].">".get_username($myrow['owner'], false, true, true, false, false, true)."
    </td>
    <td class=\"rowhead nowrap\" style=\"text-align:center;\">".$myrow['owner']."</td>
    <td class=\"rowfollow\" style=\"text-align:center;\">".$myrow['times']."</td></tr>";
    $count++;
}
    print("</table>");
end_main_frame();
stdfoot();
?>
