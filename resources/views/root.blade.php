<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @spladeHead
    @vite('resources/js/app.js')

    <style>
        /* Animação de rotação para o spinner */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Estilos Críticos para o Loader (aplicados instantaneamente) */
        #page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9fafb; /* Cor bg-gray-50 */
        }

        /* Estilos Críticos para o ÍCONE do loader */
        #page-loader > svg {
            animation: spin 1s linear infinite;
            width: 2.5rem; /* 40px - w-10 */
            height: 2.5rem; /* 40px - h-10 */
            color: #2563eb; /* Cor text-blue-600 */
        }

        /* Estilos para esconder o conteúdo principal */
        #main-content {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
    </style>
    <noscript>
        <style>
            #main-content { visibility: visible; opacity: 1; }
            #page-loader { display: none; }
        </style>
    </noscript>
</head>
<body class="font-sans antialiased">
<x-loader />

<div id="main-content">
    @splade
</div>

<script>
    window.addEventListener('load', function () {
        const loader = document.getElementById('page-loader');
        const mainContent = document.getElementById('main-content');

        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }

        if (mainContent) {
            mainContent.style.visibility = 'visible';
            mainContent.style.opacity = '1';
        }
    });
</script>
</body>
</html>
