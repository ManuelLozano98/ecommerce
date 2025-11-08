$(document).ready(function () {
  $("#form").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form");
    if (checkForm(form)) {
      insert();
    }
  });

  $("#form-edit").on("submit", function (e) {
    e.preventDefault();
    const form = getById("form-edit");
    if (checkForm(form)) {
      edit();
    }
  });
  $("#addBtn").on("click", async function (e) {
    e.preventDefault();
    const { data: dataUsername, error: errorUsername } = await apiRequest(
      "api/users/username",
      {
        method: "GET",
      }
    );
    if (dataUsername) {
      $(`#users`).empty();
      let select = getById("users");
      dataUsername.data.forEach((user) => {
        let option = new Option(user.username, user.id);
        select.append(option);
      });
    }
    if (errorUsername) {
      notifyErrorResponse(errorUsername);
    }
    const { data: dataProductsName, error: errorProductsName } =
      await apiRequest("api/products/name", {
        method: "GET",
      });
    if (dataProductsName) {
      $(`#products`).empty();
      let select = getById("products");
      dataProductsName.data.forEach((product) => {
        let option = new Option(product.name, product.id);
        select.append(option);
      });
    }
    if (errorProductsName) {
      notifyErrorResponse(errorProductsName);
    }
  });

  getReviews();
  showFullText();
  setupCounter("title", "counter-title");
  setupCounter("comment", "counter-comment");
  setupCounter("edit-title", "edit-counter-title");
  setupCounter("edit-comment", "edit-counter-comment");
  loadDeleteButton();
  loadEditForm();
  setupSelect2();
});

async function insert() {
  const formElement = getById("form");
  const formData = new FormData(formElement);
  const users = formData.getAll("users[]");
  const products = formData.getAll("products[]");
  let data = null;
  let error = null;
  for (let i = 0; i < users.length; i++) {
    for (let x = 0; x < products.length && i !== users.length; x++) {
      let obj = {
        user_id: parseInt(users[i]),
        product_id: parseInt(products[x]),
        rating: parseFloat(formData.get("rating")),
        comment: formData.get("comment"),
        title: formData.get("title"),
      };
      const { data, error } = await apiRequest("api/reviews", {
        method: "POST",
        body: JSON.stringify(obj),
      });
      if (data) {
        notifySuccessResponse(API_MSGS.Created);
        getDatatable("tableReviews").ajax.reload(null, false);
      }
      if (error) {
        notifyErrorResponse(error);
      }
    }
  }
}

async function deleteItem(id) {
  const msg =
    "<p style='color: red'>Please note that this will also remove associated products and users.</p>";
  const response = await getDeleteMsg(msg);
  if (response.isConfirmed) {
    const { data, error } = await apiRequest(`api/reviews/${id}`, {
      method: "DELETE",
    });
    if (data) {
      notifySuccessResponse(API_MSGS.Deleted);
      getDatatable("tableReviews").ajax.reload(null, false);
    }
    if (error) {
      notifyErrorResponse(error);
    }
  }
}

async function edit() {
  let id = $("#edit-idreview").val();
  let review = {
    user_id: parseInt($("#edit-iduser").val()),
    product_id: parseInt($("#edit-idproduct").val()),
    title: $("#edit-title").val(),
    comment: $("#edit-comment").val(),
    rating: $("#edit-rating").css("--val"),
    active: $("#edit-active").prop("checked") ? 1 : 0,
  };
  const { data, error } = await apiRequest(`api/reviews/${id}`, {
    method: "PUT",
    body: JSON.stringify(review),
  });
  if (data) {
    notifySuccessResponse(API_MSGS.Created);
    getDatatable("tableReviews").ajax.reload(null, false);
  }
  if (error) {
    notifyErrorResponse(error);
  }
}

function getReviews() {
  let loaderTimeout;
  let loaderShownAt = null;
  const MIN_VISIBLE_TIME = 400; // ms
  const DEBOUNCE_DELAY = 200; // ms

  $("#tableReviews").DataTable({
    serverSide: true,
    processing: false,
    ajax: function (data, callback, settings) {
      loaderTimeout = setTimeout(() => {
        $(".loader").show();
        loaderShownAt = Date.now();
      }, DEBOUNCE_DELAY);

      $.ajax({
        url: "api/reviews/detailed",
        method: "GET",
        data: data,
        success: function (response) {
          callback({
            draw: response.draw,
            recordsTotal: response.recordsTotal,
            recordsFiltered: response.recordsFiltered,
            data: response.data,
          });
        },
        error: function (xhr, status, error) {
          console.error("An error occurred while loading data:", error);
          callback({
            draw: data.draw,
            recordsTotal: 0,
            recordsFiltered: 0,
            data: [],
          });
          toastr.error(error);
        },
        complete: function () {
          clearTimeout(loaderTimeout);
          if (!loaderShownAt) return;

          const elapsed = Date.now() - loaderShownAt;
          const remainingTime = MIN_VISIBLE_TIME - elapsed;

          if (remainingTime > 0) {
            setTimeout(() => {
              $(".loader").hide();
              loaderShownAt = null;
            }, remainingTime);
          } else {
            $(".loader").hide();
            loaderShownAt = null;
          }
        },
      });
    },
    ...getSettingsDataTable(),
    buttons: getButtonsDataTable(),
    dom: getDomStyleDataTable(),
    columns: [
      { data: "id" },
      {
        data: "username",
      },
      {
        data: "product_name",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "rating",
        render: function (data, type, row) {
          return data + "/5";
        },
      },
      {
        data: "title",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "comment",
        render: function (data) {
          return truncateText(data);
        },
      },
      {
        data: "created_at",
      },
      {
        data: "updated_at",
      },
      {
        data: "active",
        render: function (data) {
          return data === false ? "No" : "Yes";
        },
      },
      getActionsColumnDataTable(),
    ],
  });
}

function loadEditForm() {
  $("#tableReviews").on("click", ".edit-button", function () {
    const table = $("#tableReviews").DataTable();
    let row = $(this).closest("tr");

    if (row.hasClass("child")) {
      row = row.prev(); // for responsive rows
    }

    const data = table.row(row).data();
    const titleLimit = 255;
    const commentLimit = 255;
    let $title = $("#edit-title");
    let $comment = $("#edit-comment");
    const $editCounterTitle = $("#edit-counter-title");
    const $editCounterComment = $("#edit-counter-comment");

    $title.val(data.title);
    $comment.val(data.comment);
    $("#edit-idreview").val(data.id);
    $("#edit-active").prop("checked", data.active === 1);
    $("#edit-iduser").val(data.user_id);
    $("#edit-idproduct").val(data.product_id);
    $("#edit-rating").css("--val", data.rating);

    updateCounter($title, $editCounterTitle, titleLimit);
    updateCounter($comment, $editCounterComment, commentLimit);
  });
}

function showFullText() {
  $("#tableReviews").on("click", ".view-full-text", function () {
    $("#modal-body").empty();
    const fullText = decodeURIComponent($(this).data("full"));
    $("#modal-body").append(`<p>${fullText}</p>`);
    $("#viewModalText").modal("show");
  });
}

function setupCounter(inputId, counterId, limit = 255) {
  getById(inputId).addEventListener("input", function () {
    updateCounter(this, getById(counterId), limit);
  });
}

function loadDeleteButton() {
  document.addEventListener("click", function (e) {
    let deleteBtn = e.target.closest('[id^="btn-delete_"]');
    if (deleteBtn) {
      let id = deleteBtn.id.split("_")[1];
      deleteItem(id);
    }
  });
}
function setupSelect2() {
  $(".select2").select2();
}
