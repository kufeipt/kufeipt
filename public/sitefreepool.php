<?php
require "../include/bittorrent.php";
dbconn(true);
stdhead("银行");
//require_once(get_langfile_path());
loggedinorreturn(true);
$zmc_query = sql_query("select * from free_pool_info where is_current = 1");
$zmc_res = mysql_fetch_array($zmc_query);
$zmc_current_schedule = floor($zmc_res['current_bonus'] / $zmc_res['need_bonus'] * 10000) / 100;
$zmc_current_schedule = min($zmc_current_schedule, 100);
?>
    <link rel="stylesheet" href="./vendor/layui/css/layui.css">
    <style>
        .danmu {
            position: fixed;
            white-space: nowrap;
            overflow: hidden;
            animation-duration: 10s;
            animation-timing-function: linear;
            animation-iteration-count: 1;
            animation-fill-mode: forwards;
            font-size: 16px;
            color: white;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 5px 10px;
            border-radius: 3px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            justify-content: center;
            text-align: center;
            display: flex;
            align-items: center;
            background-color: rgb(255 87 34 / 63%);
            padding: 5px;
        }

        .danmu img {
            width: 35px;
            height: auto;
            margin-right: 10px;
        }

        @keyframes scrollDanmu {
            to {
                transform: translateX(-100%);
            }
        }
    </style>
    <div>
        <!-- title -->
        <fieldset class="layui-elem-field layui-field-title">
            <legend>站免池&nbsp;当前&nbsp;第<?php echo $zmc_res['periods'];?>期</legend>
        </fieldset>
        <!-- 进度条 -->
        <div
            style="display: flex;width: 700px;margin: 23px 0;height: 50px;border: 1px solid #999;border-radius: 999px;background: #e8e2e0;">
            <div class="dynamic-div" id="dynamicDiv"
                style="display: flex; justify-content: center; align-items: center; width: <?php echo $zmc_current_schedule;?>%; height: 100%; background: rgb(254 177 71); border-radius: 999px; position: relative;">
                <span id="percentageSpan"
                    style="font-weight: 500; font-size: 16px; position: absolute; bottom: -30px; right: -15px; color: #673AB7;">
                    <?php echo $zmc_current_schedule;?>%
                </span>
                <img src="./pic/R-bar.gif" style="position: absolute; height: 70px; right: -30px;"
                    draggable="false">
            </div>
        </div>
        <!-- 动图 -->
        <img style="width: 265px; height: 265px; object-fit: cover; cursor: pointer;user-select: none;"
            src="./pic/R-C1.gif" id="clickableImage" draggable="false">
        <!-- 说明 -->
        <blockquote class="layui-elem-quote layui-font-18" style='border-color: rgb(254 177 71); margin-left: 10px;'>
            <h1>关于站免系统的说明</h1>
            <ul class="layui-text">
                <li>每次开启站免消耗 &gt;= <span class="layui-font-red"><?php echo $zmc_res['need_bonus']; ?></span>魔力值</li>
                <li>每次站免开启后会持续<span class="layui-font-red"> <?php echo $zmc_res['sustain_time']; ?>天 </span> 站点页头可以看到站免进度</li>
                <li>每次站免开启后皮卡皮卡休息<span class="layui-font-red"> <?php echo $zmc_res['rest_time']; ?>天 </span> 后即可投喂</li>
            </ul>
        </blockquote>
        <!-- top榜单 -->
        <div style="display:flex;flex-direction:row;flex-wrap:nowrap;justify-content:space-around;">
            <div>
                <h1>TOP榜</h1>
                <table style="text-align: center;" border="1" cellspacing="0" cellpadding="5" align="center" width="300px" id='all-top'>
                    <thead>
                        <tr>
                            <td class="colhead">排名</td>
                            <td class="colhead">用户名</td>
                            <td class="colhead">次数</td>
                            <td class="colhead">投喂魔力</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!-- 最新投喂 -->
            <div>
                <h1>最新投喂</h1>
                <table style="text-align: center;" border="1" cellspacing="0" cellpadding="5" align="center" width="500px" id='latest-list'>
                    <thead>
                        <tr>
                            <td class="colhead" style="width: 10px;">No.</td>
                            <td class="colhead">用户名</td>
                            <td class="colhead" style="width: 10px;">投喂魔力</td>
                            <td class="colhead" style="width: 120px;">投喂时间</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!-- 本期TOP榜 -->
            <div>
                <h1>本期TOP榜</h1>
                <table style="text-align: center;" border="1" cellspacing="0" cellpadding="5" align="center" width="300px" id="top-list">
                    <thead>
                        <tr>
                            <td class="colhead">排名</td>
                            <td class="colhead">
                                用户名
                            </td>
                            <td class="colhead">次数</td>
                            <td class="colhead">
                                投喂魔力
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="danmuContainer"></div>
    </div>
    <script src="./vendor/layui/layui.js"></script>


    <script>
        jQuery(function () {
            // 定义点击区域
            const polygons = [
                [{ x:97.5,y: 76.5625},
                { x:72.5,y: 98.5625},
                { x:56.5,y: 134.5625},
                { x:55.5,y: 171.5625},
                { x:86.5,y: 239.5625},
                { x:83.5,y: 250.5625},
                { x:89.5,y: 255.5625},
                { x:97.5,y: 244.5625},
                { x:135.5,y: 243.5625},
                { x:171.5,y: 239.5625},
                { x:179.5,y: 253.5625},
                { x:197.5,y: 235.5625},
                { x:210.5,y: 215.5625},
                { x:213.5,y: 199.5625},
                { x:211.5,y: 187.5625},
                { x:217.5,y: 174.5625},
                { x:209.5,y: 161.5625},
                { x:203.5,y: 133.5625},
                { x:194.5,y: 108.5625},
                { x:192.5,y: 93.5625},]
            ];
            const ellipses = [
                // nose 鼻子
                { center: { x:132.5,y: 138.5625}, radiusX: 20, radiusY: 20, label: "nose" },
                // tail 尾巴
                { center: { x:30.5,y: 147.5625}, radiusX: 15, radiusY: 53, label: "tail" },
                { center: { x:51.5,y: 212.5625}, radiusX: 26, radiusY:33, label: "tail" },
                // ear 耳朵
                { center: { x:188.5,y: 63.5625}, radiusX: 30, radiusY: 30, label: "ear" },
                { center: { x:67.5,y: 65.5625}, radiusX: 30, radiusY: 30, label: "ear" },
                // forehead 精灵球
                { center: { x:128.5,y: 50.5625}, radiusX: 30, radiusY: 30, label: "forehead" },
            ];
            // 创建漂浮弹幕
            function createDanmu(message) {
                const animationDuration = 10; // 每个弹幕的动画持续时间（秒）

                // 设置随机的初始位置（整个窗口范围）
                const randomTop = Math.floor(Math.random() * jQuery(window).height());
                const randomLeft = Math.floor(Math.random() * jQuery(window).width());

                let $danmuElement = jQuery("<div>")
                    .addClass("danmu")
                    .append(jQuery("<img>"))
                    .append(jQuery("<span>").text(message))
                    .css({
                        "animation": `scrollDanmu ${animationDuration}s linear`,
                        "top": `${randomTop}px`,
                        "left": `${randomLeft}px`
                    })
                    .appendTo("#danmuContainer") // 假设你的弹幕容器的ID为danmuContainer
                    .on("animationend", function () {
                        jQuery(this).remove(); // 动画结束后移除弹幕
                    });
            }

            function isPointInsideEllipse(x, y, center, radiusX, radiusY) {
                const dx = x - center.x;
                const dy = y - center.y;
                return (dx * dx) / (radiusX * radiusX) + (dy * dy) / (radiusY * radiusY) <= 1;
            }
            function isPointInsidePolygon(x, y, polygon) {
                let isInside = false;

                for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
                    const xi = polygon[i].x, yi = polygon[i].y;
                    const xj = polygon[j].x, yj = polygon[j].y;

                    const intersect = ((yi > y) !== (yj > y)) &&
                        (x < ((xj - xi) * (y - yi)) / (yj - yi) + xi);

                    if (intersect) isInside = !isInside;
                }

                return isInside;
            }
            // TOP榜
            function updateAllTopList(){
                jQuery.ajax({
                    url: '/api/all-top-list',
                    type: 'get',
                    dataType: 'json',
                    success(response) {
                        if(response.ret === 0){
                            jQuery("#all-top tbody").html(response.data)
                        }
                    }
                })
            }
            // 最新投喂
            function updateLatestList(){
                jQuery.ajax({
                    url: '/api/latest-list',
                    type: 'get',
                    dataType: 'json',
                    success(response) {
                        if(response.ret === 0){
                            jQuery("#latest-list tbody").html(response.data)
                        }
                    }
                })
            }
            // 本期TOP榜
            function updateTopList(){
                jQuery.ajax({
                    url: '/api/periods-top-list',
                    type: 'get',
                    dataType: 'json',
                    success(response) {
                        if(response.ret === 0){
                            jQuery("#top-list tbody").html(response.data)
                        }
                    }
                })
            };
            // 初始化刷新一次
            (()=>{
                updateAllTopList()
                updateLatestList()
                updateTopList()
            })();
            function getResultMessage(clickX, clickY) {
                // 根据点击位置获取结果消息
                for (const ellipse of ellipses) {
                    if (isPointInsideEllipse(clickX, clickY, ellipse.center, ellipse.radiusX, ellipse.radiusY)) {
                        return ellipse.label;
                    }
                }
                for (const polygon of polygons) {
                    if (isPointInsidePolygon(clickX, clickY, polygon)) {
                        return "body";
                    }
                }

                return false;
            }
            // 点击事件
            const clickableImage = jQuery("#clickableImage");
            clickableImage.click((event) => {
                const offset = clickableImage.offset();
                // 获取点击的相对图片的坐标
                const clickX = event.pageX - offset.left;
                const clickY = event.pageY - offset.top;
                const resultMessage = getResultMessage(clickX, clickY);
                if(!resultMessage) {
                    return
                }
                const jsonData = {
                    position: resultMessage
                };
                // 接口地址
                jQuery.ajax({
                    url: '/api/site-free-pool-feed',
                    type: 'POST',
                    data: jsonData,
                    dataType: 'json',
                    success(response) {
                        createDanmu(response.msg);
                        if (response.ret === 0) {
                            const newWidth = response.data.feed_schedule;
                            const self_bonus = response.data.self_bonus
                            if(newWidth>=100){
                                location.reload();
                            }
                            jQuery("#dynamicDiv")[0].style.width = `${newWidth}%`;
                            jQuery("#percentageSpan")[0].textContent = `${newWidth}%`;
                            jQuery("#banner-pool-text")[0].textContent = `${newWidth}%`;
                            jQuery("#banner-pool-bar")[0].style.width = `${newWidth}%`;
                            self_bonus && (jQuery("#self_bonus")[0].innerHTML = `<font class="color_bonus">魔力值 </font>${self_bonus.toFixed(1).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')}`)
                        }
                        // 更新数据
                        updateAllTopList()
                        updateLatestList()
                        updateTopList()
                    },
                    error(err) {
                        console.error('err: ', err);
                    }
                })
            })
            var layer = layui.layer;
            var tipsTimeout = ''
            clickableImage.hover(function() {
                var offset = jQuery(this).offset();
                var left = offset.left;
                var bottom = jQuery(window).height() - offset.top;

                var tipContent = `
                    <div>
                        <p style="margin-bottom: 10px;">点击电耗子开始调戏吧</p>
                        <i>你的调戏会加快进度条增长哦~</i>
                        <p>精灵球：喂养50000~100000小钱钱</p>
                        <p>耳朵：喂养10000~50000小钱钱</p>
                        <p>嘴巴：喂养1000~5000小钱钱</p>
                        <p>尾巴：喂养5000~10000小钱钱</p>
                        <p>肚子：喂养500~1000小钱钱</p>
                    </div>
                `;

                layer.tips(tipContent, '#clickableImage', {
                    tips: [3, 'rgb(254 177 71)'],
                    area: ['300px', 'auto'],
                    offset: [bottom, left],
                });

                clearTimeout(tipsTimeout); // 清除之前的计时器
            }, function() {
                tipsTimeout = setTimeout(function() {
                    layer.closeAll('tips');
                }, 3000); // 5秒后关闭提示框
            });
        })

    </script>

<?php


end_main_frame();
stdfoot();
?>
