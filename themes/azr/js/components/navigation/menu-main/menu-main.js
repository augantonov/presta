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

for (e of document.querySelectorAll(".toggler")) {
  let toggle_target_id = e.getAttribute("toggle-target");
  let toggle_target = document.getElementById(toggle_target_id);
  e.addEventListener('click', function() {
    for (t of document.querySelectorAll(".toggle")) {
    	if (t != toggle_target) {
    		t.classList.remove('toggle-active');
      }
    }
    toggle_target.classList.toggle('toggle-active');
  });
