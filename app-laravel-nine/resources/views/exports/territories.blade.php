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
                <td colspan="11" style="font-size: 12px; font-weight: bold; text-align: center;">
                    TERRITORIES
                </td>
            </tr>
            <tr style="">
                <td class="head-cell" style="width: 5px; background-color: cadetblue; font-weight: bold;">No.</td>
                <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Territory</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">Employee ID</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">PIC Territory</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">Area</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">PIC Area</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">Region</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">PIC Region</td>
                <td class="head-cell" style="background-color: cadetblue; font-weight: bold;">Status Active</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">Created At</td>
                <td class="head-cell" style="background-color: orange; font-weight: bold;">Updated At</td>
            </tr>
            @foreach ($territories as $key => $territory)
                <tr>
                    <td>{{++$key}}</td>
                    <td>{{ $territory->name }}</td>
                    <td>{{ optional($territory->employee)->employee_code }}</td>
                    <td>{{ optional($territory->employee)->fullname }}</td>
                    <td>{{ $territory->area->area_name }}</td>
                    <td>{{ optional($territory->area->employee)->fullname }}</td>
                    <td>{{ $territory->area->region->region_name }}</td>
                    <td>{{ optional($territory->area->region->employee)->fullname }}</td>
                    <td>{{ $territory->status ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $territory->created_at }}</td>
                    <td>{{ $territory->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>