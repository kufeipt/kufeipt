<?php
function menu ($selected = "home") {
	global $lang_functions;
	global $BASEURL,$CURUSER;
	global $enableoffer, $enablespecial, $enableextforum, $extforumurl, $where_tweak;
	global $USERUPDATESET;
	//no this option in config.php
    $enablerequest = 'yes';
	$script_name = $_SERVER["SCRIPT_FILENAME"];
	if (preg_match("/index/i", $script_name)) {
		$selected = "home";
	}elseif (preg_match("/forums/i", $script_name)) {
		$selected = "forums";
	}elseif (preg_match("/torrents/i", $script_name)) {
		if(isset($_GET['tag_id']) && $_GET['tag_id']){
	        $selected = $_GET['tag_id'];
        }elseif (isset($_GET['incldead']) && $_GET['incldead']) {
            $selected = $_GET['incldead'];
        }else{
        $selected = "torrents";
        }
	}elseif (preg_match("/special/i", $script_name)) {
		$selected = "special";
	}elseif (preg_match("/offers/i", $script_name) OR preg_match("/offcomment/i", $script_name)) {
		$selected = "offers";
    }elseif (preg_match("/requests/i", $script_name)) {
        $selected = "requests";
	}elseif (preg_match("/upload/i", $script_name)) {
		$selected = "upload";
	}elseif (preg_match("/subtitles/i", $script_name)) {
		$selected = "subtitles";
	}elseif (preg_match("/usercp/i", $script_name)) {
		$selected = "usercp";
	}elseif (preg_match("/topten/i", $script_name)) {
		$selected = "topten";
	}elseif (preg_match("/log/i", $script_name)) {
		$selected = "log";
	}elseif (preg_match("/rules/i", $script_name)) {
		$selected = "rules";
	}elseif (preg_match("/faq/i", $script_name)) {
		$selected = "faq";
    }elseif (preg_match("/contactstaff/i", $script_name)) {
        $selected = "contactstaff";
    }elseif (preg_match("/staff/i", $script_name)) {
        $selected = "staff";
	}else
	$selected = "torrents";
	$menu = apply_filter('nexus_menu');
?>
<html>
<style type="text/css">
#nav{height:30px;}
#nav *{margin:0;padding:0;}
#nav a{text-decoration:none;}
/*一级菜单*/
#nav ul{margin:0 auto; width: 1155px;display:block;list-style-type:none;}
#nav ul li{background-color:#006a00;display:block;width:80px;height:25px; line-height:25px; float:left;text-align:center;border-right:solid 1px #55aa55;}
#nav ul li:last-child{border-right:none;}
#nav ul li:hover{background-color:#140599;}
/*二级菜单*/
#nav ul li ul{display:none;}
#nav ul li ul li{width:80px; height:25px; line-height:25px;background-color:#00aa00;border-bottom:solid 1px #008000;}
#nav ul li:hover ul{display:block;position:relative;width:120px;}
#nav ul li ul li:hover{background-color:#1b00ff;}
/*三级菜单*/
#nav ul li:hover ul li ul{display:none;}
#nav ul li:hover ul li:hover ul{display:block; position: relative; left: 80px; top: -25px;}
#nav ul li:hover ul li:hover ul li{width:80px; height:25px; line-height: 25px;}
#nav ul li:hover ul li:hover ul li:hover{background-color:#1b00ff;}
a.kkkk{
    color:#FFFFFF;
    font-size: 120%;
}

</style>
</head>
<body>
<div id="nav">
    <ul>
        <li><a  class="selected" href="index.php">&nbsp;&nbsp;首&nbsp;&nbsp;页&nbsp;&nbsp;</a></li>
        <li><a  class="selected" href="forums.php">&nbsp;&nbsp;论&nbsp;&nbsp;坛&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="torrents.php">&nbsp;&nbsp;种&nbsp;&nbsp;子&nbsp;&nbsp;▼</a>
            <ul>
                <li><a class="kkkk" href="torrents.php?cat401=1&cat402=1&cat403=1">&nbsp;&nbsp;影&nbsp;&nbsp;视&nbsp;&nbsp;▶</a>
                    <ul>
                        <li><a class="kkkk" href="torrents.php?cat=401">&nbsp;&nbsp;电&nbsp;&nbsp;影&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="torrents.php?cat=402">&nbsp;&nbsp;电&nbsp;视&nbsp;剧&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="torrents.php?cat=403">&nbsp;&nbsp;综&nbsp;&nbsp;艺&nbsp;&nbsp;</a></li>
                    </ul>
                </li>
                <li><a class="kkkk" class="kkkk" href="torrents.php?cat406=1&cat408=1">&nbsp;&nbsp;音&nbsp;&nbsp;乐&nbsp;&nbsp;▶</a>
                     <ul>
                        <li><a class="kkkk" href="torrents.php?cat=406">&nbsp;&nbsp;M&nbsp;&nbsp;V&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="torrents.php?cat=408">&nbsp;&nbsp;音&nbsp;&nbsp;乐&nbsp;&nbsp;</a></li>
                    </ul>
                </li>
                <li><a class="kkkk" href="torrents.php?cat=409">&nbsp;&nbsp;其&nbsp;&nbsp;他&nbsp;&nbsp;</a></li>
            </ul>
        </li>
        <li><a class="kkkk" href="special.php">&nbsp;&nbsp;动&nbsp;&nbsp;漫&nbsp;&nbsp;▼</a>
            <ul>
                <li><a class="kkkk" href="special.php?cat=417">&nbsp;&nbsp;国&nbsp;&nbsp;漫&nbsp;&nbsp;▶</a>
                    <ul>
                        <li><a class="kkkk" href="special.php?cat417=1&processing3=1">&nbsp;&nbsp;电&nbsp;&nbsp;影&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat417=1&processing4=1">&nbsp;&nbsp;剧&nbsp;&nbsp;集&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat417=1&processing6=1">&nbsp;&nbsp;漫&nbsp;&nbsp;画&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat417=1&processing7=1">&nbsp;&nbsp;其&nbsp;&nbsp;他&nbsp;&nbsp;</a></li>
                    </ul>
                </li>
                <li><a class="kkkk" href="special.php?cat=418">&nbsp;&nbsp;日&nbsp;&nbsp;漫&nbsp;&nbsp;▶</a>
                    <ul>
                        <li><a class="kkkk" href="special.php?cat418=1&processing3=1">&nbsp;&nbsp;电&nbsp;&nbsp;影&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat418=1&processing4=1">&nbsp;&nbsp;剧&nbsp;&nbsp;集&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat418=1&processing6=1">&nbsp;&nbsp;漫&nbsp;&nbsp;画&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat418=1&processing7=1">&nbsp;&nbsp;其&nbsp;&nbsp;他&nbsp;&nbsp;</a></li>
                    </ul>
                </li>
                <li><a class="kkkk" href="special.php?cat=419">&nbsp;&nbsp;韩&nbsp;&nbsp;漫&nbsp;&nbsp;▶</a>
                    <ul>
                        <li><a class="kkkk" href="special.php?cat419=1&processing3=1">&nbsp;&nbsp;电&nbsp;&nbsp;影&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat419=1&processing4=1">&nbsp;&nbsp;剧&nbsp;&nbsp;集&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat419=1&processing6=1">&nbsp;&nbsp;漫&nbsp;&nbsp;画&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat419=1&processing7=1">&nbsp;&nbsp;其&nbsp;&nbsp;他&nbsp;&nbsp;</a></li>
                    </ul>
                </li>
                <li><a class="kkkk" href="special.php?cat=420">&nbsp;&nbsp;欧&nbsp;&nbsp;美&nbsp;&nbsp;▶</a>
                    <ul>
                        <li><a class="kkkk" href="special.php?cat420=1&processing3=1">&nbsp;&nbsp;电&nbsp;&nbsp;影&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat420=1&processing4=1">&nbsp;&nbsp;剧&nbsp;&nbsp;集&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat420=1&processing6=1">&nbsp;&nbsp;漫&nbsp;&nbsp;画&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat420=1&processing7=1">&nbsp;&nbsp;其&nbsp;&nbsp;他&nbsp;&nbsp;</a></li>
                    </ul>
                </li>
                <li><a class="kkkk" href="special.php?cat=421"> 其 他 ▶ </a>
                    <ul>
                        <li><a class="kkkk" href="special.php?cat421=1&processing3=1">&nbsp;&nbsp;电&nbsp;&nbsp;影&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat421=1&processing4=1">&nbsp;&nbsp;剧&nbsp;&nbsp;集&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat421=1&processing6=1">&nbsp;&nbsp;漫&nbsp;&nbsp;画&nbsp;&nbsp;</a></li>
                        <li><a class="kkkk" href="special.php?cat421=1&processing7=1">&nbsp;&nbsp;其&nbsp;&nbsp;他&nbsp;&nbsp;</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li><a class="kkkk" href="#">&nbsp;&nbsp;保&nbsp;&nbsp;种&nbsp;&nbsp;▼</a>
            <ul>
                <li><a class="kkkk" href="torrents.php?class=bz">&nbsp;&nbsp;种&nbsp;子&nbsp;区&nbsp;&nbsp;</a></li>
                <li><a class="kkkk" href="special.php?class=bz">&nbsp;&nbsp;动&nbsp;漫&nbsp;区&nbsp;&nbsp;</a></li>
            </ul>
        </li>
        <li><a class="kkkk" href="#">&nbsp;&nbsp;断&nbsp;&nbsp;种&nbsp;&nbsp;▼</a>
            <ul>
                <li><a class="kkkk" href="torrents.php?incldead=2">&nbsp;&nbsp;种&nbsp;子&nbsp;区&nbsp;&nbsp;</a></li>
                <li><a class="kkkk" href="special.php?incldead=2">&nbsp;&nbsp;动&nbsp;漫&nbsp;区&nbsp;&nbsp;</a></li>
            </ul>
        </li>
        <li><a class="kkkk" href="viewrequests.php">&nbsp;&nbsp;求&nbsp;&nbsp;种&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="upload.php">&nbsp;&nbsp;发&nbsp;&nbsp;布&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="subtitles.php">&nbsp;&nbsp;字&nbsp;&nbsp;幕&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="topten.php">&nbsp;&nbsp;排&nbsp;行&nbsp;榜&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="rules.php">&nbsp;&nbsp;规&nbsp;&nbsp;则&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="faq.php">&nbsp;&nbsp;常见问题&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="staff.php">&nbsp;&nbsp;管&nbsp;理&nbsp;组&nbsp;&nbsp;</a></li>
        <li><a class="kkkk" href="contactstaff.php">联系管理组</a></li>
    </ul>
</div>
</body>
</html>