// Run: node --test tests/fullscreen-close.test.cjs
const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(require('node:path').join(__dirname, '../Plugins/vibe-photo-galleries/assets/js/lightbox.js'), 'utf8');
function fixture() {
    const viewer = {};
    const document = { fullscreenElement: viewer, getElementById: () => viewer };
    const chain = new Proxy({}, {get: (_, key) => key === 'ready' ? () => {} : () => chain});
    const context = { document, window: {}, jQuery: () => chain, Promise, console: {warn() {}} };
    vm.createContext(context);
    vm.runInContext(source.replace('})(jQuery);', `window.testHooks = {
        setController: function(controller) { activeLightbox = controller; },
        setOperation: function(operation) { fullscreenOperation = operation; },
        close: requestViewerClose
    }; })(jQuery);`), context);
    return {document, viewer, hooks: context.window.testHooks};
}
const settle = () => new Promise(resolve => setImmediate(resolve));
test('closing waits for browser fullscreen exit; repeated close requests do not duplicate close', async () => {
    const f = fixture(); let finish, closed = 0;
    f.document.exitFullscreen = () => new Promise(resolve => {finish = () => {f.document.fullscreenElement = null; resolve();};});
    f.hooks.setController({close() {closed++;}});
    f.hooks.close(); f.hooks.close(); await settle();
    assert.equal(closed, 0); finish(); await settle(); assert.equal(closed, 1);
});
test('failed exit keeps viewer visible and allows retry', async () => {
    const f = fixture(); let closed = 0;
    f.hooks.setController({close() {closed++;}});
    f.document.exitFullscreen = () => Promise.reject(new Error('denied'));
    f.hooks.close(); await settle(); assert.equal(closed, 0);
    f.document.exitFullscreen = () => {f.document.fullscreenElement = null; return Promise.resolve();};
    f.hooks.close(); await settle(); assert.equal(closed, 1);
});
test('close waits for pending entry before exiting', async () => {
    const f = fixture(); let enter, exits = 0, closed = 0;
    f.document.fullscreenElement = null;
    f.hooks.setOperation(new Promise(resolve => {enter = () => {f.document.fullscreenElement = f.viewer; resolve();};}));
    f.hooks.setController({close() {closed++;}});
    f.document.exitFullscreen = () => {exits++; f.document.fullscreenElement = null; return Promise.resolve();};
    f.hooks.close(); await settle(); assert.equal(closed, 0); enter(); await settle();
    assert.equal(exits, 1); assert.equal(closed, 1);
});
test('non-fullscreen close leaves unrelated browser fullscreen alone', async () => {
    const f = fixture(); let exits = 0, closed = 0;
    f.document.fullscreenElement = {};
    f.document.exitFullscreen = () => {exits++;};
    f.hooks.setController({close() {closed++;}});
    f.hooks.close(); await settle(); assert.equal(exits, 0); assert.equal(closed, 1);
});
