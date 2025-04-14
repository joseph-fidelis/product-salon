

export const valueUpdater = (updaterOrValue: any, ref: any) => {
    if (typeof updaterOrValue === 'function') {
      ref.value = updaterOrValue(ref.value);
    } else {
      ref.value = updaterOrValue;
    }
  };
  