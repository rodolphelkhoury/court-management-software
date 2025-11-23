<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5; /* indigo */
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #4f46e5; /* indigo */
        }

        .header p {
            margin: 5px 0 0;
            font-size: 14px;
            color: #777;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 25px;
        }

        .details p {
            margin: 3px 0;
            font-size: 14px;
        }

        .details .label {
            font-weight: bold;
            color: #555;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
        }

        .table th {
            background-color: #eef2ff; /* light indigo */
            color: #4f46e5; /* indigo */
        }

        .total {
            text-align: right;
            margin-top: 10px;
        }

        .total h3 {
            margin: 0;
            font-size: 18px;
            color: #4f46e5; /* indigo */
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }

        @media (max-width: 600px) {
            .details {
                grid-template-columns: 1fr;
            }
            .table th, .table td {
                padding: 8px 10px;
            }
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>Invoice</h1>
            <p>Complex: {{ $invoice->reservation->court->complex->name }}</p>
        </div>

        <div class="details">
            <p><span class="label">Invoice ID:</span> {{ $invoice->id }}</p>
            <p><span class="label">Customer Name:</span> {{ $invoice->customer->name }}</p>
            <p><span class="label">Phone Number:</span> {{ $invoice->customer->customer->phone_number }}</p>
            <p><span class="label">Reservation ID:</span> {{ $invoice->reservation_id }}</p>
            <p><span class="label">Court Name:</span> {{ $invoice->reservation->court->name }}</p>
            <p><span class="label">Court Type:</span> {{ $invoice->reservation->court->court_type->name }}</p>
            <p><span class="label">Date:</span> {{ \Carbon\Carbon::parse($invoice->reservation->reservation_date)->format('d M Y') }}</p>
            <p><span class="label">Due Date:</span> {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') : 'Not specified' }}</p>
            <p><span class="label">Status:</span> {{ ucfirst($invoice->status) }}</p>
            @if ($invoice->status == 'paid')
                <p><span class="label">Paid At:</span> {{ \Carbon\Carbon::parse($invoice->paid_at)->format('d M Y') }}</p>
            @endif
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Reservation Charges</td>
                    <td>${{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            <h3>Total: ${{ number_format($invoice->amount, 2) }}</h3>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>If you have any questions about this invoice, please contact us.</p>
        </div>
    </div>
</body>
</html>
