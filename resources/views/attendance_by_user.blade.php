<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Report</title>
    <style>
        table, th, td {
            border: 1px solid black;
        }

    </style>
</head>
<body>
<div class="card">
    <div class="card-body">
        <div class="card-body">

            <div style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;text-align: center;">
                تقرير الحضور
<br>
                {{$data['user']->name}}
<br>
                من {{$from}} الي {{$to}}
            </div>
            {{-- branch_irregularities--}}
            <br>
            <br>
            <div>
                <table
                    style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;width:100%">
                    <thead>
                    <tr>
                        <th style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;text-align:center">
                            التاريخ
                        </th>
                        <th style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;text-align:center">
                            النوع
                        </th>
                        <th style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;text-align:center">
                            الوقت
                        </th>
                        <th style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;text-align:center; width: 35%;">
                            الملاحظات
                        </th>
                        <th style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;text-align:center">
                            خطوط العرض
                        </th>
                        <th style="font-family: DejaVu Sans, sans-serif ;font-size: 13px;text-align:center">
                            خطوط الطول
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                        @foreach($data['report'] as $details)
                            <tr>
                                <td style="text-align: center" rowspan="2" >{{$details['date']}}</td>
                                <td style="text-align: center">الحضور</td>
                                <td style="text-align: center">{{$details['in_time'] ? date('g:i a', strtotime($details['in_time'])) : 'لم يتم الحضور'}}</td>
                                <td style="text-align: center">{{$details['notes']}}</td>
                                <td style="text-align: center">{{$details['lat']}}</td>
                                <td style="text-align: center">{{$details['lng']}}</td>
                            </tr>
                            <tr>

                                <td style="text-align: center">الانصراف</td>
                                <td style="text-align: center">{{$details['out_time'] ? date('g:i a', strtotime($details['out_time'])) : 'لم يتم الانصراف'}}</td>
                                <td style="text-align: center">{{$details['out_notes']}}</td>
                                <td style="text-align: center">{{$details['out_lat']}}</td>
                                <td style="text-align: center">{{$details['out_lng']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<style>
    #footer {
        position: absolute;
        bottom: 0;
        height: 2.5rem; /* Footer height */
    }
</style>

<footer id="footer" class=" "
        style="font-family:DejaVu Sans, sans-serif ;font-size: 13px;text-align: center;padding-left: 175px">
    <br>
{{--    <p class="clearfix text-muted text-sm-center mb-0 px-2"><span class="float-md-left d-xs-block d-md-inline-block">Copyright   2021 <a--}}
{{--                href="#" target="_blank"--}}
{{--                class="text-bold-800 grey darken-2">Uram IT </a>, All rights reserved. </span><span--}}
{{--            class="float-md-right d-xs-block d-md-inline-block"> </span></p>--}}
</footer>
</body>
</html>

