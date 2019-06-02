<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <!-- <link href="https://fonts.googleapis.com/css?family=lucida:200,600" rel="stylesheet" type="text/css"> -->
    </head>
    <style>
        body{
            padding: 5px 5px 0px 5px;
            font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
        }
        p{
            line-height: 0.4;
        }
        .heading{
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        p{
            font-size: 11px;
            line-height: normal;
            text-align: justify;
        }

        table{
            font-size: 11px;

        }
        table td{
                /*border: 1px solid #000;*/
            }
    </style>
    <body>
        <p>
            Hello {{ $userInfo['email'] }}, <br><br>

            This is a confirmation email of your password reset activity.<br><br>
            
            
            CBP Team

        </p>
    </body>
</html>
