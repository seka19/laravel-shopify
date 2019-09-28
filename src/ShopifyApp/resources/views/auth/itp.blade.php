<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Redirecting...</title>
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <style>
            * {
                -moz-box-sizing: border-box;
                -webkit-box-sizing: border-box;
                box-sizing: border-box;
            }

            body {
                padding: 2.5em 0;
                color: #212b37;
                font-family: -apple-system,BlinkMacSystemFont,San Francisco,Roboto,Segoe UI,Helvetica Neue,sans-serif;
            }

            .container {
                width: 100%;
                text-align: center;
                margin-left: auto;
                margin-right: auto;
            }

            @media screen and (min-width: 510px) {
                .container {
                    width: 510px;
                }
            }

            .title {
                font-size: 1.5em;
                margin: 2em auto;
                display: flex;
                align-items: center;
                justify-content: center;
                word-break: break-all;
            }

            .subtitle {
                font-size: 0.8em;
                font-weight: 500;
                color: #64737f;
                line-height: 2em;
            }

            .marketing-button {
                display: inline-block;
                width: 100%;
                padding: 1.0625em 1.875em;
                background-color: #5e6ebf;
                color: #fff;
                font-weight: 700;
                font-size: 1em;
                text-align: center;
                outline: none;
                border: 0 solid transparent;
                border-radius: 5px;
                cursor: pointer;
            }

            .marketing-button:hover {
                background: linear-gradient(to bottom, #5c6ac4, #4959bd);
                border-color: #3f4eae;
            }

            .marketing-button:focus {
                box-shadow: 0 0 0.1875em 0.1875em rgba(94,110,191,0.5);
                background-color: #223274;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <main class="container" role="main">
            <h3 class="title">
                Your browser needs to authenticate
            </h3>

            <p class="subhead">
                Your browser requires Shopify apps to ask you for cookie access before the app can open.<br>
                Would you like to continue?
            </p>

            <a href="{{ route('session.itp', ['accept' => true ]) }}" class="marketing-button">Continue</a>
        </main>
    </body>
</html>