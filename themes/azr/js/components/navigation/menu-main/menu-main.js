function addToggler(name) {
  var toggler = document.querySelector('[data-drupal-selector="'+name+'-toggle"]');
  var item = document.querySelector('[data-drupal-selector="'+name+'"]');

  function toggleItem() {
    toggler.classList.toggle(name+'-toggle--active');
    item.classList.toggle(name+'--active');
    return false;
  }

  toggler.addEventListener('click', toggleItem);
};

(function() {
  addToggler("menu-main");
  addToggler("address");
  addToggler("phones");
  addToggler("schedule");
})();
