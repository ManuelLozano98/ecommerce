const BASE_URL = "/Ecommerce";
setupPasswordButtons();
$("#loginForm").submit(function (e) {
  e.preventDefault();
  username = $("#username").val();
  password = $("#password").val();

  if (username && password) {
    fetch("login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        login: username,
        password: password,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.error) {
          loginInvalid();
        }
        if (data.success) {
          window.location.href = data.redirect || BASE_URL;
        }
      })
      .catch((error) => {
        Swal.fire({
          title: "Unexpected error",
          text: error,
          icon: "error",
          confirmButtonColor: "#3085d6",
          showCloseButton: true,
        });
        console.error(error);
      });
  }
});

function setupPasswordButtons() {
  $("#passwordlock").click(function () {
    toggleIcon(this.id);
    toggleInput("password");
  });
}

function toggleIcon(elementId) {
  if ($("#" + elementId).attr("class") === "fas fa-lock") {
    $("#" + elementId).attr("class", "fas fa-unlock");
  } else {
    $("#" + elementId).attr("class", "fas fa-lock");
  }
}
function toggleInput(elementId) {
  if ($("#" + elementId).attr("type") === "password") {
    $("#" + elementId).attr("type", "text");
  } else {
    $("#" + elementId).attr("type", "password");
  }
}
