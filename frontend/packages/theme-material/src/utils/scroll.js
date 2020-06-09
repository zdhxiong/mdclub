const easeInOutQuad = (t, b, c, d) => {
  t /= d / 2;
  if (t < 1) return (c / 2) * t * t + b;
  t -= 1;
  return (-c / 2) * (t * (t - 2) - 1) + b;
};

export function scrollToTop(duration = 300) {
  const start = window.scrollY || window.pageYOffset;
  const stop = document.body.getBoundingClientRect().top + start;
  const distance = stop - start;
  let timeStart;
  let timeElapsed;
  let next;

  function loop(timeCurrent) {
    if (!timeStart) {
      timeStart = timeCurrent;
    }

    timeElapsed = timeCurrent - timeStart;
    next = easeInOutQuad(timeElapsed, start, distance, duration);

    window.scrollTo(0, next);

    if (timeElapsed < duration) {
      window.requestAnimationFrame(loop);
    } else {
      window.scrollTo(0, start + distance);
      timeStart = false;
    }
  }

  window.requestAnimationFrame(loop);
}

export function scrollHorizontal(element, options) {
  const duration = options.duration || 300;
  const offset = options.offset || 0;
  const { callback } = options;
  const start = element.scrollLeft;
  let timeStart;
  let timeElapsed;
  let next;

  function loop(timeCurrent) {
    if (!timeStart) {
      timeStart = timeCurrent;
    }

    timeElapsed = timeCurrent - timeStart;
    next = easeInOutQuad(timeElapsed, start, offset, duration);

    element.scrollLeft = next;

    if (timeElapsed < duration) {
      window.requestAnimationFrame(loop);
    } else {
      // 滚动结束后，纠正误差
      element.scrollLeft = start + offset;

      if (callback) {
        callback();
      }

      timeStart = false;
    }
  }

  window.requestAnimationFrame(loop);
}
