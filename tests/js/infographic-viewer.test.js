import assert from "node:assert/strict";
import test from "node:test";

import {
    createInfographicImagePresentation,
    createInfographicViewportState,
} from "../../resources/js/infographic-viewer.js";

test("zooms around the selected point", () => {
    const state = createInfographicViewportState();

    state.zoomAt(2, 100, 50);

    assert.deepEqual(state.snapshot(), { scale: 2, panX: -100, panY: -50 });
});

test("limits zoom and resets the position at 100%", () => {
    const state = createInfographicViewportState();

    state.zoomAt(10);
    state.panBy(30, -20);
    state.zoomAt(0);

    assert.deepEqual(state.snapshot(), { scale: 1, panX: 0, panY: 0 });
});

test("pans and restores the initial view", () => {
    const state = createInfographicViewportState();

    state.zoomAt(1.5);
    state.panBy(24, 36);

    assert.deepEqual(state.snapshot(), { scale: 1.5, panX: 24, panY: 36 });
    assert.deepEqual(state.reset(), { scale: 1, panX: 0, panY: 0 });
});

test("zooms by increasing the rendered image dimensions instead of scaling its preview", () => {
    const presentation = createInfographicImagePresentation(300, 450, {
        scale: 5,
        panX: 20,
        panY: -10,
    });

    assert.deepEqual(presentation, {
        width: 1500,
        height: 2250,
        transform: "translate(-50%, -50%) translate(20px, -10px)",
    });
    assert.doesNotMatch(presentation.transform, /scale/);
});
