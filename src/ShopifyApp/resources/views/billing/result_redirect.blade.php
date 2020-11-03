<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <base target="_top">

    <title>Redirecting...</title>

    <script type="text/javascript">
      var shopDomain = '{!! $shopDomain !!}';
      var apiKey = '{!! $apiKey !!}';
      window.top.location.href = 'https://' + shopDomain + '/admin/apps/' + apiKey;
    </script>
</head>
<body>
</body>
</html>