<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
</head>
<body style="margin:0;padding:0;background:#F7F9FC;font-family:Arial,Helvetica,sans-serif;color:#16213A;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F7F9FC;padding:32px 0;">
  <tr>
    <td align="center">
      <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#FFFFFF;border-radius:12px;overflow:hidden;">
        <tr>
          <td style="background:#082159;padding:24px 32px;">
            <span style="color:#FFFFFF;font-size:18px;font-weight:700;">Altura Workforce Solutions</span>
          </td>
        </tr>
        <tr>
          <td style="padding:32px;">
            <h2 style="margin:0 0 20px;font-size:20px;color:#082159;">{{ $heading }}</h2>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
              @foreach($lines as $label => $value)
                <tr>
                  <td style="padding:8px 0;border-bottom:1px solid #EEF1F8;font-size:13px;color:#6B7590;width:160px;vertical-align:top;">{{ $label }}</td>
                  <td style="padding:8px 0;border-bottom:1px solid #EEF1F8;font-size:14px;color:#16213A;vertical-align:top;">{{ $value }}</td>
                </tr>
              @endforeach
            </table>

            @if($actionUrl)
              <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:28px;">
                <tr>
                  <td style="background:#C89B3C;border-radius:8px;">
                    <a href="{{ $actionUrl }}" style="display:inline-block;padding:12px 24px;color:#082159;font-weight:700;font-size:14px;text-decoration:none;">{{ $actionLabel ?? 'View in Admin' }}</a>
                  </td>
                </tr>
              </table>
            @endif

            <p style="margin-top:28px;font-size:12px;color:#94A0BD;">This is an automated notification from the Altura Workforce Solutions website.</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
