import assert from "node:assert/strict";
import test from "node:test";

import { createRequestTracker, loadingMessageFor } from "../../resources/js/action-loader.js";

test("uses the closest custom loading message", () => {
    const element = {
        closest: () => ({ dataset: { loadingText: "Verificando resposta..." } }),
    };

    assert.equal(loadingMessageFor(element), "Verificando resposta...");
});

test("falls back to the default loading message", () => {
    const element = { closest: () => null };

    assert.equal(loadingMessageFor(element), "Carregando...");
});

test("keeps the loader visible until every request finishes", () => {
    const events = [];
    const tracker = createRequestTracker(
        (message) => events.push(`show:${message}`),
        () => events.push("hide"),
    );

    tracker.start("Primeira");
    tracker.start("Segunda");
    tracker.finish();

    assert.equal(tracker.pending(), 1);
    assert.deepEqual(events, ["show:Primeira", "show:Segunda"]);

    tracker.finish();

    assert.equal(tracker.pending(), 0);
    assert.deepEqual(events, ["show:Primeira", "show:Segunda", "hide"]);
});
