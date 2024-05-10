<!DOCTYPE html>
<html class="{{ auth()->user()->theme }}" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-200  dark:bg-slate-900 dark:text-white overflow-y-hidden">

    @if(session('success'))
        <x-toast-alert></x-toast-alert>
    @endif

    <div class="grid grid-cols-12 gap-4 py-8 h-screen">
        <div class="col-span-1"></div>
        <div class="col-span-2 p-4 dark:bg-gray-800 bg-white rounded-lg flex flex-col shadow-lg">
            <div class="flex items-center justify-between">
                <h1 class="border-b pb-4 mb-4 dark:text-gray-300 dark:border-gray-400 pt-2">Notes</h1>
                <a href="{{ route('notes.index') }}" class="flex gap-2 items-center font-light px-4 self-start py-2 border border-indigo-500 rounded-md bg-transparent text-indigo-500 hover:bg-indigo-400 hover:text-white transition ease-in-out focus:outline-none">
                    <i class="fa fa-solid fa-plus"></i>  <span>New Note</span>
                </a>
            </div>
            <div>
                @forelse($notes as $note)
                    <div class="flex justify-between items-center gap-4">
                        <a href="{{ route('notes.show', $note) }}"
                           class="{{ isset($selected_note) && $note->id == $selected_note->id ? 'bg-gray-200' : '' }} block w-full mb-2 p-4 hover:bg-gray-300 dark:hover:bg-gray-700 rounded-md
                       dark:bg-gray-900 truncate">
                            {{ $note->title }}
                        </a>
                        <form action="{{ route('notes.destroy', $note) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-700 text-sm rounded-full transition hover:bg-red-800 text-white px-3 py-2" onclick="return confirm('Are you sure you want to delete this note?')">
                                <i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                @empty
                    <div class="p-4 dark:bg-gray-800 rounded-lg">No Notes</div>
                @endforelse
            </div>
            <div class="mt-auto flex flex-row-reverse justify-between items-center">
                <a href="#" title="Toggle theme" id="themeToggler" onclick="toggleTheme()" class="block hover:rotate-180 text-center dark:text-white bg-gray-100 hover:bg-gray-600 hover:text-white dark:bg-gray-700 dark:hover:bg-gray-200 dark:hover:text-gray-700 transition py-2 px-3 rounded-full">
                    @if(auth()->user()->theme == 'dark')
                        <i class="fa-solid fa-sun"></i>
                    @else
                        <i class="fa-solid fa-moon"></i>
                    @endif
                </a>

                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class=" hover:rotate-180 bg-red-800 text-white py-2 px-3 rounded-full transition hover:bg-white hover:text-red-800" title="Log out">
                        <i class="fa-solid fa-arrow-right-from-bracket rotate-180"></i>
                    </button>
                </form>
            </div>
        </div>
        <div class="col-span-4 p-4 dark:bg-gray-800 bg-white rounded-lg shadow-lg">

            @if(isset($selected_note))
                <form action="{{ route('notes.update', $selected_note) }}" method="post" class="flex flex-col gap-4 h-full justify-between">
                    @method('PUT')
            @else
                <form action="{{ route('notes.store') }}" method="post" class="flex flex-col gap-4 h-full justify-between">
            @endif
                @csrf

                <div class="flex justify-between">

                <h1 class="border-b pb-4 dark:text-gray-300
            dark:border-gray-400">
                    Edit Note
                </h1>

                <input type="text" name="title" placeholder="Note Title" class="dark:bg-transparent rounded-lg" {!! isset($selected_note) ? 'value="' . $selected_note->title . '"' : '' !!} required>
            </div>
            <label for="note" class="sr-only">Enter your note</label>

                <textarea id="note" placeholder="Enter your note" name="body"
                          class="w-full rounded-lg border
                          border-gray-300 p-3 focus:outline-none
                          dark:bg-gray-700 dark:border-gray-600 resize-y
                          h-full" required>{{ isset($selected_note) ? $selected_note->body : old('body') }}</textarea>

                <button type="submit" class="px-12 self-end py-2 rounded-md bg-indigo-500 text-white font-bold hover:bg-indigo-700 transition duration-300 ease-in-out focus:outline-none">
                    Save Note
                </button>
            </form>
        </div>
        <div class="col-span-4 p-4 dark:bg-gray-800 bg-white rounded-lg shadow-lg h-full overflow-y-scroll" >
            <h1 class="border-b pb-4 mb-4 dark:text-gray-300 dark:border-gray-400">Preview</h1>

            <div id="preview" class="prose dark:prose-invert"></div>

        </div>

    </div>

    <script>
        const noteTextarea = document.getElementById('note');
        const previewDiv = document.getElementById('preview');

        function updatePreview() {
            const markdownText = noteTextarea.value;
            const htmlContent = marked.parse(markdownText); // Correct call
            previewDiv.innerHTML = htmlContent;
        }

        // Initial update on page load
        updatePreview();

        // Update preview on every keypress
        noteTextarea.addEventListener('keyup', updatePreview);


        setTimeout(() => {
            const toastElement = document.getElementById('toast');
            if (toastElement) {
                toastElement.style.opacity = 0;

                toastElement.addEventListener('transitionend', () => {
                    toastElement.remove();
                });

                setTimeout(() => {
                    toastElement.remove();
                }, 1000);
            }
        }, 2000);

    </script>

    <script>
        const themeToggler = document.getElementById('themeToggler');

        function toggleTheme() {
            fetch('/toggle_theme')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // Parse the JSON response
                })
                .then(data => {
                    if(data.theme === "dark"){
                        document.documentElement.classList.remove('light');
                        document.documentElement.classList.add('dark');
                        themeToggler.innerHTML = '<i class="fa-solid fa-sun"></i>';
                    }else if(data.theme === "light"){
                        document.documentElement.classList.remove('dark');
                        document.documentElement.classList.add('light');
                        themeToggler.innerHTML = '<i class="fa-solid fa-moon"></i>';
                    }
                    console.log(data)
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    </script>
</body>
</html>
