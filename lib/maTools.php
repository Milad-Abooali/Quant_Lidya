<?php

namespace MATools;

class maTools
{

}

?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <!-- Bootstrap CSS -->
        <style>
            body{
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            }

            /* Terminal */


            /* Top Menu */
            topMenu {
                display: block;
                background: #eee !important;
            }
            topMenu nav{
                width: 750px;
                margin: 0 auto;
            }
            topMenu ul{
                margin: 0px;
                padding: 0px;
                list-style: none;
            }
            topMenu ul.dropdown{
                position: relative;
                width: 100%;
            }
            topMenu ul.dropdown li{
                font-weight: bold;
                float: left;
                width: 130px;
                position: relative;
                background: #ecf0f1;
            }
            topMenu ul.dropdown a:hover{
                color: #000;
            }
            topMenu ul.dropdown li.title{
                display: block;
                width: auto;
                width: 100px;
                padding: 8px 4px;
                color: #000;
                z-index: 2000;
                text-align: center;
                font-weight: 500;
            }
            topMenu ul.dropdown li a{
                display: block;
                padding: 8px 4px;
                color: #34495e;
                position: relative;
                z-index: 2000;
                text-align: center;
                text-decoration: none;
                font-weight: 200;
            }
            topMenu ul.dropdown li a:hover,
            topMenu ul.dropdown li a.hover{
                background: #0c3c5c;
                position: relative;
                color: #fff;
            }
            topMenu ul.dropdown ul{
                display: none;
                position: absolute;
                top: 0;
                left: 0;
                width: 180px;
                z-index: 1000;
            }
            topMenu ul.dropdown ul li {
                font-weight: normal;
                background: #f6f6f6;
                color: #000;
                border-bottom: 1px solid #ccc;
            }
            topMenu ul.dropdown ul li a{
                display: block;
                color: #34495e !important;
                background: #eee !important;
            }
            topMenu ul.dropdown ul li a:hover{
                display: block;
                background: #3498db !important;
                color: #fff !important;
            }
            topMenu .drop > a{
                position: relative;
            }
            topMenu .drop > a:after{
                content:"";
                position: absolute;
                right: 10px;
                top: 40%;
                border-left: 5px solid transparent;
                border-top: 5px solid #333;
                border-right: 5px solid transparent;
                z-index: 999;
            }
            topMenu .drop > a:hover:after{
                content:"";
                border-left: 5px solid transparent;
                border-top: 5px solid #fff;
                border-right: 5px solid transparent;
            }
        </style>
        <title>MATools</title>
    </head>
    <body>

    <topMenu>
      <nav>
        <ul class="dropdown">
            <li class="title">MA Tools</li>
            <li class="drop"><a href="#">System</a>
                <ul class="sub_menu">
                    <li><a href="#">File Manager</a></li>
                    <li><a href="#">Run Command</a></li>
                    <li><a href="#">PHP info</a></li>
                    <li><a href="#">ENV</a></li>
                    <li><a href="#">Hardware Info</a></li>
                    <li><a href="#">systeminfo</a></li>
                </ul>
            </li>
            <li class="drop"><a href="#">Network</a>
                <ul class="sub_menu">
                    <li><a href="#">Lorem</a></li>
                    <li><a href="#">Ipsum</a></li>
                    <li><a href="#">Dolor</a></li>
                    <li><a href="#">Lipsum</a></li>
                    <li><a href="#">Consectetur </a></li>
                    <li><a href="#">Duis</a></li>
                    <li><a href="#">Sed</a></li>
                    <li><a href="#">Natus</a></li>
                    <li><a href="#">Excepteur</a></li>
                    <li><a href="#">Voluptas</a></li>
                    <li><a href="#">Voluptate</a></li>
                    <li><a href="#">Malorum</a></li>
                    <li><a href="#">Bonorum</a></li>
                    <li><a href="#">Nemo</a></li>
                    <li><a href="#">Quisquam</a></li>

                </ul>
            </li>
            <li class="drop"><a href="#">App</a>
                <ul class="sub_menu">
                    <li><a href="#">List Vars</a></li>
                    <li><a href="#">List Class</a></li>
                    <li><a href="#">DB Connections</a></li>
                    <li><a href="#">Session Manager</a></li>
                    <li><a href="#">ENV</a></li>
                </ul>
            </li>
            <li class="drop"><a href="#">Mods</a>
                <ul class="sub_menu">
                    <li><a href="#">Mail</a></li>
                    <li><a href="#">Curl</a></li>
                    <li><a href="#">MySQL</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    </topMenu>

    <main>
        <div>
            test
        </div>
    </main>





    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script>

        // Top Menu
        var maxHeight = 400;
        $(function(){
            $("topMenu .dropdown > li").hover(function() {
                const $container = $(this),
                    $list = $container.find("ul"),
                    $anchor = $container.find("a"),
                    height = $list.height() * 1.1,       // make sure there is enough room at the bottom
                    multiplier = height / maxHeight;     // needs to move faster if list is taller
                // need to save height here so it can revert on mouseout
                $container.data("origHeight", $container.height());
                // so it can retain it's rollover color all the while the dropdown is open
                $anchor.addClass("hover");
                // make sure dropdown appears directly below parent list item
                $list
                    .show()
                    .css({
                        paddingTop: $container.data("origHeight")
                    });
                // don't do any animation if list shorter than max
                if (multiplier > 1) {
                    $container
                        .css({
                            height: maxHeight,
                            overflow: "hidden"
                        })
                        .mousemove(function(e) {
                            const offset = $container.offset();
                            const relativeY = ((e.pageY - offset.top) * multiplier) - ($container.data("origHeight") * multiplier);
                            if (relativeY > $container.data("origHeight")) {
                                $list.css("top", -relativeY + $container.data("origHeight"));
                            };
                        });
                }
            }, function() {
                const $el = $(this);
                // put things back to normal
                $el
                    .height($(this).data("origHeight"))
                    .find("ul")
                    .css({ top: 0 })
                    .hide()
                    .end()
                    .find("a")
                    .removeClass("hover");
            });
        });


    </script>

    </body>
</html>


