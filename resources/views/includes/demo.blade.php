@if(config('default.app_demo'))
<div id="demo-banner" tabindex="-1" class="hidden fixed z-50 flex flex-col md:flex-row justify-between w-[calc(100%-2rem)] p-4 -translate-x-1/2 bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm lg:max-w-7xl left-1/2 bottom-4 dark:bg-yellow-100 dark:border-yellow-300">
    <div class="flex flex-col items-start mr-6 md:items-center md:flex-row md:mb-0">
        <p class="flex items-center text-sm font-normal text-gray-500 dark:text-gray-600">
            <span><x-ui.icon icon="exclamation-triangle" class="w-5 h-5 mr-2" /></span> {{ trans('common.demo_mode') }}</p>
    </div>
    <div class="flex items-center flex-shrink-0">
        <button data-dismiss-target="#demo-banner" onclick="setCookie('demo', 'true', 7);" type="button" class="absolute top-2.5 right-2.5 md:relative md:top-auto md:right-auto flex-shrink-0 inline-flex justify-center items-center text-gray-400 hover:bg-yellow-200 hover:text-gray-900 rounded-lg text-sm p-1.5 dark:hover:bg-yellow-600 dark:hover:text-white">
            <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            <span class="sr-only">Close banner</span>
        </button>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Check if the 'demo' cookie is not set
    if(!window.checkCookie('demo')) {
        // If the cookie is not set, remove the 'hidden' class from the 'demo-banner'
        document.getElementById('demo-banner').classList.remove('hidden');
    }
});
</script>
@endif