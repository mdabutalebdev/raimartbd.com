<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f9; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f6f9; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 520px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #0F172A; padding: 26px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 800; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
                                Raimart
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 35px 32px;">
                            <h2 style="margin: 0 0 16px 0; color: #0F172A; font-size: 20px; font-weight: 600;">
                                Password Reset Request
                            </h2>
                            <p style="margin: 0 0 20px 0; color: #475569; font-size: 15px; line-height: 1.6;">
                                Hello <strong>{{ $userName }}</strong>,
                            </p>
                            <p style="margin: 0 0 24px 0; color: #475569; font-size: 15px; line-height: 1.6;">
                                We received a request to reset your password for your <strong>Raimart</strong> account. Please use the following One-Time Password (OTP) to complete the reset process:
                            </p>

                            <!-- OTP Box -->
                            <div style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border: 2px dashed #F97316; border-radius: 12px; padding: 20px; text-align: center; margin: 25px 0;">
                                <span style="display: block; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #c2410c; margin-bottom: 6px;">Your 6-Digit OTP</span>
                                <span style="display: inline-block; font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #ea580c; font-family: 'Courier New', Courier, monospace;">{{ $otp }}</span>
                                <span style="display: block; font-size: 12px; color: #9a3412; margin-top: 6px;">Valid for 15 minutes only</span>
                            </div>

                            <p style="margin: 0 0 16px 0; color: #64748b; font-size: 14px; line-height: 1.5;">
                                If you did not request a password reset, please ignore this email or contact support if you suspect unauthorized activity on your account.
                            </p>
                            
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0;">
                            
                            <p style="margin: 0; color: #94a3b8; font-size: 12px; line-height: 1.5; text-align: center;">
                                This is an automated email from Raimart. Please do not reply directly to this message.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 18px 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; color: #64748b; font-size: 12px;">
                                &copy; {{ date('Y') }} Raimart. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
