import assert from "node:assert/strict";
import test from "node:test";

import { createInfographicViewportState } from "../../resources/js/infographic-viewer.js";

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
