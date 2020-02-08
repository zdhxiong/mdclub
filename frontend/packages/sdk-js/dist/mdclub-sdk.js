/*!
 * mdclub-sdk 1.0.0 (https://github.com/zdhxiong/mdclub-sdk-js#readme)
 * Copyright 2018-2020 zdhxiong
 * Licensed under MIT
 */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
  typeof define === 'function' && define.amd ? define(['exports'], factory) :
  (global = global || self, factory(global.mdclubSDK = {}));
}(this, (function (exports) { 'use strict';

  !function(){try{return new MouseEvent("test")}catch(e){}var e=function(e,t){t=t||{bubbles:!1,cancelable:!1};var n=document.createEvent("MouseEvent");return n.initMouseEvent(e,t.bubbles,t.cancelable,window,0,t.screenX||0,t.screenY||0,t.clientX||0,t.clientY||0,t.ctrlKey||!1,t.altKey||!1,t.shiftKey||!1,t.metaKey||!1,t.button||0,t.relatedTarget||null),n};e.prototype=Event.prototype,window.MouseEvent=e;}();

  !function(){function t(t,e){e=e||{bubbles:!1,cancelable:!1,detail:void 0};var n=document.createEvent("CustomEvent");return n.initCustomEvent(t,e.bubbles,e.cancelable,e.detail),n}"function"!=typeof window.CustomEvent&&(t.prototype=window.Event.prototype,window.CustomEvent=t);}();

  /**
   * @this {Promise}
   */
  function finallyConstructor(callback) {
    var constructor = this.constructor;
    return this.then(
      function(value) {
        // @ts-ignore
        return constructor.resolve(callback()).then(function() {
          return value;
        });
      },
      function(reason) {
        // @ts-ignore
        return constructor.resolve(callback()).then(function() {
          // @ts-ignore
          return constructor.reject(reason);
        });
      }
    );
  }

  // Store setTimeout reference so promise-polyfill will be unaffected by
  // other code modifying setTimeout (like sinon.useFakeTimers())
  var setTimeoutFunc = setTimeout;

  function isArray(x) {
    return Boolean(x && typeof x.length !== 'undefined');
  }

  function noop() {}

  // Polyfill for Function.prototype.bind
  function bind(fn, thisArg) {
    return function() {
      fn.apply(thisArg, arguments);
    };
  }

  /**
   * @constructor
   * @param {Function} fn
   */
  function Promise$1(fn) {
    if (!(this instanceof Promise$1))
      { throw new TypeError('Promises must be constructed via new'); }
    if (typeof fn !== 'function') { throw new TypeError('not a function'); }
    /** @type {!number} */
    this._state = 0;
    /** @type {!boolean} */
    this._handled = false;
    /** @type {Promise|undefined} */
    this._value = undefined;
    /** @type {!Array<!Function>} */
    this._deferreds = [];

    doResolve(fn, this);
  }

  function handle(self, deferred) {
    while (self._state === 3) {
      self = self._value;
    }
    if (self._state === 0) {
      self._deferreds.push(deferred);
      return;
    }
    self._handled = true;
    Promise$1._immediateFn(function() {
      var cb = self._state === 1 ? deferred.onFulfilled : deferred.onRejected;
      if (cb === null) {
        (self._state === 1 ? resolve : reject)(deferred.promise, self._value);
        return;
      }
      var ret;
      try {
        ret = cb(self._value);
      } catch (e) {
        reject(deferred.promise, e);
        return;
      }
      resolve(deferred.promise, ret);
    });
  }

  function resolve(self, newValue) {
    try {
      // Promise Resolution Procedure: https://github.com/promises-aplus/promises-spec#the-promise-resolution-procedure
      if (newValue === self)
        { throw new TypeError('A promise cannot be resolved with itself.'); }
      if (
        newValue &&
        (typeof newValue === 'object' || typeof newValue === 'function')
      ) {
        var then = newValue.then;
        if (newValue instanceof Promise$1) {
          self._state = 3;
          self._value = newValue;
          finale(self);
          return;
        } else if (typeof then === 'function') {
          doResolve(bind(then, newValue), self);
          return;
        }
      }
      self._state = 1;
      self._value = newValue;
      finale(self);
    } catch (e) {
      reject(self, e);
    }
  }

  function reject(self, newValue) {
    self._state = 2;
    self._value = newValue;
    finale(self);
  }

  function finale(self) {
    if (self._state === 2 && self._deferreds.length === 0) {
      Promise$1._immediateFn(function() {
        if (!self._handled) {
          Promise$1._unhandledRejectionFn(self._value);
        }
      });
    }

    for (var i = 0, len = self._deferreds.length; i < len; i++) {
      handle(self, self._deferreds[i]);
    }
    self._deferreds = null;
  }

  /**
   * @constructor
   */
  function Handler(onFulfilled, onRejected, promise) {
    this.onFulfilled = typeof onFulfilled === 'function' ? onFulfilled : null;
    this.onRejected = typeof onRejected === 'function' ? onRejected : null;
    this.promise = promise;
  }

  /**
   * Take a potentially misbehaving resolver function and make sure
   * onFulfilled and onRejected are only called once.
   *
   * Makes no guarantees about asynchrony.
   */
  function doResolve(fn, self) {
    var done = false;
    try {
      fn(
        function(value) {
          if (done) { return; }
          done = true;
          resolve(self, value);
        },
        function(reason) {
          if (done) { return; }
          done = true;
          reject(self, reason);
        }
      );
    } catch (ex) {
      if (done) { return; }
      done = true;
      reject(self, ex);
    }
  }

  Promise$1.prototype['catch'] = function(onRejected) {
    return this.then(null, onRejected);
  };

  Promise$1.prototype.then = function(onFulfilled, onRejected) {
    // @ts-ignore
    var prom = new this.constructor(noop);

    handle(this, new Handler(onFulfilled, onRejected, prom));
    return prom;
  };

  Promise$1.prototype['finally'] = finallyConstructor;

  Promise$1.all = function(arr) {
    return new Promise$1(function(resolve, reject) {
      if (!isArray(arr)) {
        return reject(new TypeError('Promise.all accepts an array'));
      }

      var args = Array.prototype.slice.call(arr);
      if (args.length === 0) { return resolve([]); }
      var remaining = args.length;

      function res(i, val) {
        try {
          if (val && (typeof val === 'object' || typeof val === 'function')) {
            var then = val.then;
            if (typeof then === 'function') {
              then.call(
                val,
                function(val) {
                  res(i, val);
                },
                reject
              );
              return;
            }
          }
          args[i] = val;
          if (--remaining === 0) {
            resolve(args);
          }
        } catch (ex) {
          reject(ex);
        }
      }

      for (var i = 0; i < args.length; i++) {
        res(i, args[i]);
      }
    });
  };

  Promise$1.resolve = function(value) {
    if (value && typeof value === 'object' && value.constructor === Promise$1) {
      return value;
    }

    return new Promise$1(function(resolve) {
      resolve(value);
    });
  };

  Promise$1.reject = function(value) {
    return new Promise$1(function(resolve, reject) {
      reject(value);
    });
  };

  Promise$1.race = function(arr) {
    return new Promise$1(function(resolve, reject) {
      if (!isArray(arr)) {
        return reject(new TypeError('Promise.race accepts an array'));
      }

      for (var i = 0, len = arr.length; i < len; i++) {
        Promise$1.resolve(arr[i]).then(resolve, reject);
      }
    });
  };

  // Use polyfill for setImmediate for performance gains
  Promise$1._immediateFn =
    // @ts-ignore
    (typeof setImmediate === 'function' &&
      function(fn) {
        // @ts-ignore
        setImmediate(fn);
      }) ||
    function(fn) {
      setTimeoutFunc(fn, 0);
    };

  Promise$1._unhandledRejectionFn = function _unhandledRejectionFn(err) {
    if (typeof console !== 'undefined' && console) {
      console.warn('Possible Unhandled Promise Rejection:', err); // eslint-disable-line no-console
    }
  };

  /** @suppress {undefinedVars} */
  var globalNS = (function() {
    // the only reliable means to get the global object is
    // `Function('return this')()`
    // However, this causes CSP violations in Chrome apps.
    if (typeof self !== 'undefined') {
      return self;
    }
    if (typeof window !== 'undefined') {
      return window;
    }
    if (typeof global !== 'undefined') {
      return global;
    }
    throw new Error('unable to locate global object');
  })();

  if (!('Promise' in globalNS)) {
    globalNS['Promise'] = Promise$1;
  } else if (!globalNS.Promise.prototype['finally']) {
    globalNS.Promise.prototype['finally'] = finallyConstructor;
  }

  function isFunction(target) {
      return typeof target === 'function';
  }
  function isString(target) {
      return typeof target === 'string';
  }
  function isNumber(target) {
      return typeof target === 'number';
  }
  function isUndefined(target) {
      return typeof target === 'undefined';
  }
  function isWindow(target) {
      return target instanceof Window;
  }
  function isNode(target) {
      return target instanceof Node;
  }
  function isArrayLike(target) {
      if (isFunction(target) || isWindow(target)) {
          return false;
      }
      return isNumber(target.length);
  }
  function isObjectLike(target) {
      return typeof target === 'object' && target !== null;
  }
  /**
   * 获取子节点组成的数组
   * @param target
   * @param parent
   */
  function getChildNodesArray(target, parent) {
      var tempParent = document.createElement(parent);
      tempParent.innerHTML = target;
      return [].slice.call(tempParent.childNodes);
  }

  function each(target, callback) {
      if (isArrayLike(target)) {
          for (var i = 0; i < target.length; i += 1) {
              if (callback.call(target[i], i, target[i]) === false) {
                  return target;
              }
          }
      }
      else {
          var keys = Object.keys(target);
          for (var i$1 = 0; i$1 < keys.length; i$1 += 1) {
              if (callback.call(target[keys[i$1]], keys[i$1], target[keys[i$1]]) === false) {
                  return target;
              }
          }
      }
      return target;
  }

  /**
   * 为了使用模块扩充，这里不能使用默认导出
   */
  var JQ = function JQ(arr) {
      var this$1 = this;

      this.length = 0;
      if (!arr) {
          return this;
      }
      each(arr, function (i, item) {
          // @ts-ignore
          this$1[i] = item;
      });
      this.length = arr.length;
      return this;
  };

  function get$() {
      var $ = function (selector) {
          if (!selector) {
              return new JQ();
          }
          // JQ
          if (selector instanceof JQ) {
              return selector;
          }
          // function
          if (isFunction(selector)) {
              if (/complete|loaded|interactive/.test(document.readyState) &&
                  document.body) {
                  selector.call(document, $);
              }
              else {
                  document.addEventListener('DOMContentLoaded', function () { return selector.call(document, $); }, false);
              }
              return new JQ([document]);
          }
          // String
          if (isString(selector)) {
              var html = selector.trim();
              // 根据 HTML 字符串创建 JQ 对象
              if (html[0] === '<' && html[html.length - 1] === '>') {
                  var toCreate = 'div';
                  var tags = {
                      li: 'ul',
                      tr: 'tbody',
                      td: 'tr',
                      th: 'tr',
                      tbody: 'table',
                      option: 'select',
                  };
                  each(tags, function (childTag, parentTag) {
                      if (html.indexOf(("<" + childTag)) === 0) {
                          toCreate = parentTag;
                          return false;
                      }
                      return;
                  });
                  return new JQ(getChildNodesArray(html, toCreate));
              }
              // 根据 CSS 选择器创建 JQ 对象
              var isIdSelector = selector[0] === '#' && !selector.match(/[ .<>:~]/);
              if (!isIdSelector) {
                  return new JQ(document.querySelectorAll(selector));
              }
              var element = document.getElementById(selector.slice(1));
              if (element) {
                  return new JQ([element]);
              }
              return new JQ();
          }
          if (isArrayLike(selector) && !isNode(selector)) {
              return new JQ(selector);
          }
          return new JQ([selector]);
      };
      $.fn = JQ.prototype;
      return $;
  }
  var $ = get$();

  $.fn.each = function (callback) {
      return each(this, callback);
  };

  /**
   * 把第二个数组的元素追加到第一个数组中，并返回合并后的数组
   * @param first 第一个数组
   * @param second 该数组的元素将被追加到第一个数组中
   * @example
  ```js
  merge( [ 0, 1, 2 ], [ 2, 3, 4 ] )
  // [ 0, 1, 2, 2, 3, 4 ]
  ```
   */
  function merge(first, second) {
      each(second, function (_, value) {
          first.push(value);
      });
      return first;
  }

  $.fn.get = function (index) {
      return index === undefined
          ? [].slice.call(this)
          : this[index >= 0 ? index : index + this.length];
  };

  $.fn.find = function (selector) {
      var foundElements = [];
      this.each(function (_, element) {
          merge(foundElements, $(element.querySelectorAll(selector)).get());
      });
      return new JQ(foundElements);
  };

  /**
   * 解析事件名中的命名空间
   */
  function parse(type) {
      var parts = type.split('.');
      return {
          type: parts[0],
          ns: parts
              .slice(1)
              .sort()
              .join(' '),
      };
  }

  $.fn.trigger = function (type, extraParameters) {
      var event = parse(type);
      var eventObject;
      var eventParams = {
          bubbles: true,
          cancelable: true,
      };
      var isMouseEvent = ['click', 'mousedown', 'mouseup', 'mousemove'].indexOf(event.type) > -1;
      if (isMouseEvent) {
          // Note: MouseEvent 无法传入 detail 参数
          eventObject = new MouseEvent(event.type, eventParams);
      }
      else {
          eventParams.detail = extraParameters;
          eventObject = new CustomEvent(event.type, eventParams);
      }
      // @ts-ignore
      eventObject._detail = extraParameters;
      // @ts-ignore
      eventObject._ns = event.ns;
      return this.each(function () {
          this.dispatchEvent(eventObject);
      });
  };

  function extend(target, object1) {
      var objectN = [], len = arguments.length - 2;
      while ( len-- > 0 ) objectN[ len ] = arguments[ len + 2 ];

      objectN.unshift(object1);
      each(objectN, function (_, object) {
          each(object, function (prop, value) {
              if (!isUndefined(value)) {
                  target[prop] = value;
              }
          });
      });
      return target;
  }

  /**
   * 将数组或对象序列化，序列化后的字符串可作为 URL 查询字符串使用
   *
   * 若传入数组，则格式必须和 serializeArray 方法的返回值一样
   * @param obj 对象或数组
   * @example
  ```js
  param({ width: 1680, height: 1050 });
  // width=1680&height=1050
  ```
   * @example
  ```js
  param({ foo: { one: 1, two: 2 }})
  // foo[one]=1&foo[two]=2
  ```
   * @example
  ```js
  param({ids: [1, 2, 3]})
  // ids[]=1&ids[]=2&ids[]=3
  ```
   * @example
  ```js
  param([
    {"name":"name","value":"mdui"},
    {"name":"password","value":"123456"}
  ])
  // name=mdui&password=123456
  ```
   */
  function param(obj) {
      if (!isObjectLike(obj) && !Array.isArray(obj)) {
          return '';
      }
      var args = [];
      function destructure(key, value) {
          var keyTmp;
          if (isObjectLike(value)) {
              each(value, function (i, v) {
                  if (Array.isArray(value) && !isObjectLike(v)) {
                      keyTmp = '';
                  }
                  else {
                      keyTmp = i;
                  }
                  destructure((key + "[" + keyTmp + "]"), v);
              });
          }
          else {
              if (value == null || value === '') {
                  keyTmp = '=';
              }
              else {
                  keyTmp = "=" + (encodeURIComponent(value));
              }
              args.push(encodeURIComponent(key) + keyTmp);
          }
      }
      if (Array.isArray(obj)) {
          each(obj, function () {
              destructure(this.name, this.value);
          });
      }
      else {
          each(obj, destructure);
      }
      return args.join('&');
  }

  // 全局配置参数
  var globalOptions = {};
  // 全局事件名
  var ajaxEvents = {
      ajaxStart: 'start.mdui.ajax',
      ajaxSuccess: 'success.mdui.ajax',
      ajaxError: 'error.mdui.ajax',
      ajaxComplete: 'complete.mdui.ajax',
  };

  /**
   * 判断此请求方法是否通过查询字符串提交参数
   * @param method 请求方法，大写
   */
  function isQueryStringData(method) {
      return ['GET', 'HEAD'].indexOf(method) >= 0;
  }
  /**
   * 添加参数到 URL 上，且 URL 中不存在 ? 时，自动把第一个 & 替换为 ?
   * @param url
   * @param query
   */
  function appendQuery(url, query) {
      return (url + "&" + query).replace(/[&?]{1,2}/, '?');
  }
  /**
   * 合并请求参数，参数优先级：options > globalOptions > defaults
   * @param options
   */
  function mergeOptions(options) {
      // 默认参数
      var defaults = {
          url: '',
          method: 'GET',
          data: '',
          processData: true,
          async: true,
          cache: true,
          username: '',
          password: '',
          headers: {},
          xhrFields: {},
          statusCode: {},
          dataType: 'text',
          contentType: 'application/x-www-form-urlencoded',
          timeout: 0,
          global: true,
      };
      // globalOptions 中的回调函数不合并
      each(globalOptions, function (key, value) {
          var callbacks = [
              'beforeSend',
              'success',
              'error',
              'complete',
              'statusCode' ];
          // @ts-ignore
          if (callbacks.indexOf(key) < 0 && !isUndefined(value)) {
              defaults[key] = value;
          }
      });
      return extend({}, defaults, options);
  }
  /**
   * 发送 ajax 请求
   * @param options
   * @example
  ```js
  ajax({
    method: "POST",
    url: "some.php",
    data: { name: "John", location: "Boston" }
  }).then(function( msg ) {
    alert( "Data Saved: " + msg );
  });
  ```
   */
  function ajax(options) {
      // 是否已取消请求
      var isCanceled = false;
      // 事件参数
      var eventParams = {};
      // 参数合并
      var mergedOptions = mergeOptions(options);
      var url = mergedOptions.url || window.location.toString();
      var method = mergedOptions.method.toUpperCase();
      var data = mergedOptions.data;
      var processData = mergedOptions.processData;
      var async = mergedOptions.async;
      var cache = mergedOptions.cache;
      var username = mergedOptions.username;
      var password = mergedOptions.password;
      var headers = mergedOptions.headers;
      var xhrFields = mergedOptions.xhrFields;
      var statusCode = mergedOptions.statusCode;
      var dataType = mergedOptions.dataType;
      var contentType = mergedOptions.contentType;
      var timeout = mergedOptions.timeout;
      var global = mergedOptions.global;
      // 需要发送的数据
      // GET/HEAD 请求和 processData 为 true 时，转换为查询字符串格式，特殊格式不转换
      if (data &&
          (isQueryStringData(method) || processData) &&
          !isString(data) &&
          !(data instanceof ArrayBuffer) &&
          !(data instanceof Blob) &&
          !(data instanceof Document) &&
          !(data instanceof FormData)) {
          data = param(data);
      }
      // 对于 GET、HEAD 类型的请求，把 data 数据添加到 URL 中
      if (data && isQueryStringData(method)) {
          // 查询字符串拼接到 URL 中
          url = appendQuery(url, data);
          data = null;
      }
      /**
       * 触发事件和回调函数
       * @param event
       * @param params
       * @param callback
       * @param args
       */
      function trigger(event, params, callback) {
          var args = [], len = arguments.length - 3;
          while ( len-- > 0 ) args[ len ] = arguments[ len + 3 ];

          // 触发全局事件
          if (global) {
              $(document).trigger(event, params);
          }
          // 触发 ajax 回调和事件
          var result1;
          var result2;
          if (callback) {
              // 全局回调
              if (callback in globalOptions) {
                  // @ts-ignore
                  result1 = globalOptions[callback].apply(globalOptions, args);
              }
              // 自定义回调
              if (mergedOptions[callback]) {
                  // @ts-ignore
                  result2 = mergedOptions[callback].apply(mergedOptions, args);
              }
              // beforeSend 回调返回 false 时取消 ajax 请求
              if (callback === 'beforeSend' &&
                  (result1 === false || result2 === false)) {
                  isCanceled = true;
              }
          }
      }
      // XMLHttpRequest 请求
      function XHR() {
          var textStatus;
          return new Promise(function (resolve, reject) {
              // GET/HEAD 请求的缓存处理
              if (isQueryStringData(method) && !cache) {
                  url = appendQuery(url, ("_=" + (Date.now())));
              }
              // 创建 XHR
              var xhr = new XMLHttpRequest();
              xhr.open(method, url, async, username, password);
              if (contentType ||
                  (data && !isQueryStringData(method) && contentType !== false)) {
                  xhr.setRequestHeader('Content-Type', contentType);
              }
              // 设置 Accept
              if (dataType === 'json') {
                  xhr.setRequestHeader('Accept', 'application/json, text/javascript');
              }
              // 添加 headers
              if (headers) {
                  each(headers, function (key, value) {
                      // undefined 值不发送，string 和 null 需要发送
                      if (!isUndefined(value)) {
                          xhr.setRequestHeader(key, value + ''); // 把 null 转换成字符串
                      }
                  });
              }
              // 检查是否是跨域请求，跨域请求时不添加 X-Requested-With
              var crossDomain = /^([\w-]+:)?\/\/([^/]+)/.test(url) &&
                  RegExp.$2 !== window.location.host;
              if (!crossDomain) {
                  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
              }
              if (xhrFields) {
                  each(xhrFields, function (key, value) {
                      // @ts-ignore
                      xhr[key] = value;
                  });
              }
              eventParams.xhr = xhr;
              eventParams.options = mergedOptions;
              var xhrTimeout;
              xhr.onload = function () {
                  if (xhrTimeout) {
                      clearTimeout(xhrTimeout);
                  }
                  // AJAX 返回的 HTTP 响应码是否表示成功
                  var isHttpStatusSuccess = (xhr.status >= 200 && xhr.status < 300) ||
                      xhr.status === 304 ||
                      xhr.status === 0;
                  var responseData;
                  if (isHttpStatusSuccess) {
                      if (xhr.status === 204 || method === 'HEAD') {
                          textStatus = 'nocontent';
                      }
                      else if (xhr.status === 304) {
                          textStatus = 'notmodified';
                      }
                      else {
                          textStatus = 'success';
                      }
                      if (dataType === 'json') {
                          try {
                              responseData =
                                  method === 'HEAD' ? undefined : JSON.parse(xhr.responseText);
                              eventParams.data = responseData;
                          }
                          catch (err) {
                              textStatus = 'parsererror';
                              trigger(ajaxEvents.ajaxError, eventParams, 'error', xhr, textStatus);
                              reject(new Error(textStatus));
                          }
                          if (textStatus !== 'parsererror') {
                              trigger(ajaxEvents.ajaxSuccess, eventParams, 'success', responseData, textStatus, xhr);
                              resolve(responseData);
                          }
                      }
                      else {
                          responseData =
                              method === 'HEAD'
                                  ? undefined
                                  : xhr.responseType === 'text' || xhr.responseType === ''
                                      ? xhr.responseText
                                      : xhr.response;
                          eventParams.data = responseData;
                          trigger(ajaxEvents.ajaxSuccess, eventParams, 'success', responseData, textStatus, xhr);
                          resolve(responseData);
                      }
                  }
                  else {
                      textStatus = 'error';
                      trigger(ajaxEvents.ajaxError, eventParams, 'error', xhr, textStatus);
                      reject(new Error(textStatus));
                  }
                  // statusCode
                  each([globalOptions.statusCode, statusCode], function (_, func) {
                      if (func && func[xhr.status]) {
                          if (isHttpStatusSuccess) {
                              func[xhr.status](responseData, textStatus, xhr);
                          }
                          else {
                              func[xhr.status](xhr, textStatus);
                          }
                      }
                  });
                  trigger(ajaxEvents.ajaxComplete, eventParams, 'complete', xhr, textStatus);
              };
              xhr.onerror = function () {
                  if (xhrTimeout) {
                      clearTimeout(xhrTimeout);
                  }
                  trigger(ajaxEvents.ajaxError, eventParams, 'error', xhr, xhr.statusText);
                  trigger(ajaxEvents.ajaxComplete, eventParams, 'complete', xhr, 'error');
                  reject(new Error(xhr.statusText));
              };
              xhr.onabort = function () {
                  var statusText = 'abort';
                  if (xhrTimeout) {
                      statusText = 'timeout';
                      clearTimeout(xhrTimeout);
                  }
                  trigger(ajaxEvents.ajaxError, eventParams, 'error', xhr, statusText);
                  trigger(ajaxEvents.ajaxComplete, eventParams, 'complete', xhr, statusText);
                  reject(new Error(statusText));
              };
              // ajax start 回调
              trigger(ajaxEvents.ajaxStart, eventParams, 'beforeSend', xhr);
              if (isCanceled) {
                  reject(new Error('cancel'));
                  return;
              }
              // Timeout
              if (timeout > 0) {
                  xhrTimeout = setTimeout(function () {
                      xhr.abort();
                  }, timeout);
              }
              // 发送 XHR
              xhr.send(data);
          });
      }
      return XHR();
  }

  var defaults = {
      apiPath: '',
      methodOverride: false,
      timeout: 30000,
  };

  var GET = 'GET';
  var POST = 'POST';
  var PUT = 'PUT';
  var PATCH = 'PATCH';
  var DELETE = 'DELETE';

  var defaultExport = function defaultExport () {};

  defaultExport.prototype.getStorage = function getStorage (key) {
      return window.localStorage.getItem(key);
  };
  /**
   * 设置数据存储
   * @param key
   * @param data
   */
  defaultExport.prototype.setStorage = function setStorage (key, data) {
      window.localStorage.setItem(key, data);
  };
  /**
   * 删除数据存储
   * @param key
   */
  defaultExport.prototype.removeStorage = function removeStorage (key) {
      window.localStorage.removeItem(key);
  };

  /**
   * 浏览器适配器，使用 mdui.jq 中的 ajax 函数实现
   */
  var defaultExport$1 = /*@__PURE__*/(function (BrowserAbstract) {
      function defaultExport () {
          BrowserAbstract.apply(this, arguments);
      }

      if ( BrowserAbstract ) defaultExport.__proto__ = BrowserAbstract;
      defaultExport.prototype = Object.create( BrowserAbstract && BrowserAbstract.prototype );
      defaultExport.prototype.constructor = defaultExport;

      defaultExport.prototype.request = function request (options) {
          var headers = {
              'Content-Type': 'application/json',
              token: this.getStorage('token') || undefined,
          };
          if (options.data && options.data instanceof FormData) {
              headers['Content-Type'] = 'multipart/form-data';
          }
          if (options.headers) {
              headers = extend({}, headers, options.headers);
          }
          return new Promise(function (resolve, reject) {
              ajax({
                  method: options.method || GET,
                  url: ("" + (defaults.apiPath) + (options.url || '')),
                  data: JSON.stringify(options.data),
                  headers: headers,
                  dataType: 'json',
                  contentType: 'application/json',
                  timeout: defaults.timeout,
                  global: false,
                  beforeSend: function () {
                      defaults.beforeSend && defaults.beforeSend();
                  },
                  success: function (data) {
                      defaults.success && defaults.success(data);
                      data.code === 0 ? resolve(data) : reject(data);
                  },
                  error: function (_, textStatus) {
                      defaults.error && defaults.error(textStatus);
                      reject({
                          code: 999999,
                          message: textStatus,
                      });
                  },
                  complete: function () {
                      defaults.complete && defaults.complete();
                  },
              });
          });
      };

      return defaultExport;
  }(defaultExport));

  if (isUndefined(defaults.adapter)) {
      throw new Error('adapter must be set. e.g. new BrowserAdapter() or new MiniProgramAdapter()');
  }
  var requestHandle = function (method, url, data) {
      var headers = {};
      var XHttpMethodOverride = 'X-Http-Method-Override';
      if (defaults.methodOverride) {
          if (method === PATCH || method === PUT) {
              headers[XHttpMethodOverride] = method;
              method = POST;
          }
          if (method === DELETE) {
              headers[XHttpMethodOverride] = method;
              method = GET;
          }
      }
      return defaults.adapter.request({ method: method, url: url, data: data, headers: headers });
  };
  var getRequest = function (url, data) { return requestHandle(GET, url, data); };
  var postRequest = function (url, data) { return requestHandle(POST, url, data); };
  var patchRequest = function (url, data) { return requestHandle(PATCH, url, data); };
  var putRequest = function (url, data) { return requestHandle(PUT, url, data); };
  var deleteRequest = function (url, data) { return requestHandle(DELETE, url, data); };

  /**
   * 替换 url 中的变量占位符，并添加 queryParam
   * @param path             含变量占位符的 url
   * @param params           含 path 参数、 query 参数、requestBody 参数的对象
   * @param queryParamNames  query 参数名数组
   */
  function buildURL(path, params, queryParamNames) {
      if ( queryParamNames === void 0 ) queryParamNames = [];

      // 替换 path 参数
      var url = path.replace(/({.*?})/g, function (match) {
          var pathParamName = match.substr(1, match.length - 2);
          if (params[pathParamName] == null) {
              throw new Error(("Missing required parameter " + pathParamName));
          }
          return String(params[pathParamName]);
      });
      // 添加 query 参数
      var queryObj = {};
      queryParamNames.forEach(function (name) {
          if (params[name] != null) {
              queryObj[name] = String(params[name]);
          }
      });
      var queryString = param(queryObj);
      return queryString ? (url + "?" + queryString) : url;
  }
  /**
   * 生成 requestBody 参数
   * @param params           含 path 参数、 query 参数、requestBody 参数的对象
   * @param requestBodyNames requestBody 参数名数组
   */
  function buildRequestBody(params, requestBodyNames) {
      var requestBody = {};
      requestBodyNames.forEach(function (name) {
          if (params[name] != null) {
              requestBody[name] = params[name];
          }
      });
      return requestBody;
  }

  /**
   * 删除回答
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除回答。回答作者是否可删除回答，由管理员在后台的设置决定。
   */
  var del = function (params) { return deleteRequest(buildURL('/answers/{answer_id}', params)); };
  /**
   * 为回答投票
   * 为回答投票
   */
  var addVote = function (params) { return postRequest(buildURL('/answers/{answer_id}/voters', params), buildRequestBody(params, ['type'])); };
  /**
   * 在指定回答下发表评论
   * 在指定回答下发表评论
   */
  var createComment = function (params) { return postRequest(buildURL('/answers/{answer_id}/comments', params, ['include']), buildRequestBody(params, ['content'])); };
  /**
   * 🔐批量删除回答
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。
   */
  var deleteMultiple = function (params) { return deleteRequest(buildURL('/answers/{answer_ids}', params)); };
  /**
   * 取消为回答的投票
   * 取消为回答的投票
   */
  var deleteVote = function (params) { return deleteRequest(buildURL('/answers/{answer_id}/voters', params)); };
  /**
   * 获取回答详情
   * 获取回答详情
   */
  var get = function (params) { return getRequest(buildURL('/answers/{answer_id}', params, ['include'])); };
  /**
   * 获取指定回答的评论
   * 获取指定回答的评论。
   */
  var getComments = function (params) { return getRequest(buildURL('/answers/{answer_id}/comments', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取回答列表
   * 获取回答列表。
   */
  var getList = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/answers', params, [
      'page',
      'per_page',
      'order',
      'include',
      'answer_id',
      'question_id',
      'user_id',
      'trashed' ]));
  };
  /**
   * 获取回答的投票者
   * 获取回答的投票者
   */
  var getVoters = function (params) { return getRequest(buildURL('/answers/{answer_id}/voters', params, [
      'page',
      'per_page',
      'include',
      'type' ])); };
  /**
   * 🔐把回答放入回收站
   * 仅管理员可调用该接口
   */
  var trash = function (params) { return postRequest(buildURL('/answers/{answer_id}/trash', params, ['include'])); };
  /**
   * 🔐批量把回答放入回收站
   * 仅管理员可调用该接口。
   */
  var trashMultiple = function (params) { return postRequest(buildURL('/answers/{answer_ids}/trash', params, ['include'])); };
  /**
   * 🔐把回答移出回收站
   * 仅管理员可调用该接口。
   */
  var untrash = function (params) { return postRequest(buildURL('/answers/{answer_id}/untrash', params, ['include'])); };
  /**
   * 🔐批量把回答移出回收站
   * 仅管理员可调用该接口。
   */
  var untrashMultiple = function (params) { return postRequest(buildURL('/answers/{answer_ids}/untrash', params, ['include'])); };
  /**
   * 修改回答信息
   * 管理员可修改回答。回答作者是否可修改回答，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   */
  var update = function (params) { return patchRequest(buildURL('/answers/{answer_id}', params, ['include']), buildRequestBody(params, ['content_markdown', 'content_rendered'])); };

  var AnswerApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    del: del,
    addVote: addVote,
    createComment: createComment,
    deleteMultiple: deleteMultiple,
    deleteVote: deleteVote,
    get: get,
    getComments: getComments,
    getList: getList,
    getVoters: getVoters,
    trash: trash,
    trashMultiple: trashMultiple,
    untrash: untrash,
    untrashMultiple: untrashMultiple,
    update: update
  });

  /**
   * 删除文章
   * 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。  管理员可删除文章。文章作者是否可删除文章，由管理员在后台的设置决定。
   */
  var del$1 = function (params) { return deleteRequest(buildURL('/articles/{article_id}', params)); };
  /**
   * 添加关注
   * 添加关注
   */
  var addFollow = function (params) { return postRequest(buildURL('/articles/{article_id}/followers', params)); };
  /**
   * 为文章投票
   * 为文章投票
   */
  var addVote$1 = function (params) { return postRequest(buildURL('/articles/{article_id}/voters', params), buildRequestBody(params, ['type'])); };
  /**
   * 发表文章
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   */
  var create = function (params) { return postRequest(buildURL('/articles', params, ['include']), buildRequestBody(params, [
      'title',
      'topic_ids',
      'content_markdown',
      'content_rendered' ])); };
  /**
   * 在指定文章下发表评论
   * 在指定文章下发表评论
   */
  var createComment$1 = function (params) { return postRequest(buildURL('/articles/{article_id}/comments', params, ['include']), buildRequestBody(params, ['content'])); };
  /**
   * 取消关注
   * 取消关注
   */
  var deleteFollow = function (params) { return deleteRequest(buildURL('/articles/{article_id}/followers', params)); };
  /**
   * 🔐批量删除文章
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有文章被删除，该接口都会返回成功。
   */
  var deleteMultiple$1 = function (params) { return deleteRequest(buildURL('/articles/{article_ids}', params)); };
  /**
   * 取消为文章的投票
   * 取消为文章的投票
   */
  var deleteVote$1 = function (params) { return deleteRequest(buildURL('/articles/{article_id}/voters', params)); };
  /**
   * 获取指定文章信息
   * 获取指定文章信息
   */
  var get$1 = function (params) { return getRequest(buildURL('/articles/{article_id}', params, ['include'])); };
  /**
   * 获取指定文章的评论列表
   * 获取指定文章的评论列表。
   */
  var getComments$1 = function (params) { return getRequest(buildURL('/articles/{article_id}/comments', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取指定文章的关注者
   * 获取指定文章的关注者
   */
  var getFollowers = function (params) { return getRequest(buildURL('/articles/{article_id}/followers', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取文章列表
   * 获取文章列表。
   */
  var getList$1 = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/articles', params, [
      'page',
      'per_page',
      'order',
      'include',
      'article_id',
      'user_id',
      'topic_id',
      'trashed' ]));
  };
  /**
   * 获取文章的投票者
   * 获取文章的投票者
   */
  var getVoters$1 = function (params) { return getRequest(buildURL('/articles/{article_id}/voters', params, [
      'page',
      'per_page',
      'include',
      'type' ])); };
  /**
   * 🔐把文章放入回收站
   * 仅管理员可调用该接口
   */
  var trash$1 = function (params) { return postRequest(buildURL('/articles/{article_id}/trash', params, ['include'])); };
  /**
   * 🔐批量把文章放入回收站
   * 仅管理员可调用该接口。
   */
  var trashMultiple$1 = function (params) { return postRequest(buildURL('/articles/{article_ids}/trash', params, ['include'])); };
  /**
   * 🔐把文章移出回收站
   * 仅管理员可调用该接口。
   */
  var untrash$1 = function (params) { return postRequest(buildURL('/articles/{article_id}/untrash', params, ['include'])); };
  /**
   * 🔐批量把文章移出回收站
   * 仅管理员可调用该接口。
   */
  var untrashMultiple$1 = function (params) { return postRequest(buildURL('/articles/{article_ids}/untrash', params, ['include'])); };
  /**
   * 更新文章信息
   * 管理员可修改文章。文章作者是否可修改文章，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   */
  var update$1 = function (params) { return patchRequest(buildURL('/articles/{article_id}', params, ['include']), buildRequestBody(params, [
      'title',
      'topic_ids',
      'content_markdown',
      'content_rendered' ])); };

  var ArticleApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    del: del$1,
    addFollow: addFollow,
    addVote: addVote$1,
    create: create,
    createComment: createComment$1,
    deleteFollow: deleteFollow,
    deleteMultiple: deleteMultiple$1,
    deleteVote: deleteVote$1,
    get: get$1,
    getComments: getComments$1,
    getFollowers: getFollowers,
    getList: getList$1,
    getVoters: getVoters$1,
    trash: trash$1,
    trashMultiple: trashMultiple$1,
    untrash: untrash$1,
    untrashMultiple: untrashMultiple$1,
    update: update$1
  });

  /**
   * 生成新的图形验证码
   * 生成新的图形验证码
   */
  var generate = function () { return postRequest(buildURL('/captchas', {})); };

  var CaptchaApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    generate: generate
  });

  /**
   * 删除评论
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除评论。评论作者是否可删除评论，由管理员在后台的设置决定。
   */
  var del$2 = function (params) { return deleteRequest(buildURL('/comments/{comment_id}', params)); };
  /**
   * 为评论投票
   * 为评论投票
   */
  var addVote$2 = function (params) { return postRequest(buildURL('/comments/{comment_id}/voters', params), buildRequestBody(params, ['type'])); };
  /**
   * 🔐批量删除评论
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有评论被删除，该接口都会返回成功。
   */
  var deleteMultiple$2 = function (params) { return deleteRequest(buildURL('/comments/{comment_ids}', params)); };
  /**
   * 取消为评论的投票
   * 取消为评论的投票
   */
  var deleteVote$2 = function (params) { return deleteRequest(buildURL('/comments/{comment_id}/voters', params)); };
  /**
   * 获取评论详情
   * 获取评论详情
   */
  var get$2 = function (params) { return getRequest(buildURL('/comments/{comment_id}', params, ['include'])); };
  /**
   * 获取所有评论
   * 获取所有评论。
   */
  var getList$2 = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/comments', params, [
      'page',
      'per_page',
      'order',
      'include',
      'comment_id',
      'commentable_id',
      'commentable_type',
      'user_id',
      'trashed' ]));
  };
  /**
   * 获取评论的投票者
   * 获取评论的投票者
   */
  var getVoters$2 = function (params) { return getRequest(buildURL('/comments/{comment_id}/voters', params, [
      'page',
      'per_page',
      'include',
      'type' ])); };
  /**
   * 🔐把评论放入回收站
   * 仅管理员可调用该接口
   */
  var trash$2 = function (params) { return postRequest(buildURL('/comments/{comment_id}/trash', params, ['include'])); };
  /**
   * 🔐批量把评论放入回收站
   * 仅管理员可调用该接口。
   */
  var trashMultiple$2 = function (params) { return postRequest(buildURL('/comments/{comment_ids}/trash', params, ['include'])); };
  /**
   * 🔐把评论移出回收站
   * 仅管理员可调用该接口。
   */
  var untrash$2 = function (params) { return postRequest(buildURL('/comments/{comment_id}/untrash', params, ['include'])); };
  /**
   * 🔐批量把评论移出回收站
   * 仅管理员可调用该接口。
   */
  var untrashMultiple$2 = function (params) { return postRequest(buildURL('/comments/{comment_ids}/untrash', params, ['include'])); };
  /**
   * 修改评论
   * 管理员可修改评论。评论作者是否可修改评论，由管理员在后台的设置决定。
   */
  var update$2 = function (params) { return patchRequest(buildURL('/comments/{comment_id}', params, ['include']), buildRequestBody(params, ['content'])); };

  var CommentApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    del: del$2,
    addVote: addVote$2,
    deleteMultiple: deleteMultiple$2,
    deleteVote: deleteVote$2,
    get: get$2,
    getList: getList$2,
    getVoters: getVoters$2,
    trash: trash$2,
    trashMultiple: trashMultiple$2,
    untrash: untrash$2,
    untrashMultiple: untrashMultiple$2,
    update: update$2
  });

  /**
   * 🔐发送邮件
   * 用于后台管理员发送邮件，需要管理员权限
   */
  var send = function (params) { return postRequest(buildURL('/emails', params), buildRequestBody(params, ['email', 'subject', 'content'])); };

  var EmailApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    send: send
  });

  /**
   * 🔐删除指定图片
   * 仅管理员可调用该接口
   */
  var del$3 = function (params) { return deleteRequest(buildURL('/images/{key}', params)); };
  /**
   * 🔐批量删除图片
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
   */
  var deleteMultiple$3 = function (params) { return deleteRequest(buildURL('/images/{keys}', params)); };
  /**
   * 获取指定图片信息
   * 获取指定图片信息
   */
  var get$3 = function (params) { return getRequest(buildURL('/images/{key}', params, ['include'])); };
  /**
   * 🔐获取图片列表
   * 仅管理员可调用该接口
   */
  var getList$3 = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/images', params, [
      'page',
      'per_page',
      'include',
      'key',
      'item_type',
      'item_id',
      'user_id' ]));
  };
  /**
   * 🔐更新指定图片信息
   * 仅管理员可调用该接口
   */
  var update$3 = function (params) { return patchRequest(buildURL('/images/{key}', params, ['include']), buildRequestBody(params, ['filename'])); };
  /**
   * 上传图片
   * 上传图片
   */
  var upload = function (params) { return postRequest(buildURL('/images', params, ['include']), buildRequestBody(params, ['image'])); };

  var ImageApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    del: del$3,
    deleteMultiple: deleteMultiple$3,
    get: get$3,
    getList: getList$3,
    update: update$3,
    upload: upload
  });

  /**
   * 获取站点全局设置参数
   * 获取站点全局设置参数
   */
  var get$4 = function () { return getRequest(buildURL('/options', {})); };
  /**
   * 🔐更新站点全局设置
   * 仅管理员可调用该接口
   */
  var update$4 = function (params) { return patchRequest(buildURL('/options', params), buildRequestBody(params, [
      'answer_can_delete',
      'answer_can_delete_before',
      'answer_can_delete_only_no_comment',
      'answer_can_edit',
      'answer_can_edit_before',
      'answer_can_edit_only_no_comment',
      'article_can_delete',
      'article_can_delete_before',
      'article_can_delete_only_no_comment',
      'article_can_edit',
      'article_can_edit_before',
      'article_can_edit_only_no_comment',
      'cache_memcached_host',
      'cache_memcached_password',
      'cache_memcached_port',
      'cache_memcached_username',
      'cache_prefix',
      'cache_redis_host',
      'cache_redis_password',
      'cache_redis_port',
      'cache_redis_username',
      'cache_type',
      'comment_can_delete',
      'comment_can_delete_before',
      'comment_can_edit',
      'comment_can_edit_before',
      'language',
      'question_can_delete',
      'question_can_delete_before',
      'question_can_delete_only_no_answer',
      'question_can_delete_only_no_comment',
      'question_can_edit',
      'question_can_edit_before',
      'question_can_edit_only_no_answer',
      'question_can_edit_only_no_comment',
      'site_description',
      'site_gongan_beian',
      'site_icp_beian',
      'site_keywords',
      'site_name',
      'site_static_url',
      'smtp_host',
      'smtp_password',
      'smtp_port',
      'smtp_reply_to',
      'smtp_secure',
      'smtp_username',
      'storage_aliyun_access_id',
      'storage_aliyun_access_secret',
      'storage_aliyun_bucket',
      'storage_aliyun_endpoint',
      'storage_ftp_host',
      'storage_ftp_passive',
      'storage_ftp_password',
      'storage_ftp_port',
      'storage_ftp_root',
      'storage_ftp_ssl',
      'storage_ftp_username',
      'storage_local_dir',
      'storage_qiniu_access_id',
      'storage_qiniu_access_secret',
      'storage_qiniu_bucket',
      'storage_qiniu_zone',
      'storage_sftp_host',
      'storage_sftp_password',
      'storage_sftp_port',
      'storage_sftp_root',
      'storage_sftp_username',
      'storage_type',
      'storage_upyun_bucket',
      'storage_upyun_operator',
      'storage_upyun_password',
      'storage_url',
      'theme' ])); };

  var OptionApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    get: get$4,
    update: update$4
  });

  /**
   * 删除提问
   * 只要没有错误异常，无论是否有回答被删除，该接口都会返回成功。  管理员可删除提问。提问作者是否可删除提问，由管理员在后台的设置决定。
   */
  var del$4 = function (params) { return deleteRequest(buildURL('/questions/{question_id}', params)); };
  /**
   * 添加关注
   * 添加关注
   */
  var addFollow$1 = function (params) { return postRequest(buildURL('/questions/{question_id}/followers', params)); };
  /**
   * 为提问投票
   * 为提问投票
   */
  var addVote$3 = function (params) { return postRequest(buildURL('/questions/{question_id}/voters', params), buildRequestBody(params, ['type'])); };
  /**
   * 发表提问
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   */
  var create$1 = function (params) { return postRequest(buildURL('/questions', params, ['include']), buildRequestBody(params, [
      'title',
      'topic_ids',
      'content_markdown',
      'content_rendered' ])); };
  /**
   * 在指定提问下发表回答
   * &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   */
  var createAnswer = function (params) { return postRequest(buildURL('/questions/{question_id}/answers', params, ['include']), buildRequestBody(params, ['content_markdown', 'content_rendered'])); };
  /**
   * 在指定提问下发表评论
   * 在指定提问下发表评论
   */
  var createComment$2 = function (params) { return postRequest(buildURL('/questions/{question_id}/comments', params, ['include']), buildRequestBody(params, ['content'])); };
  /**
   * 取消关注
   * 取消关注
   */
  var deleteFollow$1 = function (params) { return deleteRequest(buildURL('/questions/{question_id}/followers', params)); };
  /**
   * 🔐批量删除提问
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有提问被删除，该接口都会返回成功。
   */
  var deleteMultiple$4 = function (params) { return deleteRequest(buildURL('/questions/{question_ids}', params)); };
  /**
   * 取消为提问的投票
   * 取消为提问的投票
   */
  var deleteVote$3 = function (params) { return deleteRequest(buildURL('/questions/{question_id}/voters', params)); };
  /**
   * 获取指定提问信息
   * 获取指定提问信息
   */
  var get$5 = function (params) { return getRequest(buildURL('/questions/{question_id}', params, ['include'])); };
  /**
   * 获取指定提问下的回答
   * 获取指定提问下的回答。
   */
  var getAnswers = function (params) { return getRequest(buildURL('/questions/{question_id}/answers', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取指定提问的评论
   * 获取指定提问的评论。
   */
  var getComments$2 = function (params) { return getRequest(buildURL('/questions/{question_id}/comments', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取指定提问的关注者
   * 获取指定提问的关注者
   */
  var getFollowers$1 = function (params) { return getRequest(buildURL('/questions/{question_id}/followers', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取提问列表
   * 获取提问列表。
   */
  var getList$4 = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/questions', params, [
      'page',
      'per_page',
      'order',
      'include',
      'question_id',
      'user_id',
      'topic_id',
      'trashed' ]));
  };
  /**
   * 获取提问的投票者
   * 获取提问的投票者
   */
  var getVoters$3 = function (params) { return getRequest(buildURL('/questions/{question_id}/voters', params, [
      'page',
      'per_page',
      'include',
      'type' ])); };
  /**
   * 🔐把提问放入回收站
   * 仅管理员可调用该接口
   */
  var trash$3 = function (params) { return postRequest(buildURL('/questions/{question_id}/trash', params, ['include'])); };
  /**
   * 🔐批量把提问放入回收站
   * 仅管理员可调用该接口。
   */
  var trashMultiple$3 = function (params) { return postRequest(buildURL('/questions/{question_ids}/trash', params, ['include'])); };
  /**
   * 🔐把提问移出回收站
   * 仅管理员可调用该接口。
   */
  var untrash$3 = function (params) { return postRequest(buildURL('/questions/{question_id}/untrash', params, ['include'])); };
  /**
   * 🔐批量把提问移出回收站
   * 仅管理员可调用该接口。
   */
  var untrashMultiple$3 = function (params) { return postRequest(buildURL('/questions/{question_ids}/untrash', params, ['include'])); };
  /**
   * 更新提问信息
   * 管理员可修改提问。提问作者是否可修改提问，由管理员在后台的设置决定。  &#x60;content_markdown&#x60; 和 &#x60;content_rendered&#x60; 两个参数仅传入其中一个即可， 若两个参数都传入，则以 &#x60;content_markdown&#x60; 为准。
   */
  var update$5 = function (params) { return patchRequest(buildURL('/questions/{question_id}', params, ['include']), buildRequestBody(params, [
      'title',
      'topic_ids',
      'content_markdown',
      'content_rendered' ])); };

  var QuestionApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    del: del$4,
    addFollow: addFollow$1,
    addVote: addVote$3,
    create: create$1,
    createAnswer: createAnswer,
    createComment: createComment$2,
    deleteFollow: deleteFollow$1,
    deleteMultiple: deleteMultiple$4,
    deleteVote: deleteVote$3,
    get: get$5,
    getAnswers: getAnswers,
    getComments: getComments$2,
    getFollowers: getFollowers$1,
    getList: getList$4,
    getVoters: getVoters$3,
    trash: trash$3,
    trashMultiple: trashMultiple$3,
    untrash: untrash$3,
    untrashMultiple: untrashMultiple$3,
    update: update$5
  });

  /**
   * 🔐删除举报
   * 仅管理员可调用该接口
   */
  var del$5 = function (params) { return deleteRequest(buildURL('/reports/{reportable_type}:{reportable_id}', params)); };
  /**
   * 添加举报
   * 添加举报
   */
  var create$2 = function (params) { return postRequest(buildURL('/reports/{reportable_type}:{reportable_id}', params, ['include']), buildRequestBody(params, ['reason'])); };
  /**
   * 🔐批量删除举报
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有记录被删除，该接口都会返回成功。
   */
  var deleteMultiple$5 = function (params) { return deleteRequest(buildURL('/reports/{report_targets}', params)); };
  /**
   * 🔐获取被举报的内容列表
   * 仅管理员可调用该接口
   */
  var getList$5 = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/reports', params, [
      'page',
      'per_page',
      'include',
      'reportable_type' ]));
  };
  /**
   * 🔐获取被举报内容的举报详情
   * 仅管理员可调用该接口
   */
  var getReasons = function (params) { return getRequest(buildURL('/reports/{reportable_type}:{reportable_id}', params, [
      'page',
      'per_page',
      'include' ])); };

  var ReportApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    del: del$5,
    create: create$2,
    deleteMultiple: deleteMultiple$5,
    getList: getList$5,
    getReasons: getReasons
  });

  var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

  function createCommonjsModule(fn, module) {
  	return module = { exports: {} }, fn(module, module.exports), module.exports;
  }

  var sha1 = createCommonjsModule(function (module, exports) {
  (function(){

      //消息填充位，补足长度。
      function fillString(str){
          var blockAmount = ((str.length + 8) >> 6) + 1,
              blocks = [],
              i;

          for(i = 0; i < blockAmount * 16; i++){
              blocks[i] = 0;
          }
          for(i = 0; i < str.length; i++){
              blocks[i >> 2] |= str.charCodeAt(i) << (24 - (i & 3) * 8);
          }
          blocks[i >> 2] |= 0x80 << (24 - (i & 3) * 8);
          blocks[blockAmount * 16 - 1] = str.length * 8;

          return blocks;
      }

      //将输入的二进制数组转化为十六进制的字符串。
      function binToHex(binArray){
          var hexString = "0123456789abcdef",
              str = "",
              i;

          for(i = 0; i < binArray.length * 4; i++){
              str += hexString.charAt((binArray[i >> 2] >> ((3 - i % 4) * 8 + 4)) & 0xF) +
                      hexString.charAt((binArray[i >> 2] >> ((3 - i % 4) * 8  )) & 0xF);
          }

          return str;
      }

      //核心函数，输出为长度为5的number数组，对应160位的消息摘要。
      function coreFunction(blockArray){
          var w = [],
              a = 0x67452301,
              b = 0xEFCDAB89,
              c = 0x98BADCFE,
              d = 0x10325476,
              e = 0xC3D2E1F0,
              olda,
              oldb,
              oldc,
              oldd,
              olde,
              t,
              i,
              j;

          for(i = 0; i < blockArray.length; i += 16){  //每次处理512位 16*32
              olda = a;
              oldb = b;
              oldc = c;
              oldd = d;
              olde = e;

              for(j = 0; j < 80; j++){  //对每个512位进行80步操作
                  if(j < 16){
                      w[j] = blockArray[i + j];
                  }else{
                      w[j] = cyclicShift(w[j-3] ^ w[j-8] ^ w[j-14] ^ w[j-16], 1);
                  }
                  t = modPlus(modPlus(cyclicShift(a, 5), ft(j, b, c, d)), modPlus(modPlus(e, w[j]), kt(j)));
                  e = d;
                  d = c;
                  c = cyclicShift(b, 30);
                  b = a;
                  a = t;
              }

              a = modPlus(a, olda);
              b = modPlus(b, oldb);
              c = modPlus(c, oldc);
              d = modPlus(d, oldd);
              e = modPlus(e, olde);
          }

          return [a, b, c, d, e];
      }

      //根据t值返回相应得压缩函数中用到的f函数。
      function ft(t, b, c, d){
          if(t < 20){
              return (b & c) | ((~b) & d);
          }else if(t < 40){
              return b ^ c ^ d;
          }else if(t < 60){
              return (b & c) | (b & d) | (c & d);
          }else{
              return b ^ c ^ d;
          }
      }

      //根据t值返回相应得压缩函数中用到的K值。
      function kt(t){
          return (t < 20) ?  0x5A827999 :
                  (t < 40) ? 0x6ED9EBA1 :
                  (t < 60) ? 0x8F1BBCDC : 0xCA62C1D6;
      }

      //模2的32次方加法，因为JavaScript的number是双精度浮点数表示，所以将32位数拆成高16位和低16位分别进行相加
      function modPlus(x, y){
          var low = (x & 0xFFFF) + (y & 0xFFFF),
              high = (x >> 16) + (y >> 16) + (low >> 16);

          return (high << 16) | (low & 0xFFFF);
      }

      //对输入的32位的num二进制数进行循环左移 ,因为JavaScript的number是双精度浮点数表示，所以移位需需要注意
      function cyclicShift(num, k){
          return (num << k) | (num >>> (32 - k));
      }

      //主函数根据输入的消息字符串计算消息摘要，返回十六进制表示的消息摘要
      function sha1(s){
          return binToHex(coreFunction(fillString(s)));
      }

      // support AMD and Node
      {
          if( module.exports) {
            exports = module.exports = sha1;
          }
          exports.sha1 = sha1;
      }

  }).call(commonjsGlobal);
  });
  var sha1_1 = sha1.sha1;

  // @ts-ignore
  /**
   * 生成 Token
   * 通过账号密码登陆，返回 Token 信息。  若登录失败，且返回信息中含参数 &#x60;captcha_token&#x60; 和 &#x60;captcha_image&#x60;， 表示下次调用该接口时，需要用户输入图形验证码，并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   */
  var login = function (params) {
      params.password = sha1(params.password);
      return postRequest(buildURL('/tokens', params), buildRequestBody(params, [
          'name',
          'password',
          'device',
          'captcha_token',
          'captcha_code' ]));
  };

  var TokenApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    login: login
  });

  /**
   * 🔐删除话题
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。
   */
  var del$6 = function (params) { return deleteRequest(buildURL('/topics/{topic_id}', params)); };
  /**
   * 关注指定话题
   * 关注指定话题
   */
  var addFollow$2 = function (params) { return postRequest(buildURL('/topics/{topic_id}/followers', params)); };
  /**
   * 🔐发布话题
   * 仅管理员可调用该接口
   */
  var create$3 = function (params) { return postRequest(buildURL('/topics', params, ['include']), buildRequestBody(params, ['name', 'description', 'cover'])); };
  /**
   * 取消关注指定话题
   * 取消关注指定话题
   */
  var deleteFollow$2 = function (params) { return deleteRequest(buildURL('/topics/{topic_id}/followers', params)); };
  /**
   * 🔐批量删除话题
   * 仅管理员可调用该接口。 只要没有错误异常，无论是否有话题被删除，该接口都会返回成功。
   */
  var deleteMultiple$6 = function (params) { return deleteRequest(buildURL('/topics/{topic_ids}', params)); };
  /**
   * 获取指定话题信息
   * 获取指定话题信息
   */
  var get$6 = function (params) { return getRequest(buildURL('/topics/{topic_id}', params, ['include'])); };
  /**
   * 获取指定话题下的文章
   * 获取指定话题下的文章。
   */
  var getArticles = function (params) { return getRequest(buildURL('/topics/{topic_id}/articles', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取指定话题的关注者
   * 不含已禁用的用户
   */
  var getFollowers$2 = function (params) { return getRequest(buildURL('/topics/{topic_id}/followers', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取全部话题
   * 获取全部话题。
   */
  var getList$6 = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/topics', params, [
      'page',
      'per_page',
      'include',
      'order',
      'topic_id',
      'name',
      'trashed' ]));
  };
  /**
   * 获取指定话题下的提问
   * 获取指定话题下的提问。
   */
  var getQuestions = function (params) { return getRequest(buildURL('/topics/{topic_id}/questions', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 🔐把话题放入回收站
   * 仅管理员可调用该接口
   */
  var trash$4 = function (params) { return postRequest(buildURL('/topics/{topic_id}/trash', params, ['include'])); };
  /**
   * 🔐批量把话题放入回收站
   * 仅管理员可调用该接口。
   */
  var trashMultiple$4 = function (params) { return postRequest(buildURL('/topics/{topic_ids}/trash', params, ['include'])); };
  /**
   * 🔐把话题移出回收站
   * 仅管理员可调用该接口。
   */
  var untrash$4 = function (params) { return postRequest(buildURL('/topics/{topic_id}/untrash', params, ['include'])); };
  /**
   * 🔐批量把话题移出回收站
   * 仅管理员可调用该接口。
   */
  var untrashMultiple$4 = function (params) { return postRequest(buildURL('/topics/{topic_ids}/untrash', params, ['include'])); };
  /**
   * 🔐更新话题信息
   * **仅管理员可调用该接口**  因为 formData 类型的数据只能通过 post 请求提交，所以这里不用 patch 请求
   */
  var update$6 = function (params) { return postRequest(buildURL('/topics/{topic_id}', params, ['include']), buildRequestBody(params, ['name', 'description', 'cover'])); };

  var TopicApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    del: del$6,
    addFollow: addFollow$2,
    create: create$3,
    deleteFollow: deleteFollow$2,
    deleteMultiple: deleteMultiple$6,
    get: get$6,
    getArticles: getArticles,
    getFollowers: getFollowers$2,
    getList: getList$6,
    getQuestions: getQuestions,
    trash: trash$4,
    trashMultiple: trashMultiple$4,
    untrash: untrash$4,
    untrashMultiple: untrashMultiple$4,
    update: update$6
  });

  // @ts-ignore
  /**
   * 添加关注
   * 添加关注
   */
  var addFollow$3 = function (params) { return postRequest(buildURL('/users/{user_id}/followers', params)); };
  /**
   * 🔐删除指定用户的头像，并重置为默认头像
   * 仅管理员可调用该接口
   */
  var deleteAvatar = function (params) { return deleteRequest(buildURL('/users/{user_id}/avatar', params)); };
  /**
   * 🔐删除指定用户的封面，并重置为默认封面
   * 仅管理员可调用该接口
   */
  var deleteCover = function (params) { return deleteRequest(buildURL('/users/{user_id}/cover', params)); };
  /**
   * 取消关注
   * 取消关注
   */
  var deleteFollow$3 = function (params) { return deleteRequest(buildURL('/users/{user_id}/followers', params)); };
  /**
   * 删除当前登录用户的头像，并重置为默认头像
   * 删除当前登录用户的头像，并重置为默认头像
   */
  var deleteMyAvatar = function () { return deleteRequest(buildURL('/user/avatar', {})); };
  /**
   * 删除当前登录用户的封面，并重置为默认封面
   * 删除当前登录用户的封面，并重置为默认封面
   */
  var deleteMyCover = function () { return deleteRequest(buildURL('/user/cover', {})); };
  /**
   * 🔐禁用指定用户
   * 仅管理员可调用该接口
   */
  var disable = function (params) { return postRequest(buildURL('/users/{user_id}/disable', params, ['include'])); };
  /**
   * 🔐批量禁用用户
   * 仅管理员可调用该接口。
   */
  var disableMultiple = function (params) { return postRequest(buildURL('/users/{user_ids}/disable', params, ['include'])); };
  /**
   * 🔐恢复指定用户
   * 仅管理员可调用该接口。
   */
  var enable = function (params) { return postRequest(buildURL('/users/{user_id}/enable', params, ['include'])); };
  /**
   * 🔐批量恢复用户
   * 仅管理员可调用该接口。
   */
  var enableMultiple = function (params) { return postRequest(buildURL('/users/{user_ids}/enable', params, ['include'])); };
  /**
   * 获取指定用户信息
   * 若是管理员调用该接口、或当前登录用户读取自己的个人信息，将返回用户的所有信息。 其他情况仅返回部分字段（去掉了隐私信息，隐私字段已用 🔐 标明）
   */
  var get$7 = function (params) { return getRequest(buildURL('/users/{user_id}', params, ['include'])); };
  /**
   * 获取指定用户发表的回答
   * 获取指定用户发表的回答
   */
  var getAnswers$1 = function (params) { return getRequest(buildURL('/users/{user_id}/answers', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取指定用户发表的文章
   * 获取指定用户发表的文章
   */
  var getArticles$1 = function (params) { return getRequest(buildURL('/users/{user_id}/articles', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取指定用户发表的评论
   * 获取指定用户发表的评论
   */
  var getComments$3 = function (params) { return getRequest(buildURL('/users/{user_id}/comments', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 获取指定用户关注的用户列表
   * 获取指定用户关注的用户列表
   */
  var getFollowees = function (params) { return getRequest(buildURL('/users/{user_id}/followees', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取指定用户的关注者
   * 获取指定用户的关注者
   */
  var getFollowers$3 = function (params) { return getRequest(buildURL('/users/{user_id}/followers', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取指定用户关注的文章列表
   * 获取指定用户关注的文章列表
   */
  var getFollowingArticles = function (params) { return getRequest(buildURL('/users/{user_id}/following_articles', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取指定用户关注的提问列表
   * 获取指定用户关注的提问列表
   */
  var getFollowingQuestions = function (params) { return getRequest(buildURL('/users/{user_id}/following_questions', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取指定用户关注的话题列表
   * 获取指定用户关注的话题列表
   */
  var getFollowingTopics = function (params) { return getRequest(buildURL('/users/{user_id}/following_topics', params, [
      'page',
      'per_page',
      'include' ])); };
  /**
   * 获取用户列表
   * 仅管理员可使用 email 参数进行搜索  仅管理员可获取已禁用的用户列表
   */
  var getList$7 = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/users', params, [
      'page',
      'per_page',
      'order',
      'include',
      'user_id',
      'username',
      'email',
      'disabled' ]));
  };
  /**
   * 获取当前登录用户的信息
   * 获取当前登录用户的信息
   */
  var getMine = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user', params, ['include']));
  };
  /**
   * 获取当前登录用户发表的回答
   * 获取当前登录用户发表的回答
   */
  var getMyAnswers = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/answers', params, ['page', 'per_page', 'order', 'include']));
  };
  /**
   * 获取当前登录用户发表的文章
   * 获取当前登录用户发表的文章
   */
  var getMyArticles = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/articles', params, [
      'page',
      'per_page',
      'order',
      'include' ]));
  };
  /**
   * 获取当前登录用户发表的评论
   * 获取当前登录用户发表的评论
   */
  var getMyComments = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/comments', params, [
      'page',
      'per_page',
      'order',
      'include' ]));
  };
  /**
   * 获取当前登录用户关注的用户
   * 获取当前登录用户关注的用户
   */
  var getMyFollowees = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/followees', params, ['page', 'per_page', 'include']));
  };
  /**
   * 获取当前登录用户的关注者
   * 获取当前登录用户的关注者
   */
  var getMyFollowers = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/followers', params, ['page', 'per_page', 'include']));
  };
  /**
   * 获取登录用户关注的文章
   * 获取登录用户关注的文章
   */
  var getMyFollowingArticles = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/following_articles', params, [
      'page',
      'per_page',
      'include' ]));
  };
  /**
   * 获取登录用户关注的提问
   * 获取登录用户关注的提问
   */
  var getMyFollowingQuestions = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/following_questions', params, [
      'page',
      'per_page',
      'include' ]));
  };
  /**
   * 获取登录用户关注的话题
   * 获取登录用户关注的话题
   */
  var getMyFollowingTopics = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/following_topics', params, ['page', 'per_page', 'include']));
  };
  /**
   * 获取登录用户发表的提问
   * 获取登录用户发表的提问
   */
  var getMyQuestions = function (params) {
      if ( params === void 0 ) params = {};

      return getRequest(buildURL('/user/questions', params, [
      'page',
      'per_page',
      'order',
      'include' ]));
  };
  /**
   * 获取指定用户发表的提问
   * 获取指定用户发表的提问
   */
  var getQuestions$1 = function (params) { return getRequest(buildURL('/users/{user_id}/questions', params, [
      'page',
      'per_page',
      'order',
      'include' ])); };
  /**
   * 验证邮箱并创建账号
   * 返回用户信息
   */
  var register = function (params) {
      params.password = sha1(params.password);
      return postRequest(buildURL('/users', params, ['include']), buildRequestBody(params, ['email', 'email_code', 'username', 'password']));
  };
  /**
   * 发送重置密码邮箱验证码
   * 若返回参数中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   */
  var sendPasswordResetEmail = function (params) { return postRequest(buildURL('/user/password/email', params), buildRequestBody(params, ['email', 'captcha_token', 'captcha_code'])); };
  /**
   * 发送注册邮箱验证码
   * 若返回信息中含参数 captcha_token 和 captcha_image，表示下次调用该接口时，需要用户输入图形验证码， 并把 &#x60;captcha_token&#x60; 和 &#x60;captcha_code&#x60; 参数传递到服务端。
   */
  var sendRegisterEmail = function (params) { return postRequest(buildURL('/user/register/email', params), buildRequestBody(params, ['email', 'captcha_token', 'captcha_code'])); };
  /**
   * 🔐更新指定用户信息
   * 仅管理员可调用该接口
   */
  var update$7 = function (params) { return patchRequest(buildURL('/users/{user_id}', params, ['include']), buildRequestBody(params, [
      'headline',
      'bio',
      'blog',
      'company',
      'location' ])); };
  /**
   * 更新当前登录用户信息
   * 更新当前登录用户信息
   */
  var updateMine = function (params) { return patchRequest(buildURL('/user', params, ['include']), buildRequestBody(params, [
      'headline',
      'bio',
      'blog',
      'company',
      'location' ])); };
  /**
   * 验证邮箱并更新密码
   * 验证邮箱并更新密码
   */
  var updatePassword = function (params) {
      params.password = sha1(params.password);
      return putRequest(buildURL('/user/password', params), buildRequestBody(params, ['email', 'email_code', 'password']));
  };
  /**
   * 上传当前登录用户的头像
   * 上传当前登录用户的头像
   */
  var uploadMyAvatar = function (params) { return postRequest(buildURL('/user/avatar', params), buildRequestBody(params, ['avatar'])); };
  /**
   * 上传当前登录用户的封面
   * 上传当前登录用户的封面
   */
  var uploadMyCover = function (params) { return postRequest(buildURL('/user/cover', params), buildRequestBody(params, ['cover'])); };

  var UserApi = /*#__PURE__*/Object.freeze({
    __proto__: null,
    addFollow: addFollow$3,
    deleteAvatar: deleteAvatar,
    deleteCover: deleteCover,
    deleteFollow: deleteFollow$3,
    deleteMyAvatar: deleteMyAvatar,
    deleteMyCover: deleteMyCover,
    disable: disable,
    disableMultiple: disableMultiple,
    enable: enable,
    enableMultiple: enableMultiple,
    get: get$7,
    getAnswers: getAnswers$1,
    getArticles: getArticles$1,
    getComments: getComments$3,
    getFollowees: getFollowees,
    getFollowers: getFollowers$3,
    getFollowingArticles: getFollowingArticles,
    getFollowingQuestions: getFollowingQuestions,
    getFollowingTopics: getFollowingTopics,
    getList: getList$7,
    getMine: getMine,
    getMyAnswers: getMyAnswers,
    getMyArticles: getMyArticles,
    getMyComments: getMyComments,
    getMyFollowees: getMyFollowees,
    getMyFollowers: getMyFollowers,
    getMyFollowingArticles: getMyFollowingArticles,
    getMyFollowingQuestions: getMyFollowingQuestions,
    getMyFollowingTopics: getMyFollowingTopics,
    getMyQuestions: getMyQuestions,
    getQuestions: getQuestions$1,
    register: register,
    sendPasswordResetEmail: sendPasswordResetEmail,
    sendRegisterEmail: sendRegisterEmail,
    update: update$7,
    updateMine: updateMine,
    updatePassword: updatePassword,
    uploadMyAvatar: uploadMyAvatar,
    uploadMyCover: uploadMyCover
  });

  defaults.adapter = new defaultExport$1();

  exports.AnswerApi = AnswerApi;
  exports.ArticleApi = ArticleApi;
  exports.CaptchaApi = CaptchaApi;
  exports.CommentApi = CommentApi;
  exports.EmailApi = EmailApi;
  exports.ImageApi = ImageApi;
  exports.OptionApi = OptionApi;
  exports.QuestionApi = QuestionApi;
  exports.ReportApi = ReportApi;
  exports.TokenApi = TokenApi;
  exports.TopicApi = TopicApi;
  exports.UserApi = UserApi;
  exports.defaults = defaults;

  Object.defineProperty(exports, '__esModule', { value: true });

})));
//# sourceMappingURL=mdclub-sdk.js.map
