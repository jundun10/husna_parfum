<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Husna Parfum' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #555;
            background: #ffffff;
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }

.auth-logo {
    width: 180px;
    height: 150px;

    margin: 0 auto 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    position: relative;
}

.auth-logo::before {
    content: "";

    position: absolute;

    width: 125px;
    height: 115px;

    top: 8px;

    border: 1.5px solid #c8d8c1;

    border-bottom: none;

    border-radius: 70px 70px 0 0;

    opacity: 0.8;
}

.auth-logo::after {
    content: "✦";

    position: absolute;

    bottom: 0;

    left: 50%;

    transform: translateX(-50%);

    color: #a5bd99;

    font-size: 14px;

    background: white;

    padding: 0 10px;
}

.auth-logo img {
    position: relative;
    z-index: 2;

    width: 115px;
    height: 115px;

    object-fit: contain;
}
        /* =========================
           CONTENT
        ========================= */

        .auth-content {
            flex: 1;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 55px 20px;
        }

        .auth-box {
            width: 100%;
            max-width: 360px;
        }

        .auth-title {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-title h1 {
            margin: 0;
            font-family: Georgia, serif;
            font-size: 23px;
            font-weight: normal;
            color: #555;
        }

        .auth-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 17px;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;

            font-size: 10px;
            color: #777;
        }

        .form-input {
            width: 100%;
            height: 38px;

            padding: 0 12px;

            border: none;
            border-radius: 5px;

            background: #eeeeee;

            color: #555;
            font-size: 10px;

            outline: none;
        }

        .form-input:focus {
            background: #e9eeee;
            box-shadow: 0 0 0 2px rgba(126, 189, 108, .25);
        }

        .error {
            margin-top: 5px;
            color: #dc6b6b;
            font-size: 9px;
        }

        /* =========================
           FORGOT PASSWORD
        ========================= */

        .forgot {
            text-align: right;
            margin-top: -7px;
            margin-bottom: 20px;
        }

        .forgot a {
            color: #777;
            font-size: 9px;
            text-decoration: none;
        }

        .forgot a:hover {
            text-decoration: underline;
        }

        /* =========================
           BUTTON
        ========================= */

        .btn-submit {
            display: block;

            margin: 20px auto 0;

            border: none;
            border-radius: 5px;

            background: #7fbd6c;
            color: white;

            padding: 9px 28px;

            font-size: 10px;
            cursor: pointer;

            transition: .2s;
        }

        .btn-submit:hover {
            background: #6daa5c;
        }

        /* =========================
           SWITCH LOGIN / REGISTER
        ========================= */

        .auth-switch {
            text-align: center;
            margin-top: 12px;

            font-size: 9px;
            color: #777;
        }

        .auth-switch a {
            color: #6daa5c;
            text-decoration: none;
            margin-left: 4px;
        }

        .auth-switch a:hover {
            text-decoration: underline;
        }

        /* =========================
           SOCIAL LOGIN
        ========================= */

        .social-section {
            margin-top: 30px;
            text-align: center;
        }

        .social-title {
            font-size: 9px;
            color: #777;
            margin-bottom: 12px;
        }

        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .social-btn {
            border: 1px solid #dddddd;
            background: white;

            border-radius: 4px;

            padding: 6px 13px;

            font-size: 8px;
            color: #666;

            cursor: pointer;
        }

        .social-btn:hover {
            background: #f8f8f8;
        }

    </style>
</head>

<body>

<div class="auth-page">
    <main class="auth-content">

        <div class="auth-box">
<div class="auth-logo">
    <img
        src="{{ asset('images/logo.jpg') }}"
        alt="Lamore Perfumes"
    >
</div>

            {{ $slot }}

        </div>

    </main>

</div>

</body>
</html>