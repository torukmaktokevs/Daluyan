//DOM Elements
const bars = document.querySelector(".fa-bars");
const mobileMenuFixed = document.querySelector(".mobile-menu-fixed");
const menu = document.querySelector(".mobile-menu-container ");

if (!bars || !mobileMenuFixed || !menu) {
  //Elements not found, don't run the script
} else {

function openMenu() {
  mobileMenuFixed.classList.add('is-open');
}

function closeMenu() {
  mobileMenuFixed.classList.remove('is-open');
}

function toggleMenu() {
  if(mobileMenuFixed.classList.contains('is-open')) {
    closeMenu();
  } else {
    openMenu();
  }
}

//Toggle Menu on click
bars.addEventListener('click', function (e) {
  e.stopPropagation();
  toggleMenu();
})

//Close when a link inside the menu is clicked
menu.addEventListener('click', function (e) {
  const link = e.target.closest('.mobile-menu-link');
  if (link) {
    closeMenu();
  }
})

//Close when clicking outside the menu (or anywhere in the document)
document.addEventListener("click", function(e) {
const clickedInsideMenu = menu.contains(e.target);
const clickedBars = bars.contains(e.target);

if (!clickedInsideMenu && !clickedBars) {
  closeMenu();
}
})

}
document.addEventListener('DOMContentLoaded', function() {
  // CSRF token for AJAX requests
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;
  if (csrfToken) {
    // Make it available globally
    window.csrfToken = csrfToken;
  }

    // Your existing template JavaScript
    console.log('Tenant dashboard loaded!');
});