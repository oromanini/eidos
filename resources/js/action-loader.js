const DEFAULT_MESSAGE = "Carregando...";

export function loadingMessageFor(element) {
    const customMessage = element?.closest?.("[data-loading-text]")?.dataset?.loadingText?.trim();

    return customMessage || DEFAULT_MESSAGE;
}

export function createRequestTracker(show, hide) {
    let pendingRequests = 0;

    return {
        start(message) {
            pendingRequests += 1;
            show(message);
        },
        finish() {
            pendingRequests = Math.max(0, pendingRequests - 1);

            if (pendingRequests === 0) {
                hide();
            }
        },
        reset() {
            pendingRequests = 0;
            hide();
        },
        pending() {
            return pendingRequests;
        },
    };
}

export function initActionLoader(documentObject = document, windowObject = window) {
    const loader = documentObject.getElementById("action-loader");
    const message = documentObject.getElementById("action-loader-message");

    if (!loader || !message) {
        return;
    }

    let nextMessage = DEFAULT_MESSAGE;
    let nativeNavigationTimer = null;

    const show = (text = DEFAULT_MESSAGE) => {
        message.textContent = text;
        loader.classList.remove("hidden");
        loader.classList.add("flex");
        loader.setAttribute("aria-hidden", "false");
        documentObject.body.setAttribute("aria-busy", "true");
    };

    const hide = () => {
        loader.classList.add("hidden");
        loader.classList.remove("flex");
        loader.setAttribute("aria-hidden", "true");
        documentObject.body.removeAttribute("aria-busy");
    };

    const clearNavigationTimer = () => {
        if (nativeNavigationTimer !== null) {
            windowObject.clearTimeout(nativeNavigationTimer);
            nativeNavigationTimer = null;
        }
    };

    const scheduleNativeNavigation = (text) => {
        clearNavigationTimer();
        nativeNavigationTimer = windowObject.setTimeout(() => show(text), 75);
    };

    const tracker = createRequestTracker(show, hide);

    documentObject.addEventListener("click", (event) => {
        const trigger = event.target.closest?.("a, button, [data-loading-text]");

        if (!trigger) {
            return;
        }

        nextMessage = loadingMessageFor(event.target);

        const link = event.target.closest?.("a[href]");
        const opensCurrentPage = link
            && !link.hasAttribute("download")
            && (!link.target || link.target === "_self")
            && !link.getAttribute("href").startsWith("#")
            && !event.ctrlKey
            && !event.metaKey
            && !event.shiftKey
            && !event.altKey;

        if (opensCurrentPage) {
            scheduleNativeNavigation(nextMessage);
        }
    }, true);

    documentObject.addEventListener("submit", (event) => {
        nextMessage = loadingMessageFor(event.submitter || event.target);
        scheduleNativeNavigation(nextMessage);
    }, true);

    documentObject.addEventListener("splade:internal:request", () => {
        clearNavigationTimer();
        tracker.start(nextMessage);
    });
    documentObject.addEventListener("splade:internal:request-response", () => tracker.finish());
    documentObject.addEventListener("splade:internal:request-error", () => tracker.finish());

    windowObject.addEventListener("pageshow", () => {
        clearNavigationTimer();
        tracker.reset();
    });
}
