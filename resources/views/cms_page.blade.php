<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $page->page_name ?? '' }}</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9fafb;
        color: #111827;
        line-height: 1.6;
      }

      main {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
        background-color: #ffffff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
      }

      h1 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #2563eb;
        margin-bottom: 1rem;
        text-align: center;
      }

      p {
        font-size: 1rem;
        margin-bottom: 1rem;
      }
    </style>
  </head>
  <body>
    <main>
      <h1>{{ $page->page_name ?? '' }}</h1>
      <p>{!! $page->description ?? '' !!}</p>
    </main>
  </body>
</html>
