/*Social Sharing*/
jQuery(function ($) {
    'use strict';
    // Share Icons
    $.fn.socShare = function (opts) {
        var $this = this;
        //var $win = $(window);
        var url, name;
        opts = $.extend({
            attr: 'href',
            facebook: false,
            google_plus: false,
            twitter: false,
            linked_in: false,
            pinterest: false
        }, opts);

        for (var opt in opts) {

            if (opts[opt] === false) {
                continue;
            }

            switch (opt) {
                case 'facebook':
                    url = 'https://www.facebook.com/sharer/sharer.php?u=';
                    name = 'Facebook';
                    _popup(url, name, opts[opt], 400, 640);
                    break;

                case 'twitter':
                    var posttitle = $(".sbtwitter a").data("title");
                    /*@todo posttile hasn't been added yet*/
                    url = 'https://twitter.com/intent/tweet?url=';
                    name = 'Twitter';
                    _popup(url, name, opts[opt], 440, 600);
                    break;

                case 'google_plus':
                    url = 'https://plus.google.com/share?url=';
                    name = 'Google+';
                    _popup(url, name, opts[opt], 600, 600);
                    break;

                case 'linked_in':
                    url = 'https://www.linkedin.com/shareArticle?mini=true&url=';
                    name = 'LinkedIn';
                    _popup(url, name, opts[opt], 570, 520);
                    break;

                case 'pinterest':
                    url = 'https://www.pinterest.com/pin/find/?url=';
                    name = 'Pinterest';
                    _popup(url, name, opts[opt], 500, 800);
                    break;
                default:
                    break;
            }
        }

        /*Helper function to determine shared link is url*/
        function isUrl(data) {
            var regexp = new RegExp('(^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|(www\\.)?))[\\w-]+(\\.[\\w-]+)+([\\w-.,@?^=%&:/~+#-]*[\\w@?^=%&;/~+#-])?', 'gim');
            return regexp.test(data);
        }

        /*creates popup looks nice and doesn't require the user to leave page*/
        function _popup(url, name, opt, height, width) {
            if (opt !== false && $this.find(opt).length) {
                $this.on('click', opt, function (e) {
                    e.preventDefault();

                    var top = (screen.height / 2) - height / 2;
                    var left = (screen.width / 2) - width / 2;
                    var share_link = $(this).attr(opts.attr);

                    if (!isUrl(share_link)) {
                        share_link = window.location.href;
                    }

                    window.open(
                        url + encodeURIComponent(share_link),
                        name,
                        'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=' + height + ',width=' + width + ',top=' + top + ',left=' + left
                    );

                    return false;
                });
            }
        }

        return false;
    };


    $('.cm-share').socShare({
        facebook: '.cm-soc-fb',
        twitter: '.cm-soc-tw',
        google_plus: '.cm-soc-gplus',
        linked_in: '.cm-soc-linkedin',
        pinterest: '.cm-soc-pinterest'
    });


});