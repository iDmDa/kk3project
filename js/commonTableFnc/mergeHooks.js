export function mergeHooks(ctx = {}, patch = {}) {
  const obj = { ...ctx };
  for (const key in patch) {
    obj[key] = [
      ...(obj[key] || []),
      ...(patch[key] || [])
    ];
  }
  return obj;
}
