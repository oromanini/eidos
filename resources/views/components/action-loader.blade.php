<div
    id="action-loader"
    class="fixed inset-0 z-[9998] hidden items-center justify-center bg-slate-950/25 px-4 backdrop-blur-[1px]"
    role="status"
    aria-live="polite"
    aria-hidden="true"
>
    <div class="flex min-w-52 flex-col items-center gap-3 rounded-2xl bg-white px-8 py-6 text-center shadow-2xl">
        <svg class="h-9 w-9 animate-spin text-blue-600 motion-reduce:animate-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p id="action-loader-message" class="font-semibold text-slate-800">Carregando...</p>
        <p class="text-xs text-slate-500">Aguarde um instante.</p>
    </div>
</div>
