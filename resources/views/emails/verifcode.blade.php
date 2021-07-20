<!DOCTYPE html>
<html>

<head>

    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Harmelo Music</title>
    <style>
        .information-line .information-line-text, .information-line .information-line-title {
            font-weight: 400;
            line-height: 1.4285714286em;
        }
        .information-line .information-line-title {
            -ms-flex-negative: 0;
            flex-shrink: 0;
            width: 120px;
            color: #363535 !important;
        }
        .information-line .information-line-text, .information-line .information-line-title {
            font-weight: 400;
            line-height: 1.4285714286em;
        }
        .information-line {
            display: -ms-flexbox;
            display: flex;
        }
        .details-text {
            text-align: center;
            font-weight: 900;
            font-size: 1.1rem;
            text-transform: uppercase;
            margin-top: 0 !important;
        }
        .verify-btn {
            font-size: 14px;
            border-radius: 3px;
            color: #fff !important;
            padding: 14px 16px;
            border: #187eef;
            background-color: rgb(24, 126, 239);
            vertical-align: top;
            text-decoration: none !important;
        }
    </style>
</head>

<body
    style="font-family: Lato,sans-serif; box-sizing: border-box; font-size: 14px; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6 !important; margin: 0;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0"
        style="padding: 10px; padding-top:0px !important ;font-size:14px;line-height:30px;color:#000;font-family:Roboto, Helvetica, sans-serif; background-color:#f9f9f9 !important">
        <tbody>
            <tr>
                <td>
                    <div
                        style="background: #fff; border-radius: 0px;max-width: 500px;margin: 0 auto; padding: 0px 30px 0px 30px;margin-top: 20px">
                        <a style="float: right;text-decoration: none;" href="#">Need Help?</a>
                    </div>
                </td>

            </tr>
            <tr>
                <td>
                    <div
                        style="background: #f4ebf5; border-radius: 0px;max-width: 500px;margin: 0 auto; padding: 0px 30px 0px 30px;">
                        <center style="padding-top: 40px;padding-bottom: 40px;">
                            <img src="https://s3-ap-southeast-1.amazonaws.com/miko.staging.harmelo.com/public/defaults/harmelo-logo-v2.png"
                                style="margin-top: 10px; margin-bottom: 0px;width: 200px">
                        </center>
                    </div>

                    <div style="background: white !important;max-width: 500px; margin:0 auto; padding: 30px;">
                        <p style="color: #000 !important">Hi {{ $receiver }}, </p>

                        <p style="color: #000 !important; margin-top: 0">
                            Use the verification code below to proceed with your request.
                        </p>
                        <p style="color: #fff !important; background: beige; padding: 15px; border-radius: 7px">
                            {{ $verifCode }}
                        </p>
                        <p style="color: #000 !important; margin-top: 3rem">
                            If you have any questions, problems or comments please drop us an email at support@harmelo.com and we'll get back to you as quickly as possible.
                        </p>
                        <br>
                        <p style="color: #000 !important; margin-bottom: 0 !important;">
                            Best wishes
                        </p>
                        <p style="font-size: 1.4em;
                            margin-bottom: 10px;
                            font-weight: bold;
                            margin-top: 0"
                        >
                            Harmelo Team
                        </p>
                    </div>

                    <div
                        style="background: #f2f2f2; border-radius: 0px;max-width: 500px;margin: 0 auto; padding: 0px 30px 0px 30px">
                        <br>
                        <center>
                            <p style="color: #85388b">*** THIS IS A SYSTEM GENERATED MESSAGE. DO NOT REPLY TO THIS EMAIL.***
                            </p>
                            <font size="1" face="verdana, helvetica, sans-serif" color="#808080">
                                <i>
                                    If you received this email by mistake, you can safely ignore it.
                                    If you encounter any problems, please contact us: support@harmelo.com<br>
                                </i>
                            </font>
                        </center>
                        <br>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div
                        style="background: #fff; border-radius: 0px;max-width: 500px;margin: 0 auto; padding: 0px 30px 0px 30px; margin-top:20px">

                    </div>
                    <center>
                        <span style="margin-right: 10px;font-size: 12px; "><a href="#" style="text-decoration: none;">
                                Powered by: Harmelo</a></span> |
                        <span style="margin-left: 10px;font-size: 12px"><a href="#" style="text-decoration: none;">
                                Copyright © Harmelo 2021</a></span>
                    </center>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
