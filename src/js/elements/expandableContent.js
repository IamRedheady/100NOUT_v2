(() => {
  const activeContentClass = 'expandable-content--active';
  const expandContentBtnTitleDataKey = 'expandText';
  const collapseContentBtnTitleDataKey = 'collapseText';
  function toggleContent(event) {
    const button = event.target;
    const targetBlock = document.querySelector(button.dataset.contentSelector);

    if (targetBlock) {
      targetBlock.classList.toggle(activeContentClass);

      button.innerText = targetBlock.classList.contains(activeContentClass)
        ? button.dataset[expandContentBtnTitleDataKey]
        : button.dataset[collapseContentBtnTitleDataKey];
    } else {
      button.removeEventListener('click', toggleContent);
    }
  }
  document.querySelectorAll('.expandable-content__toggle').forEach(function (button) {
    if (button.dataset.contentSelector) {
      button.addEventListener('click', toggleContent);
    }
  });
})();
