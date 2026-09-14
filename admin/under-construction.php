<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Under Construction</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            color: #222;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .construction-container {
            width: 90%;
            max-width: 650px;
            padding: 50px 30px;
        }

        .icon {
            font-size: 80px;
            margin-bottom: 25px;
            animation: bounce 2s infinite;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 15px;
        }

        p {
            font-size: 18px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .loader {
            width: 100%;
            max-width: 350px;
            height: 8px;
            background: #ddd;
            border-radius: 20px;
            overflow: hidden;
            margin: 0 auto 30px;
        }

        .loader span {
            display: block;
            width: 50%;
            height: 100%;
            background: #f5b400;
            border-radius: 20px;
            animation: loading 2s infinite ease-in-out;
        }

        .back-btn {
            display: inline-block;
            padding: 12px 25px;
            background: #222;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #f5b400;
            color: #222;
        }

        @keyframes loading {
            0% {
                transform: translateX(-100%);
            }

            50% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(200%);
            }
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        @media (max-width: 576px) {
            .icon {
                font-size: 60px;
            }

            h1 {
                font-size: 34px;
            }

            p {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="construction-container">

        <div class="icon">🚧</div>

        <h1>Under Construction</h1>

        <p>
            Our website is currently under construction.
            We are working hard to bring you something amazing.
            Please check back soon!
        </p>

        <div class="loader">
            <span></span>
        </div>

        <a href="dashboard.php" class="back-btn">
            Back 
        </a>

    </div>

</body>
</html>