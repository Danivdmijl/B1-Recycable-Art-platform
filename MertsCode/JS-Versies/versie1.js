document.addEventListener("DOMContentLoaded", function () {
    const searchDiv = document.querySelector('.gc-header__search');
  
    if (searchDiv && !searchDiv.querySelector('.custom-placeholder')) {
      const placeholder = document.createElement('span');
      placeholder.className = 'custom-placeholder';
      placeholder.textContent = 'Search projects or re-makers...';
      searchDiv.appendChild(placeholder);
    }
  });
  
  