<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        td, th {
            padding: 8px;
            border: 1px solid #000;
        }
        th {
            text-align: left;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h3>{{ $productTypeName ?? 'N/A' }} Report</h3>
    
    <!-- Branch and Organization Details -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
          
             {{-- <pre>{{ print_r($branchData, true) }}</pre> --}}
            <td style="padding: 8px;">
                <strong>Branch Name:</strong> {{ $branchData['branch_name'] ?? 'N/A' }}<br>
                <strong>Branch Email:</strong> {{ $branchData['branch_email'] ?? 'N/A' }}<br>
                <strong>Branch Phone:</strong> {{ $branchData['branch_phone_number'] ?? 'N/A' }}<br>
            </td>
            <td style="padding: 8px; text-align: right;">
                <strong>Organization Name:</strong> {{ $branchData['organization_name'] ?? 'N/A' }}<br>
                <strong>Email:</strong> {{ $branchData['company_email'] ?? 'N/A' }}<br>
                <strong>Phone:</strong> {{ $branchData['company_phone_number'] ?? 'N/A' }}<br>
                <strong>Address:</strong> {{ $branchData['company_address'] ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <!-- Dynamic Product Table -->
    <h3>Report Detail(s)</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                @if (!empty($data) && isset($data[0]))
                <th>S/N</th>
              
                    @foreach(array_keys($data[0]) as $column)
                        <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                    @endforeach
                @else
                    <th>No Data Available</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @if (!empty($data))
                @foreach($data as $index => $product)
                    <tr>
                        <td>{{ $index + 1 }}</td> <!-- Serial number -->
                        @foreach($product as $value)
                            <td>{{ $value ?? 'N/A' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="100%">No data available</td>
                </tr>
            @endif
        </tbody>
    </table>
    
</body>
</html>
