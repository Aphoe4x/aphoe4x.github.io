(function () {
  "use strict";

  document.querySelectorAll(".php-email-form").forEach(function (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const loading     = form.querySelector(".loading");
      const errorMsg    = form.querySelector(".error-message");
      const successMsg  = form.querySelector(".sent-message");

      loading.style.display    = "block";
      errorMsg.style.display   = "none";
      successMsg.style.display = "none";

      fetch(form.action, {
        method: "POST",
        body: new FormData(form),
      })
        .then((res) => res.text())
        .then((data) => {
          loading.style.display = "none";
          if (data.trim() === "OK") {
            successMsg.style.display = "block";
            form.reset();
          } else {
            errorMsg.textContent   = data;
            errorMsg.style.display = "block";
          }
        })
        .catch(() => {
          loading.style.display  = "none";
          errorMsg.textContent   = "Something went wrong. Please try again.";
          errorMsg.style.display = "block";
        });
    });
  });
})();