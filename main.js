document.addEventListener("DOMContentLoaded", function () {
    const authControl = document.getElementById("authControl");
    const isLoggedIn = localStorage.getItem("isLoggedIn");
  
    if (isLoggedIn === "yes") {
      authControl.innerHTML = `
        <div class="container">
          <img src="Profile.png" alt="Menu" id="menuIcon" style="width: 50px; padding: 0; margin: 0; cursor: pointer;">
          <div id="popupMenu" class="popup" style="display: none;">
            <span class="close" style="cursor:pointer;">&times;</span>
            <ul>
              <li><a href="#">Opsi 1</a></li>
              <li><a href="#">Opsi 2</a></li>
              <li><a href="#">Opsi 3</a></li>
            </ul>
          </div>
        </div>
      `;
  
      // Show/hide menu logic
      const menuIcon = document.getElementById("menuIcon");
      const popupMenu = document.getElementById("popupMenu");
      const closeBtn = popupMenu.querySelector(".close");
  
      menuIcon.addEventListener("click", () => {
        popupMenu.style.display = "block";
      });
  
      closeBtn.addEventListener("click", () => {
        popupMenu.style.display = "none";
      });
  
      // Optional: hide popup when clicking outside
      window.addEventListener("click", (e) => {
        if (!popupMenu.contains(e.target) && e.target !== menuIcon) {
          popupMenu.style.display = "none";
        }
      });
    } else {
      authControl.innerHTML = `<button class="login-nav" onClick="toLogin()">Login</button>`;
    }
  });

  function toLogin(){
    window.location.href = "login.html";
  }
  