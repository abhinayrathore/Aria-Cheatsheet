(function (D, W) {
  var modalWrapper = D.createElement('div'),
    modalOpen = false,
    modal,
    modalTitle,
    iFrame,
    triggerElem;

  modalWrapper.innerHTML = '<link rel=stylesheet href="c/modal.min.css">' +
    '<div class=modal id=modal tabindex=-1 role=dialog>' +
      '<div class="modal-dialog" role="document">' +
        '<div class="modal-content">' +
          '<div class="modal-header">' +
            '<button type=button class="modal-close" aria-label="Close"><span aria-hidden="true" class="modal-close">&times;</span></button>' +
            '<h4 id="modal-title">Deal</h4>' +
          '</div>' +
          '<div class="modal-body"><iframe id="iframe" sandbox="allow-forms allow-popups" src="loading.htm"></iframe></div>' +
        '</div>' +
      '</div>' +
    '</div>';
  
  D.body.appendChild(modalWrapper);

  modal = D.getElementById("modal");
  modalTitle = D.getElementById("modal-title");
  iFrame = D.getElementById("iframe");

  // Contain focus in the modal for accessibility
  D.addEventListener("focus", function(event) {
    if (modalOpen && !modal.contains(event.target)) {
      event.stopPropagation();
      modal.focus();
    }
  }, true);

  // Handle ESC key to close the modal if open
  D.addEventListener("keydown", function(event) {
    if (modalOpen && event.keyCode == 27) {
      closeModal();
    }
  });

  // Click handler for opening and cloding the modal
  D.addEventListener("click", function(event) {
    var el = event.target,
      className = el.className;

    if (className === "e") {
      event.preventDefault();
      triggerElem = el;
      openModal(el.innerHTML, el.href);
    }

    // If clicked on modal close button or overlay, close the modal.
    if (className === "modal-close" || className === "modal") {
      closeModal();
    }
  });

  // Open modal and set focus
  function openModal (title, url) {
    modalTitle.innerHTML = title;
    iFrame.src = url;
    modal.style.display = 'block';
    modal.focus();
    modalOpen = true;
    D.body.className = 'modal-active';

    if (W.innerHeight > 500) {
      iFrame.style.height = (W.innerHeight - 100) + 'px';
    }
  }

  function closeModal() {
    modalOpen = false;
    iFrame.src = 'loading.htm';
    modal.style.display = 'none';
    D.body.className = '';
    
    if (triggerElem) {
      triggerElem.focus();
    }
  }
})(document, window);