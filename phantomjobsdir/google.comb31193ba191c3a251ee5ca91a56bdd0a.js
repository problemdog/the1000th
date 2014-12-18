

            var page = require('webpage').create();
            page.viewportSize = { width: 1024, height: 768 };

            page.clipRect = { top: 0, left: 0, width: 640, height: 480 };

            page.open('http://google.com', function () {
                page.render('google.comc7b920f57e553df2bb68272f61570210_1024_768.png');
                phantom.exit();
            });


            