<nav class="bg-white fixed w-full z-20 top-0 start-0 border-b border-gray-200 ">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
            {{-- <img src="https://flowbite.com/docs/images/logo.svg" class="h-8" alt="Flowbite Logo"> --}}
            <p class="text-2xl self-center">
                <span>FixIt<span class="font-bold">Hub</span></span>
            </p>
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <div class="hidden md:block">
                @if(session('user'))
                <div class="flex gap-2 items-center">
                    <p>{{ session('user')['name'] ?? session('user')['email'] }}</p>
                    <button type="button"
                        class="text-blue-700 hover:text-white hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center"
                        onclick="onSignOut()">
                        Sign Out
                    </button>
                </div>
                @else
                <div>
                    <a href="/signin">
                        <button type="button"
                            class="text-blue-700 bg-white  focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Sign
                            In</button>
                    </a>
                    <a href="/signup">
                        <button type="button"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Sign
                            Up</button>
                    </a>
                </div>
                @endif
            </div>
            <button data-collapse-toggle="navbar-sticky" type="button"
                class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 "
                aria-controls="navbar-sticky" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>
        </div>
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-sticky">
            <ul
                class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white ">
                <li>
                    <a href="/"
                        class="block py-2 px-3 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 ">Home</a>
                </li>
                <li>
                    <a href="/reports"
                        class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 ">Society Reports</a>
                </li>
                <li>
                    <a href="/about"
                        class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 ">About</a>
                </li>
                <li>
                    <a href="#"
                        class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 ">Contact</a>
                </li>
                <li>
                    <div class="block py-2 px-3 md:hidden">
                        @if(session('user'))
                        <div class="flex gap-2 items-center">
                            <p>{{ session('user')['name'] ?? session('user')['email'] }}</p>
                            <button type="button"
                                class="text-blue-700 hover:text-white hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center"
                                onclick="onSignOut()">
                                Sign Out
                            </button>
                        </div>
                        @else
                        <div>
                            <a href="/signin">
                                <button type="button"
                                    class="text-blue-700 bg-white  focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Sign
                                    In</button>
                            </a>
                            <a href="/signup">
                                <button type="button"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Sign
                                    Up</button>
                            </a>
                        </div>
                        @endif
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    function onSignOut() {
        fetch("{{ route('logout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}" // Tambahkan CSRF token
                },
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = "{{ route('signin') }}"; // Redirect ke halaman login
                } else {
                    console.error("Logout gagal");
                }
            })
            .catch(error => console.error("Error:", error));
    };

    document.addEventListener("DOMContentLoaded", () => {
        const toggleButton = document.querySelector('[data-collapse-toggle="navbar-sticky"]');
        const navbar = document.getElementById("navbar-sticky");

        toggleButton.addEventListener("click", () => {
            navbar.classList.toggle("hidden");
        });
    });
</script>