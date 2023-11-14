// 序列帧动画函数
jQuery(document).ready(function () {
    let timer = null;
    let IMGNUM = 1;
    let flag = true;
    const MAX_IMGNUM = 8;
    // 预加载函数
    function preloadImages() {
        let imagesArray = [];
        for (let i = 1; i <= MAX_IMGNUM; i++) {
            let img = new Image();
            img.src = `./pic/gifpool/${i}.png`;
            imagesArray.push(
                `<img src="./pic/gifpool/${i}.png" alt="global site free pool">`
            );
        }
        jQuery("#img-pool-gif-box").html(imagesArray.join(""));
    }
    // 提前加载图片
    preloadImages();

    jQuery("#img-pool-gif-box").hover(
        function () {
            timer = setInterval(() => {
                jQuery(this)
                    .children()
                    .eq(IMGNUM - 1)
                    .hide();
                IMGNUM += 1;
                if (IMGNUM > MAX_IMGNUM) {
                    IMGNUM = 1;
                    clearInterval(timer);
                }
                jQuery(this)
                    .children()
                    .eq(IMGNUM - 1)
                    .show();
            }, 60);
        },
        function () {
            clearInterval(timer);
            jQuery(this).children().hide();
            jQuery(this).children().first().show();
            IMGNUM = 1;
        }
    );
});
