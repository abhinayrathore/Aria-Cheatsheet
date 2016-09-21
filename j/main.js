(function (D) {
  addScript('j/modal.min.js');
  addScript('j/extra.min.js');

  function addScript(src) {
    var script = D.createElement('script');
    script.src = src;
    D.body.appendChild(script);
  }
})(document);