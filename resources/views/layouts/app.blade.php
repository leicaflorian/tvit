<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="viewport" content="viewport-fit=cover" />

  <title>Document</title>

  @vite(['resources/js/app.js'])
</head>
<body>

<main class="py-3">
  @yield('page_content')
</main>

</body>
</html>
