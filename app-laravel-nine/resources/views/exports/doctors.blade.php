<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
                <td colspan="12" style="font-size: 12px; font-weight: bold; text-align: center;">
                    IMPORT DOCTORS (UPDATE RECORDS)
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
                <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Status Active</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">Record ID</td>
            </tr>
            @foreach ($doctors as $key => $doctor)
                <tr>
                    <td>{{++$key}}</td>
                    <td>{{ $doctor->first_name }}</td>
                    <td>{{ $doctor->last_name }}</td>
                    <td>{{ $doctor->identity_id }}</td>
                    <td>{{ $doctor->front_title }}</td>
                    <td>{{ $doctor->back_title }}</td>
                    <td>{{ $doctor->gender }}</td>
                    <td>{{ $doctor->current_hospital }}</td>
                    <td>{{ $doctor->city }}</td>
                    <td>{{ $doctor->address }}</td>
                    <td>{{ $doctor->market }}</td>
                    <td>{{ $doctor->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $doctor->id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>