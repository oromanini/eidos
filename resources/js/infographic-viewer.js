const MIN_SCALE = 1;
const MAX_SCALE = 5;
const SCALE_STEP = 0.25;

const clamp = (value, minimum, maximum) => Math.min(maximum, Math.max(minimum, value));

export function createInfographicViewportState() {
    let scale = MIN_SCALE;
    let panX = 0;
    let panY = 0;

    return {
        snapshot() {
            return { scale, panX, panY };
        },
        panBy(deltaX, deltaY) {
            panX += deltaX;
            panY += deltaY;

            return this.snapshot();
        },
        zoomAt(nextScale, anchorX = 0, anchorY = 0) {
            const clampedScale = clamp(nextScale, MIN_SCALE, MAX_SCALE);
            const ratio = clampedScale / scale;

            panX = anchorX - ((anchorX - panX) * ratio);
            panY = anchorY - ((anchorY - panY) * ratio);
            scale = clampedScale;

            if (scale === MIN_SCALE) {
                panX = 0;
                panY = 0;
            }

            return this.snapshot();
        },
        reset() {
            scale = MIN_SCALE;
            panX = 0;
            panY = 0;

            return this.snapshot();
        },
    };
}

function initialiseViewer(viewer) {
    if (viewer.dataset.infographicViewerReady === "true") {
        return;
    }

    const viewport = viewer.querySelector("[data-infographic-viewport]");
    const image = viewer.querySelector("[data-infographic-image]");
    const zoomLabel = viewer.querySelector("[data-infographic-zoom-label]");

    if (!viewport || !image) {
        return;
    }

    viewer.dataset.infographicViewerReady = "true";

    const state = createInfographicViewportState();
    const pointers = new Map();
    let lastPointer = null;
    let pinchDistance = null;

    const render = () => {
        const { scale, panX, panY } = state.snapshot();
        image.style.transform = `translate(-50%, -50%) translate(${panX}px, ${panY}px) scale(${scale})`;
        viewport.classList.toggle("is-zoomed", scale > MIN_SCALE);
        viewport.setAttribute("aria-label", `Visualizador do infográfico, zoom em ${Math.round(scale * 100)}%`);

        if (zoomLabel) {
            zoomLabel.textContent = `${Math.round(scale * 100)}%`;
        }
    };

    const viewportAnchor = (clientX, clientY) => {
        const bounds = viewport.getBoundingClientRect();

        return {
            x: clientX - bounds.left - (bounds.width / 2),
            y: clientY - bounds.top - (bounds.height / 2),
        };
    };

    const zoomBy = (delta, clientX = null, clientY = null) => {
        const { scale } = state.snapshot();
        const anchor = clientX === null || clientY === null
            ? { x: 0, y: 0 }
            : viewportAnchor(clientX, clientY);

        state.zoomAt(scale + delta, anchor.x, anchor.y);
        render();
    };

    viewer.querySelector("[data-infographic-zoom-in]")?.addEventListener("click", () => zoomBy(SCALE_STEP));
    viewer.querySelector("[data-infographic-zoom-out]")?.addEventListener("click", () => zoomBy(-SCALE_STEP));
    viewer.querySelector("[data-infographic-reset]")?.addEventListener("click", () => {
        state.reset();
        render();
    });

    viewport.addEventListener("wheel", (event) => {
        event.preventDefault();
        zoomBy(event.deltaY < 0 ? SCALE_STEP : -SCALE_STEP, event.clientX, event.clientY);
    }, { passive: false });

    viewport.addEventListener("dblclick", (event) => {
        const { scale } = state.snapshot();

        if (scale > MIN_SCALE) {
            state.reset();
            render();
            return;
        }

        zoomBy(1, event.clientX, event.clientY);
    });

    viewport.addEventListener("pointerdown", (event) => {
        pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
        viewport.setPointerCapture?.(event.pointerId);

        if (pointers.size === 1) {
            lastPointer = { x: event.clientX, y: event.clientY };
        } else if (pointers.size === 2) {
            const [first, second] = [...pointers.values()];
            pinchDistance = Math.hypot(second.x - first.x, second.y - first.y);
        }
    });

    viewport.addEventListener("pointermove", (event) => {
        if (!pointers.has(event.pointerId)) {
            return;
        }

        const previousPointer = pointers.get(event.pointerId);
        pointers.set(event.pointerId, { x: event.clientX, y: event.clientY });

        if (pointers.size === 2) {
            const [first, second] = [...pointers.values()];
            const nextDistance = Math.hypot(second.x - first.x, second.y - first.y);

            if (pinchDistance && nextDistance) {
                const midpointX = (first.x + second.x) / 2;
                const midpointY = (first.y + second.y) / 2;
                const { scale } = state.snapshot();
                const anchor = viewportAnchor(midpointX, midpointY);

                state.zoomAt(scale * (nextDistance / pinchDistance), anchor.x, anchor.y);
                render();
            }

            pinchDistance = nextDistance;
            return;
        }

        if (lastPointer && state.snapshot().scale > MIN_SCALE) {
            state.panBy(event.clientX - previousPointer.x, event.clientY - previousPointer.y);
            lastPointer = { x: event.clientX, y: event.clientY };
            render();
        }
    });

    const releasePointer = (event) => {
        pointers.delete(event.pointerId);
        pinchDistance = null;
        lastPointer = pointers.size === 1 ? [...pointers.values()][0] : null;
    };

    viewport.addEventListener("pointerup", releasePointer);
    viewport.addEventListener("pointercancel", releasePointer);
    viewport.addEventListener("lostpointercapture", releasePointer);

    render();
}

export function initInfographicViewers(documentObject = document) {
    documentObject.querySelectorAll("[data-infographic-viewer]").forEach(initialiseViewer);
}

