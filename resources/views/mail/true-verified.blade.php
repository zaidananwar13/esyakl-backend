<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify your account</title>
    <link rel="icon" type="image/png" href="logo2.svg">
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Inter&display=swap');

        * {
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            height: 100vh;
            width: 100vw;
            justify-content: center;
        }

        .w-60 {
            display: flex;
            flex-direction: column;
            width: 60%;
            justify-content: center;
            grid-gap: 20px;
            align-items: center;
        }

        .card-verifier {
            display: flex;
            align-items: center;
            width: 100%;
            background: #185951;
            height: 100px;
            border-radius: 8px;
            padding: 10px;
        }

        .image-verifier {
            width: 40%;
            margin-top: 7px;
        }

        .title {
            color: #172426;
            font-family: 'Inter';
            font-style: normal;
            font-weight: 700;
            font-size: 32px;
            line-height: 64px;
        }

        .verifier-text {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            line-height: 30px;
        }

        .verifier-content {
            display: flex;
            flex-direction: column;
            grid-gap: 5px;
            align-items: center;
        }

        .paragraph-verifier {
            display: flex;
            flex-direction: column;
            grid-gap: 20px;
            align-items: center;
        }

        .verifier-text-title {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 600;
            font-size: 16px;
            line-height: 20px;
        }

        .button-verifier {
            margin-top: 10px;
            padding: 12px 20px;
            background: #185951;
            border-radius: 8px;
            border: none;
            font-family: 'Inter';
            font-style: normal;
            font-weight: 600;
            font-size: 16px;
            line-height: 24px;
            display: flex;
            align-items: center;
            text-align: center;
            color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: 0.5s;
            cursor: pointer;
        }

        .button-verifier:hover {
            background: #144A44;
        }

        .tick {
            width: 150px;
            height: 150px;
        }

        .no-decoration {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="w-60">
            <img src="<?= url('/assets/media/verify') ?>/tick.jpg" class="tick">
            <div class="verifier-content">
                <div class="title">Your account has been verified!</div>
                <div class="paragraph-verifier">
                    <p class="verifier-text">
                        Please click the button below to go back to <b>Arabic-Go.com</b>
                    </p>
                    <a href="https://arabic-go.com/sign-in" class="no-decoration">
                        <button class="button-verifier">Go to Arabic-Go</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
