

            var page = require('webpage').create();
            page.viewportSize = { width: 1024, height: 768 };

            page.clipRect = { top: 0, left: 0, width: 640, height: 480 };

            page.open('http://444.hu', function () {
                page.render('444.hu55e24ffc6f6bc7c579fb09cdb02f3360_1024_768.png');
                phantom.exit();
            });


            