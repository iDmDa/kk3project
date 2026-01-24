export function runHooks(hooks = {}, name, ctx) {
  (hooks[name] || []).forEach(fn => {
    try { fn(ctx); } catch {}
  });
}
