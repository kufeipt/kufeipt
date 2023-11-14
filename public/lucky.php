<?php
require "../include/bittorrent.php";
dbconn(true);
stdhead("幸运大转盘");
//require_once(get_langfile_path());
loggedinorreturn(true);
print("<iframe style=\"width:100%;height:1200px;border:none\" src=\"/plugin/lucky-draw\"></iframe>");
end_main_frame();
stdfoot();
?>
