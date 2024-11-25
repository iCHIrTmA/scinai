<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SCINAI</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Tailwind css -->
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="h-full grid place-items-center p-6" style="place-items: center;">
        <form action="/summarize" method="POST" class="w-6/12 lg:max-w-d lg:mx-auto">
            @csrf

            <div class="flex gap-2">
                <input type="text" name="topic" placeholder="What case do you want to summarize?" required class="border p-2 rounded flex-1">

                <button type="submit" class="rounded p-2 bg-gray-200 hover:bg-blue-500 hover:text-white">Summarize</button>
            </div>
        </form>
    </body>
</html>
