<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Rubik:wght@400;500;700&display=swap');

        * {
            padding: 0;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        @page {
            margin: 0px;
            size: A4 landscape;
        }

        body {
            margin: 0px;
        }

        .canvas {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .certificate {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        #username {
            position: absolute;
            top: 315px;
            left: 200px;
            font-size: 26pt;
            font-weight: bold;
            color: #185951;
            letter-spacing: 1px;
        }

        #class_completed {
            position: absolute;
            top: 440px;
            left: 200px;
            font-size: 22pt;
            font-weight: 600;
            color: #185951;
            letter-spacing: 0.5px;
        }

        #valid_until {
            position: absolute;
            top: 485px;
            left: 200px;
            font-size: 13pt;
            color: #718096;
            text-align: left;
            letter-spacing: 0.4px;
        }

        .signature_image {
            width: 130px;
            position: absolute;
            top: 575px;
            left: 200px;
        }

        #signature {
            position: absolute;
            top: 650px;
            left: 200px;
            font-size: 13pt;
            font-weight: bold;
            color: #185951;
        }

        .qr_image {
            width: 105px;
            height: 105px;
            position: absolute;
            top: 545px;
            right: 85px;
        }

        #no_cert {
            position: absolute;
            bottom: 35px;
            right: 55px;
            font-size: 8pt;
            color: #718096;
            letter-spacing: .5px;
        }
    </style>
</head>

<body>
    <div class="canvas">
        @php
            $bgTemplate = file_exists(resource_path('images/certificate/certificate-template.png'))
                ? resource_path('images/certificate/certificate-template.png')
                : public_path('image/certificate-template.png');
            $bgBase64 = file_exists($bgTemplate) ? base64_encode(file_get_contents($bgTemplate)) : null;

            $sigPath = file_exists(resource_path('images/certificate/signature.png'))
                ? resource_path('images/certificate/signature.png')
                : public_path('image/signature.png');
            $sigBase64 = file_exists($sigPath) ? base64_encode(file_get_contents($sigPath)) : null;
        @endphp

        @if(!empty($bgBase64))
            <img src="data:image/png;base64,{{ $bgBase64 }}" class="certificate">
        @elseif(!empty($template))
            <img src="{{ config('app-settings.media.base_url') . config('app-settings.certificate.upload_path') . $template }}" class="certificate">
        @endif

        <div id="certificate_data">
            <div id="username">{{ $name }}</div>
            <div id="class_completed">{{ $class }}</div>
            <div id="valid_until">Valid until: {{ $valid_until }}</div>
            @if(!empty($sigBase64))
                <img src="data:image/png;base64,{{ $sigBase64 }}" class="signature_image">
            @endif
            <div id="signature">Dini Hidayatul Qudsi</div>
            @if(!empty($img))
                <img class="qr_image" src="data:image/png;base64, {!! $img !!}" alt="qrcode">
            @endif
            <div id="no_cert">{{ $no_cert }}</div>
        </div>
    </div>
</body>

</html>
