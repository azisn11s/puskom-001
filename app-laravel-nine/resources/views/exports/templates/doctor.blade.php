<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <style>
        .head-cell {
            background-color: cadetblue; 
            font-weight: bold;
        }
    </style>

<table>
    <tbody>
        <tr>
            <td style="font-size: 14px; font-weight: bold;">UPCORE TECHNOLOGY</td>
        </tr>
        <tr></tr>
        <tr>
            <td colspan="13" style="font-size: 12px; font-weight: bold; text-align: center;">
                ADD DOCTORS
            </td>
        </tr>
        <tr style="">
            <td class="head-cell" style="width: 5px; background-color: cadetblue; font-weight: bold;">No.</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">First Name</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Last Name</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Code</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Front Title</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Back Title</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Gender</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Current Hospital</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">City</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Address</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Market</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Code of Territory</td>
            <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Specialty</td>
            <td class="head-cell" style="background-color: orange; font-weight: bold;">Classification</td>
        </tr>
        <tr>
            <td>1</td>
            <td>Test</td>
            <td>Name</td>
            <td>'{{ $latestDoctor->identity_id }}</td>
            <td>dr.</td>
            <td>Ph.D</td>
            <td>M</td>
            <td>The Hospital</td>
            <td>Sumedang</td>
            <td>Jl. Geusan Ulun No. 3</td>
            <td>Hypermart</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
    
</body>
</html>