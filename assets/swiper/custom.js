/** Fixes review slider */
document.addEventListener("DOMContentLoaded", function () {
	new Swiper(".fixed-reviews-swiper", {
		loop: false,
		slidesPerView: 2.5,
		spaceBetween: 24, // can be 16 if you prefer tighter
		navigation: {
			nextEl: ".jf-fixed-reviews-next",
			prevEl: ".jf-fixed-reviews-prev",
		},
		pagination: {
			el: ".jf-fixed-reviews-pagination",
			clickable: true,
		},
		breakpoints: {
			1024: { slidesPerView: 2.5, spaceBetween: 16 },
			768: { slidesPerView: 2, spaceBetween: 16 },
			0: { slidesPerView: 1, spaceBetween: 8 },
		}
	});
});

/** General reviews slider */
document.addEventListener("DOMContentLoaded", function() {
    new Swiper(".jf-reviews-slider", {
        loop: true,
        autoplay: {
            delay: 1700,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: ".jf-reviews-next",
            prevEl: ".jf-reviews-prev",
        },
        pagination: {
            el: ".jf-reviews-pagination",
            clickable: true,
        },
        breakpoints: {
            1024: { slidesPerView: 3, spaceBetween: 8 },
            768: { slidesPerView: 2, spaceBetween: 8 },
            0: { slidesPerView: 1, spaceBetween: 8 }
        }
    });
});

/** ACF FAQs */
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".jf-faq-question").forEach(button => {
        button.addEventListener("click", function() {
            let answer = this.nextElementSibling;
            let icon = this.querySelector(".jf-faq-icon");
            let isOpen = this.getAttribute("aria-expanded") === "true";

            document.querySelectorAll(".jf-faq-answer").forEach(el => el.style.display = "none");
            document.querySelectorAll(".jf-faq-question").forEach(el => {
                el.setAttribute("aria-expanded", "false");
                el.querySelector(".jf-faq-icon").textContent = "+";
            });

            if (!isOpen) {
                answer.style.display = "block";
                this.setAttribute("aria-expanded", "true");
                icon.textContent = "−";
            }
        });
    });
});

/** Read more on .page-short-desc_readmore **/
jQuery(document).ready(function($) {
    var showChar = 50; // Character count to show before "Read more"
    var ellipses = "... ";
    var moreText = "Read more";
    var lessText = "Read less";

    $(".page-short-desc_readmore").each(function() {
        var content = $(this).html();

        if (content.length > showChar) {
            var visibleText = content.substr(0, showChar);
            var hiddenText = content.substr(showChar);

            var html = visibleText + 
                       "<span class='jf-ellipsis'>" + ellipses + 
                       "<a href='#' class='jf-read-more'>" + moreText + "</a></span>" + 
                       "<span class='jf-hidden-text' style='display:none;'>" + hiddenText + 
                       " <a href='#' class='jf-read-less'>" + lessText + "</a></span>";

            $(this).html(html);
        }
    });

    // Read more
    $(document).on("click", ".jf-read-more", function(e) {
        e.preventDefault();
        var parent = $(this).closest(".page-short-desc_readmore");
        parent.find(".jf-ellipsis").hide();
        parent.find(".jf-hidden-text").show();
    });

    // Read less
    $(document).on("click", ".jf-read-less", function(e) {
        e.preventDefault();
        var parent = $(this).closest(".page-short-desc_readmore");
        parent.find(".jf-hidden-text").hide();
        parent.find(".jf-ellipsis").show();
    });
});

/**
 * Produtc ingredients mobile slider
 * @Product pages
 **/
document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.jf-ing-mobile-swiper', {
    slidesPerView: 1.5,
    spaceBetween: 10,
    pagination: {
      el: '.jf-ing-mobile-swiper .swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.jf-ing-mobile-swiper .swiper-button-next',
      prevEl: '.jf-ing-mobile-swiper .swiper-button-prev',
    },
    breakpoints: {
      768: {
        slidesPerView: 3,
        spaceBetween: 10,
        pagination: false,
        navigation: false,
      },
    },
  });
});