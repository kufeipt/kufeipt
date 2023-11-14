<?php
require "../include/bittorrent.php";
dbconn(true);
//equire_once(get_langfile_path());
loggedinorreturn(true);
stdhead("认证信息");
//begin_main_frame();
?>

<!DOCTYPE html>
<html>
    <table class="main" width="30%" border="0" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td class="embedded">
                <h2 style="width:30%; margin: 0 auto;">认证信息</h2>
            </td>
        </tr>
    </tbody>
</table>
<table width="30%" border="1" cellspacing="0" cellpadding="5">
    <tbody>
        <tr>
            <td width="20%" class="rowhead nowrap" valign="top" align="right">用户ID/UID</td>
            <?php echo "<td width=\"80%\" class=\"rowfollow\" valign=\"top\" align=\"left\">".$CURUSER['id']."</td>"; ?>
            
        </tr>
    </tbody>
    <tbody>
        <tr>
            <td width="20%" class="rowhead nowrap" valign="top" align="right">passkey</td>
            <?php echo "<td width=\"80%\" class=\"rowfollow\" valign=\"top\" align=\"left\">".$CURUSER['passkey']."</td>"; ?>
        </tr>
    </tbody>
</table>
</html>
<?php
end_main_frame();
stdfoot();
?>
