const BASE_URL = "/Ecommerce";

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
