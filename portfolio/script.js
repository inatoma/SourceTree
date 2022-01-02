const navToggle = document.querySelector('.nav-toggle');
const navLinks = document.querySelectorAll('.nav_link')

navToggle.addEventListener('click', () => {
    document.body.classList.toggle('nav-open');
});

navLinks.forEach(link => {
    link.addEventListener('click', () => {
        document.body.classList.remove('nav-open');
    })
})

$('.intro_img').each(function() {
    var img_off = $(this).attr('src');
    var img_on = $(this).attr('src').replace('off', 'on');

    $(this).hover(
        function () {
            $(this).attr('src', img_on);
        },
        function () {
            $(this).attr('src', img_off);
        }
    );
});

window.onscroll = function(){  
    var scrollTop = window.pageYOffset ;  //スクロール量を代入する
    
    if (scrollTop == 0 ) {   //最上部に戻ってきた時
    $(".intro_img").css('opacity', '1')
      $('.intro_img').css('transition', '0.5s')
    
      $(".intro_img2").css('opacity', '0')
      $('.intro_img2').css('transition', '0.5s')
    }
    if (scrollTop > 200 ) {   //１０pxスクロールした時
      $(".intro_img").css('opacity', '0')
      $('.intro_img').css('transition', '0.5s')
    
      $(".intro_img2").css('opacity', '1')
      $('.intro_img2').css('transition', '0.5s')
    }
    }

$('.slider').slick({
    autoplay: true,
    autoplaySpeed: 4000
});

$('.about-me_img').each(function() {
    var img_off2 = $(this).attr('src');
    var img_on2 = $(this).attr('src').replace('off', 'on');

    $(this).hover(
        function () {
            $(this).attr('src', img_on2);
        },
        function () {
            $(this).attr('src', img_off2);
        }
    );
});