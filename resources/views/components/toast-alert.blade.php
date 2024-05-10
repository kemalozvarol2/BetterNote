<div id="toast" x-data="{ open: true }" x-show="open" @click="open = false" class="absolute top-8 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded-md shadow-md hover:bg-green-700 transition cursor-pointer">
    {{ session('success') }}
</div>
