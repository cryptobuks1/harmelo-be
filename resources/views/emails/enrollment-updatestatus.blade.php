<!DOCTYPE html>
<html>

<head>

    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Harmelo Music</title>
    <style>
        .information-line .information-line-text, .information-line .information-line-title {;
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

                        <p style="color: #000 !important">Hi {{ $receiver }},</p>

                        @if ($booking_status == 1)
                            <p style="color: #000 !important">
                                We are pleased to inform you that your application have been approved!
                            </p>
                            <p style="color: #000 !important">Here are the details for your upcoming session:</p>
                        @endif

                        @if ($booking_status == 2)
                        <p style="color: #000 !important">
                            We are sorry to inform you that your application have been declined by the instructor.
                        </p>
                        @endif

                        @if ($booking_status == 3)
                        <p style="color: #000 !important">
                            We would like to inform you that the enrollee has cancelled this specific timeslot reservation.
                        </p>
                        @endif

                        @if ($booking_status == 4)
                        <p style="color: #000 !important">
                            We would like to inform you that your previously approved timeslot reservation have been revoked by the instructor.
                        </p>
                        @endif

                        <div class="details-area"
                                style="padding: 20px;
                                background: #e8e8e8;
                                border-radius: 7px;"
                            >
                                <p class="details-text" style="color: #000">Details </p>
                                <div class="information-line"
                                >
                                    <p class="information-line-title" style="color: #000">
                                        @if ($booking_status == 3)
                                            Enrollee
                                        @else
                                            Instructor
                                        @endif
                                    </p>
                                    <p class="information-line-text text-white" style="color: #000">
                                        {{ $instructor_name }}
                                    </p>
                                </div>

                                <div class="information-line"
                                >
                                    <p class="information-line-title" style="color: #000">Date</p>
                                    <p class="information-line-text text-white" style="color: #000"> {{ $set_date }} </p>
                                </div>

                                <div class="information-line"
                                >
                                    <p class="information-line-title" style="color: #000">Time</p>
                                    <p class="information-line-text text-white" style="color: #000">
                                        {{ $set_time }}
                                    </p>
                                </div>

                                <div class="information-line"
                                >
                                    <p class="information-line-title" style="color: #000">Instrument</p>
                                    <p class="information-line-text text-white" style="color: #000"> {{ $instrument_name }} </p>
                                </div>

                                <p><strong style="color: #000 !important">Here's a message from the Instructor, {{ $instructor_name }} :</strong></p>
                                <p style="font-size: .85rem;
                                    font-style: oblique;
                                    color: #5a5a5a;">{{ $action_message }}
                                </p>
                        </div>


                        <p style="font-size: 1.4em;
                            margin-bottom: 10px;
                            font-weight: bold;"
                        >
                            Good luck!
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
