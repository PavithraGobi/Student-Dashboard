// Toggle sidebar submenus and save state
function toggleSettings(el) {
  const label = el.textContent.trim();
  localStorage.setItem('openSubmenu', label);

  // Close other submenus
  document.querySelectorAll('.settings-toggle').forEach(item => {
    if (item !== el) {
      item.classList.remove('open');
      const otherSubmenu = item.nextElementSibling;
      if (otherSubmenu && otherSubmenu.classList.contains('submenu')) {
        otherSubmenu.style.display = 'none';
        const arrow = item.querySelector('.arrow');
        if (arrow) arrow.textContent = '▼';
      }
    }
  });

  // Toggle this submenu
  el.classList.toggle('open');
  const submenu = el.nextElementSibling;
  if (submenu && submenu.classList.contains('submenu')) {
    const isOpen = submenu.style.display === 'block';
    submenu.style.display = isOpen ? 'none' : 'block';
    const arrow = el.querySelector('.arrow');
    if (arrow) arrow.textContent = isOpen ? '▼' : '▲';
  }
}
// Toggle profile dropdown
function toggleProfileDropdown() {
  document.getElementById('profileMenu').classList.toggle('show');
}

// Hide dropdown if clicking outside
window.onclick = function(e) {
  if (!e.target.matches('.avatar')) {
    const dropdown = document.getElementById("profileDropdown");
    if (dropdown && dropdown.parentElement.classList.contains("show")) {
      dropdown.parentElement.classList.remove("show");
    }
  }
};


// Restore submenu open state on page load
window.addEventListener('DOMContentLoaded', () => {
  const savedLabel = localStorage.getItem('openSubmenu');
  if (savedLabel) {
    document.querySelectorAll('.settings-toggle').forEach(item => {
      if (item.textContent.trim() === savedLabel) {
        item.classList.add('open');
        const submenu = item.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) {
          submenu.style.display = 'block';
          const arrow = item.querySelector('.arrow');
          if (arrow) arrow.textContent = '▲';
        }
      }
    });
  }
});

