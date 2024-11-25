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
        <form action="/check-spam" method="POST" class="w-6/12 lg:max-w-d lg:mx-auto">
            @csrf

            <div class="flex gap-2">
                <div class="border-b border-gray-900/10 pb-12">
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Spam checker</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">This tool checks if the message is a spam</p>

                    <div class="mt-19 grid grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                        <div class="col-span-full">
                            <!-- <label for="body" class="block text-sm font-medium leading-6 text-gray-900">Body</label> -->
                            <div class="mt-2">
                                <textarea id="body" name="body" rows="3" class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-500" rows="20" cols="60"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                <button type="submit" class="rounded-mg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Check</button>
            </div>

            @if ($errors->any())
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm text-red-500">{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </form>
    </body>
</html>
