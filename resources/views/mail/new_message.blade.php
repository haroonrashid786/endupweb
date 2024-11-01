<!DOCTYPE html>
<html>
<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap');
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Lato', sans-serif; background-color: #f5f5f5;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse;">
        <tr>
            <td bgcolor="#74bb8e" style="padding: 20px 0;">
                <img src="{{asset('images/logo.png')}}" alt="Logo" width="200" style="display: block; margin: 0 auto;">
            </td>
        </tr>
        <tr>
            <td bgcolor="#ffffff" align="left" style="padding: 20px 30px;">
                <p style="margin: 0; font-size: 16px; color: #666666;">You have just recieved a new message.</p>
            </td>
        </tr>
        <tr>
    <td bgcolor="#ffffff" align="center" style="padding: 40px 30px;">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center">
                    <h2 style="margin: 0; font-size: 24px; color: #333333;">Message Received</h2>
                </td>
            </tr>
            <tr>
                <td height="30"></td>
            </tr>
            <tr>
                <td align="center">
                    <p style="margin: 0; font-size: 18px; color: #666666;">Subject: {{$subject}}</p>
                    <p style="margin: 0; font-size: 18px; color: #666666;">Ticket: {{$ticket_no}}</p>
                    <p style="margin: 5px 0; font-size: 18px; color: #666666;">Sender: {{Auth::user()->name}}</p>
                </td>
            </tr>
            <tr>
                <td height="30"></td>
            </tr>
            <tr>
            <td align="center">
                    <table bgcolor="#f9f9f9" width="100%" border="0" cellspacing="0" cellpadding="20" style="border-radius: 10px;">
                        <tr>
                            <td style="font-size: 16px; color: #666666;">
                                {{$text}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="30"></td>
            </tr>
            </tr>
            <tr>
                <td align="center">
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td align="center" bgcolor="#74bb8e" style="border-radius: 25px;">
                                <a href="{{route('messages.index')}}" class="button" style="display: inline-block; padding: 15px 30px; font-size: 18px; color: #ffffff; text-decoration: none; background-color: #74bb8e; border-radius: 25px;">Reply</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
    </table>
</body>
</html>
