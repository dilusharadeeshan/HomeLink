<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f7f5;font-family:Arial,Helvetica,sans-serif;color:#193038;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f7f5;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;">
                <tr><td style="padding:0 0 18px 4px;font-size:24px;font-weight:700;letter-spacing:-1px;color:#193038;">HomeLink<span style="color:#167d6b;">.</span></td></tr>
                <tr><td style="background-color:#ffffff;border:1px solid #e4ebe8;border-radius:14px;padding:36px 34px;">
                    <p style="margin:0 0 10px;color:#167d6b;font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">Booking update · {{ $booking->reference() }}</p>
                    <h1 style="margin:0 0 14px;font-size:26px;line-height:1.25;color:#193038;">{{ $heading }}</h1>
                    <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#65766b;">Hi {{ $recipientName }}, {{ $introText }}</p>

                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:0 0 24px;background-color:#f7f9f8;border-radius:10px;">
                        <tr><td style="padding:16px 18px;border-bottom:1px solid #e4ebe8;"><span style="display:block;color:#78867f;font-size:11px;">SERVICE</span><strong style="display:block;margin-top:4px;font-size:14px;color:#193038;">{{ $booking->service_title }}</strong></td></tr>
                        <tr><td style="padding:14px 18px;border-bottom:1px solid #e4ebe8;"><span style="display:block;color:#78867f;font-size:11px;">SCHEDULED</span><strong style="display:block;margin-top:4px;font-size:14px;color:#193038;">{{ $booking->starts_at->format('l, d M Y') }} · {{ $booking->starts_at->format('g:i A') }} · Asia/Colombo</strong></td></tr>
                        @if($providerNotice)
                            <tr><td style="padding:14px 18px;border-bottom:1px solid #e4ebe8;"><span style="display:block;color:#78867f;font-size:11px;">CUSTOMER</span><strong style="display:block;margin-top:4px;font-size:14px;color:#193038;">{{ $booking->customer->name }}</strong></td></tr>
                            @if($event === 'provider_request')
                                <tr><td style="padding:14px 18px;border-bottom:1px solid #e4ebe8;"><span style="display:block;color:#78867f;font-size:11px;">SERVICE ADDRESS</span><strong style="display:block;margin-top:4px;font-size:14px;color:#193038;">{{ $booking->address }}</strong></td></tr>
                                <tr><td style="padding:14px 18px;"><span style="display:block;color:#78867f;font-size:11px;">CUSTOMER PHONE</span><strong style="display:block;margin-top:4px;font-size:14px;color:#193038;">{{ $booking->phone }}</strong></td></tr>
                            @endif
                        @else
                            <tr><td style="padding:14px 18px;"><span style="display:block;color:#78867f;font-size:11px;">PROVIDER</span><strong style="display:block;margin-top:4px;font-size:14px;color:#193038;">{{ $booking->provider->name }}</strong></td></tr>
                        @endif
                    </table>

                    @if($event === 'rejected' && $booking->rejection_reason)
                        <div style="margin:0 0 24px;padding:16px 18px;border-left:3px solid #167d6b;background-color:#f7f9f8;color:#52665d;font-size:14px;line-height:1.7;">
                            <strong style="display:block;margin-bottom:5px;color:#193038;">Note from the provider</strong>{{ $booking->rejection_reason }}
                        </div>
                    @endif

                    <a href="{{ $bookingUrl }}" style="display:inline-block;padding:13px 20px;border-radius:7px;background-color:#167d6b;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">View booking</a>
                </td></tr>
                <tr><td style="padding:20px 6px 0;color:#7a8981;font-size:12px;line-height:1.7;">You’re receiving this automatic update because of a change to your HomeLink booking.<br>Good people. Better homes.</td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
