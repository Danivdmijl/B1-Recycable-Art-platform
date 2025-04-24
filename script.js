// Auction Klok
function updateClock() {
    const clock = document.getElementById("clock");
    if (clock) {
      const now = new Date();
      clock.textContent = now.toLocaleTimeString('en-GB');
    }
  }

  setInterval(updateClock, 1000);
  updateClock();


// Left Side Menu active state
document.addEventListener("DOMContentLoaded", function () {
    const currentUrl = window.location.href;
    const navItems = document.querySelectorAll(".sidebar .nav-item");

    navItems.forEach((item) => {
      if (currentUrl.startsWith(item.href)) {
        item.classList.add("active");
      } else {
        item.classList.remove("active");
      }
    });
  });