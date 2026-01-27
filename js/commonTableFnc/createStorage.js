export function createStorage(key) {
  const read = () => {
    try {
      return JSON.parse(localStorage.getItem(key)) || {};
    } catch {
      return {};
    }
  };

  const write = (data) => {
    localStorage.setItem(key, JSON.stringify(data));
  };

  return {
    get(name, def = null) {
      const data = read();
      return name in data ? data[name] : def;
    },

    set(name, value) {
      const data = read();
      data[name] = value;
      write(data);
    },

    update(patch = {}) {
      const data = read();
      write({ ...data, ...patch });
    },

    remove(name) {
      const data = read();
      delete data[name];
      write(data);
    },

    getAll() {
      return read();
    },

    clear() {
      localStorage.removeItem(key);
    }
  };
}
