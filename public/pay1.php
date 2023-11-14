<?php
require "../include/bittorrent.php";
dbconn(true);
stdhead("有效发种排行");
//require_once(get_langfile_path());
loggedinorreturn(true);
?>
<form action="pay.php" method="post">
 <center>
 <div style="font-size:15px">
 <fieldset style="width:30%; ">
  <legend>VIP发放</legend>
  <ul>
  <li>
   <label>用户ID:</label>
   <input type="text" name="id">
  </li>
   <li>
   <label>到期时间:</label>
   <input type="text" name="time">
  </li>
  <li>
   <label>passkey:</label>
   <input type="text" name="passkey">
  </li>
  <li>
   <label> </label>
   <input type="submit" value="确认">
  </li>
  </ul>
 </fieldset>
 </div>
 </center>
 </form>
<?php
end_main_frame();
stdfoot();
?>
