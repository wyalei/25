$.fn.time_Scroll = function () {
    var index = 0;
    var play = null;
    $(".bottom_button li").mouseover(function () {
       clearInterval(play);
       index = $(this).index();
       $(this).addClass("now").siblings().removeClass('now');
       $(".swiper-wrapper li").eq(index).show().siblings("li").hide();
    }).mouseout(function () {
        autoplay();
    });

    function autoplay() {
        play = setInterval(function () {
            if (index > 3) {
                index = 0;
            }

            $(".bottom_button li").eq(index).addClass("now").siblings().removeClass("now");
            $(".swiper-wrapper li").eq(index).show().siblings("li").hide();
            index++;
        }, 2000);
    }

    autoplay();
}