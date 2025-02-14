<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/f01941449c.js" crossorigin="anonymous"></script>
    <title>Courses</title>
    <style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-out forwards;
    }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Main Content -->
    <div class="flex flex-col flex-1 overflow-y-auto">
        <!-- Top Navigation -->
        <div class="bg-white border-gray-200 dark:bg-gray-900">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <!-- --------------- RightNav ----------------------------- -->
                <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                    <div class="flex items-center gap-4">
                        {% if session.role is defined %}
                            {% if session.role == 'Admin' or session.role == 'Student' or session.role == 'Teacher' %}
                                <button class="p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <i class="fas fa-bell"></i>
                                </button>

                                <button type="button"
                                    class="flex text-sm bg-gray-800 rounded-full md:me-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                                    id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                                    data-dropdown-placement="bottom">
                                    <span class="sr-only">Open user menu</span>
                                    <img class="w-8 h-8 rounded-full" src="https://tailwindcss.com/img/jonathan.jpg"
                                        alt="user photo">
                                </button>
                            {% endif %}
                        {% else %}
                            <div class="sm:flex sm:gap-4">
                                <a class="rounded-md bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 text-sm font-medium text-white shadow inline-block"
                                    href="{{ path('login') }}">
                                    Login
                                </a>
                                <div class="hidden sm:flex">
                                    <a class="rounded-md bg-gray-100 px-5 py-2.5 text-sm font-medium text-gray-500 hover:bg-indigo-700 hover:text-white"
                                        href="{{ path('signup') }}">
                                        Register
                                    </a>
                                </div>
                            </div>
                        {% endif %}
                    </div>
                    <!-- Dropdown menu -->
                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600"
                        id="user-dropdown">
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white inline-block">{{ session.username }}</span>
                            <span
                                class="block text-sm  text-gray-500 truncate dark:text-gray-400 inline-block">name@flowbite.com</span>
                        </div>
                        <ul class="py-2" aria-labelledby="user-menu-button">
                            {% if session.role is defined %}
                                {% if session.role == 'Admin' %}
                                    <li>
                                        <a href="{{ path('admin_dashboard') }}"
                                            class="block px-4 py-2 text-sm inline-block text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Dashboard</a>
                                    </li>
                                {% elseif session.role == 'Teacher' %}
                                    <li>
                                        <a href="{{ path('teacher_dashboard') }}"
                                            class="block px-4 py-2 text-sm inline-block text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Dashboard</a>
                                    </li>
                                {% endif %}
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm inline-block text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Sign
                                        out</a>
                                </li>
                            {% endif %}
                        </ul>
                    </div>
                    <button data-collapse-toggle="navbar-user" type="button"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        aria-controls="navbar-user" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 17 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1h15M1 7h15M1 13h15" />
                        </svg>
                    </button>
                </div>
                <!-- --------------- EndRightNav ----------------------------- -->

                <!-- --------------- middelNav ----------------------------- -->
                <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
                    <form
                        class="mx-auto max-w-xl py-2 px-6 rounded-full bg-gray-50 border flex focus-within:border-gray-300">
                        <input type="text" placeholder="Search anything"
                            class="bg-transparent w-full focus:outline-none pr-4 font-semibold border-0 focus:ring-0 px-0 py-0"
                            name="topic"><button
                            class="flex flex-row items-center justify-center min-w-[130px] px-4 rounded-full font-medium tracking-wide border disabled:cursor-not-allowed disabled:opacity-50 transition ease-in-out duration-150 text-base bg-black text-white font-medium tracking-wide border-transparent py-1.5 h-[38px] -mr-3">
                            Search
                        </button>
                    </form>
                </div>
                <!-- --------------- EndmiddelNav ----------------------------- -->
            </div>
        </div>
    </div>
    <main class="flex flex-col justify-center mt-10 items-center">
        <div
            class="bg-white flex-col justify-center items-center dark:bg-gray-800 rounded-xl shadow-2xl max-w-4xl w-full p-8 transition-all duration-300 animate-fade-in">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 text-center mb-8 md:mb-0">
                    <img src="../../asset/uploads/users/{{ Teacher.profile_picture }}" alt="Profile Picture"
                        class="rounded-full w-48 h-48 mx-auto mb-4 border-4 border-indigo-800 dark:border-blue-900 transition-transform duration-300 hover:scale-105">
                    <h1 class="text-2xl font-bold text-indigo-800 dark:text-white mb-2">
                        {{ Teacher.username }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">{{ Teacher.role }}</p>
                    <a href="update.php"><button
                            class="mt-4 bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-blue-900 transition-colors duration-300">Edit
                            Profile</button></a>
                </div>
                <div class="md:w-2/3 md:pl-8">
                    <h2 class="text-xl font-semibold text-indigo-800 dark:text-white mb-4">About Me</h2>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">{{ Teacher.bio }}
                        Passionate software developer with 5 years of experience in web technologies.
                        I love creating user-friendly applications and solving complex problems.
                    </p>
                    <h2 class="text-xl font-semibold text-indigo-800 dark:text-white mb-4">Contact Information</h2>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2 text-indigo-800 dark:text-blue-900" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            {{ Teacher.email }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    <!-- --------------------------- footer --------------------------- -->
    <footer class="px-3 pt-4 mt-10 lg:px-9 border-t-2 bg-gray-50">
        <div class="grid gap-10 row-gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">

            <div class="sm:col-span-2">
                <a href="#" class="inline-flex items-center">
                    <!-- <img src="https://mcqmate.com/public/images/logos/60x60.png" alt="logo" class="h-8 w-8"> -->
                    <span class="ml-2 text-xl font-bold tracking-wide text-gray-800">YouDemy</span>
                </a>
                <div class="mt-6 lg:max-w-xl">
                    <p class="text-sm text-gray-800 inline-block">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi felis mi, faucibus dignissim
                        lorem
                        id, imperdiet interdum mauris. Vestibulum ultrices sed libero non porta. Vivamus malesuada urna
                        eu
                        nibh malesuada, non finibus massa laoreet. Nunc nisi velit, feugiat a semper quis, pulvinar id
                        libero. Vivamus mi diam, consectetur non orci ut, tincidunt pretium justo. In vehicula porta
                        molestie. Suspendisse potenti.
                    </p>
                </div>
            </div>

            <div class="flex flex-col gap-2 text-sm">
                <p class="text-base font-bold tracking-wide text-gray-900 inline-block">Popular Courses</p>
                <a href="#">UPSC - Union Public Service Commission</a>
                <a href="#">General Knowledge</a>
                <a href="#">MBA</a>
                <p class="text-base font-bold tracking-wide text-gray-900 inline-block">Popular Topics</p>
                <a href="#">Human Resource Management</a>
                <a href="#">Operations Management</a>
                <a href="#">Marketing Management</a>
            </div>

            <div>
                <p class="text-base font-bold tracking-wide text-gray-900 inline-block">COMPANY IS ALSO AVAILABLE ON</p>
                <div class="flex items-center gap-1 px-2">
                    <a href="#" class="w-full min-w-xl">
                        <img src="https://mcqmate.com/public/images/icons/playstore.svg" alt="Playstore Button"
                            class="h-10">
                    </a>
                    <a class="w-full min-w-xl" href="https://www.youtube.com/channel/UCo8tEi6SrGFP8XG9O0ljFgA">
                        <img src="https://mcqmate.com/public/images/icons/youtube.svg" alt="Youtube Button"
                            class="h-28">
                    </a>
                </div>
                <p class="text-base font-bold tracking-wide text-gray-900">Contacts</p>
                <div class="flex">
                    <p class="mr-1 text-gray-800">Email:</p>
                    <a href="#" title="send email">admin@company.com</a>
                </div>
            </div>

        </div>

        <div class="flex flex-col-reverse justify-between pt-5 pb-10 border-t lg:flex-row">
            <p class="text-sm text-gray-600">© Copyright 2023 Company. All rights reserved.</p>
            <ul class="flex flex-col mb-3 space-y-2 lg:mb-0 sm:space-y-0 sm:space-x-5 sm:flex-row">
                <li>
                    <a href="#"
                        class="text-sm text-gray-600 transition-colors duration-300 hover:text-deep-purple-accent-400">Privacy
                        &amp; Cookies Policy
                    </a>
                </li>
                <li>
                    <a href="#"
                        class="text-sm text-gray-600 transition-colors duration-300 hover:text-deep-purple-accent-400">Disclaimer
                    </a>
                </li>
            </ul>
        </div>

    </footer>
    <!-- JavaScript for Toggling Content Type Fields -->
    <script>
    document.getElementById('content_type').addEventListener('change', function() {
        const contentTypeVideoField = document.getElementById('contentTypeVideoField');
        const contentTypeDocumentField = document.getElementById('contentTypeDocumentField');

        if (this.value === 'video') {
            contentTypeVideoField.style.display = 'block';
            contentTypeDocumentField.style.display = 'none';
        } else if (this.value === 'document') {
            contentTypeVideoField.style.display = 'none';
            contentTypeDocumentField.style.display = 'block';
        } else {
            contentTypeVideoField.style.display = 'none';
            contentTypeDocumentField.style.display = 'none';
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

</body>

</html>