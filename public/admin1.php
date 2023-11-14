<?php
require "../include/bittorrent.php";
dbconn(true);
//require_once(get_langfile_path());
loggedinorreturn(true);
stdhead($lang_index['head_home']);




end_main_frame();
stdfoot();
?>
