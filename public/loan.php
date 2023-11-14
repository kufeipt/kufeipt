<?php
require "../include/bittorrent.php";
dbconn(true);
stdhead("贷款");
//require_once(get_langfile_path());
loggedinorreturn(true);
//定义贷款本金、年利率和还款月数
$P = 100000; //贷款本金，单位为元
$R = 1.2; //年利率，单位为小数
$N = 12; //还款月数，单位为月

//计算月利率
$r = $R / 12;

//定义一个函数，根据还款周期计算每次还款本息和总利息
function calculate($period, $P, $r, $N) {
  //根据还款周期计算每次还款的月数
  $m = $period / 30;
  //根据等额本息公式计算每次还款本息
  $A = $P * $r * pow(1 + $r, $N) / (pow(1 + $r, $N) - 1) * $m;
  //根据等额本息公式计算总利息
  $I = $P * $r * ($N + 1) / 2;
  //返回一个数组，包含每次还款本息和总利息
  return array($A, $I);
}

//根据不同的还款周期调用函数并输出结果
echo "总利息=" . round(calculate(30, $P, $r, $N)[1], 2) . ",总还款=". round(calculate(30, $P, $r, $N)[1], 2)+$P." \n<br />";
for ($i=1;$i<=$N;$i++){
echo "第".$i."月还款本息=" . round(calculate(30, $P, $r, $N)[0], 2) . "，每月本金=" . round(calculate(30, $P, $r, $N)[0], 2)-round(calculate(30, $P, $r, $N)[1]/$N, 2) . "，每月利息=" . round(calculate(30, $P, $r, $N)[1]/$N, 2) . "\n<br />";
}
end_main_frame();
stdfoot();
?>