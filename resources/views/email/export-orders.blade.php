<!DOCTYPE html>
<html>
<head>
    <title>Order Export Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Order Export Report</h2>
        <p>Your order data export is ready</p>
    </div>
    
    <div class="content">
        <p>Hello,</p>
        <p>Your order export has been successfully generated. The report contains detailed information about the orders based on your selected filters.</p>
        
        <p><strong>Export Details:</strong></p>
        <ul>
            <li>Export Date: {{ \Carbon\Carbon::now()->format('M j, Y g:i A') }}</li>
            @if($order_from && $order_to)
                <li>Date Range: {{ \Carbon\Carbon::parse($order_from)->format('M j, Y') }} to {{ \Carbon\Carbon::parse($order_to)->format('M j, Y') }}</li>
            @endif
        </ul>
        
        <p>Please find the attached Excel file containing the exported order data.</p>
        
        <p>If you have any questions or need assistance, please contact our support team.</p>
        
        <p>Thank you!</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Bluetti CRM. All rights reserved.</p>
    </div>
</body>
</html>