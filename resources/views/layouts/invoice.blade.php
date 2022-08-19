<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{setting('app_name')}} | {{setting('app_short_description')}}</title>
    {{-- <link rel="icon" type="image/png"   href="{{ url('images/logo.png') }}"> --}}
    {{-- <title>{{ config('app.name') }}</title> --}}
    <link rel="icon" type="image/png" href="{{$app_logo}}"/>
    <link rel="stylesheet" type="text/css" href="{{ url('css/sheets-of-paper.css') }}">

</head>

<body class="document">

    <style>
        /* arabic */
        @font-face {
            font-family: 'Tajawal';
            font-style: normal;
            font-weight: 400;
            src: url( {{ url('fonts/Tajawal/Tajawal-Medium.ttf') }} );
        }

        body {
            direction: rtl;
            font-family: Tajawal;
        }

        .header {
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #000;
            margin-bottom: 20px;
        }

        .header img {
            height: 80px;
        }

        .header .title {
            font-size: 24px;
            text-align: right;
            padding-right: 10px;
            margin: 0;
        }

        .page-title {
            text-align: center;
            font-size: 18px;
            text-decoration: underline;
            text-underline-position: under;
        }

        .info {
            display: flex;
            justify-content: space-between;
        }

        .info ul {
            padding: 0;
            list-style: none;
            max-width: 50%;
        }

        .info ul li {
            line-height: 1.5;
        }

        .table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
            page-break-inside : auto;
        }

        .table th , .table td {
            padding: 7px;
            border: 1px solid #ddd;
          }
        .footer ul {
            padding: 0;
            list-style: none;
        }

        .footer ul li {
            line-height: 1.5;
        }
    </style>

    <div class="page">

        <div class="header">
            <img src="{{$app_logo}}" />
        </div>

        <h2 class="page-title">@yield('title')</h2>

        <div class="info">
            @yield('headers')
        </div>

        <div class="footer">

            @yield('content')

        </div>

    </div>


    <script>
        print();
        //window.onafterprint = function(e) { window.close() };
    </script>

</body>

</html>