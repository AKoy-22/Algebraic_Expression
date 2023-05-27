<?php
function display_html_head($title){
    print <<<_HTML_HEAD
    <!doctype html>
    <html>
    <head>
        <title>$title</title>
        <style>
            label {
                font-weight: bold;
                display: inline-block;
                width: 200px;
                text-align: right;
                margin-right: 10px;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>

    _HTML_HEAD;
}

function display_html_foot(){
    print <<<_HTML_FOOT
    </body>
    </html>
    _HTML_FOOT;
}