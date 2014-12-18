

            var page = require('webpage').create();
            page.viewportSize = { width: 1024, height: 768 };

            page.clipRect = { top: 0, left: 0, width: 640, height: 480 };

            page.open('http://codex.wordpress.org/Plugin_API/Action_Reference/post_submitbox_misc_actions', function () {
                page.render('codex.wordpress.orgd8fe54738029eeaa7ce3b14f735d1a0f_1024_768.png');
                phantom.exit();
            });


            