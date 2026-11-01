<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Rubik:wght@400;500;700&display=swap');


        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: normal;
            src: url(https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Rubik:wght@400;500;700&display=swap) format('truetype');
        }

        * {
            padding: 0;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .canvas {
            width: 100%;
            height: 100%;
        }

        .certificate {
            position: absolute;
            background-color: #fff;
            width: 100%;
        }

        #certificate_data {
            position: relative;
        }

        #username {
            position: absolute;
            top: 300px;
            left: 211px;
            font-size: 28pt;
            color: #185951;
            letter-spacing: 3px;
        }

        #class_completed {
            position: absolute;
            top: 433px;
            left: 211px;
            font-size: 25pt;
            color: #185951;
            letter-spacing: 1px;
        }

        #valid_until {
            position: absolute;
            top: 553px;
            left: 211px;
            font-size: 18pt;
            color: #185951;
            text-align: left;
            letter-spacing: 0.6px;
        }

        #signature {
            position: absolute;
            font-weight: bold;
            top: 690px;
            left: 215px;
            font-size: 16pt;
            color: #185951;
        }

        .signature_image {
            width: 15%;
            position: absolute;
            top: 600px;
            left: 215px;
        }

        #no_cert {
            position: absolute;
            bottom: 38px;
            right: 53px;
            font-size: 10pt;
            color: #185951;
            letter-spacing: .5;
        }

        .qr_image {
            width: 12%;
            position: absolute;
            top: 565px;
            right: 60px;
        }
    </style>
</head>

<body>
    <div class="canvas">
        <img src="{{ config('app-settings.media.base_url') . config('app-settings.certificate.upload_path') . $template }}" class="certificate">
        <div id="certificate_data">
            <div id="username">{{ $name }}</div>
            <div id="class_completed">{{ $class }}</div>
            <div id="valid_until">{{ $valid_until }}</div>
            <div id="no_cert">{{ config('app-settings.frontend.base_url') }}certificate/{{ $no_cert }}</div>
            <div id="signature">Dini Hidayatul Qudsi</div>
            <img src="image/signature.png" class="signature_image">
            <img class="qr_image" src="data:image/png;base64, {!! $img !!}" alt="qrcode">
        </div>
    </div>
</body>

</html>