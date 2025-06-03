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
  const currentUrl = window.location.href.replace(/\/$/, ""); // remove trailing slash
  const navItems = document.querySelectorAll(".sidebar .nav-item");

  navItems.forEach((item) => {
    const itemUrl = item.href.replace(/\/$/, ""); // also remove trailing slash from href
    if (currentUrl === itemUrl) {
      item.classList.add("active");
    } else {
      item.classList.remove("active");
    }
  });
});



// Onboarding Register
  function togglePassword(el) {
    const input = el.previousElementSibling;
    input.type = input.type === "password" ? "text" : "password";
  }

  document.addEventListener("DOMContentLoaded", function () {
    if (window.isUserLoggedIn) {
      const loginButton = document.querySelector(".aratlogin");
      if (loginButton) {
        loginButton.style.display = "none";
      }
    }
  });